<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\IsModel;
use App\Models\LibHospcodeModel;
use App\Models\LibChangwatModel;

class DashboardController extends Controller
{
    public function get_province_from_health_zone(Request $request) // Ajax ส่งค่าเขตสุขภาพเพื่อหาจังหวัด
    {
        $region = $request->region;
        if ($region == 'ทั้งหมด') {
            $provinces = Cache::remember('cached_get_province_from_health_zone_all', now()->addDays(3), function () {
                return LibChangwatModel::select('code', 'name', 'region')
                    ->orderby('name', 'ASC')
                    ->get();
            });
        } else {
            $region = sprintf("%02d", $request->region);
            $provinces = Cache::remember("cached_get_province_from_health_zone_R{$region}", now()->addDays(3), function () use ($region) {
                return  LibChangwatModel::select('code', 'name', 'region')
                    ->where('region', $region)
                    ->orderby('name', 'ASC')
                    ->get();
            });
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

        // 🔑 สร้าง cache key ที่ไม่ชนกัน
        $province_key = implode('-', $province);
        $cache_name = "get_hospital_from_province_R{$health_zone}_P{$province_key}";
        $hospcodes = Cache::remember($cache_name, now()->addHours(3), function () use ($health_zone, $province) {
            $query = LibHospcodeModel::select('region', 'changwatcode', 'off_id', 'name');
            if (!in_array('ทั้งหมด', $province)) {
                $query->whereIn('changwatcode', $province);
            } elseif ($health_zone != 'ทั้งหมด' && in_array('ทั้งหมด', $province)) {
                $query->where('region', sprintf("%02d", $health_zone));
            }
            return $query->orderBy('name', 'ASC')->get();
        });

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

        // 🔑 สร้าง cache key ที่ไม่ชนกัน
        $province_key = implode('-', $province);
        $cache_name = "cached_get_hospital_asm1_from_province_R{$health_zone}_P{$province_key}";
        $hospcodes = Cache::remember($cache_name, now()->addHours(3), function () use ($health_zone, $province) {
            $query = LibHospcodeModel::select('region', 'changwatcode', 'off_id', 'name')
                ->whereIn('splevel', ['A', 'S', 'M1']);
            if (!in_array('ทั้งหมด', $province)) {
                $query->whereIn('changwatcode', $province);
            } elseif ($health_zone != 'ทั้งหมด' && in_array('ทั้งหมด', $province)) {
                $query->where('region', sprintf("%02d", $health_zone));
            }
            return $query->orderBy('name', 'ASC')->get();
        });

        return $hospcodes;
    }

    public function hospital_21_variables(Request $request)
    {
        ini_set('max_execution_time', 120); // เพิ่มเป็น 120 วินาที

        $data = new Collection();
        if ($request->isMethod('post')) {
            $date_start = $request->date_start ?? null; // วันที่เริ่มต้น
            $date_end = $request->date_end ?? null; // วันที่สิ้นสุด
            $health_zone = $request->health_zone ?? null; // เขตสุขภาพ
            $health_zone = $request->health_zone ?? null; // เขตสุขภาพ
            $province = $request->province ?? null; // จังหวัด
            $hospital = $request->hospital ?? null; // โรงพยาบาล

            // $lib_hospcode_array = LibHospcodeModel::limit(100)
            //     ->pluck('off_id')
            //     ->map(function ($id) {
            //         return str_pad($id, 5, '0', STR_PAD_LEFT); // บังคับให้มี 5 หลักเสมอ
            //     })
            //     ->toArray(); // เอา off_id ของ รพ. มาทั้งหมด เก็บในรูปแบบ Array

            $user_id = user_info('uid');
            $province_to_str = implode("-", $province);
            $hospital_to_str = implode("-", $hospital);
            $cache_data_name = "cached_hospital_21_variables_UID{$user_id}_DS{$date_start}_DE{$date_end}_R{$health_zone}_P{$province_to_str}_H{$hospital_to_str}";
            $data = Cache::remember($cache_data_name, now()->addMinutes(1), function () use ($date_start, $date_end, $health_zone, $province, $hospital) {
                $date_start = Carbon::parse($date_start)->startOfDay();
                $date_end = Carbon::parse($date_end)->endOfDay();

                $all_date = collect();

                IsModel::selectRaw("
                    is.prov,
                    is.hosp,
                    lib_hospcode.name as hosp_name,
                    lib_hospcode.changwat,
                    lib_hospcode.region,
                    lib_hospcode.splevel,
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
                    ->join('lib_hospcode', 'is.hosp', '=', 'lib_hospcode.off_id')
                    ->whereNotNull('is.hosp')
                    ->where('is.hosp', '!=', '')
                    ->whereBetween('is.hdate', [$date_start, $date_end])
                    ->whereIn('lib_hospcode.splevel', ['A', 'S', 'M1'])
                    ->when($health_zone && $health_zone != 'ทั้งหมด', function ($query) use ($health_zone) {
                        $province_array = \App\Models\LibChangwatModel::where('region', sprintf("%02d", $health_zone))->pluck('code')->toArray();
                        return $query->whereIn('is.prov', $province_array);
                    })
                    ->when($province && !in_array("ทั้งหมด", (array)$province), function ($query) use ($province) {
                        $province_array = is_array($province) ? $province : [$province];
                        return $query->whereIn('is.prov', $province_array);
                    })
                    ->when($hospital && !in_array("ทั้งหมด", (array)$hospital), function ($query) use ($hospital) {
                        $hospital_array = is_array($hospital) ? $hospital : [$hospital];
                        return $query->whereIn('is.hosp', $hospital_array);
                    })
                    ->groupBy(
                        'is.prov',
                        'is.hosp',
                        'lib_hospcode.name',
                        'lib_hospcode.changwat',
                        'lib_hospcode.region',
                        'lib_hospcode.splevel'
                    )
                    ->orderBy('lib_hospcode.region')
                    ->orderBy('lib_hospcode.changwat')
                    ->orderBy('lib_hospcode.splevel')
                    ->orderBy('lib_hospcode.name')
                    ->chunk(1000, function ($rows) use (&$all_date) {
                        $all_date = $all_date->merge($rows);
                    });

                return $all_date;
            });
            // dd($data);
        }
        return view('dashboard.hospital_21_variables', compact('data'));
    }

    public function hospital_overview(Request $request)
    {
        ini_set('max_execution_time', 120); // เพิ่มเป็น 120 วินาที

        $hosp_count_send_data = new Collection();
        $hosp_send_data = new Collection();
        $hosp_send_data_result = new Collection();
        $hosp_send_data_pivot = new Collection();
        $hosp_send_data_pivot_month_totals = new Collection(); // รวมทุก รพ. รายเดือน
        $hosp_send_data_pivot_splevel_totals = new Collection(); // รายเดือน แยกตาม splevel

        $fiscal_year = $request->fiscal_year ?? null; // ปีงบประมาณ
        $month = $request->month ?? null; // เดือน
        $health_zone = $request->health_zone ?? null; // เขตสุขภาพ
        $province = $request->province ?? null; // จังหวัด
        $hospital = $request->hospital ?? null; // โรงพยาบาล

        if ($request->isMethod('post')) {
            // 1. ดึงจำนวนทั้งหมดจาก LibHospcodeModel (ฝั่งโรงพยาบาลทั้งหมด)
            $lib_hospcode_counts = LibHospcodeModel::select('splevel', DB::raw('COUNT(*) as count'))
                ->whereIn('splevel', ['A', 'S', 'M1'])
                ->when($health_zone && $health_zone != 'ทั้งหมด', function ($query) use ($health_zone) {
                    $province_array = LibChangwatModel::where('region', sprintf("%02d", $health_zone))->pluck('code')->toArray();
                    return $query->whereIn('changwatcode', $province_array);
                })
                ->when($province && !in_array("ทั้งหมด", $province), function ($query) use ($province) {
                    $province_array = is_array($province) ? $province : [$province];
                    return $query->whereIn('changwatcode', $province_array);
                })
                ->when($hospital && !in_array("ทั้งหมด", $hospital), function ($query) use ($hospital) {
                    $hospital_array = is_array($hospital) ? $hospital : [$hospital];
                    return $query->whereIn('off_id', $hospital_array);
                })
                ->groupBy('splevel')
                ->get()
                ->keyBy('splevel'); // แปลงเป็น key => value เพื่อให้เทียบง่าย

            // 2. ดึงจำนวนจาก IsModel ที่ส่งข้อมูล (join กับ LibHospcodeModel เพื่อได้ splevel)
            $user_id = user_info('uid');
            $province_to_str = implode("-", $province);
            $hospital_to_str = implode("-", $hospital);
            $cache_is_counts_name = "cached_hospital_overview_UID{$user_id}_R{$health_zone}_P{$province_to_str}_H{$hospital_to_str}";
            $is_counts = Cache::remember($cache_is_counts_name, now()->addMinutes(1), function () use ($health_zone, $province, $hospital) {
                return IsModel::select('lib_hospcode.splevel', DB::raw('COUNT(distinct is.hosp) as count'))
                    ->selectRaw("
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
                    ) AS complete_21")
                    ->join('lib_hospcode', 'is.hosp', '=', 'lib_hospcode.off_id')
                    ->whereIn('lib_hospcode.splevel', ['A', 'S', 'M1'])
                    ->when($health_zone && $health_zone != 'ทั้งหมด', function ($query) use ($health_zone) {
                        $province_array = LibChangwatModel::where('region', sprintf("%02d", $health_zone))->pluck('code')->toArray();
                        return $query->whereIn('is.prov', $province_array);
                    })
                    ->when($province && !in_array("ทั้งหมด", $province), function ($query) use ($province) {
                        $province_array = is_array($province) ? $province : [$province];
                        return $query->whereIn('is.prov', $province_array);
                    })
                    ->when($hospital && !in_array("ทั้งหมด", $hospital), function ($query) use ($hospital) {
                        $hospital_array = is_array($hospital) ? $hospital : [$hospital];
                        return $query->whereIn('is.hosp', $hospital_array);
                    })
                    ->groupBy('lib_hospcode.splevel')
                    ->get()
                    ->keyBy('splevel');
            });

            $user_id = user_info('uid');
            $province_to_str = implode("-", $province);
            $hospital_to_str = implode("-", $hospital);
            $cache_data_21_name = "cached_hospital_overview_data_21_UID{$user_id}_R{$health_zone}_P{$province_to_str}_H{$hospital_to_str}";
            Cache::forget($cache_data_21_name);
            $data_21 = Cache::remember($cache_data_21_name, now()->addMinutes(1), function () use ($health_zone, $province, $hospital, $fiscal_year, $month) {
                return IsModel::selectRaw("
                    prov,
                    hosp,
                    lib_hospcode.name as hosp_name,
                    lib_hospcode.changwat,
                    lib_hospcode.region,
                    lib_hospcode.splevel,
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
                    ->join('lib_hospcode', 'is.hosp', '=', 'lib_hospcode.off_id')
                    ->whereNotNull('is.hosp')
                    ->where('is.hosp', '!=', '')
                    ->whereYear('is.adate', $fiscal_year)
                    ->whereIn(DB::raw('MONTH(is.adate)'), $month)
                    ->whereIn('lib_hospcode.splevel', ['A', 'S', 'M1'])
                    ->when($health_zone && $health_zone != 'ทั้งหมด', function ($query) use ($health_zone) {
                        $province_array = LibChangwatModel::where('region', sprintf("%02d", $health_zone))->pluck('code')->toArray();
                        return $query->whereIn('is.prov', $province_array);
                    })
                    ->when($province && !in_array("ทั้งหมด", $province), function ($query) use ($province) {
                        $province_array = is_array($province) ? $province : [$province];
                        return $query->whereIn('is.prov', $province_array);
                    })
                    ->when($hospital && !in_array("ทั้งหมด", $hospital), function ($query) use ($hospital) {
                        $hospital_array = is_array($hospital) ? $hospital : [$hospital];
                        return $query->whereIn('is.hosp', $hospital_array);
                    })
                    ->groupBy(
                        'is.prov',
                        'is.hosp',
                        'lib_hospcode.name',
                        'lib_hospcode.changwat',
                        'lib_hospcode.region',
                        'lib_hospcode.splevel'
                    )
                    ->orderBy('lib_hospcode.region')
                    ->orderBy('lib_hospcode.changwat')
                    ->orderBy('lib_hospcode.splevel')
                    ->orderBy('lib_hospcode.name')
                    ->get();
            });

            $has_complete_21_count = $data_21
                ->filter(function ($item) {
                    return !empty($item->hosp);  // กรองเอาเฉพาะแถวที่ hosp ไม่ว่าง ไม่เป็น null
                })
                ->groupBy('splevel')
                ->mapWithKeys(function ($group, $splevel) {
                    $count = $group->filter(function ($item) {
                        return (int) $item->complete_21 > 0;
                    })->count();
                    return [$splevel => $count];
                });

            // 3. รวมข้อมูลสองฝั่ง
            $hosp_count_send_data = collect(['A', 'S', 'M1'])->map(function ($splevel) use ($lib_hospcode_counts, $is_counts, $has_complete_21_count) {
                return (object) [
                    'splevel' => $splevel,
                    'all' => $lib_hospcode_counts[$splevel]->count ?? 0,
                    'sent' => $is_counts[$splevel]->count ?? 0,
                    'complete_21' => $has_complete_21_count[$splevel] ?? 0,
                ];
            });
            // dd($has_complete_21_count, $data_21, $hosp_count_send_data);

            $month_array = [
                10 => 'ตุลาคม',
                11 => 'พฤศจิกายน',
                12 => 'ธันวาคม',
                1 => 'มกราคม',
                2 => 'กุมภาพันธ์',
                3 => 'มีนาคม',
                4 => 'เมษายน',
                5 => 'พฤษภาคม',
                6 => 'มิถุนายน',
                7 => 'กรกฎาคม',
                8 => 'สิงหาคม',
                9 => 'กันยายน',
            ];

            // query นับข้อมูล per month ที่ user เลือก
            $hosp_send_data = IsModel::select(
                DB::raw('MONTH(is.adate) as month'),
                'is.hosp',
                'lib_hospcode.region',
                'lib_hospcode.changwat',
                'lib_hospcode.name AS hosp_name',
                'lib_hospcode.splevel',
                DB::raw('COUNT(*) as count')
            )
                ->join('lib_hospcode', 'is.hosp', '=', 'lib_hospcode.off_id') // ใช้ชื่อ table จริง
                ->whereYear('is.adate', $fiscal_year)
                ->whereIn(DB::raw('MONTH(is.adate)'), $month)
                ->whereIn('lib_hospcode.splevel', ['A', 'S', 'M1'])
                ->when($health_zone && $health_zone != 'ทั้งหมด', function ($query) use ($health_zone) {
                    $province_array = LibChangwatModel::where('region', sprintf("%02d", $health_zone))->pluck('code')->toArray();
                    return $query->whereIn('is.prov', $province_array);
                })
                ->when($province && !in_array("ทั้งหมด", $province), function ($query) use ($province) {
                    $province_array = is_array($province) ? $province : [$province];
                    return $query->whereIn('is.prov', $province_array);
                })
                ->when($hospital && !in_array("ทั้งหมด", $hospital), function ($query) use ($hospital) {
                    $hospital_array = is_array($hospital) ? $hospital : [$hospital];
                    return $query->whereIn('is.hosp', $hospital_array);
                })
                ->groupBy(
                    DB::raw('MONTH(is.adate)'),
                    'is.hosp',
                    'lib_hospcode.region',
                    'lib_hospcode.changwat',
                    'lib_hospcode.name',
                    'lib_hospcode.splevel'
                )
                ->orderBy('lib_hospcode.region')
                ->orderBy('lib_hospcode.splevel')
                ->orderBy('lib_hospcode.changwat')
                ->orderBy('lib_hospcode.name')
                ->get();

            foreach ($month_array as $m) {
                if (in_array($m, $month)) {
                    $hosp_send_data_result[$m] = (object) [
                        'year' => $fiscal_year,
                        'region' => $hosp_send_data[$m]->region ?? '',
                        'hosp_name' => $hosp_send_data[$m]->hosp_name ?? '',
                        'changwat' => $hosp_send_data[$m]->changwat ?? '',
                        'splevel' => $hosp_send_data[$m]->splevel ?? '',
                        'month' => $m,
                        'label' => $month_array[$m],
                        'count' => isset($hosp_send_data[$m]) ? $hosp_send_data[$m]->count : 0
                    ];
                }
            }

            $seen_hospitals = [];
            foreach ($hosp_send_data as $item) {
                $hosp_name = $item->hosp_name ?? 'ไม่ทราบชื่อ';

                $existing = $hosp_send_data_pivot->get($hosp_name, (object) [
                    'region' => $item->region ?? '',
                    'changwat' => $item->changwat ?? '',
                    'splevel' => $item->splevel ?? '',
                    'counts' => [],
                    'total' => 0,
                ]);

                $existing->counts[$item->month] = $item->count; // เก็บ count แยกเดือน
                $existing->total += $item->count; // รวม count ทุกเดือน
                $hosp_send_data_pivot->put($hosp_name, $existing);


                // ✅ 1. รวมยอดรายเดือนทั้งหมด
                $current_month_total = $hosp_send_data_pivot_month_totals->get($item->month, 0);
                $hosp_send_data_pivot_month_totals->put($item->month, $current_month_total + $item->count);

                // ✅ 2. รวมยอดรายเดือนแยกตาม splevel
                $splevel = $item->splevel ?? 'ไม่ระบุ';

                // ตรวจสอบว่าเคยนับ hosp_name นี้ใน splevel + เดือนนี้หรือยัง
                $seen_key = "{$splevel}_{$item->month}_{$hosp_name}";
                if (isset($seen_hospitals[$seen_key])) {
                    continue; // เคยนับแล้ว ข้าม
                }

                $seen_hospitals[$seen_key] = true; // ✅ mark ว่านับแล้ว

                // ✅ ดึงข้อมูลเดิมจาก collection
                $splevel_data = $hosp_send_data_pivot_splevel_totals->get($splevel, []);
                $splevel_data[$item->month] = ($splevel_data[$item->month] ?? 0) + 1; // นับเป็น 1 โรงพยาบาล
                $hosp_send_data_pivot_splevel_totals->put($splevel, $splevel_data);
            }

            // dd($hosp_send_data_pivot);
        }

        return view('dashboard.hospital_overview', [
            'hosp_count_send_data' => $hosp_count_send_data,
            'hosp_send_data' => (object) [
                'result' => $hosp_send_data_result,
                'pivot' => $hosp_send_data_pivot,
                'pivot_month_totals' => $hosp_send_data_pivot_month_totals->toArray(),
                'pivot_splevel_totals' => $hosp_send_data_pivot_splevel_totals->toArray(),
            ],
            'req_month' => $month,
        ]);
    }
}
