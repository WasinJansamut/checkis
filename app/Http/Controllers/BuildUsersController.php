<?php

namespace App\Http\Controllers;

use App\Models\HospcodeModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BuildUsersController extends Controller
{

    public function index(){

        dd('..');
        $rows = HospcodeModel::get();
        $count = 0;

        foreach ($rows as $row){
            $hospcode = $row->hospcode;
            $name = $row->full_name;
            $hospcode_hash = \Illuminate\Support\Facades\Hash::make($hospcode);

            User::create([
                'name' => $name,
                'password' => $hospcode_hash,
                'username' => $hospcode,
                'type'=> 0
            ]);
            $count = $count + 1;

            dump($count);
        }

        dd('DONE');
    }
}
