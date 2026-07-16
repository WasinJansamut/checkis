<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Models\LibHospcode;
use App\Models\LibUserLevel;
use App\Models\UserSession;

class AuthCallbackController extends Controller
{
    public function handle(Request $request)
    {
        /*
            เห็นได้แค่โรงพยาบาลตัวเอง
            ถ้าเป็น สสจ. ให้เห็นแค่โรงพยาบาลจังหวัดตัวเอง
            ถ้าเป็น สคร. ให้เห็นจังหวัดในเขตตัวเอง
            ยูเซอร์โรงพยาบาลสามารถเห็นทุกรายงาน
            ส่วนยูเซอร์อื่นๆมันจะมีบางรายงานที่ไม่เห็น
        */

        if (config('app.is_get_token_from_pher_plus') == 0) {
            return redirect()->route('home')->with('clear_local_storage', true);
        }

        if ($request->query('ex') == 'true' && in_array($request->getHost(), ['localhost', '127.0.0.1'])) {
            Session::put('pending_auth_callback', [
                'example' => true,
            ]);

            return redirect()->route('auth_loading');
        }

        // Parameter ที่ได้รับ ?kw=is-checking-5630-XXOfVFubrA4ZWgSj6gQre0nwnyPXVyal&task=is-checking
        $token = $request->query('kw');
        $task = $request->query('task');

        if (user_info() && !$token) {
            return redirect()->route('home');
        }

        if (!$token || $task != 'is-checking') {
            return redirect()->route('auth_callback')->with('warning', 'กรุณาเข้าผ่าน PHER PLUS ในเมนู "IS Checking"');
        }

        Session::put('pending_auth_callback', [
            'token' => $token,
            'task' => $task,
            'received_at' => now()->format('Y-m-d H:i:s'),
        ]);

        return redirect()->route('auth_loading');
    }

    public function loading()
    {
        if (!Session::has('pending_auth_callback')) {
            return redirect()->route('auth_callback')->with('warning', 'กรุณาเข้าผ่าน PHER PLUS ในเมนู "IS Checking"');
        }

        return view('auth_loading');
    }

    public function process(Request $request)
    {
        /*
            เห็นได้แค่โรงพยาบาลตัวเอง
            ถ้าเป็น สสจ. ให้เห็นแค่โรงพยาบาลจังหวัดตัวเอง
            ถ้าเป็น สคร. ให้เห็นจังหวัดในเขตตัวเอง
            ยูเซอร์โรงพยาบาลสามารถเห็นทุกรายงาน
            ส่วนยูเซอร์อื่นๆมันจะมีบางรายงานที่ไม่เห็น
        */

        if (config('app.is_get_token_from_pher_plus') == 0) {
            return redirect()->route('home')->with('clear_local_storage', true);
        }

        $pending_auth = Session::get('pending_auth_callback', []);

        if (($pending_auth['example'] ?? false) === true && in_array($request->getHost(), ['localhost', '127.0.0.1'])) {
            Session::forget('pending_auth_callback');
            $this->put_session_example();
            return redirect()->route('home')->with('clear_local_storage', true);
        }

        $token = $pending_auth['token'] ?? null;
        $task = $pending_auth['task'] ?? null;

        if (user_info() && !$token) {
            Session::forget('pending_auth_callback');
            return redirect()->route('home');
        }

        if (!$token || $task != 'is-checking') {
            Session::forget('pending_auth_callback');
            return redirect()->route('auth_callback')->with('warning', 'กรุณาเข้าผ่าน PHER PLUS ในเมนู "IS Checking"');
        }

        try {
            $response = $this->requestPherUserDetail($token);

            if (!$response->successful()) {
                $error_detail = $this->getApiErrorDetail($response);

                Log::warning('API response unsuccessful', [
                    'status_code' => $response->status(),
                    'response_body' => $response->body(),
                    'error_detail' => $error_detail,
                ]);

                return redirect()->route('auth_callback')->with(
                    'danger',
                    'ไม่สามารถเชื่อมต่อระบบหลักได้' . ($error_detail ? ' : ' . $error_detail : '')
                );
            }

            $data = $response->json();
            $user = (array) ($data['user'] ?? []);

            if (isset($data['status']) && $data['status'] !== 200) {
                Log::warning('Token API error', [
                    'status' => $data['status'],
                    'message' => $data['message'] ?? ''
                ]);
                return redirect()->route('auth_callback')->with('danger', 'Token Error: ' . ($data['message'] ?? 'Unknown error'));
            }

            if (empty($user)) {
                return redirect()->route('auth_callback')->with('warning', 'ไม่พบข้อมูลผู้ใช้งาน');
            }

            $hosp = LibHospcode::select('region', 'name', 'changwatcode')
                ->when(!empty($user['hcode']), function ($query) use ($user) {
                    $query->where('off_id', $user['hcode']);
                })
                ->whereNotNull('off_id')
                ->first();

            $user_level = LibUserLevel::select('name', 'type', 'type_user')
                ->when(!empty($user['user_level']), function ($query) use ($user) {
                    $query->where('code', $user['user_level']);
                })
                ->first();

            $user_level_code = $user_level->type ?? null;
            $user_type = $user_level->type_user ?? null;

            if (($user['user_level'] ?? null) == 12) {
                $user_level_code = 'MOPH';
                $user_type = 'ADMIN';
            }

            $user_data = [
                'session_id'      => Session::getId(),
                'token'           => $token,
                'uid'             => $user['uid'] ?? null,
                'name'            => $user['name'] ?? null, // ชื่อผู้ใช้งาน
                'email'           => $user['email'] ?? null, // อีเมล
                'position'        => $user['position'] ?? null, // ตำแหน่ง
                'hosp_code'       => $user['hcode'] ?? null, // รหัสโรงพยาบาล
                'hosp_code9'      => $user['hcode9'] ?? null,
                'hosp_name'       => $hosp->name ?? null, // ชื่อโรงพยาบาล
                'region'          => $hosp->region ?? null, // เขตสุขภาพ
                'province_code'   => $hosp->changwatcode ?? null, // รหัสจังหวัด
                'user_level'      => $user['user_level'] ?? null, // ระดับใช้งาน
                'user_level_name' => $user_level->name ?? null,
                'user_level_code' => $user_level_code, // REGION, MOPH, PROV, HOSP, DDPM, AMP, POLICE, OTHER
                'user_type'       => $user_type, // SUPER ADMIN, ADMIN, USER
                'login_at'        => now()->format('Y-m-d H:i:s'), // วันที่เข้าสู่ระบบ
                'last_active'     => now()->format('Y-m-d H:i:s'), // วันที่ใช้งานล่าสุด (เพื่อไว้เช็คว่าหมดอายุ Session)
                'ip'              => $user['ip'] ?? null,
            ];

            $old_session_id = Session::getId();
            Session::regenerate();
            if (Config::get('session.driver') === 'file') {
                $old_session_file = storage_path("framework/sessions/{$old_session_id}");
                if (file_exists($old_session_file)) {
                    @unlink($old_session_file);
                }
            }
            // dd($data, $user_data);
            Session::forget('user_info'); // ถ้าอยากเคลียร์ค่าเดิม
            Session::put('user_info', $user_data); // เก็บข้อมูลผู้ใช้งานใน Session (เฉพาะข้อมูลจำเป็น)
            UserSession::updateOrCreate(['uid' => $user['uid']], $user_data); // บันทึกการเข้าสู่ระบบ
            Session::forget('pending_auth_callback');

            return redirect()->route('home')->with('clear_local_storage', true);
        } catch (\Exception $e) {
            Log::error('API request failed', [
                'exception' => $e->getMessage(),
            ]);
            return redirect()->route('auth_callback')->with(
                'danger',
                'ไม่สามารถเชื่อมต่อระบบหลักได้ : ' . $e->getMessage()
            );
        }
    }

    private function requestPherUserDetail(string $token): Response
    {
        $url = config('app.pher_plus_user_detail_api', 'https://connect.moph.go.th/is-api-3/pher/user-detail/');
        $host = parse_url($url, PHP_URL_HOST) ?: 'connect.moph.go.th';
        $fallbackIp = config('app.pher_plus_host_ip');
        $attempts = 3;
        $lastException = null;

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                $options = [
                    'connect_timeout' => 5,
                    'force_ip_resolve' => 'v4',
                ];

                if ($fallbackIp && $attempt === $attempts && defined('CURLOPT_RESOLVE')) {
                    $options['curl'] = [
                        CURLOPT_RESOLVE => ["{$host}:443:{$fallbackIp}"],
                    ];
                }

                Log::info('PHER user-detail request attempt', [
                    'attempt' => $attempt,
                    'host' => $host,
                    'uses_fallback_ip' => !empty($options['curl'][CURLOPT_RESOLVE] ?? null),
                ]);

                return Http::withToken($token)
                    ->withOptions($options)
                    ->timeout(10)
                    ->get($url . $token);
            } catch (ConnectionException $e) {
                $lastException = $e;

                Log::warning('PHER user-detail connection failed', [
                    'attempt' => $attempt,
                    'host' => $host,
                    'fallback_ip' => $fallbackIp,
                    'message' => $e->getMessage(),
                ]);

                if (!$this->isDnsResolutionError($e->getMessage())) {
                    throw $e;
                }

                if ($attempt < $attempts) {
                    usleep(500000);
                }
            }
        }

        throw $lastException ?: new \RuntimeException('ไม่สามารถเชื่อมต่อระบบหลักได้');
    }

    private function getApiErrorDetail(Response $response): string
    {
        $details = [];

        if ($response->status()) {
            $details[] = 'HTTP ' . $response->status();
        }

        $json = $response->json();

        if (is_array($json)) {
            $message = $json['message'] ?? $json['error'] ?? $json['detail'] ?? null;
            if (is_string($message) && trim($message) !== '') {
                $details[] = trim($message);
            }
        }

        if (empty($details)) {
            $body = trim($response->body());
            if ($body !== '') {
                $details[] = mb_strimwidth($body, 0, 300, '...');
            }
        }

        return implode(' | ', $details);
    }

    private function isDnsResolutionError(string $message): bool
    {
        $message = strtolower($message);

        return strpos($message, 'resolving timed out') !== false
            || strpos($message, 'could not resolve host') !== false
            || strpos($message, 'getaddrinfo') !== false;
    }

    private function put_session_example()
    {
        $user_data = [
            'session_id'      => Session::getId(),
            'token'           => 'kw=is-checking-5630-gpnicIDBY4hhTltXslG4PCiu0a9uMs8I',
            'uid'             => 9999,
            'name'            => 'นายทดสอบระบบ', // ชื่อผู้ใช้งาน
            'email'           => 'wisarutsj1996@gmail.com', // อีเมล
            'position'        => 'นักวิชาการคอมพิวเตอร์', // ตำแหน่ง
            'hosp_code'       => '10697', // รหัสโรงพยาบาล
            'hosp_code9'      => 'EA0010697',
            'hosp_name'       => 'โรงพยาบาลพุทธโสธร', // ชื่อโรงพยาบาล
            'region'          => '06', // เขตสุขภาพ
            'province_code'   => '24', // รหัสจังหวัด
            'user_level'      => '9', // ระดับใช้งาน
            'user_level_name' => 'ระดับเขต',
            'user_level_code' => 'MOPH', // REGION, MOPH, PROV, HOSP, DDPM, AMP, POLICE, OTHER
            'user_type'       => 'SUPER ADMIN', // SUPER ADMIN, ADMIN, USER
            'login_at'        => now()->format('Y-m-d H:i:s'), // วันที่เข้าสู่ระบบ
            'last_active'     => now()->format('Y-m-d H:i:s'), // วันที่ใช้งานล่าสุด (เพื่อไว้เช็คว่าหมดอายุ Session)
            'ip'              => '',
        ];

        $old_session_id = Session::getId();
        Session::regenerate();
        if (Config::get('session.driver') === 'file') {
            $old_session_file = storage_path("framework/sessions/{$old_session_id}");
            if (file_exists($old_session_file)) {
                @unlink($old_session_file);
            }
        }
        Session::forget('user_info');
        Session::put('user_info', $user_data);
    }

    public function logout(Request $request)
    {
        Session::flush();
        Session::invalidate();
        Session::regenerateToken();

        return redirect()->route('auth_callback')->with('success', 'ออกจากระบบเรียบร้อย');
    }
}
