<?php

namespace App\Http\Controllers;

use App\Models\HospcodeModel;
use App\Models\IsModel;
use App\Models\JobsModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use Illuminate\Support\Facades\DB;



class ReOrderController extends Controller
{
    private $new_jobs_id = [];

    public function index()
    {
        $hosps = [];
        $area_codes = [];
        $start = new Carbon('first day of last month');
        $end = new Carbon('last day of last month');

        $start = $start->addYear(543)->format("d/m/Y");
        $end = $end->addYear(543)->format("d/m/Y");


        $now = Carbon::now()->addYear(543)->format("Y-m-d");
        $type = Auth::user()->type;
        if ($type == 1) {
            $hosps = HospcodeModel::get();
            $area_codes = HospcodeModel::select('area_code')->groupBy('area_code')->pluck('area_code');
        } elseif ($type == 2) {
            $area = Auth::user()->area;
            $hosps = HospcodeModel::where("area_code", $area)->get();
        } elseif ($type == 3) {
            $code = Auth::user()->province;
            $hosps = HospcodeModel::where("province_code", $code)->get();
        }

        return view("reorder", ['hosps' => $hosps, 'now' => $now, 'area_codes' => $area_codes, 'start' => $start, 'end' => $end]);
    }

    public function sortHosp(Request $request)
    {
        $hosp = $request->hosp;

        $codes = HospcodeModel::whereIf('hospcode', $hosp)->groupBy('area_code')->get('area_code');
        $html = "";
        foreach ($codes as $code) {
            $html .= "<option value='$code->area_code'> $code->area_code</option>";
        }

        return $html;
    }

    public function sortAreaCode(Request $request)
    {
        $code = $request->code;

        $hosps = HospcodeModel::whereIf('area_code', $code)->get('full_name');
        $html = "<option value='all_hosp'>โรงพยาบาลทั้งหมดในเขต</option>";
        foreach ($hosps as $hosp) {
            $html .= "<option value='$hosp->hospcode'> $hosp->full_name</option>";
        }

        return $html;
    }

    public function addReport(Request $request)
    {
        try {
            $user = Auth::user();
            $start_date = Carbon::createFromFormat('d/m/Y', $request->input('start_date'))->format('Y-m-d');
            $end_date = Carbon::createFromFormat('d/m/Y', $request->input('end_date'))->format('Y-m-d');
            $area_code = $request->input('area_code');

            $start_date = Carbon::parse($start_date)->subYear(543)->format("Y-m-d");
            $end_date = Carbon::parse($end_date)->subYear(543)->format("Y-m-d");

            if ($user->type == 0) { //for user
                $hosp = $user->username;
                $this->checkJob($hosp, $start_date, $end_date);
            } else { //for admin
                $hosp = $request->input('hosp');
                $range = (date_diff(date_create($start_date), date_create($end_date)))->format("%a"); //ระยะห่างเวลา

                if ((is_null($hosp) || $hosp == "") || (is_null($area_code) || $area_code == "")) { //ต้องมีตัวใดตัวนึง hosp || area_code

                    Session::flash("incomplete value");

                    return redirect()->route('reorder');
                }

                //            if(($hosp == "all_hosp" || is_null($hosp) && $hosp == "") && (!is_null($area_code) && $area_code != "") ){ //ถ้ามีแต่เขต
                if (($hosp == "all_hosp") && (!is_null($area_code) && $area_code != "")) { //ถ้ามีแต่เขต เลือก โรงบาลทั้งหมด

                    if (((int)$range >= 366)) { //เช็คปีต้องไม่เกิน 1 ปี ถ้าเลือกแต่เขต
                        Session::flash("time range too long");
                        return redirect()->route('reorder');
                    }

                    $all_hosp = HospcodeModel::where('area_code', $area_code)->pluck('hospcode');

                    foreach ($all_hosp as $row) { //เอา hosp ที่ตรงกับเขตไป check job ทั้งหมด
                        $this->checkJob($row, $start_date, $end_date);
                    }
                } else { //ถ้ามีแต่โรงบาล หรือ มีทั้งคู่

                    if ((!is_null($hosp) && $hosp != "") && (!is_null($area_code) && $area_code != "")) { //ถ้ามีทั้งคู่
                        $count = HospcodeModel::where('hospcode', $hosp)->where('area_code', $area_code)->count(); //เช็ค hosp กับ area ว่าตรงกันไหม
                        if ($count == 0) {
                            Session::flash("wrong hosp");
                            return redirect()->route('reorder');
                        }
                    }

                    $this->checkJob($hosp, $start_date, $end_date);
                }
            }
            foreach ($this->new_jobs_id as $job_id) {
                $job = JobsModel::where("id", $job_id)->where("status", 'waiting')->first();
                if ($job) {
                    CheckingController::runJob($job);
                }
            }
        } catch (\Exception $e) {
            dd($e);
        }


        return redirect()->route('retrospective_report');
    }

    public function checkJob($hosp, $start_date, $end_date)
    {

        $count = IsModel::where('hosp', $hosp)->whereBetween('hdate', [$start_date, $end_date])->count();
        $count_job = JobsModel::where('hosp', $hosp)->where('start_date', $start_date)->where('end_date', $end_date)->where('status', 'waiting')->count();
        $area_code = HospcodeModel::where('hospcode', $hosp)->first('area_code');
        //        dd();

        if ($count == 0) {
            Session::flash('no_data');
            return redirect()->route('reorder');
        }
        if ($count_job > 0) {
            Session::flash("duplicate job");
            return redirect()->route('reorder');
        }
        $user = Auth::id();
        $this->addJob($hosp, $start_date, $end_date, $count, $area_code, $user);
        return redirect()->route('retrospective_report');
    }


    public function addJob($hosp, $start_date, $end_date, $count, $area_code, $user_id)
    {
        $row = new JobsModel();
        $row->start_date = $start_date;
        $row->end_date = $end_date;
        $row->hosp = $hosp;
        $row->count = $count;
        $row->area_code = $area_code['area_code'];
        $row->user_id = $user_id;

        $user = User::where("id", $user_id)->first();

        if ($user->type > 1) {
            $row->is_export_data = 0;
        } else {
            $row->is_export_data = 1;
        }

        $row->save();
        $this->new_jobs_id[] = $row->id;
    }

    public function addJob_ASM1()
    {
        //GET HOPS ASM1
        $hosp_asm1 = DB::table('hosp_asm1')->select('hospcode')->get();
        // dd($hosp_asm1);

        $start_date = '2022-10-01 00:00:00';
        $end_date = '2023-09-30 23:59:59';


        foreach ($hosp_asm1 as $rowhosp) {

            //  dd( $row->hospcode);

            // GET IS Data
            $count = IsModel::where('hosp', $rowhosp->hospcode)->whereBetween('hdate', [$start_date, $end_date])->count();

            $row = new JobsModel();
            $row->start_date = $start_date;
            $row->end_date = $end_date;
            $row->hosp = $rowhosp->hospcode;
            $row->count = $count;
            $row->is_export_data = 1;
            $row->save();
            $this->new_jobs_id[] = $row->id;
        }

        echo 'success';
    }


    // for cronjob create jobs
    public function  monthlyCreateJobs()
    {
        try {
            dump("create job monthly");
            $hosps = HospcodeModel::get('hospcode'); //get all hospcode

            $date = Carbon::now(); //getdate
            $sub_month = $date->subMonth()->format('d/m/Y'); //previous month
            $end_sub_month = $date->endOfMonth()->format('d/m/Y');

            dump("start finding");
            foreach ($hosps as $hosp) { //loop for build job
                $hospcode = $hosp->hospcode;
                $area_code = $hosp->area_code;

                //check if have this job already
                $countJob = JobsModel::where('hosp', $hospcode)->where('start_date', $date)->count();
                if ($countJob == 0) {
                    $result = IsModel::where('hosp', $hospcode)->whereBetween('hdate', [$sub_month, $end_sub_month])->get();

                    if (sizeof($result) > 0) {
                        $user = Auth::id();
                        $this->addJob($hospcode, $sub_month, $end_sub_month, $countJob, $area_code, $user);
                        dump($hospcode, "added");
                    }
                }
            }
            dump("monthly createjob done");
        } catch (\Exception $error) {
            echo $error;
            dd($error);
        }
    }
}
