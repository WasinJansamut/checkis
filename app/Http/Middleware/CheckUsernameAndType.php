<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUsernameAndType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        // ตรวจสอบว่า username และ type เป็น null, false, "" (string ว่าง), 0, 0.0, หรือ [] (array ว่าง) หรือไม่
        if (empty($user->username) || (empty($user->type) && $user->type === '')) {
            // ถ้าเป็น null หรือว่าง ให้ redirect ไปยังหน้าอื่น (เช่น '/another-page')
            return redirect()->route('thaid.index_register_step_2');
        }

        return $next($request);
    }
}
