<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;

class CheckUserSession
{
    public function handle($request, Closure $next)
    {
        if (!user_info()) {
            return redirect()->route('auth_callback')->with('warning', 'กรุณาเข้าผ่าน Pher Plus ในเมนู "IS Checking"');
        }

        try {
            $login_at = Carbon::createFromFormat('Y-m-d H:i:s', user_info('login_at'));
            $last_active = Carbon::createFromFormat('Y-m-d H:i:s', user_info('last_active'));
        } catch (\Exception $e) {
            $this->expireSession();
            return redirect()->route('auth_callback')->with('warning', 'ข้อมูล Token ไม่ถูกต้อง');
        }

        // SESSION_LIFETIME จาก config/session.php (หน่วยเป็นนาที)
        $session_lifetime = Config::get('session.lifetime', 1440);

        // หมดอายุ inactivity
        if (now()->diffInMinutes($last_active) > $session_lifetime) {
            $this->expireSession();
            return redirect()->route('auth_callback')->with('warning', 'Token หมดอายุ (ไม่มีการใช้งานนาน)');
        }

        // ต่ออายุ last_active ทุก request
        Session::put('user_info.last_active', now()->format('Y-m-d H:i:s'));

        return $next($request);
    }

    // ลบไฟล์ session ของ Laravel ถ้า driver เป็น file
    protected function deleteSessionFile()
    {
        if (Config::get('session.driver') === 'file') {
            $session_id = Session::getId();
            $session_file = storage_path("framework/sessions/{$session_id}");

            if (file_exists($session_file)) {
                session()->save(); // save ข้อมูลปัจจุบัน
                session_write_close(); // ปิด session
                @unlink($session_file);
            }
        }
    }

    protected function expireSession()
    {
        // $this->deleteSessionFile();
        Session::flush();
        cookie()->queue(cookie()->forget(config('session.cookie'))); // ลบ cookie ของ session
    }
}
