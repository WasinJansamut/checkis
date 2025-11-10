<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ManageUsers extends Controller
{
    public function index()
    {
        $username = "";
        if (user_info('user_level_code') == 'MOPH' && user_info('user_type') == 'SUPER ADMIN') {
            $users = User::paginate(20);
            $usersAll = User::get();

            return view('manage_users', ['users' => $users, 'usersAll' => $usersAll, 'username' => $username]);
        } else {
            return redirect()->route('present_report');
        }
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $usersAll = User::get();

        $users = User::where('username', $search)->paginate(1);
        $username = $users[0]->username;

        if (empty($users)) {
            Session::flash('no data');
            return redirect()->route('manage_users');
        }

        return view('manage_users', ['users' => $users, 'usersAll' => $usersAll, 'username' => $username]);
    }
}
