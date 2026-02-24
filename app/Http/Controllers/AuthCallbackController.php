<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

        // เข้าตรงไม่ผ่่าน Pher Plus (สำหรับทดสอบ) ผ่าน localhost หรือ 127.0.0.1 เท่านั้น (http://localhost:8000/?ex=true)
        $host = explode(':', $request->server('HTTP_HOST'))[0]; // ตัด port ออก
        if ($request->query('ex') == 'true' && in_array($host, ['localhost', '127.0.0.1'])) {
            $this->put_session_example();
            return redirect()->route('home')->with('clear_local_storage', true);
        }

        // Parameter ที่ได้รับ ?kw=is-checking-5630-XXOfVFubrA4ZWgSj6gQre0nwnyPXVyal&task=is-checking
        $token = $request->query('kw'); // รับ token kw=is-checking-5630-XXOfVFubrA4ZWgSj6gQre0nwnyPXVyal
        $task = $request->query('task'); // รับ token task=is-checking

        if (user_info() && !$token) {
            return redirect()->route('home');
        }

        if (!$token || $task != 'is-checking') {
            return redirect()->route('auth_callback')->with('warning', 'กรุณาเข้าผ่าน Pher Plus ในเมนู "IS Checking"');
        }

        try {
            // เรียก API ตรวจ Token
            $response = Http::timeout(10) // timeout ป้องกัน request ค้าง
                ->get('https://connect.moph.go.th/is-api-3/pher/user-detail/' . $token);

            if (!$response->successful()) {
                return redirect()->route('auth_callback')->with('danger', '[ERROR001] ไม่สามารถเชื่อมต่อระบบหลักได้');
            }

            $data = $response->json();
            $user = (array) ($data['user'] ?? []);

            // ตรวจ status ของ API
            if (isset($data['status']) && $data['status'] !== 200) {
                Log::warning('Token API error', [
                    'status' => $data['status'],
                    'message' => $data['message'] ?? ''
                ]);
                return redirect()->route('auth_callback')->with('danger', 'Token Error: ' . ($data['message'] ?? 'Unknown error'));
            }

            // ตรวจ user object
            if (empty($user)) {
                return redirect()->route('auth_callback')->with('warning', 'ไม่พบข้อมูลผู้ใช้งาน');
            }

            /*
                ตัวอย่างข้อมูล
                {
                    "status": 200,
                    "task": "standard-report",
                    "callback": "https://connect.moph.go.th/pher-plus-beta/#/report-moph/dashboard-hospital",
                    "user": {
                        "uid": 1085,
                        "name": "นายทดสอบระบบ",
                        "position": "นักวิชาการคอมพิวเตอร์",
                        "hcode": "10733",
                        "user_level": 7
                    }
                }

                user_level
                0   ไม่ระบุ                                  OTHER       USER
                1   ระดับสถานพยาบาล - ผู้บันทึกข้อมูลอย่างเดียว     HOSP        USER
                2   ระดับสถานพยาบาล - ผู้ให้การรักษา/ดูแลผู้ป่วย    HOSP        USER
                3   ระดับสถานพยาบาล - IT/Admin ระบบ         HOSP        ADMIN
                4   ระดับสถานพยาบาล - ผู้บริหาร                HOSP        USER
                5   ระดับ สสจ.                              PROV        ADMIN
                6   ระดับเขต                                REGION      ADMIN
                7   ระดับ คร./กสธฉ. กระทรวงสาธารณสุข         MOPH        ADMIN
                8   ปภ. กระทรวงมหาดไทย                     DDPM        USER
                9   Super Admin                            MOPH        SUPER ADMIN
                10  ระดับ สสอ.                              AMP         USER
                11  PCU (รพ.สต., ศสช.)                     HOSP        USER
                12  ระดับกรม,กอง ในกระทรวงสาธารณสุข          HOSP        USER
                20  ตำรวจ                                  POLICE      USER
                22  บริษัทกลาง                               OTHER       USER
                24  แขวงการทาง                             OTHER       USER
                26  ขนส่ง                                   OTHER       USER
                99  หน่วยงาน อื่น ๆ                           OTHER       USER
            */

            // หาจังหวัด จากรหัสโรงพยาบาล
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

            $user_data = [
                'session_id'      => Session::getId(),
                'token'           => $token,
                'uid'             => $user['uid'] ?? null,
                'name'            => $user['name'] ?? null, // ชื่อผู้ใช้งาน
                'email'            => $user['email'] ?? null, // อีเมล
                'position'        => $user['position'] ?? null, // ตำแหน่ง
                'hosp_code'       => $user['hcode'] ?? null, // รหัสโรงพยาบาล
                'hosp_name'       => $hosp->name ?? null, // ชื่อโรงพยาบาล
                'region'          => $hosp->region ?? null, // เขตสุขภาพ
                'province_code'   => $hosp->changwatcode ?? null, // รหัสจังหวัด
                'user_level'      => $user['user_level'] ?? null, // ระดับใช้งาน
                'user_level_name' => $user_level->name ?? null,
                'user_level_code' => $user_level->type ?? null, // REGION, MOPH, PROV, HOSP, DDPM, AMP, POLICE, OTHER
                'user_type'       => $user_level->type_user ?? null, // SUPER ADMIN, ADMIN, USER
                'login_at'        => now()->format('Y-m-d H:i:s'), // วันที่เข้าสู่ระบบ
                'last_active'     => now()->format('Y-m-d H:i:s'), // วันที่ใช้งานล่าสุด (เพื่อไว้เช็คว่าหมดอายุ Session)
            ];

            $old_session_id = Session::getId();
            Session::regenerate(); // Regenerate Session ป้องกัน Session Fixation
            if (Config::get('session.driver') === 'file') {
                $old_session_file = storage_path("framework/sessions/{$old_session_id}");
                if (file_exists($old_session_file)) {
                    @unlink($old_session_file);
                }
            }
            Session::forget('user_info'); // ถ้าอยากเคลียร์ค่าเดิม
            Session::put('user_info', $user_data); // เก็บข้อมูลผู้ใช้งานใน Session (เฉพาะข้อมูลจำเป็น)
            UserSession::updateOrCreate(['uid' => $user['uid']], $user_data); // บันทึกการเข้าสู่ระบบ

            return redirect()->route('home')->with('clear_local_storage', true);;
        } catch (\Exception $e) {
            // จับ Exception ทั้งหมด ป้องกัน API down / Network Error
            Log::error('API request failed', [
                'exception' => $e->getMessage(),
            ]);
            return redirect()->route('auth_callback')->with('danger', '[ERROR002] ไม่สามารถเชื่อมต่อระบบหลักได้');
        }
    }

    private function put_session_example()
    {
        $user_data = [
            'session_id'      => Session::getId(),
            'token'           => 'kw=is-checking-5630-gpnicIDBY4hhTltXslG4PCiu0a9uMs8I',
            'uid'             => 9999,
            'name'            => 'นายทดสอบระบบ', // ชื่อผู้ใช้งาน
            'email'            => null, // อีเมล
            'position'        => 'เทสระบบ', // ตำแหน่ง
            'hosp_code'       => '14633', // รหัสโรงพยาบาล
            'hosp_name'       => 'สำนักงานป้องกันควบคุมโรคที่ 2 จังหวัดพิษณุโลก', // ชื่อโรงพยาบาล
            'region'          => '02', // เขตสุขภาพ
            'province_code'   => '65', // รหัสจังหวัด
            'user_level'      => '6', // ระดับใช้งาน
            'user_level_name' => 'ระดับเขต',
            'user_level_code' => 'REGION', // REGION, MOPH, PROV, HOSP, DDPM, AMP, POLICE, OTHER
            'user_type'       => 'ADMIN', // SUPER ADMIN, ADMIN, USER
            'login_at'        => now()->format('Y-m-d H:i:s'), // วันที่เข้าสู่ระบบ
            'last_active'     => now()->format('Y-m-d H:i:s'), // วันที่ใช้งานล่าสุด (เพื่อไว้เช็คว่าหมดอายุ Session)
        ];

        $old_session_id = Session::getId();
        Session::regenerate(); // Regenerate Session ป้องกัน Session Fixation
        if (Config::get('session.driver') === 'file') {
            $old_session_file = storage_path("framework/sessions/{$old_session_id}");
            if (file_exists($old_session_file)) {
                @unlink($old_session_file);
            }
        }
        Session::forget('user_info'); // ถ้าอยากเคลียร์ค่าเดิม
        Session::put('user_info', $user_data); // เก็บข้อมูลผู้ใช้งานใน Session (เฉพาะข้อมูลจำเป็น)
    }

    public function logout(Request $request)
    {
        // เคลียร์ session ทั้งหมด
        Session::flush();

        // regenerate session id เพื่อความปลอดภัย
        Session::invalidate();
        Session::regenerateToken();

        return redirect()->route('auth_callback')->with('success', 'ออกจากระบบเรียบร้อย');
    }
}
