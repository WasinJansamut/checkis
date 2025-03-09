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
        $user = Auth::user();
        $ip = request()->ip();

        $log = new SystemLogModel();

        $log->user_id = $user->id;
        $log->first_name = $user->firstname ?? null;
        $log->last_name = $user->lastname ?? null;
        $log->hospital = $user->name ?? null;
        $log->hospcode = $user->username ?? null;
        $log->ip = $ip ?? null;
        $log->detail = $detail ?? null;
        $log->action = $action ?? null;
        $log->target = $target ?? null;
        $log->save();
    }
}
