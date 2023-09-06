<?php

namespace App\Http\Controllers;

use App\Models\HospcodeModel;
use App\Models\IsModel;
use App\Models\JobsModel;
use App\Models\PresentReportModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PresentReportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $hosps = [];
        if (Auth::user()->type > 0) {
            $data = JobsModel::where('status', 'checked')->with('getHospName')->orderBy('id', 'DESC')->first();

            $type = Auth::user()->type;

            if ($type == 1) {
                $hosps = HospcodeModel::get();
            } else if ($type == 2) {

                $area = Auth::user()->area;
                $hosps = HospcodeModel::where("area_code", $area)->get();
            } else if ($type == 3) {

                $code = Auth::user()->province;
                $hosps = HospcodeModel::where("province_code", $code)->get();
            }
        } else {
            $username = Auth::user()->username;
            $data = JobsModel::where('hosp', $username)->where('status', 'checked')->orderBy('id', 'DESC')->first();
        }

        //        dd($data);
        return view('present_report', ['datas' => $data, 'hosps' => $hosps]);
    }

    public function search(Request $request)
    {
        $hosp = $request->input('hosp_search');

        $data = JobsModel::where('status', 'checked')->where('hosp', $hosp)->with('getHospName')->orderBy('id', 'DESC')->first();


        $type = Auth::user()->type;
        if ($type == 1) {
            $hosps = HospcodeModel::get();
        } else if ($type == 2) {

            $area = Auth::user()->area;
            $hosps = HospcodeModel::where("area_code", $area)->get();
        } else if ($type == 3) {

            $code = Auth::user()->province;
            $hosps = HospcodeModel::where("province_code", $code)->get();
        }

        if (empty($data)) {
            Session::flash("no data");
            return redirect()->route('present_report');
        }
        //        return redirect()->route('present_report');
        return view('present_report', ['datas' => $data, 'hosps' => $hosps]);
    }
}
