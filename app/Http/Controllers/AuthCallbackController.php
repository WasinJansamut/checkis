<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AuthCallbackController extends Controller
{
    public function handle(Request $request)
    {
        // For testing purposes, we can mock the response
        if ($request->has('mock')) {
            $token = $request->query('token', 'mock-token');
            return response()->view('auth.mockup', ['token' => $token]);
        }

        $token = $request->query('token');

        if (!$token) {
            return redirect()->route('home')->with('danger', 'ไม่พบ Token');
        }

        // เรียก API ระบบหลักเพื่อเช็ค token
        $response = Http::withToken($token)->get('https://main-system.example.com/api/check_token');

        if ($response->successful()) {
            $data = $response->json();

            if ($data['valid']) {

                // ตัวอย่าง Response จากระบบหลัก
                //                 {
                //   "valid": true,
                //   "name": "นายสมชาย ใจดี",
                //   "role": "admin",
                //   "organization": "สำนักงานสาธารณสุขจังหวัดพิษณุโลก"
                // }
                // เก็บข้อมูลไว้ใน session หรือ storage
                Session::put('user_info', [
                    'name' => $data['name'],
                    'role' => $data['role'],
                    'organization' => $data['organization'],
                    'token' => $token,
                ]);

                return redirect()->route('report_standard.index')->with('success', 'เข้าสู่ระบบสำเร็จ');
            } else {
                return redirect()->route('home')->with('danger', 'Token หมดอายุหรือไม่ถูกต้อง');
            }
        }

        return redirect()->route('home')->with('danger', 'ไม่สามารถเชื่อมต่อระบบหลักได้');
    }
}
