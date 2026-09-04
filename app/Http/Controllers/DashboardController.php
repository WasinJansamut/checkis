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
    public function is_completeness()
    {
        return view('dashboard.is_completeness', [
            'yearType' => 'calendar',
            'selectedYear' => 2569,
            'selectedMonths' => [],
            'selectedZones' => [],
            'hospitalRows' => collect(),
            'levelData' => collect(),
            'provinceData' => collect(),
            'targetByLevel' => collect(),
            'recordTotal' => 0,
            'completeTotal' => 0,
            'quality' => 0,
        ]);
    }

    public function is_completeness_summary(Request $request)
    {
        [$yearType, $selectedYear, $selectedMonths, $selectedZones] = $this->isCompletenessFilters($request);
        $calendarYear = $selectedYear - 543;

        $dateRanges = collect($selectedMonths)->map(function ($month) use ($yearType, $calendarYear) {
            $year = $calendarYear - ($yearType === 'fiscal' && $month >= 10 ? 1 : 0);
            $date = Carbon::create($year, $month, 1);
            return [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()];
        });

        $provinceCodes = $selectedZones
            ? LibChangwatModel::whereIn('region', $selectedZones)->pluck('code')->all()
            : [];
        $empty = fn($column) => "($column IS NULL OR TRIM($column) = '')";
        $completeCase = "CASE WHEN
            {$empty('is.adate')} OR {$empty('is.atime')} OR {$empty('is.hdate')} OR {$empty('is.htime')} OR
            {$empty('is.staer')} OR {$empty('is.apoint')} OR {$empty('is.tinj')} OR
            {$empty('is.risk1')} OR {$empty('is.risk2')} OR
            (is.pmi = '1' AND is.staer = '1' AND ({$empty('is.e')} OR {$empty('is.v')} OR {$empty('is.m')})) OR
            ({$empty('is.age')} AND {$empty('is.month')} AND {$empty('is.day')}) OR
            {$empty('is.bp1')} OR {$empty('is.bp2')} OR {$empty('is.rr')} OR {$empty('is.pr')} OR
            {$empty('is.br1')} OR {$empty('is.ais1')} OR {$empty('is.cause')} OR {$empty('is.cause_t')} OR
            {$empty('is.icdcause')} OR {$empty('is.ps')} OR
            (is.injt IN ('02', '021', '022', '023') AND {$empty('is.risk4')}) OR
            (is.injt IN ('04', '041', '05', '06', '07', '08', '09', '10', '18', '181', '182', '19', '191', '192') AND {$empty('is.risk3')})
            THEN 0 ELSE 1 END";

        $dashboardLevels = ['A', 'S', 'M1', 'M2', 'F1', 'F2', 'F3'];
        $targetByLevel = LibHospcodeModel::selectRaw('TRIM(splevel) as splevel, COUNT(*) as total')
            ->whereIn(DB::raw('TRIM(splevel)'), $dashboardLevels)
            ->when($selectedZones, fn($query) => $query->whereIn('changwatcode', $provinceCodes))
            ->groupBy(DB::raw('TRIM(splevel)'))->pluck('total', 'splevel');

        sort($selectedMonths);
        sort($selectedZones);
        $hospitalCacheKey = 'is_completeness_hospitals_v2_' . md5(json_encode([$yearType, $selectedYear, $selectedMonths, $selectedZones]));
        $hospitalRows = Cache::remember($hospitalCacheKey, now()->addMinutes(5), function () use ($completeCase, $dateRanges, $selectedZones, $provinceCodes, $dashboardLevels) {
            return IsModel::selectRaw("is.prov, is.hosp, lib_hospcode.name as hospital, lib_hospcode.changwat as province, lib_hospcode.region, TRIM(lib_hospcode.splevel) as splevel, COUNT(*) as records, SUM($completeCase) as complete_records, MAX(CASE WHEN is.lastupdate <= NOW() THEN is.lastupdate END) as lastupdate")
            ->join('lib_hospcode', function ($join) {
                $join->on('is.hosp', '=', 'lib_hospcode.off_id')->on('is.prov', '=', 'lib_hospcode.changwatcode');
            })
            ->where(function ($query) use ($dateRanges) {
                foreach ($dateRanges as [$start, $end]) $query->orWhereBetween('is.adate', [$start, $end]);
            })
            ->whereIn(DB::raw('TRIM(lib_hospcode.splevel)'), $dashboardLevels)
            ->when($selectedZones, fn($query) => $query->whereIn('is.prov', $provinceCodes))
            ->groupBy('is.prov', 'is.hosp', 'lib_hospcode.name', 'lib_hospcode.changwat', 'lib_hospcode.region', DB::raw('TRIM(lib_hospcode.splevel)'))
            ->get();
        });
        $hospitalRows = $hospitalRows->sortByDesc(fn($row) => $row->records ? $row->complete_records / $row->records : 0)->values();

        $levelColors = ['A' => '#2185d0', 'S' => '#2185d0', 'M1' => '#2185d0', 'M2' => '#2185d0', 'F1' => '#2185d0', 'F2' => '#e2a11d', 'F3' => '#dd713a'];
        $levelData = collect(['A', 'S', 'M1', 'M2', 'F1', 'F2', 'F3'])->map(function ($level) use ($hospitalRows, $targetByLevel, $levelColors) {
            $rows = $hospitalRows->where('splevel', $level);
            $sent = $rows->count();
            $complete = $rows->filter(fn($row) => $row->records > 0 && $row->complete_records == $row->records)->count();
            return (object) ['level' => $level, 'target' => $targetByLevel[$level] ?? 0, 'sent' => $sent, 'complete' => $complete, 'percent' => $sent ? $complete / $sent * 100 : 0, 'color' => $levelColors[$level]];
        });
        $recordTotal = $hospitalRows->sum('records');
        $completeTotal = $hospitalRows->sum('complete_records');
        $quality = $recordTotal ? $completeTotal / $recordTotal * 100 : 0;
        $lastUpdated = $hospitalRows->pluck('lastupdate')->filter()->max();
        $lastUpdatedAt = $lastUpdated
            ? Carbon::parse($lastUpdated)->addYears(543)->format('d/m/Y H:i') . ' น.'
            : null;
        $provinceData = $hospitalRows->groupBy('province')->map(function ($rows, $province) {
            return (object) ['province' => $province, 'region' => $rows->first()->region, 'records' => $rows->sum('records'), 'complete' => $rows->sum('complete_records')];
        })->values();

        return response()->json(compact('yearType', 'selectedYear', 'selectedMonths', 'selectedZones', 'hospitalRows', 'levelData', 'provinceData', 'targetByLevel', 'recordTotal', 'completeTotal', 'quality', 'lastUpdatedAt'));
    }

    public function is_completeness_cache_status(Request $request)
    {
        [$yearType, $selectedYear, $selectedMonths, $selectedZones] = $this->isCompletenessFilters($request);
        sort($selectedMonths);
        sort($selectedZones);
        $cacheKey = 'is_completeness_hospitals_v2_' . md5(json_encode([$yearType, $selectedYear, $selectedMonths, $selectedZones]));

        return response()->json(['cached' => Cache::has($cacheKey)]);
    }

    private function isCompletenessFilters(Request $request): array
    {
        $yearType = $request->input('year_type', 'calendar');
        $yearType = in_array($yearType, ['calendar', 'fiscal'], true) ? $yearType : 'calendar';
        $selectedYear = (int) $request->input('fiscal_year', 2569);
        $selectedMonths = collect($request->input('months', []))
            ->filter(fn($month) => is_numeric($month) && $month >= 1 && $month <= 12)
            ->map(fn($month) => (int) $month)->values()->all();
        $selectedMonths = $selectedMonths ?: ($yearType === 'fiscal' ? [10, 11, 12, 1, 2, 3, 4, 5, 6, 7, 8, 9] : range(1, 12));
        $selectedZones = collect($request->input('health_zones', []))->filter()->values()->all();

        return [$yearType, $selectedYear, $selectedMonths, $selectedZones];
    }

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

    public function get_hospital_asm1_from_province(Request $request) // Ajax ส่งค่าจังหวัดเพื่อหาโรงพยาบาล
    {
        $health_zone = $request->health_zone;
        $province = $request->input('province', []);
        $province = is_array($province) ? $province : explode(',', $province);
        $province = array_filter($province, fn ($value) => $value !== '');
        $selected = (array) $request->input('selected', []);
        $term = trim($request->input('term', ''));
        $page = max((int) $request->input('page', 1), 1);
        $perPage = 30;

        $query = LibHospcodeModel::select('off_id', 'name')
            ->whereIn('splevel', ['A', 'S', 'M1', 'M2', 'F1', 'F2', 'F3']);

        if ($selected) {
            $hospcodes = $query->whereIn('off_id', $selected)->orderBy('name')->get();
            return ['results' => $hospcodes->map(fn ($hospital) => ['id' => $hospital->off_id, 'text' => $hospital->name])];
        }

        if (!$province) {
            return ['results' => [], 'pagination' => ['more' => false]];
        }

        if (!in_array('ทั้งหมด', $province)) {
            $query->whereIn('changwatcode', $province);
        } elseif ($health_zone != 'ทั้งหมด') {
            $query->where('region', sprintf('%02d', $health_zone));
        }

        if ($term !== '') {
            $query->where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('off_id', 'like', "%{$term}%");
            });
        }

        $hospcodes = $query
            ->orderBy('name')
            ->forPage($page, $perPage + 1)
            ->get();
        $more = $hospcodes->count() > $perPage;
        $results = $hospcodes->take($perPage)->map(fn ($hospital) => ['id' => $hospital->off_id, 'text' => $hospital->name]);

        if ($term === '' && $page === 1) {
            $results->prepend(['id' => 'ทั้งหมด', 'text' => 'ทั้งหมด']);
        }

        return [
            'results' => $results,
            'pagination' => ['more' => $more],
        ];
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
            // Cache::forget($cache_data_name);
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
                            WHEN
                                (
                                    -- เงื่อนไข Injuries / Cause
                                    (CAST(`is`.`injt` AS UNSIGNED) = 2 AND `is`.`risk4` IS NOT NULL AND `is`.`risk4` != '') OR
                                    (CAST(`is`.`injt` AS UNSIGNED) IN (4,5,6,7,8,9,10,18,19,191,192) AND `is`.`risk3` IS NOT NULL AND `is`.`risk3` != '') OR
                                    (CAST(`is`.`injt` AS UNSIGNED) NOT IN (2,4,5,6,7,8,9,10,18,19,191,192)) OR
                                    (CAST(`is`.`cause` AS UNSIGNED) != 1)
                                )
                                AND
                                -- ตรวจสอบ 21 ตัวแปรครบ
                                `is`.`adate` IS NOT NULL AND
                                `is`.`atime` IS NOT NULL AND
                                `is`.`hdate` IS NOT NULL AND
                                `is`.`htime` IS NOT NULL AND
                                (`is`.`staer` IS NOT NULL AND `is`.`staer` != '') AND
                                (`is`.`apoint` IS NOT NULL AND `is`.`apoint` != '') AND
                                (`is`.`tinj` IS NOT NULL AND `is`.`tinj` != '') AND
                                (`is`.`risk1` IS NOT NULL AND `is`.`risk1` != '') AND
                                (`is`.`risk2` IS NOT NULL AND `is`.`risk2` != '') AND
                                `is`.`cause_t` IN ('0','1','2','3','4','5','6','7','N') AND
                                `is`.`e` IS NOT NULL AND
                                `is`.`v` IS NOT NULL AND
                                `is`.`m` IS NOT NULL AND
                                `is`.`age` IS NOT NULL AND
                                `is`.`bp1` IS NOT NULL AND
                                `is`.`rr` IS NOT NULL AND
                                `is`.`pr` IS NOT NULL AND
                                `is`.`ps` IS NOT NULL AND
                                `is`.`br1` IS NOT NULL AND
                                `is`.`ais1` IS NOT NULL
                            THEN 1
                            ELSE 0
                        END
                    ) AS complete_21,
                    SUM(
                      CASE
                            WHEN
                                (
                                    -- เงื่อนไข Injuries / Cause
                                    (CAST(`is`.`injt` AS UNSIGNED) = 2 AND `is`.`risk4` IS NOT NULL AND `is`.`risk4` != '') OR
                                    (CAST(`is`.`injt` AS UNSIGNED) IN (4,5,6,7,8,9,10,18,19,191,192) AND `is`.`risk3` IS NOT NULL AND `is`.`risk3` != '') OR
                                    (CAST(`is`.`injt` AS UNSIGNED) NOT IN (2,4,5,6,7,8,9,10,18,19,191,192)) OR
                                    (CAST(`is`.`cause` AS UNSIGNED) != 1)
                                )
                                AND
                                -- ตรวจสอบ 21 ตัวแปรครบ
                                `is`.`adate` IS NOT NULL AND
                                `is`.`atime` IS NOT NULL AND
                                `is`.`hdate` IS NOT NULL AND
                                `is`.`htime` IS NOT NULL AND
                                (`is`.`staer` IS NOT NULL AND `is`.`staer` != '') AND
                                (`is`.`apoint` IS NOT NULL AND `is`.`apoint` != '') AND
                                (`is`.`tinj` IS NOT NULL AND `is`.`tinj` != '') AND
                                (`is`.`risk1` IS NOT NULL AND `is`.`risk1` != '') AND
                                (`is`.`risk2` IS NOT NULL AND `is`.`risk2` != '') AND
                                `is`.`cause_t` IN ('0','1','2','3','4','5','6','7','N') AND
                                `is`.`e` IS NOT NULL AND
                                `is`.`v` IS NOT NULL AND
                                `is`.`m` IS NOT NULL AND
                                `is`.`age` IS NOT NULL AND
                                `is`.`bp1` IS NOT NULL AND
                                `is`.`rr` IS NOT NULL AND
                                `is`.`pr` IS NOT NULL AND
                                `is`.`ps` IS NOT NULL AND
                                `is`.`br1` IS NOT NULL AND
                                `is`.`ais1` IS NOT NULL
                            THEN 0
                            ELSE 1
                        END
                    ) AS incomplete_21,
                    COUNT(*) AS total
                ")
                    ->join('lib_hospcode', function ($join) {
                        $join->on('is.hosp', '=', 'lib_hospcode.off_id')
                            ->on('is.prov', '=', 'lib_hospcode.changwatcode');
                    })
                    ->whereNotNull('is.hosp')
                    ->where('is.hosp', '!=', '')
                    ->whereBetween('is.hdate', [$date_start, $date_end])
                    ->whereIn('lib_hospcode.splevel', ['A', 'S', 'M1', 'M2', 'F1', 'F2', 'F3'])
                    ->when($health_zone && $health_zone != 'ทั้งหมด', function ($query) use ($health_zone) {
                        $province_array = LibChangwatModel::where('region', sprintf("%02d", $health_zone))->pluck('code')->toArray();
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
                    ->orderBy(DB::raw('TRIM(lib_hospcode.splevel)'))
                    ->orderBy('lib_hospcode.name')
                    ->chunk(10000, function ($rows) use (&$all_date) {
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
        ini_set('memory_limit', '256M');
        ini_set('max_execution_time', '300'); // 300 = 5 นาที

        $hosp_count_send_data = new Collection();
        $hosp_send_data = new Collection();
        $hosp_send_data_result = new Collection();
        $hosp_send_data_pivot = new Collection();
        $hosp_send_data_pivot_month_totals = new Collection(); // รวมทุก รพ. รายเดือน
        $hosp_send_data_pivot_splevel_totals = new Collection(); // รายเดือน แยกตาม splevel

        $fiscal_year = $request->fiscal_year ?? null; // ปีงบประมาณ
        $month = $request->month ?? []; // เดือน
        sort($month); // เรียงเดือนจากน้อยไปมาก
        $health_zone = $request->health_zone ?? null; // เขตสุขภาพ
        $province = $request->province ?? null; // จังหวัด
        $hospital = $request->hospital ?? null; // โรงพยาบาล

        if ($request->isMethod('post')) {
            // 1. ดึงจำนวนทั้งหมดจาก LibHospcodeModel (ฝั่งโรงพยาบาลทั้งหมด)
            $lib_hospcode_counts =  LibHospcodeModel::select(
                DB::raw('TRIM(splevel) as splevel'),
                DB::raw('COUNT(*) as count')
            )
                // ->whereIn('splevel', ['A', 'S', 'M1', 'M2', 'F1', 'F2', 'F3'])
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
                ->groupBy(DB::raw('TRIM(splevel)'))
                ->get()
                ->keyBy('splevel'); // แปลงเป็น key => value เพื่อให้เทียบง่าย

            // 2. ดึงจำนวนจาก IsModel ที่ส่งข้อมูล (join กับ LibHospcodeModel เพื่อได้ splevel)
            $user_id = user_info('uid');
            $province_to_str = implode("-", $province);
            $hospital_to_str = implode("-", $hospital);

            $month_to_str = implode("-", $month);
            $cache_is_counts_name = "cached_hospital_overview_UID{$user_id}_FY{$fiscal_year}_M{$month_to_str}_R{$health_zone}_P{$province_to_str}_H{$hospital_to_str}";
            // Cache::forget($cache_is_counts_name);
            $is_counts = Cache::remember($cache_is_counts_name, now()->addMinutes(1), function () use ($health_zone, $province, $hospital, $fiscal_year, $month) {
                return IsModel::selectRaw("
                        TRIM(lib_hospcode.splevel) as splevel,
                        COUNT(distinct is.hosp) as count,
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
                        ) AS complete_21
                    ")
                    ->join('lib_hospcode', function ($join) {
                        $join->on('is.hosp', '=', 'lib_hospcode.off_id')
                            ->on('is.prov', '=', 'lib_hospcode.changwatcode');
                    })
                    ->where(function ($q) use ($month, $fiscal_year) {
                        foreach ($month as $m) {
                            if ($m < 1 || $m > 12) continue;

                            $date_start = Carbon::create($fiscal_year, $m, 1)->startOfMonth();
                            $date_end   = Carbon::create($fiscal_year, $m, 1)->endOfMonth();

                            $q->orWhereBetween('is.adate', [$date_start, $date_end]);
                        }
                    })
                    // ->whereIn('lib_hospcode.splevel', ['A', 'S', 'M1', 'M2', 'F1', 'F2', 'F3'])
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
                    ->groupBy(DB::raw('TRIM(lib_hospcode.splevel)'))
                    ->get()
                    ->keyBy('splevel');
            });

            $user_id = user_info('uid');
            $province_to_str = implode("-", $province);
            $hospital_to_str = implode("-", $hospital);

            $cache_data_21_name = "cached_hospital_overview_data_21_UID{$user_id}_R{$health_zone}_P{$province_to_str}_H{$hospital_to_str}";
            // Cache::forget($cache_data_21_name);
            $data_21 = Cache::remember($cache_data_21_name, now()->addMinutes(1), function () use ($health_zone, $province, $hospital, $fiscal_year, $month) {
                return IsModel::selectRaw("
                        prov,
                        hosp,
                        lib_hospcode.name as hosp_name,
                        lib_hospcode.changwat,
                        lib_hospcode.region,
                        TRIM(lib_hospcode.splevel) as splevel,
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
                    ->join('lib_hospcode', function ($join) {
                        $join->on('is.hosp', '=', 'lib_hospcode.off_id')
                            ->on('is.prov', '=', 'lib_hospcode.changwatcode');
                    })
                    ->whereNotNull('is.hosp')
                    ->where('is.hosp', '!=', '')
                    // ->whereYear('is.adate', $fiscal_year)
                    // ->whereIn(DB::raw('MONTH(is.adate)'), $month)
                    ->where(function ($q) use ($month, $fiscal_year) {
                        foreach ($month as $m) {
                            // กันเดือนหลุด
                            if ($m < 1 || $m > 12) continue;

                            $date_start = Carbon::create($fiscal_year, $m, 1)->startOfMonth();
                            $date_end   = Carbon::create($fiscal_year, $m, 1)->endOfMonth();

                            $q->orWhereBetween('is.adate', [$date_start, $date_end]);
                        }
                    })
                    // ->whereIn('lib_hospcode.splevel', ['A', 'S', 'M1', 'M2', 'F1', 'F2', 'F3'])
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
                        DB::raw('TRIM(lib_hospcode.splevel)')
                    )
                    ->orderBy('lib_hospcode.region')
                    ->orderBy('lib_hospcode.changwat')
                    ->orderBy(DB::raw('TRIM(lib_hospcode.splevel)'))
                    ->orderBy('lib_hospcode.name')
                    ->get();
            });

            $has_complete_21_count = $data_21
                ->filter(function ($item) {
                    return !empty($item->hosp);  // กรองเอาเฉพาะแถวที่ hosp ไม่ว่าง ไม่เป็น null
                })
                ->groupBy(fn($item) => trim($item->splevel))
                ->mapWithKeys(function ($group, $splevel) {
                    $count = $group->filter(function ($item) {
                        return (int) $item->complete_21 > 0;
                    })->count();
                    return [$splevel => $count];
                });

            // 3. รวมข้อมูลสองฝั่ง
            $hosp_count_send_data = collect(['A', 'S', 'M1', 'M2', 'F1', 'F2', 'F3'])->map(function ($splevel) use ($lib_hospcode_counts, $is_counts, $has_complete_21_count) {
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
                DB::raw('TRIM(lib_hospcode.splevel) as splevel'),
                DB::raw('COUNT(*) as count')
            )
                ->join('lib_hospcode', function ($join) {
                    $join->on('is.hosp', '=', 'lib_hospcode.off_id')
                        ->on('is.prov', '=', 'lib_hospcode.changwatcode');
                })
                // ->whereYear('is.adate', $fiscal_year)
                // ->whereIn(DB::raw('MONTH(is.adate)'), $month)
                ->where(function ($q) use ($month, $fiscal_year) {
                    foreach ($month as $m) {
                        // กันเดือนหลุด
                        if ($m < 1 || $m > 12) continue;

                        $date_start = Carbon::create($fiscal_year, $m, 1)->startOfMonth();
                        $date_end   = Carbon::create($fiscal_year, $m, 1)->endOfMonth();

                        $q->orWhereBetween('is.adate', [$date_start, $date_end]);
                    }
                })
                // ->whereIn('lib_hospcode.splevel', ['A', 'S', 'M1', 'M2', 'F1', 'F2', 'F3'])
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
                ->orderBy('lib_hospcode.changwat')
                ->orderBy(DB::raw('TRIM(lib_hospcode.splevel)'))
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

                // ✅ 2. รวมจำนวนข้อมูลรายเดือนแยกตาม splevel
                $splevel = trim($item->splevel ?? 'ไม่ระบุ');

                // ตรวจสอบว่าเคยนับ hosp_name นี้ใน splevel + เดือนนี้หรือยัง
                $seen_key = "{$splevel}_{$item->month}_{$hosp_name}";
                if (isset($seen_hospitals[$seen_key])) {
                    continue; // เคยนับแล้ว ข้าม
                }

                $seen_hospitals[$seen_key] = true; // ✅ mark ว่านับแล้ว

                // ✅ ดึงข้อมูลเดิมจาก collection
                $splevel_data = $hosp_send_data_pivot_splevel_totals->get($splevel, []);
                $splevel_data[$item->month] = ($splevel_data[$item->month] ?? 0) + $item->count;
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
