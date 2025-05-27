<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IsModel;
use App\Models\LibHospcodeModel;
use App\Models\LibChangwatModel;

class DashboardController extends Controller
{
    public function hospital_overview(Request $request)
    {
        $query = collect();
        if ($request->isMethod('post')) {
            $query = IsModel::with('_hosp')->limit(7)->get();
        }
        return view('dashboard.hospital_overview', compact('query'));
    }

    public function get_province_from_health_zone(Request $request) // Ajax ส่งค่าเขตสุขภาพเพื่อหาจังหวัด
    {
        $region = $request->region;
        if ($region == 'ทั้งหมด') {
            $provinces = LibChangwatModel::select('code', 'name', 'region')
                ->orderby('name', 'ASC')
                ->get();
        } else {
            $region = sprintf("%02d", $request->region);
            $provinces = LibChangwatModel::select('code', 'name', 'region')
                ->where('region', $region)
                ->orderby('name', 'ASC')
                ->get();
        }

        return $provinces;
    }

    public function get_hospital_from_province(Request $request) // Ajax ส่งค่าจังหวัดเพื่อหาโรงพยาบาล
    {
        // // Debug ตรวจสอบค่าที่ส่งมาจาก AJAX
        // Log::info('get_hospital_from_province Request Data: ', $request->all());

        $health_zone = $request->health_zone;
        $province = $request->province;
        if (!is_array($province)) {
            $province = explode(",", $province);
        }
        $hospcodes = LibHospcodeModel::select('region', 'changwatcode', 'off_id', 'name');
        // ->whereIn('splevel', ['A', 'S', 'M1']);

        if (!in_array('ทั้งหมด', $province)) {
            $hospcodes->whereIn('changwatcode', $province);
        } elseif ($health_zone != 'ทั้งหมด' && in_array('ทั้งหมด', $province)) {
            $hospcodes->where('region', sprintf("%02d", $health_zone));
        }

        $hospcodes = $hospcodes->orderby('name', 'ASC')->get();

        return $hospcodes;
    }
}
