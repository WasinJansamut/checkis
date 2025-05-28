@extends('layouts.app')
@section('style')
    <style>
        table thead tr th {
            text-align: center !important;
            vertical-align: middle !important;
        }

        .bg-light2 {
            background-color: #e2e3e5;
        }
    </style>
@endsection
@section('content')
    @php
        if (!function_exists('bg_percent')) {
            function bg_percent($value)
            {
                if (!is_numeric($value)) {
                    return '';
                }

                if ($value > 90) {
                    $bg_color = 'bg-success';
                } elseif ($value >= 70) {
                    $bg_color = 'bg-warning';
                } else {
                    $bg_color = 'bg-danger';
                }
                return $bg_color;
            }
        }
    @endphp
    <div class="container mb-3">
        <h3>Dashboard การติดตามการส่งข้อมูลและความครบถ้วนของข้อมูลตามเกณฑ์ระบบเฝ้าระวังการบาดเจ็บ Injury Surveillance (IS) ในโรงพยาบาล A S M1</h3>
        <div class="col-12">
            <form id="form" action="{{ route('dashboard.hospital_overview') }}" method="post">
                @method('POST')
                @csrf
                <div class="row">
                    <div class="col-sm-12 col-md-3 col-lg-2 mb-3">
                        <label for="fiscal_year">ปีงบประมาณ</label>
                        <span class="text-danger">*</span>
                        <select name="fiscal_year" id="fiscal_year" class="form-select select2" required>
                            <option value="">=== กรุณาเลือก ===</option>
                            @for ($year = date('Y'); $year >= 2023; $year--)
                                <option value="{{ $year }}" @if ($year == old('fiscal_year', request()->fiscal_year)) selected @endif>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6 mb-3">
                        @php
                            $month_array = [
                                10 => ['full' => 'ตุลาคม', 'short' => 'ต.ค.'],
                                11 => ['full' => 'พฤศจิกายน', 'short' => 'พ.ย.'],
                                12 => ['full' => 'ธันวาคม', 'short' => 'ธ.ค.'],
                                1 => ['full' => 'มกราคม', 'short' => 'ม.ค.'],
                                2 => ['full' => 'กุมภาพันธ์', 'short' => 'ก.พ.'],
                                3 => ['full' => 'มีนาคม', 'short' => 'มี.ค.'],
                                4 => ['full' => 'เมษายน', 'short' => 'เม.ย.'],
                                5 => ['full' => 'พฤษภาคม', 'short' => 'พ.ค.'],
                                6 => ['full' => 'มิถุนายน', 'short' => 'มิ.ย.'],
                                7 => ['full' => 'กรกฎาคม', 'short' => 'ก.ค.'],
                                8 => ['full' => 'สิงหาคม', 'short' => 'ส.ค.'],
                                9 => ['full' => 'กันยายน', 'short' => 'ก.ย.'],
                            ];
                        @endphp
                        <label for="month">เดือน</label>
                        <span class="text-danger">*</span>
                        <select name="month[]" id="month" class="form-select select2" multiple="multiple" required>
                            <option value="">=== กรุณาเลือก ===</option>
                            @foreach ($month_array as $key => $value)
                                <option value="{{ $key }}">
                                    {{ $value['full'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                        @php
                            $health_zones = [
                                'ทั้งหมด' => 'ทั้งหมด',
                                1 => 'เขตสุขภาพที่ 1',
                                2 => 'เขตสุขภาพที่ 2',
                                3 => 'เขตสุขภาพที่ 3',
                                4 => 'เขตสุขภาพที่ 4',
                                5 => 'เขตสุขภาพที่ 5',
                                6 => 'เขตสุขภาพที่ 6',
                                7 => 'เขตสุขภาพที่ 7',
                                8 => 'เขตสุขภาพที่ 8',
                                9 => 'เขตสุขภาพที่ 9',
                                10 => 'เขตสุขภาพที่ 10',
                                11 => 'เขตสุขภาพที่ 11',
                                12 => 'เขตสุขภาพที่ 12',
                            ];
                        @endphp
                        <label for="health_zone">เขตสุขภาพ</label>
                        <span class="text-danger">*</span>
                        <select name="health_zone" id="health_zone" class="form-select select2" required>
                            <option value="">=== กรุณาเลือก ===</option>
                            @foreach ($health_zones as $key => $value)
                                <option value="{{ $key }}">
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                        <label for="province">จังหวัด</label>
                        <span class="text-danger">*</span>
                        <select name="province[]" id="province" class="form-select select2 overflow-auto" multiple="multiple" required>
                            {{-- <option value="">=== กรุณาเลือก ===</option> --}}
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-6 mb-3">
                        <label for="hospital">โรงพยาบาล</label>
                        <span class="text-danger">*</span>
                        <select name="hospital[]" id="hospital" class="form-select select2" multiple="multiple"required>
                            {{-- <option value="">=== กรุณาเลือก ===</option> --}}
                        </select>
                    </div>
                </div>
                <div class="col-12 text-end mb-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <button type="button" id="clear_filter" class="d-none btn btn-dark">
                                <i class="fa-solid fa-xmark me-1"></i>
                                ล้างค่า
                            </button>
                            <button type="submit" class="btn btn-success ms-auto">
                                <i class="fa-solid fa-magnifying-glass-chart me-1"></i>
                                ค้นหา
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        @if (request()->isMethod('post'))
            @php
                // [Start] Format ของร้อยละ ถ้าได้ 100.00 ให้แสดง 100 ถ้าไม่ใช่ ก็ให้แสดงทศนิยม 2 ตำแหน่งด้วย
                if (!function_exists('number_format_percent')) {
                    function number_format_percent($value, $decimal = 2)
                    {
                        if (!is_numeric($value)) {
                            return '-';
                        }

                        return floatval($value) == 100.0 ? '100' : number_format($value, $decimal);
                    }
                }
                // [End] Format ของร้อยละ ถ้าได้ 100.00 ให้แสดง 100 ถ้าไม่ใช่ ก็ให้แสดงทศนิยม 2 ตำแหน่งด้วย
            @endphp

            <fieldset class="reset border border-dark px-3 rounded">
                <legend class="reset text-dark float-none w-auto px-2">
                    การติดตามการส่งข้อมูลระบบเฝ้าระวังการบาดเจ็บ (IS) ในโรงพยาบาล A S M1
                </legend>
                @php
                    $sum_all = $hosp_count_send_data->sum('all') ?? 0;
                    $sum_sent = $hosp_count_send_data->sum('sent') ?? 0;
                    $sum_complete_21 = $hosp_count_send_data->sum('complete_21') ?? 0;
                    $percent_sent = $sum_all > 0 ? ($sum_sent / $sum_all) * 100 : 0; // อัตราการส่งข้อมูล รพ.
                    $percent_complete_21 = $sum_all > 0 ? ($sum_complete_21 / $sum_all) * 100 : 0; // ร้อยละคุณภาพข้อมูล

                @endphp
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                        <div class="card border-1 border-dark">
                            <div class="card-header border-1 border-dark bg-light2 fw-bold">
                                <i class="fa-solid fa-bullseye me-1"></i>
                                เป้าหมาย
                            </div>
                            <div class="card-body h4 mb-0 fw-bold text-center">{{ number_format($sum_all) }}</div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                        <div class="card border-1 border-dark">
                            <div class="card-header border-1 border-dark bg-light2 fw-bold">
                                <i class="fa-solid fa-database me-1"></i>
                                ส่งข้อมูล รพ.
                            </div>
                            <div class="card-body h4 mb-0 fw-bold text-center">{{ number_format($sum_sent) }}</div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                        <div class="card border-1 border-dark">
                            <div class="card-header border-1 border-dark bg-light2 fw-bold">
                                <i class="fa-solid fa-percent me-1"></i>
                                อัตราการส่งข้อมูล รพ.
                            </div>
                            <div class="card-body h4 mb-0 fw-bold text-center">{{ number_format_percent($percent_sent) }}</div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 mb-3">
                        <div class="card border-1 border-dark">
                            <div class="card-header border-1 border-dark bg-light2 fw-bold">
                                <i class="fa-regular fa-thumbs-up me-1"></i>
                                ครบ 21 ตัวแปร โรงพยาบาล
                            </div>
                            <div class="card-body h4 mb-0 fw-bold text-center">{{ number_format($sum_complete_21) }}</div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 mb-3">
                        <div class="card border-1 border-dark {{ bg_percent($percent_complete_21) }} bg-opacity-25">
                            <div class="card-header border-1 border-dark bg-light2 fw-bold">
                                <i class="fa-solid fa-percent me-1"></i>
                                ร้อยละคุณภาพข้อมูล
                            </div>
                            <div class="card-body h4 mb-0 fw-bold text-center">{{ number_format_percent($percent_complete_21) }}</div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-6 mb-3">
                        <table class="table table-bordered table-hover table-striped border-dark mb-0" data-toggle="data-tablex" data-page-length="5">
                            <thead>
                                <tr class="table-secondary border-dark fw-bold">
                                    <th>ระดับ รพ.</th>
                                    <th>จำนวน รพ. ทั้งหมด</th>
                                    <th>จำนวน รพ. ที่ส่งข้อมูล</th>
                                    <th>โรงพยาบาล ที่ยังไม่ส่งข้อมูล</th>
                                </tr>

                            </thead>
                            <tbody>
                                @foreach ($hosp_count_send_data as $row)
                                    @php
                                        $all = $row->all ?? 0;
                                        $sent = $row->sent ?? 0;
                                        $not_sent = $all - $sent;
                                    @endphp
                                    <tr class="text-end">
                                        <td class="text-center">{{ $row->splevel ?? '-' }}</td>
                                        <td>{{ number_format($all) }}</td>
                                        <td>{{ number_format($sent) }}</td>
                                        <td>{{ number_format($not_sent) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                @php
                                    $sum_not_sent = $sum_all - $sum_sent;
                                @endphp
                                <tr class="text-end">
                                    <td class="text-center">รวม</td>
                                    <td>{{ number_format($sum_all) }}</td>
                                    <td>{{ number_format($sum_sent) }}</td>
                                    <td>{{ number_format($sum_not_sent) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="col-sm-12 col-md-12 col-lg-6 mb-3">
                        <div class="border border-1 border-dark overflow-auto p-1" style="max-height: 300px; box-sizing: border-box;">
                            <table class="table table-bordered table-hover table-striped border-dark mb-0" data-toggle="data-tablex" data-page-length="5">
                                <thead class="table-secondary border-dark fw-bold position-sticky top-0">
                                    <tr>
                                        <th>เขต</th>
                                        <th>จังหวัด</th>
                                        <th>ชื่อโรงพยาบาล</th>
                                        <th>จำนวนเดือนที่เลือก ใช้สำหรับคำนวณเปอร์เซ็น</th>
                                        <th>จำนวนเดือนที่ส่งข้อมูล</th>
                                        <th>อัตราเดือน ที่มีการส่งข้อมูล (ปัจจุบัน)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for ($i = 1; $i <= 1; $i++)
                                        <tr>
                                            <td class="text-center">เขต</td>
                                            <td>จังหวัด</td>
                                            <td>ชื่อโรงพยาบาล</td>
                                            <td class="text-end">-</td>
                                            <td class="text-end">-</td>
                                            <td class="text-end">-</td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-12 mb-3">
                        <div class="border border-1 border-dark overflow-auto p-1" style="max-height: 400px; box-sizing: border-box;">
                            <table class="table table-bordered table-hover table-striped border-dark mb-0" data-toggle="data-tablex" data-page-length="5">
                                <thead class="table-secondary border-dark fw-bold position-sticky top-0">
                                    <tr class="table-secondary border-dark fw-bold">
                                        <th>เขต</th>
                                        <th>ระดับ</th>
                                        <th>จังหวัด</th>
                                        <th>โรงพยาบาล</th>
                                        @foreach ($req_month as $m)
                                            <th>{{ $month_array[$m]['full'] }}</th>
                                        @endforeach
                                        <th>รวม</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!$hosp_send_data->pivot->isEmpty())
                                        @foreach ($hosp_send_data->pivot as $hosp_name => $data)
                                            <tr>
                                                <td class="text-center">{{ $data->region }}</td>
                                                <td class="text-center">{{ $data->splevel }}</td>
                                                <td class="text-center">{{ $data->changwat }}</td>
                                                <td>{{ $hosp_name }}</td>
                                                @foreach ($req_month as $m)
                                                    @php
                                                        $send_data_count = $data->counts[$m] ?? 0;
                                                    @endphp
                                                    <td class="text-end @if (empty($send_data_count)) table-danger border-dark @endif">{{ number_format($send_data_count) }}</td>
                                                @endforeach
                                                <td class="text-end">{{ number_format($data->total ?? 0) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="{{ count($req_month) + 5 }}" class="text-center">ไม่พบข้อมูล</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-12 mb-3">
                        <div class="border border-1 border-dark p-1">
                            <!-- [Start] กราฟ -->
                            <div id="div_show_chart">
                                <figure class="highcharts-figure">
                                    <div id="container"></div>
                                    <p class="highcharts-description"></p>
                                </figure>
                            </div>
                            <!-- [End] กราฟ -->
                        </div>
                    </div>
                </div>
            </fieldset>

            {{-- <fieldset class="reset border border-dark px-3 rounded">
                <legend class="reset text-dark float-none w-auto px-2">
                    การติดตามความครบถ้วนของข้อมูลตามเกณฑ์ ระบบเฝ้าระวังการบาดเจ็บ (IS) ในโรงพยาบาล A S M1
                </legend>
                <div class="row mb-3">
                    <div class="col-12">
                        <table class="table table-bordered table-hover table-striped border-dark mb-1" data-toggle="data-tablex" data-page-length="5">
                            <thead>
                                <tr class="table-secondary border-dark fw-bold">
                                    <th>เขตสุขภาพ</th>
                                    <th>ระดับ รพ.</th>
                                </tr>

                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>1</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </fieldset> --}}
        @else
            <div class="text-center">
                <div class="alert alert-warning py-4" role="alert">
                    คลิกปุ่ม "<b><i class="fa-solid fa-magnifying-glass-chart small"></i> ค้นหา</b>" เพื่อเรียกดูข้อมูล
                </div>
            </div>
        @endif
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            var is_onload_hospitals = true;

            $("#fiscal_year option[value='" + localStorage.getItem('fiscal_year') + "']").prop('selected', true).trigger('change');
            if (localStorage.getItem('month')) {
                $('#month').val(localStorage.getItem('month').split(',')).trigger('change'); // ใช้ trigger('change') สำหรับ select2 ด้วย
            }
            $("#health_zone option[value='" + localStorage.getItem('health_zone') + "']").prop('selected', true).trigger('change');
            $("#province").val(localStorage.getItem('province'));
            $("#hospital").val(localStorage.getItem('hospital'));

            if (localStorage.getItem('health_zone') !== null) {
                $("#clear_filter").removeClass('d-none');
            }

            $("#form").on("submit", function() {
                localStorage.clear();

                function safeSetLocalStorage(key, selector) {
                    if ($(selector).length) {
                        localStorage.setItem(key, $(selector).val());
                    } else {
                        // localStorage.setItem(key, '');
                        localStorage.removeItem(key);
                    }
                }

                safeSetLocalStorage('fiscal_year', '#fiscal_year');
                safeSetLocalStorage('month', '#month');
                safeSetLocalStorage('health_zone', '#health_zone');
                safeSetLocalStorage('province', '#province');
                safeSetLocalStorage('hospital', '#hospital');

            });

            $("#clear_filter").on("click", function() {
                localStorage.clear();
                location.href = "{{ route('dashboard.hospital_overview') }}";
            });

            $(document).ready(async function() {
                if (localStorage.getItem('health_zone')) {
                    const health_zone = localStorage.getItem('health_zone');
                    await load_provinces(health_zone, 'onload'); // รอให้โหลดจังหวัดเสร็จก่อน
                }
                if (localStorage.getItem('province')) {
                    const health_zone = localStorage.getItem('health_zone');
                    const province = localStorage.getItem('province');
                    is_onload_hospitals = true; // ✅ ตั้งไว้ก่อนโหลด
                    await load_hospitals(health_zone, province, 'onload');
                } else {
                    is_onload_hospitals = false; // ✅ ตั้ง false หลังโหลดเสร็จ
                }
            });


            // ฟังก์ชันโหลดจังหวัด
            function load_provinces(region, action) {
                return new Promise(function(resolve, reject) {
                    if (region) {
                        $.ajax({
                            url: "{{ route('dashboard.get_province_from_health_zone') }}",
                            type: 'GET',
                            data: {
                                region: region
                            },
                            success: function(response) {
                                $("#province option[value]").remove(); // Clear ค่าที่เลือกไว้
                                $("#hospital option[value]").remove(); // Clear ค่าที่เลือกไว้
                                var options = '<option value="ทั้งหมด">ทั้งหมด</option>';
                                $.each(response, function(index, value) {
                                    options += '<option value="' + value.code + '">' + value.name + '</option>';
                                });
                                $('#province').html(options);

                                if (action == 'onload' && localStorage.getItem('province')) {
                                    let storedProvinces = localStorage.getItem('province').split(',');
                                    $('#province').val(storedProvinces).trigger('change');
                                }
                                initializeSelect2('#province');
                                resolve(); // เพิ่ม resolve เพื่อบอกว่า Promise เสร็จแล้ว
                            },
                            error: function(xhr, status, error) {
                                console.error(error);
                            }
                        });
                    }
                });
            }

            // ฟังก์ชันโหลดโรงพยาบาล
            function load_hospitals(health_zone, province, action) {
                return new Promise(function(resolve, reject) {
                    if (province) {
                        $.ajax({
                            url: "{{ route('dashboard.get_hospital_asm1_from_province') }}",
                            type: 'GET',
                            data: {
                                health_zone: health_zone,
                                province: province
                            },
                            // traditional: true, // สำคัญ: ใช้ traditional เพื่อส่ง array ในรูปแบบ `province[]=value`
                            success: function(response) {
                                // console.log(response);
                                $("#hospital option[value]").remove(); // Clear ค่าที่เลือกไว้
                                var options = '<option value="ทั้งหมด">ทั้งหมด</option>';
                                $.each(response, function(index, value) {
                                    options += '<option value="' + value.off_id + '">' + value.name + '</option>';
                                });
                                $('#hospital').html(options);

                                if (is_onload_hospitals == true && localStorage.getItem('hospital')) {
                                    let storedProvinces = localStorage.getItem('hospital').split(',');
                                    $('#hospital').val(storedProvinces).trigger('change');
                                    is_onload_hospitals = false
                                }
                                initializeSelect2('#hospital');
                                resolve(); // เพิ่ม resolve เพื่อบอกว่า Promise เสร็จแล้ว
                            },
                            error: function(xhr, status, error) {
                                console.error(error);
                            }
                        });
                    }
                });
            }

            // เรียกใช้ฟังก์ชันโหลดจังหวัดเมื่อเลือกเขตสุขภาพ
            $('#health_zone').on("change", function() {
                if (!$(this).val()) { // ตรวจสอบว่า select2 ว่างหรือยัง
                    $("#province option[value]").remove(); // Clear ค่าที่เลือกไว้
                    $("#hospital option[value]").remove(); // Clear ค่าที่เลือกไว้
                }
                $('#hospital').html('<option value="">=== กรุณาเลือก ===</option>');
                load_provinces($(this).val(), 'change');
            });

            // เรียกใช้ฟังก์ชันโหลดโรงพยาบาลเมื่อเลือกจังหวัด
            $('#province').on("change", function() {
                if (is_onload_hospitals == false) {
                    if (!$(this).val()) { // ตรวจสอบว่า select2 ว่างหรือยัง
                        $("#hospital option[value]").remove(); // Clear ค่าที่เลือกไว้
                    }

                    // ตรวจสอบว่ามี "ทั้งหมด" หรือไม่
                    let all_selected = $(this).val() && $(this).val().includes('ทั้งหมด');
                    if (all_selected) {
                        // ถ้าเลือก "ทั้งหมด" → ให้เอา option อื่นออกจากการเลือก
                        $(this).find('option:not([value="ทั้งหมด"])').prop('selected', false);
                    } else {
                        // ถ้าเลือก option อื่น → ให้เอา "ทั้งหมด" ออกจากการเลือก
                        $(this).find('option[value="ทั้งหมด"]').prop('selected', false);
                    }

                    var health_zone = $('#health_zone').val()
                    var province = $(this).val()
                    load_hospitals(health_zone, province, 'change');
                }
            });

            $('#hospital').on("change", function() {
                // ตรวจสอบว่ามี "ทั้งหมด" หรือไม่
                let all_selected = $(this).val() && $(this).val().includes('ทั้งหมด');
                if (all_selected) {
                    // ถ้าเลือก "ทั้งหมด" → ให้เอา option อื่นออกจากการเลือก
                    $(this).find('option:not([value="ทั้งหมด"])').prop('selected', false);
                } else {
                    // ถ้าเลือก option อื่น → ให้เอา "ทั้งหมด" ออกจากการเลือก
                    $(this).find('option[value="ทั้งหมด"]').prop('selected', false);
                }
            });

            // ฟังก์ชันสำหรับตั้งค่า Select2
            function initializeSelect2(selector) {
                // const closeOnSelectValue = selector === '#province' || selector === '#hospital' ? false : true;
                const isMultiple = $(selector).prop('multiple'); // ตรวจสอบว่ามี attribute multiple หรือไม่
                const closeOnSelectValue = isMultiple ? false : true; // ถ้าเป็น multiple ให้ปิด closeOnSelect

                $(selector).select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    allowClear: true,
                    placeholder: "=== กรุณาเลือก ===",
                    closeOnSelect: closeOnSelectValue, // ตั้งค่า closeOnSelect ตามเงื่อนไข
                });

                $(document).on('select2:open', () => {
                    document.querySelector('.select2-search__field').focus();
                });
            }
        });
    </script>

    @if (request()->isMethod('post'))
        <!-- Highcharts -->
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/modules/export-data.js"></script>
        <script src="https://code.highcharts.com/modules/accessibility.js"></script>
        <script>
            const pivotMonthTotals = @json($hosp_send_data->pivot_month_totals);
            const pivotSplevelTotals = @json($hosp_send_data->pivot_splevel_totals);
            console.log(pivotMonthTotals);
            console.log(pivotSplevelTotals);

            const monthNames = {
                10: {
                    full: "ตุลาคม",
                    short: "ต.ค."
                },
                11: {
                    full: "พฤศจิกายน",
                    short: "พ.ย."
                },
                12: {
                    full: "ธันวาคม",
                    short: "ธ.ค."
                },
                1: {
                    full: "มกราคม",
                    short: "ม.ค."
                },
                2: {
                    full: "กุมภาพันธ์",
                    short: "ก.พ."
                },
                3: {
                    full: "มีนาคม",
                    short: "มี.ค."
                },
                4: {
                    full: "เมษายน",
                    short: "เม.ย."
                },
                5: {
                    full: "พฤษภาคม",
                    short: "พ.ค."
                },
                6: {
                    full: "มิถุนายน",
                    short: "มิ.ย."
                },
                7: {
                    full: "กรกฎาคม",
                    short: "ก.ค."
                },
                8: {
                    full: "สิงหาคม",
                    short: "ส.ค."
                },
                9: {
                    full: "กันยายน",
                    short: "ก.ย."
                }
            };
            const fiscalOrder = [10, 11, 12, 1, 2, 3, 4, 5, 6, 7, 8, 9];

            // หาค่าเดือนที่มีข้อมูลจาก pivotMonthTotals
            const availableMonths = Object.keys(pivotMonthTotals).map(Number);
            const orderedMonths = fiscalOrder.filter(m => availableMonths.includes(m));

            // รวมค่าเฉพาะ splevel A, S, M1
            const splevelKeys = ['A', 'S', 'M1'];
            const seriesData = orderedMonths.map(month => {
                return splevelKeys.reduce((sum, splevel) => {
                    return sum + (pivotSplevelTotals[splevel]?.[month] || 0);
                }, 0);
            });

            Highcharts.chart('container', {
                chart: {
                    type: 'line',
                    style: {
                        fontFamily: 'Noto Sans Thai',
                    },
                },
                title: {
                    text: '',
                    align: 'left'
                },
                credits: {
                    enabled: true,
                    text: 'แหล่งที่มาข้อมูล : ระบบเฝ้าระวังการบาดเจ็บ Injury Surveillance (IS)'
                },
                xAxis: {
                    categories: orderedMonths.map(m => monthNames[m].short)
                },
                yAxis: {
                    title: {
                        text: 'จำนวน รพ.'
                    }
                },
                tooltip: {
                    useHTML: true, // ✅ เพิ่มบรรทัดนี้

                    formatter: function() {
                        const month = orderedMonths[this.point.index];
                        const monthLabel = monthNames[month].full || 'ไม่ระบุเดือน';
                        let totalSplevelForMonth = 0;

                        let tooltip = `<b style="font-size: 16px;">${monthLabel}</b><br>`;
                        tooltip += `จำนวนที่บันทึก : <b>${pivotMonthTotals?.[month] ?? 0}</b><br>`;

                        tooltip += '<ul style="padding-left: 1.2em; margin: 0;">';
                        for (const splevel of splevelKeys) {
                            const value = pivotSplevelTotals?.[splevel]?.[month] ?? 0;
                            totalSplevelForMonth += value;
                            tooltip += `<li>${splevel} : ${value}</li>`;
                        }
                        tooltip += `<li>รวม (A, S, M1) : <b>${totalSplevelForMonth}</b></li>`;
                        tooltip += '</ul>';
                        return tooltip;
                    },
                },
                plotOptions: {
                    series: {
                        dataLabels: {
                            enabled: true, // ✅ เปิดการแสดงตัวเลข
                            style: {
                                fontSize: '12px'
                            }
                        }
                    }
                },
                series: [{
                    name: 'รวม A, S, M1',
                    color: '#4e79a7',
                    data: seriesData
                }]
            });
        </script>
    @endif
@endsection
