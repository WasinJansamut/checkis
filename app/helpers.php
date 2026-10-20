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

if (!function_exists('user_has_level')) {
    function user_has_level($group)
    {
        return in_array((int) user_info('user_level'), config("user_levels.{$group}", []), true);
    }
}

if (!function_exists('user_has_any_level')) {
    function user_has_any_level(array $groups)
    {
        foreach ($groups as $group) {
            if (user_has_level($group)) {
                return true;
            }
        }

        return false;
    }
}
