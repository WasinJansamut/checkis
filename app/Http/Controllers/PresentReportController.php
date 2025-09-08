<?php

namespace App\Http\Controllers;

use App\Models\HospcodeModel;
use App\Models\IsModel;
use App\Models\LibHospcodeModel;
use App\Models\JobsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

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
    public function index(Request $request)
    {
        $datas = [];
        $hospitals = [];
        $hosp_stats = [];
        $req_year = (int) $request->year ?? null;
        $req_hospcode = $request->hospcode ?? null;
        $req_year_en = ($req_year - 543);
        $year_now_th = Carbon::now()->year + 543; // ปี พ.ศ. ปัจจุบัน

        if (user_info('hosp_code') === null || user_info('user_level_code') === null) {
            return redirect()->route('auth_callback')->with('danger', 'เกิดข้อผิดพลาด!');
        }

        if (!empty($req_year)) {
            if ($req_year < ($year_now_th - 4) || $req_year > $year_now_th) {
                return redirect()->route('home')->with('danger', "ปีที่เลือกต้องอยู่ช่วง " . ($year_now_th - 4) . " ถึง $year_now_th เท่านั้น");
            }
        }

        /*
            *** อันนี้ของเดิม ก่อนทำรับ Token จาก Pher Plus ***
            แสดงผล Select2 หน่วยงาน / Select2 ปี โดยแสดงปีปัจจุบัน ย้อนไป 5 ปี
            ถ้าเป็น [type = 0] ผู้ใช้งาน รพ. แสดงเฉพาะ รพ. ตัวเอง
            ถ้าเป็น [type = 1] ผู้ใช้งาน แอดมิน แสดงทุก รพ.
            ถ้าเป็น [type = 2] ผู้ใช้งาน สคร แสดงทุก รพ. ในเขตสุขภาพตัวเอง
            ถ้าเป็น [type = 3] ผู้ใช้งาน สสจ แสดง แค่ รพ. ในจังหวัดตัวเอง
        */
        if (user_info('user_level_code') == 'HOSP') {
            // ผู้ใช้งาน รพ. แสดงเฉพาะ รพ. ตัวเอง
            $hospitals = HospcodeModel::where("hospcode", user_info('hosp_code'))->get();
            $datas = JobsModel::where('hosp', user_info('hosp_code'))->where('status', 'checked')->orderBy('id', 'DESC')->first();
        } elseif (user_info('user_level_code') == 'MOPH' && user_info('user_type') == 'SUPER ADMIN') {
            // ผู้ใช้งาน แอดมิน ให้แสดง รพ. ทั้งหมด
            $hospitals = HospcodeModel::get();
        } elseif (user_info('user_level_code') == 'MOPH') {
            // ผู้ใช้งาน สคร แสดงทุก รพ. ในเขตสุขภาพตัวเอง
            $area = user_info('region');
            $hospitals = HospcodeModel::where("area_code", $area)->get();
        } elseif (user_info('user_level_code') == 'PROV') {
            // ผู้ใช้งาน สสจ แสดง แค่ รพ. ในจังหวัดตัวเอง
            $code = user_info('province_code');
            $hospitals = HospcodeModel::where("province_code", $code)->get();
        }

        if (empty($req_hospcode)) {
            $datas = JobsModel::where('status', 'checked')->where('hosp', user_info('hosp_code'))->with('getHospName')->orderBy('id', 'DESC')->first();
        } else {
            $datas = JobsModel::where('status', 'checked')->where('hosp', $req_hospcode)->with('getHospName')->orderBy('id', 'DESC')->first();
        }

        if (!empty($req_hospcode)) {
            if (!$hospitals->contains('hospcode', $req_hospcode)) {
                return redirect()->route('home')->with('danger', 'คุณไม่มีสิทธิ์เข้าถึงหน่วยงานที่เลือก');
            }
        }

        // if ($request->isMethod('post')) {
        if (!empty($req_year) && !empty($req_hospcode)) {
            // GET HOSP DETAIL
            $hosp_name = LibHospcodeModel::where('off_id', '=', $req_hospcode)->pluck('name')->first();

            if (empty($hosp_name)) {
                return redirect()->route('home')->with('danger', 'ไม่พบข้อมูลหน่วยงานที่คุณเลือก ในฐานข้อมูลส่วนกลาง');
            }

            $months = ['01' => 'มกราคม', '02' => 'กุมภาพันธ์', '03' => 'มีนาคม', '04' => 'เมษายน', '05' => 'พฤษภาคม', '06' => 'มิถุนายน', '07' => 'กรกฎาคม', '08' => 'สิงหาคม', '09' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม'];

            // ความสม่ำเสมอของข้อมูล
            $is_win_hosp_stats_all = IsModel::selectRaw('COUNT(*) as data, DATE_FORMAT(hdate, "%Y-%m") as new_date')
                ->whereYear("hdate", $req_year_en)
                ->where('hosp', '=', $req_hospcode)
                ->groupBy('new_date')
                ->orderByRaw("STR_TO_DATE(new_date, '%Y-%m')")
                ->get();

            $is_win_hosp_stats = [];
            $is_win_hosp_stats_count = 0;
            foreach ($months as $month_num => $month_name) {
                // ค้นหาข้อมูลของเดือนนั้นจาก $is_win_hosp_stats
                $data = $is_win_hosp_stats_all->firstWhere('new_date', $req_year_en . '-' . $month_num);
                // หากไม่พบข้อมูล ให้ตั้งค่าข้อมูลเป็น 0
                $is_win_hosp_stats[] = (object) [
                    'data_yymm' => $req_year . '-' . $month_num,
                    'month_th' => $month_name,
                    'data' => $data->data ?? 0 // หากไม่มีข้อมูล ให้ใช้ค่า 0
                ];

                $is_win_hosp_stats_count += $data ? $data->data : 0;
            }

            $hosp_stats = collect([
                'stats' => $is_win_hosp_stats,
                'count' => $is_win_hosp_stats_count,
                'filter' => (object) [
                    'hospname' => $hosp_name,
                    'hospcode' => $req_hospcode ?? '',
                    'year' => $req_year ?? '',
                ]
            ])->toArray();

            $hosp_stats = (object) $hosp_stats; // แปลงเป็น object
            // return view("page.tracking_detail", compact('hosp_name', 'isDataCountyear', 'year', 'ISCount'));
        }

        // dd($hosp_stats);
        // dd($data);
        return view('present_report', compact('datas', 'hospitals', 'hosp_stats'));
    }

    public function search(Request $request)
    {
        $hosp = $request->input('hosp_search');
        $data = JobsModel::where('status', 'checked')->where('hosp', $hosp)->with('getHospName')->orderBy('id', 'DESC')->first();
        if (user_info('user_level_code') == 'HOSP') {
            $hosps = HospcodeModel::get();
        } elseif (user_info('user_level_code') == 'MOPH') {
            $area = user_info('region');
            $hosps = HospcodeModel::where("area_code", $area)->get();
        } elseif (user_info('user_level_code') == 'PROV') {
            $code = user_info('province_code');
            $hosps = HospcodeModel::where("province_code", $code)->get();
        }

        if (empty($data)) {
            Session::flash('no data');
            return redirect()->route('present_report');
        }
        // return redirect()->route('present_report');
        return view('present_report', ['datas' => $data, 'hosps' => $hosps]);
    }
}
