<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Models\IsModel;
use App\Models\LibHospcodeModel;
use App\Models\LibChangwatModel;

class DashboardController extends Controller
{
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

        if (!in_array('ทั้งหมด', $province)) {
            $hospcodes->whereIn('changwatcode', $province);
        } elseif ($health_zone != 'ทั้งหมด' && in_array('ทั้งหมด', $province)) {
            $hospcodes->where('region', sprintf("%02d", $health_zone));
        }

        $hospcodes = $hospcodes->orderby('name', 'ASC')->get();

        return $hospcodes;
    }

    public function get_hospital_asm1_from_province(Request $request) // Ajax ส่งค่าจังหวัดเพื่อหาโรงพยาบาล (A, S, M1)
    {
        // // Debug ตรวจสอบค่าที่ส่งมาจาก AJAX
        // Log::info('get_hospital_from_province Request Data: ', $request->all());

        $health_zone = $request->health_zone;
        $province = $request->province;
        if (!is_array($province)) {
            $province = explode(",", $province);
        }
        $hospcodes = LibHospcodeModel::select('region', 'changwatcode', 'off_id', 'name')
            ->whereIn('splevel', ['A', 'S', 'M1']);

        if (!in_array('ทั้งหมด', $province)) {
            $hospcodes->whereIn('changwatcode', $province);
        } elseif ($health_zone != 'ทั้งหมด' && in_array('ทั้งหมด', $province)) {
            $hospcodes->where('region', sprintf("%02d", $health_zone));
        }

        $hospcodes = $hospcodes->orderby('name', 'ASC')->get();

        return $hospcodes;
    }

    public function hospital_21_variables(Request $request)
    {
        $data = new Collection();
        if ($request->isMethod('post')) {
            $health_zone = $request->health_zone ?? null; // เขตสุขภาพ
            $province = $request->province ?? null; // จังหวัด
            $hospital = $request->hospital ?? null; // โรงพยาบาล

            // $lib_hospcode_array = LibHospcodeModel::limit(100)
            //     ->pluck('off_id')
            //     ->map(function ($id) {
            //         return str_pad($id, 5, '0', STR_PAD_LEFT); // บังคับให้มี 5 หลักเสมอ
            //     })
            //     ->toArray(); // เอา off_id ของ รพ. มาทั้งหมด เก็บในรูปแบบ Array

            $data = IsModel::with('_hosp:off_id,name,changwat,region,splevel')
                ->selectRaw("
                    prov,
                    hosp,
                    SUM(
                    CASE
                        WHEN adate IS NOT NULL
                        AND atime IS NOT NULL
                        AND hdate IS NOT NULL
                        AND htime IS NOT NULL
                        AND staer IS NOT NULL
                        AND apoint IS NOT NULL
                        AND tinj IS NOT NULL
                        AND risk1 IS NOT NULL
                        AND risk2 IS NOT NULL
                        AND e IS NOT NULL
                        AND v IS NOT NULL
                        AND m IS NOT NULL
                        AND age IS NOT NULL
                        AND bp1 IS NOT NULL
                        AND rr IS NOT NULL
                        AND pr IS NOT NULL
                        AND br1 IS NOT NULL
                        AND ais1 IS NOT NULL
                        AND cause_t IS NOT NULL
                        AND ps IS NOT NULL
                        AND (
                        (
                            injt IN ('02', '021', '022', '023')
                            AND risk4 IS NOT NULL
                        )
                        OR (
                            injt NOT IN('02', '021', '022', '023')
                            AND risk3 IS NOT NULL
                        )
                        ) THEN 1
                        ELSE 0
                    END
                    ) AS complete_21,
                    SUM(
                    CASE
                        WHEN NOT(
                        adate IS NOT NULL
                        AND atime IS NOT NULL
                        AND hdate IS NOT NULL
                        AND htime IS NOT NULL
                        AND staer IS NOT NULL
                        AND apoint IS NOT NULL
                        AND tinj IS NOT NULL
                        AND risk1 IS NOT NULL
                        AND risk2 IS NOT NULL
                        AND e IS NOT NULL
                        AND v IS NOT NULL
                        AND m IS NOT NULL
                        AND age IS NOT NULL
                        AND bp1 IS NOT NULL
                        AND rr IS NOT NULL
                        AND pr IS NOT NULL
                        AND br1 IS NOT NULL
                        AND ais1 IS NOT NULL
                        AND cause_t IS NOT NULL
                        AND ps IS NOT NULL
                        AND (
                            (
                            injt IN ('02', '021', '022', '023')
                            AND risk4 IS NOT NULL
                            )
                            OR (
                            injt NOT IN('02', '021', '022', '023')
                            AND risk3 IS NOT NULL
                            )
                        )
                        ) THEN 1
                        ELSE 0
                    END
                    ) AS incomplete_21,
                    COUNT(*) AS total
                ")
                ->whereNotNull('hosp')
                ->where('hosp', '!=', '');

            // เงื่อนเขตสุขภาพ
            if (!is_null($health_zone) && $health_zone != 'ทั้งหมด') {
                $provinces_from_region = LibChangwatModel::select('code')
                    ->where('region', sprintf("%02d", $health_zone))
                    ->pluck('code') // ใช้ pluck() เพื่อดึงค่าเป็น array
                    ->toArray(); // แปลงเป็น array
                $data = $data->whereIn('prov', $provinces_from_region);
            }

            // เงื่อนไขจังหวัด
            if (!is_null($province) && !in_array("ทั้งหมด", $province)) {
                // $data =  $data->whereIn('prov', $province);
                $province_array = is_array($province) ? $province : [$province];
                $data = $data->whereIn('prov', $province_array);
            }

            // เงื่อนไขโรงพยาบาล
            if (!is_null($hospital) && !in_array("ทั้งหมด", $hospital)) {
                // $data =  $data->whereIn('hosp', $hospital);
                $hospital_array = is_array($hospital) ? $hospital : [$hospital];
                $data = $data->whereIn('hosp', $hospital_array);
            } else {
                // ถ้าไม่เจาะจงรพ. ให้ใช้รพ.ทั้งหมดที่มีใน lib_hospcode_array
                // $data = $data->whereIn('hosp', $lib_hospcode_array);
            }

            $data = $data->groupBy('prov', 'hosp')
                ->orderBy('prov', 'ASC')
                ->orderBy('hosp', 'ASC')
                // ->limit(10)
                ->get();
            // dd($data);
        }
        return view('dashboard.hospital_21_variables', compact('data'));
    }

    public function hospital_overview(Request $request)
    {
        $hosp_count_send_data = new Collection();
        if ($request->isMethod('post')) {
            $hosp_count_send_data = LibHospcodeModel::select('splevel', DB::raw('count(*) as count'))
                ->whereIn('splevel', ['A', 'S', 'M1'])
                ->groupBy('splevel')
                ->get();

            // 1. ดึงจำนวนทั้งหมดจาก LibHospcodeModel (ฝั่งโรงพยาบาลทั้งหมด)
            $lib_hospcode_counts = LibHospcodeModel::select('splevel', DB::raw('count(*) as count'))
                ->whereIn('splevel', ['A', 'S', 'M1'])
                ->groupBy('splevel')
                ->get()
                ->keyBy('splevel'); // แปลงเป็น key => value เพื่อให้เทียบง่าย

            // 2. ดึงจำนวนจาก IsModel ที่ส่งข้อมูล (join กับ LibHospcodeModel เพื่อได้ splevel)
            $is_counts = IsModel::select('lib_hospcode.splevel', DB::raw('count(distinct is.hosp) as count'))
                ->join('lib_hospcode', 'is.hosp', '=', 'lib_hospcode.off_id')
                ->whereIn('lib_hospcode.splevel', ['A', 'S', 'M1'])
                ->groupBy('lib_hospcode.splevel')
                ->get()
                ->keyBy('splevel');

            // 3. รวมข้อมูลสองฝั่ง
            $hosp_count_send_data = collect(['A', 'S', 'M1'])->map(function ($splevel) use ($lib_hospcode_counts, $is_counts) {
                return (object) [
                    'splevel' => $splevel,
                    'all' => $lib_hospcode_counts[$splevel]->count ?? 0,
                    'sent' => $is_counts[$splevel]->count ?? 0,
                ];
            });
            // dd($hosp_count_send_data);
        }

        return view('dashboard.hospital_overview', compact('hosp_count_send_data'));
    }
}
