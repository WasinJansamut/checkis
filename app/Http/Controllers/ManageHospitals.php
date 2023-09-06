<?php

namespace App\Http\Controllers;

use App\Models\HospcodeModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ManageHospitals extends Controller
{
    public function index()
    {
        if (Auth::user()->type == 1) {
            $hospital_name = "";
            $hospitals = HospcodeModel::paginate(20);
            $hospitals_all = HospcodeModel::get();
            return view('manage_hospitals', ['hospitals' => $hospitals, 'hospital_name' => $hospital_name, 'hospitals_all' => $hospitals_all]);
        } else {
            return redirect()->route('present_report');
        }
    }

    public function form()
    {
        $hospital = "";
        return view('hospital_form', ['hospital' => $hospital]);
    }

    public function edit($hospcode)
    {
        $hospital = HospcodeModel::where('hospcode', $hospcode)->first();

        return view('hospital_form', ['hospital' => $hospital]);
    }

    public function delete($hospcode)
    {
        HospcodeModel::where('hospcode', $hospcode)->first()->delete();

        return redirect()->route('manage_hospitals');
    }

    public function create(Request $request)
    {
        //        dd($request);
        $hospital = new HospcodeModel();
        $hospital->timestamps = false;
        $hospital->health_district = $request->health_district;
        $hospital->code_district = $request->code_district;
        $hospital->hospcode9 = $request->hospcode9;
        $hospital->hospcode = $request->hospcode;
        $hospital->name = $request->name;
        $hospital->full_name = $request->full_name;
        $hospital->type = $request->type;
        $hospital->type_code = $request->type_code;
        $hospital->bed_amount = $request->bed_amount;
        $hospital->province_code = $request->province_code;
        $hospital->province_name = $request->province_name;
        $hospital->district_id = $request->district_id;
        $hospital->district_name = $request->district_name;
        $hospital->sub_district_id = $request->sub_district_id;
        $hospital->sub_district_name = $request->sub_district_name;
        $hospital->moo = $request->moo;
        $hospital->area_code = $request->area_code;
        $hospital->area_gorverment = $request->area_gorverment;
        $hospital->hosptype = $request->hosptype;

        $hospital->save();


        return redirect()->route('manage_hospitals');
    }

    public function update(Request $request)
    {
        //        dd($request);
        $hospital = HospcodeModel::where('hospcode', $request->hospcode)->first();
        $hospital->timestamps = false;
        $hospital->health_district = $request->health_district;
        $hospital->code_district = $request->code_district;
        $hospital->hospcode9 = $request->hospcode9;
        $hospital->hospcode = $request->hospcode;
        $hospital->name = $request->name;
        $hospital->full_name = $request->full_name;
        $hospital->type = $request->type;
        $hospital->type_code = $request->type_code;
        $hospital->bed_amount = $request->bed_amount;
        $hospital->province_code = $request->province_code;
        $hospital->province_name = $request->province_name;
        $hospital->district_id = $request->district_id;
        $hospital->district_name = $request->district_name;
        $hospital->sub_district_id = $request->sub_district_id;
        $hospital->sub_district_name = $request->sub_district_name;
        $hospital->moo = $request->moo;
        $hospital->area_code = $request->area_code;
        $hospital->area_gorverment = $request->area_gorverment;
        $hospital->hosptype = $request->hosptype;

        $hospital->save();


        return redirect()->route('manage_hospitals');
    }



    public function search(Request $request)
    {
        $hospcode = $request->input('search');
        $hospitals_all = HospcodeModel::get();

        $hospitals = HospcodeModel::where('hospcode', $hospcode)->paginate(1);

        $hospital_name = $hospitals[0]['full_name'];

        if (empty($hospitals)) {
            Session::flash("no data");
            return redirect()->route('manage_hospitals');
        }

        return view('manage_hospitals', ['hospitals' => $hospitals, 'hospitals_all' => $hospitals_all, 'hospital_name' => $hospital_name]);
    }
}
