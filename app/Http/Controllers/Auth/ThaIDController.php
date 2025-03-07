<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\HospcodeModel;

class ThaIDController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('index_register_step_2', 'update_register_step_2');
    }

    private function check_http($url)
    {
        // ใช้ cURL ตรวจสอบ HTTP status
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // ให้ cURL ตามการ redirect
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // ส่งคืนผลลัพธ์
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HEADER, true); // ดึง Header กลับมาด้วย
        curl_setopt($ch, CURLOPT_NOBODY, true); // ไม่ต้องโหลดเนื้อหาทั้งหมด
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_exec($ch); // ทำการเชื่อมต่อ

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // รับค่า HTTP status code
        $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL); // ดึง URL ที่ redirect ไป
        $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); // URL ที่ถูก redirect ไป
        $error = curl_error($ch); // เก็บข้อความ error ถ้ามี

        curl_close($ch);

        return [
            'http_code' => $httpCode,
            'redirect_url' => $redirectUrl,
            'effective_url' => $effectiveUrl,
            'response' => $response,
            'error' => $error,
            'info' => $info,
        ];
    }


    public function thaid()
    {
        Session::forget('thaid_state');
        $state = bin2hex(random_bytes(16));
        $redirect_success = urlencode(route('login.thaid.check'));
        $redirect_fail = urlencode('https://rti.moph.go.th/thaiid/rtidc/fail.php');
        $url = "https://rti.moph.go.th/thaiid/rtidc/index.php?state=$state&redirect_success=$redirect_success&redirect_fail=$redirect_fail";
        $check_http = $this->check_http($url);
        // dd($check_http);

        // ตรวจสอบว่า URL ที่เราตรวจสอบนั้น มีการ redirect ไปที่ไหน
        if ($check_http['http_code'] == 200) {
            // ตรวจสอบว่า URL มีการ redirect ไปที่ไหนหรือไม่
            // if ($redirectUrl) {
            //     // ถ้ามีการ redirect ไปที่อื่น
            //     return redirect($redirectUrl);
            // } else {
            //     // ถ้าไม่มีการ redirect ไปที่ไหน ก็ redirect ตามปกติ
            //     Session::put('thaid_state', $state);
            //     Session::save();
            //     return redirect($url);
            // }

            Session::put('thaid_state', $state);
            Session::save();
            return redirect($url);
        } else {
            return redirect()->route('login')->with('danger', "ไม่สามารถเชื่อมต่อกับ ThaID ได้ (HTTP Code: {$check_http['http_code']}, Error: {$check_http['error']})");
        }
    }

    public function check_login_thaid(Request $request)
    {
        $state = Session::get('thaid_state') ?? null;
        if (empty($state)) {
            return redirect()->route('login')->with('danger', '[ERROR: 001] เกิดข้อผิดพลาดในการเข้าสู่ระบบด้วย ThaID');
        }

        // $client = new \GuzzleHttp\Client();
        $client = new \GuzzleHttp\Client([
            'verify' => false, // ปิดการตรวจสอบ SSL
        ]);
        $headers = [
            'Content-Type' => 'application/json'
        ];
        $body = json_encode([
            'state' => $state
        ]);
        $request = new \GuzzleHttp\Psr7\Request('POST', 'https://rti.moph.go.th/thaiid/rtidc/check.php', $headers, $body);
        $res = $client->sendAsync($request)->wait();
        // dd($state, $res->getBody());
        // echo "<br><br>";

        $json_data = json_decode($res->getBody(), true);
        if (!isset($json_data['success']) || !$json_data['success']) {
            return redirect()->route('login')->with('danger', '[ERROR: 002] เกิดข้อผิดพลาดในการเข้าสู่ระบบด้วย ThaID');
        }

        $personal_data = json_decode($json_data['data']['response_body'], true);
        // dd($personal_data);
        // print_r($personal_data);
        $pid = $personal_data['pid'] ?? null;
        if (!$pid) {
            return redirect()->route('login')->with('danger', '[ERROR: 003] เกิดข้อผิดพลาดในการเข้าสู่ระบบด้วย ThaID');
        }

        $user = User::where('cid', $pid)->latest()->first();
        if (empty($user)) {
            $given_name = $personal_data['given_name'] ?? null;
            $family_name = $personal_data['family_name'] ?? null;

            $user = new User;
            $user->cid = $pid;
            // $user->name = "$given_name $family_name";
            $user->firstname = $given_name;
            $user->lastname = $family_name;
            $user->birth_date = $personal_data['birthdate'] ?? null;
            $user->address = $personal_data['address']['formatted'] ?? null;
            $user->type = 0;
            $user->register_from = "ThaID";
            $user->save();
        }

        // มีผู้ใช้งานในระบบแล้ว ให้เข้าสู่ระบบได้เลย
        // Auth::login($user);
        // Auth::loginUsingId($user->id);  // เข้าสู่ระบบโดยใช้ ID ของผู้ใช้
        Session::forget('thaid_state');
        Auth::guard('web')->login($user);

        return view('home_thaid')->with('redirect', true); // ที่ใช้แบบนี้เพราะมัน redirect()->route('home') แล้วไม่เก็บค่า login
    }

    public function index_register_step_2()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('danger', 'ไม่พบข้อมูลผู้ใช้งานของคุณในระบบ');
        }

        if (Auth::user()->username && empty(Auth::user()->type) && Auth::user()->type !== 0) { // ถ้ามี username (รหัส รพ.) ให้ไปหน้าหลักเลย
            return redirect()->url('/');
        }

        $hospitals = HospcodeModel::get();

        return view('auth.thaid.register_step_2', compact('hospitals'));
    }

    public function update_register_step_2(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('danger', 'ไม่พบข้อมูลผู้ใช้งานของคุณในระบบ');
        }

        $user = User::findOrFail(Auth::user()->id);
        if ($user->username && (empty($user->type) && $user->type !== 0)) { // ถ้ามี username, type ให้ไปหน้าหลักเลย
            return redirect()->url('/');
        }

        $hospital = HospcodeModel::where('hospcode', $request->hospcode)->first();

        $user->name = $hospital->full_name;
        $user->username = $hospital->hospcode;
        $user->type = $hospital->type_user;
        $user->update();

        return redirect()->route('home');
    }
}
