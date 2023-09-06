<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UpdatePasswordController extends Controller
{
    public function index($id){
        $user = User::where('id',$id)->first();

        return view('update_password',['user'=>$user]);
    }

    public function submit(Request $request){

        $request->validate([
            'password'=>'confirmed',
        ]);
        $user = User::where('id',$request->input('id'))->first();

        if($request->password != null){
            $user->password = Hash::make($request->password);
        }

        if($request->email != null){
            $user->email = $request->email;
        }

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->phone = $request->phone;
        $user->save();

        Session::flash("success");
        return redirect('/manage/users');
    }
}
