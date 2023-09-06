<?php

namespace App\Http\Controllers;

use App\Models\SystemLogModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogController extends Controller
{
    public static function addlog($action,$target = NULL,$detail = NULL){
        $user = Auth::user();
        $ip = request()->ip();

        $log = new SystemLogModel();

        $log->user_id = $user->id;
        $log->first_name = $user->firstname;
        $log->last_name = $user->lastname;
        $log->hospital = $user->name;
        $log->hospcode = $user->username;
        $log->ip = $ip;
        $log->detail = $detail;
        $log->action = $action;
        $log->target = $target;

        $log->save();

    }

}
