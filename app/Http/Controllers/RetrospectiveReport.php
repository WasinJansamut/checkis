<?php

namespace App\Http\Controllers;

use App\Mail\SentMail;
use App\Models\HospcodeModel;
use App\Models\JobsModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class RetrospectiveReport extends Controller
{
    public function index(Request $request)
    {
        $hosps = [];
        $hosp = "";
        $month = "";
        $year = "";
        $code = "";
        $now = Carbon::now()->addYear(543)->format("Y-m-d");
        $start = Carbon::parse('first day of last month')->format('d/m/') . (Carbon::parse('first day of last month')->year + 543);
        $end = Carbon::parse('last day of last month')->format('d/m/') . (Carbon::parse('last day of last month')->year + 543);
        $area_codes = HospcodeModel::select('area_code')->groupBy('area_code')->pluck('area_code');

        if (user_info('user_level_code') == 'MOPH') {
            $area = user_info('region');
            $hosps = HospcodeModel::where("area_code", $area)->get();
        } elseif (user_info('user_level_code') == 'PROV') {
            $code = user_info('province_code');
            $hosps = HospcodeModel::where("province_code", $code)->get();
        }

        if (in_array(user_info('user_level_code'), ['HOSP', 'MOPH', 'PROV'])) {
            $jobs = JobsModel::with('user')->where("user_id", user_info('uid'))->orderBy('created_at', 'DESC')->paginate(20);
        } else {
            $jobs = JobsModel::with('getHospName', 'user')->whereIn('users.username', $hosps)->orderBy('created_at', 'DESC')->paginate(20);
            $hosps = HospcodeModel::get();
        }

        return view('retrospective_report', ['jobs' => $jobs, 'hosps' => $hosps, 'month' => $month, 'year' => $year, 'hospCode' => $hosp, 'now' => $now, 'area_codes' => $area_codes, 'code' => $code, 'start' => $start, 'end' => $end]);
    }
    public function download($id)
    {
        $path = JobsModel::where('id', $id)->first('path');

        LogController::addlog("download", "jobs", $path);

        return redirect(asset($path['path']));
    }

    public function GetReportPerPage(Request $request)
    {
        try {
            if (user_info('user_level_code') == 'MOPH') {
                $area = user_info('region');
                $hosps = HospcodeModel::where("area_code", $area)->get();
            } elseif (user_info('user_level_code') == 'PROV') {
                $code = user_info('province_code');
                $hosps = HospcodeModel::where("province_code", $code)->get();
            }

            if (in_array(user_info('user_level_code'), ['HOSP', 'MOPH', 'PROV'])) {
                $jobs = JobsModel::with('user')->where("user_id", user_info('uid'))->orderBy('created_at', 'DESC')->paginate(20);
            } else {
                $jobs = JobsModel::with('getHospName', 'user')->whereIn('users.username', $hosps)->orderBy('created_at', 'DESC')->paginate(20);
                $hosps = HospcodeModel::get();
            }

            if (user_info('email')) {
                $path_array = [];
                foreach ($jobs as $job) {
                    if ($job['status'] == 'checked') {
                        $path_array[] = public_path() . '/' . $job->path;
                    }
                }
                $data["subject"] = "[IS-Checking] แจ้งเตือนรายการสั่งตรวจใหม่";
                $data["email"] = user_info('email');
                $data["title"] = "รายการดาวน์โหลดผลการตรวจสอบ";
                $data["body"] = "รายการดาวน์โหลดผลการตรวจสอบ";

                Mail::send('mail_attachments', $data, function ($message) use ($data, $path_array) {
                    $message->to($data["email"], $data["email"])
                        ->subject($data["subject"]);
                    foreach ($path_array as $file) {
                        $message->attach($file);
                    }
                });
                Session::flash("success email");
                return redirect()->route('retrospective_report');
            } else {
                Session::flash("no email");
                return redirect()->route('retrospective_report');
            }
        } catch (\Exception $e) {
            Session::flash("error");
            return redirect()->route('retrospective_report');
        }
    }

    public function search(Request $request)
    {
        // dd('show');
        $hosp = $request->input('hosp_search');

        // $start_date = Carbon::createFromFormat('d/m/Y', $request->input('start_date'))->format('Y-m-d');
        // $end_date = Carbon::createFromFormat('d/m/Y', $request->input('end_date'))->format('Y-m-d');
        // $_start_date = Carbon::parse($start_date)->subYear(543)->format("Y-m-d");
        // $_end_date = Carbon::parse($end_date)->subYear(543)->format("Y-m-d");

        // $now = Carbon::now()->addYear(543)->format("Y-m-d");

        try {
            // แปลงวันที่จากรูปแบบ d/m/Y (พ.ศ.) เป็น Y-m-d (ค.ศ.)
            $start_date = Carbon::createFromFormat('d/m/Y', $request->input('start_date'))->subYears(543)->format('Y-m-d');
            $end_date = Carbon::createFromFormat('d/m/Y', $request->input('end_date'))->subYears(543)->format('Y-m-d');

            // กำหนดวันที่เริ่มและสิ้นสุดใหม่
            $_start_date = Carbon::parse($start_date)->format('Y-m-d');
            $_end_date = Carbon::parse($end_date)->format('Y-m-d');

            // เพิ่มปี 543 ให้กับวันที่ปัจจุบัน (พ.ศ.)
            $now = Carbon::now()->addYears(543)->format('Y-m-d');

            // แสดงผลเพื่อดีบั๊ก
            // dd([
            //     'start_date' => $start_date,
            //     'end_date' => $end_date,
            //     '_start_date' => $_start_date,
            //     '_end_date' => $_end_date,
            //     'now' => $now,
            // ]);
        } catch (\Exception $e) {
            // แสดงข้อผิดพลาด
            return response()->json(['error' => $e->getMessage()], 400);
        }

        $start = $start_date;
        $end = $end_date;


        $code = $request->input('area_code');
        $area_codes = HospcodeModel::select('area_code')->groupBy('area_code')->pluck('area_code');
        $hosps = [];
        //        $jobs = JobsModel::query()->whereIf("");

        if (!is_null($hosp) && !is_null($code)) {
            $count = HospcodeModel::where('hospcode', $hosp)->where('area_code', $code)->count(); //เช็ค hosp กับ area ว่าตรงกันไหม
            if ($count == 0) {
                Session::flash("wrong hosp");
                return redirect()->route('retrospective_report');
            }
        }

        if (user_info('user_level_code') == 'MOPH') {
            $area = user_info('region');
            $hosps = HospcodeModel::where("area_code", $area)->get();
        } elseif (user_info('user_level_code') == 'PROV') {
            $code = user_info('province_code');
            $hosps = HospcodeModel::where("province_code", $code)->get();
        }

        if (in_array(user_info('user_level_code'), ['HOSP', 'MOHP', 'PROV']) && user_info('user_type') != 'SUPER ADMIN') {
            $jobs = JobsModel::with('user')->where("user_id", user_info('uid'))->orderBy('created_at', 'DESC')->paginate(20);
        } elseif (user_info('user_level_code') == 'MOHP' && user_info('user_type') == 'SUPER ADMIN') {
            //เป็น admin
            // ตรวจสอบว่ามีการกำหนดค่า $hosp หรือไม่
            $query = JobsModel::with('getHospName', 'user')->where('user_id', user_info('uid'))->orderBy('created_at', 'DESC');

            if ($hosp) {
                // ถ้ามีค่า $hosp ให้เพิ่มเงื่อนไขการค้นหาตามรหัสโรงพยาบาล
                $query->where('hosp', $hosp);
            }

            // ใช้ paginate เพื่อแบ่งหน้า
            $jobs = $query->paginate(20);
            $hosps = HospcodeModel::get();
        } else {
            $jobs = JobsModel::with('getHospName', 'user')->whereIn('users.username', $hosps)->orderBy('created_at', 'DESC')->paginate(20);
            $hosps = HospcodeModel::get();
        }

        // dd($jobs,$start_date,$end_date,$code);

        if (empty($jobs)) {
            Session::flash('no data');
            return redirect()->route('retrospective_report');
        }
        // $jobs = $jobs->paginate(20);

        return view('retrospective_report', ['jobs' => $jobs, 'hosps' => $hosps, 'start_date' => $start_date, 'end_date' => $end_date, 'hospCode' => $hosp, 'now' => $now, 'code' => $code, 'area_codes' => $area_codes, "start" => $request->input('start_date'), 'end' => $request->input('end_date')]);
    }
}
