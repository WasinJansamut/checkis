<?php

if (!function_exists('user_info')) {
    function user_info($key = null, $default = null, $reload = false)
    {
        static $cached_user_info = null;

        if ($cached_user_info === null || $reload) {
            $cached_user_info = session('user_info', []);
        }

        if ($key === null) return $cached_user_info;
        return $cached_user_info[$key] ?? $default;
    }
}
