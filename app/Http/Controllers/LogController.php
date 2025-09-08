<?php

namespace App\Http\Controllers;

use App\Models\SystemLogModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogController extends Controller
{
    public static function addlog($action, $target = NULL, $detail = NULL)
    {
        $ip = request()->ip();

        $log = new SystemLogModel();
        $log->user_id = user_info('uid');
        $log->first_name = user_info('name') ?? null;
        $log->last_name = null;
        $log->hospital = user_info('hosp_name');
        $log->hospcode = user_info('hosp_code') ?? null;
        $log->ip = $ip ?? null;
        $log->detail = $detail ?? null;
        $log->action = $action ?? null;
        $log->target = $target ?? null;
        $log->save();
    }
}
