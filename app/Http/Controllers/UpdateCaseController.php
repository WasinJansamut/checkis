<?php

namespace App\Http\Controllers;

use App\Models\CasesModel;
use App\Models\ErrorTypeModel;
use App\Models\IsModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UpdateCaseController extends Controller
{
    public function index($id)
    {
        $case = CasesModel::where('id', $id)->first();
        $selectedFields = json_decode($case->check_fields ?? '', true);
        if (!is_array($selectedFields)) {
            $selectedFields = array_filter(array_map('trim', explode(',', $case->check_fields ?? '')));
        }
        $fields = array_keys(IsModel::first()->toArray());
        $errorTypes = ErrorTypeModel::where('is_using', true)->get();

        return view('update_case', ['case' => $case, 'fields' => $fields, 'selectedFields' => $selectedFields, 'errorTypes' => $errorTypes]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'error_type' => 'required'
        ]);
        // $count = CasesModel::where('number', $request->input('number'))->where('id', '!=', $request->input('id'))->count();
        // if ($count > 0) {
        //     $id = $request->input('id');
        //     Session::flash("duplicated case");
        //     return redirect()->route('update_case_controller', [$id]);
        // }

        $case = CasesModel::where('id', $request->input('id'))->first();
        // $case->number = $request->input('number');
        $case->name = $request->input('name');
        $case->description = $request->input('description');
        $case->check_fields = json_encode(array_values(array_unique($request->input('check_fields', []))), JSON_UNESCAPED_UNICODE);
        $case->errorType = $request->input('error_type');
        $case->save();


        LogController::addlog("edit", "case", $case);


        // Session::flash("success");
        return redirect('/manage/cases');
    }
}
