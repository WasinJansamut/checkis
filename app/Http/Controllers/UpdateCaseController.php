<?php

namespace App\Http\Controllers;

use App\Models\CasesModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UpdateCaseController extends Controller
{
    public function index($id){
        $case = CasesModel::where('id',$id)->first();


        return view('update_case',['case'=>$case]);
    }

    public function submit(Request $request){
        $request->validate([
            'name' =>'required',
            'error_type' => 'required'
        ]);
//        $count = CasesModel::where('number',$request->input('number'))->where('id','!=',$request->input('id'))->count();
//        if($count > 0){
//            $id = $request->input('id');
//            Session::flash("duplicated case");
//            return redirect()->route('update_case_controller',[$id]);
//        }

        $case = CasesModel::where('id',$request->input('id'))->first();
//        $case->number = $request->input('number');
        $case->name = $request->input('name');
        $case->errorType = $request->input('error_type');
        $case->save();


        LogController::addlog("edit","case",$case);


//        Session::flash("success");
        return redirect('/manage/cases');
    }
}
