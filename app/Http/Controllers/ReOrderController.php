<?php

namespace App\Http\Controllers;

use App\Models\LibHospcode;
use App\Models\IsModel;
use App\Models\JobsModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class ReOrderController extends Controller
{
    private $new_jobs_id = [];

    public function index()
    {
        $hosps = [];
        $area_codes = [];
        $start = Carbon::parse('first day of last month')->format('d/m/') . (Carbon::parse('first day of last month')->year + 543);
        $end = Carbon::parse('last day of last month')->format('d/m/') . (Carbon::parse('last day of last month')->year + 543);
        $now = Carbon::now()->addYear(543)->format("Y-m-d");
        if (user_info('user_level_code') == 'MOPH' && user_info('user_type') == 'SUPER ADMIN') {
            $hosps = LibHospcode::get();
            $area_codes = LibHospcode::select('region')->groupBy('region')->pluck('region');
        } elseif (user_info('user_level_code') == 'MOPH') {
            $area = user_info('region');
            $hosps = LibHospcode::where('region', $area)->get();
        } elseif (user_info('user_level_code') == 'PROV') {
            $code = user_info('province_code');
            $hosps = LibHospcode::where("changwatcode", $code)->get();
        }

        return view("reorder", ['hosps' => $hosps, 'now' => $now, 'area_codes' => $area_codes, 'start' => $start, 'end' => $end]);
    }

    public function sortHosp(Request $request)
    {
        $hosp = $request->hosp;

        $codes = LibHospcode::when($hosp, function ($query, $hosp) {
            return $query->where('off_id', $hosp);
        })->groupBy('region')
            ->get(['region']);

        $html = "";
        foreach ($codes as $code) {
            $html .= "<option value='{$code->region}'>{$code->region}</option>";
        }

        return $html;
    }

    public function sortAreaCode(Request $request)
    {
        $code = $request->code;
        $hosps = LibHospcode::whereIf('region', $code)->get('name');
        $html = "<option value='all_hosp'>โรงพยาบาลทั้งหมดในเขต</option>";
        foreach ($hosps as $hosp) {
            $html .= "<option value='$hosp->hospcode'> $hosp->full_name</option>";
        }

        return $html;
    }

    public function addReport(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');

        try {
            $start_date = Carbon::createFromFormat('d/m/Y', $request->input('start_date'))->format('Y-m-d');
            $end_date = Carbon::createFromFormat('d/m/Y', $request->input('end_date'))->format('Y-m-d');
            $area_code = $request->input('area_code');

            $start_date = Carbon::parse($start_date)->subYear(543)->format("Y-m-d");
            $end_date = Carbon::parse($end_date)->subYear(543)->format("Y-m-d");

            if (user_info('user_level_code') == 'HOSP') {
                $hosp = user_info('hosp_code');
                $this->checkJob($hosp, $start_date, $end_date);
            } else { // for admin
                $hosp = $request->input('hosp');
                $range = (date_diff(date_create($start_date), date_create($end_date)))->format("%a"); //ระยะห่างเวลา

                if ((is_null($hosp) || $hosp == "") || (is_null($area_code) || $area_code == "")) { //ต้องมีตัวใดตัวนึง hosp || area_code
                    Session::flash("incomplete value");
                    return redirect()->route('reorder');
                }

                // if(($hosp == "all_hosp" || is_null($hosp) && $hosp == "") && (!is_null($area_code) && $area_code != "") ){ //ถ้ามีแต่เขต
                if (($hosp == "all_hosp") && (!is_null($area_code) && $area_code != "")) { //ถ้ามีแต่เขต เลือก โรงบาลทั้งหมด

                    if (((int)$range >= 366)) { //เช็คปีต้องไม่เกิน 1 ปี ถ้าเลือกแต่เขต
                        Session::flash("time range too long");
                        return redirect()->route('reorder');
                    }

                    $all_hosp = LibHospcode::where('region', $area_code)->pluck('off_id');

                    foreach ($all_hosp as $row) { //เอา hosp ที่ตรงกับเขตไป check job ทั้งหมด
                        $this->checkJob($row, $start_date, $end_date);
                    }
                } else { //ถ้ามีแต่โรงบาล หรือ มีทั้งคู่
                    if ((!is_null($hosp) && $hosp != "") && (!is_null($area_code) && $area_code != "")) { //ถ้ามีทั้งคู่
                        $count = LibHospcode::where('off_id', $hosp)->where('region', $area_code)->count(); //เช็ค hosp กับ area ว่าตรงกันไหม
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
            // dd($e);
            exit('หมดเวลาการเชื่อมต่อ');
        }

        return redirect()->route('retrospective_report');
    }

    public function checkJob($hosp, $start_date, $end_date)
    {
        $count = IsModel::where('hosp', $hosp)->whereBetween('hdate', [$start_date, $end_date])->count();
        $count_job = JobsModel::where('hosp', $hosp)->where('start_date', $start_date)->where('end_date', $end_date)->where('status', 'waiting')->count();
        $area_code = LibHospcode::where('off_id', $hosp)->first('region');

        if ($count == 0) {
            Session::flash('no data');
            return redirect()->route('reorder');
        }
        if ($count_job > 0) {
            Session::flash("duplicate job");
            return redirect()->route('reorder');
        }
        $user = user_info('uid');
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
        $row->area_code = $area_code['region'];
        $row->user_id = $user_id;

        if (user_info('user_level_code') == 'HOSP' || (user_info('user_level_code') == 'MOPH' && user_info('user_type') == 'SUPER ADMIN')) {
            $row->is_export_data = 1;
        } else {
            $row->is_export_data = 0;
        }

        $row->save();
        $this->new_jobs_id[] = $row->id;
    }

    public function addJob_ASM1()
    {
        //GET HOPS ASM1
        $hosp_asm1 = DB::table('hosp_asm1')->select('off_id')->get();
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
            $hosps = LibHospcode::get('off_id'); //get all hospcode

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
                        $user = user_info('uid');
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
