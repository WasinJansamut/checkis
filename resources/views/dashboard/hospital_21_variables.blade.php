@extends('layouts.app')
@section('style')
    <style>
        table thead tr th {
            text-align: center !important;
            vertical-align: middle !important;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid dashboard-page hospital-21-page">
        <div class="d-flex align-items-center gap-3 my-4 overview-page-header">
            <span class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-3 p-3 fs-5"><i class="fa-solid fa-chart-column"></i></span>
            <div>
                <h1 class="h3 fw-bold mb-1">Dashboard</h1>
                <p class="text-muted small mb-0">สรุปข้อมูลโรงพยาบาล (21 ตัวแปร)</p>
            </div>
        </div>
        <div class="col-12">
            <form id="form" action="{{ route('dashboard.hospital_21_variables') }}" method="post" class="dashboard-filter">
                @method('POST')
                @csrf
                <div class="d-flex align-items-center gap-2 pb-3 mb-3 border-bottom">
                    <span class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-3 p-2"><i class="fa-solid fa-sliders"></i></span>
                    <div>
                        <h2 class="h6 fw-bold mb-0">ตัวกรองรายงาน</h2>
                        <small class="text-muted">เลือกช่วงเวลาและหน่วยงานที่ต้องการสรุปผล</small>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <label for="date_start" class="form-label">วันที่เริ่มต้น</label>
                        <small><i class="fa-solid fa-circle-info text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="วันที่มาถึงโรงพยาบาล"></i></small>
                        <span class="text-danger">*</span>
                        <input type="date" name="date_start" id="date_start" class="form-control" required>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <label for="date_end" class="form-label">วันที่สิ้นสุด</label>
                        <small><i class="fa-solid fa-circle-info text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="วันที่มาถึงโรงพยาบาล"></i></small>
                        <span class="text-danger">* ไม่เกิน 90 วัน</span>
                        <input type="date" name="date_end" id="date_end" class="form-control" required>
                    </div>
                </div>
                <div class="row g-3 mt-0">
                    <div class="col-sm-12 col-md-6 col-lg-3">
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
                        <label for="health_zone" class="form-label">เขตสุขภาพ</label>
                        <span class="text-danger">*</span>
                        <select name="health_zone" id="health_zone" class="form-select select2-dynamic" required>
                            <option value="">=== กรุณาเลือก ===</option>
                            @foreach ($health_zones as $key => $value)
                                <option value="{{ $key }}">
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <label for="province" class="form-label">จังหวัด</label>
                        <span class="text-danger">*</span>
                        <select name="province[]" id="province" class="form-select select2-dynamic overflow-auto" multiple="multiple" required>
                            {{-- <option value="">=== กรุณาเลือก ===</option> --}}
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-6">
                        <label for="hospital" class="form-label">โรงพยาบาล</label>
                        <span class="text-danger">*</span>
                        <select name="hospital[]" id="hospital" class="form-select select2-dynamic" multiple="multiple"required>
                            <option value="ทั้งหมด">ทั้งหมด</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-3 mt-3 border-top">
                    <button type="button" id="clear_filter" class="d-none btn btn-outline-secondary">
                        <i class="fa-solid fa-xmark me-1"></i>
                        ล้างค่า
                    </button>
                    <button type="submit" class="btn btn-success px-4 ms-auto">
                        <i class="fa-solid fa-magnifying-glass-chart me-1"></i>
                        ค้นหา
                    </button>
                </div>
            </form>
        </div>

        @if (request()->isMethod('post'))
            <div class="overview-section-heading">
                <span class="overview-section-heading__icon"><i class="fa-solid fa-table-columns"></i></span>
                <div>
                    <h2 class="h5 fw-bold mb-0">ผลสรุปความครบถ้วน 21 ตัวแปร</h2>
                    <p class="text-muted small mb-0">แสดงจำนวนข้อมูลและร้อยละความครบถ้วนรายโรงพยาบาล</p>
                </div>
            </div>
            <div class="dashboard-table-card">
                <table class="table table-bordered table-hover mb-0" data-toggle="data-table" data-page-length="-1">
                    <thead>
                        <tr class="border-white text-white fw-bold" style="background-color: #006637;">
                            <th rowspan="2" style="width: 75px; min-width: 75px; max-width: 75px;">เขตสุขภาพ</th>
                            <th rowspan="2" style="width: 105px; min-width: 105px; max-width: 105px;">จังหวัด</th>
                            <th rowspan="2" style="width: 65px; min-width: 65px; max-width: 65px;">ระดับ รพ.</th>
                            <th rowspan="2" style="min-width: 150px;">โรงพยาบาล</th>
                            <th colspan="3">จำนวน (ราย)</th>
                            <th rowspan="2" style="width: 55px; min-width: 55px; max-width: 55px;">ร้อยละ<br><small>(ครบ)</small></th>
                            <th rowspan="2" style="width: 55px; min-width: 55px; max-width: 55px;">ร้อยละ<br><small>(ไม่ครบ)</small></th>
                        </tr>
                        <tr class="border-white text-white fw-bold" style="background-color: #006637;">
                            <th style="width: 105px; min-width: 105px; max-width: 105px;">ทั้งหมด</th>
                            <th style="width: 105px; min-width: 105px; max-width: 105px;"><small>ครบ 21 ตัวแปร</small></th>
                            <th style="width: 105px; min-width: 105px; max-width: 105px;"><small>ไม่ครบ 21 ตัวแปร</small></th>
                        </tr>
                    </thead>
                    <tbody>
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

                            if (!function_exists('bg_percent')) {
                                function bg_percent($value)
                                {
                                    if (!is_numeric($value)) {
                                        return '';
                                    }

                                    if ($value > 90) {
                                        $bg_color = 'table-success border-dark';
                                    } elseif ($value >= 70) {
                                        $bg_color = 'table-warning border-dark';
                                    } else {
                                        $bg_color = 'table-danger border-dark';
                                    }
                                    return $bg_color;
                                }
                            }

                            $sum_percent_complete_21 = 0;
                            $sum_percent_incomplete_21 = 0;
                        @endphp
                        @foreach ($data as $row)
                            @php
                                $complete_21 = $row->complete_21 ?? 0;
                                $incomplete_21 = $row->incomplete_21 ?? 0;
                                $total = $complete_21 + $incomplete_21;
                                $percent_complete_21 = $total > 0 ? number_format_percent(($complete_21 / $total) * 100, 2) : 0;
                                $percent_incomplete_21 = number_format_percent(100 - $percent_complete_21, 2);
                                $sum_percent_complete_21 += $percent_complete_21;
                                $sum_percent_incomplete_21 += $percent_incomplete_21;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $row->region ?? '' }}</td>
                                <td>{{ $row->changwat ?? '' }}</td>
                                <td class="text-center">{{ trim($row->splevel ?? '') }}</td>
                                <td>{{ $row->hosp_name ?? '' }}</td>
                                <td class="text-end">{{ number_format($total) }}</td>
                                <td class="text-end">{{ number_format($complete_21) }}</td>
                                <td class="text-end">{{ number_format($incomplete_21) }}</td>
                                <td class="{{ bg_percent($percent_complete_21) }} text-end">{{ $percent_complete_21 }}</td>
                                <td class="text-end">{{ $percent_incomplete_21 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @php
                            $sum_complete_21 = $data->sum('complete_21') ?? 0; // รวมทั้งหมดครบ 21 ตัวแปร
                            $sum_incomplete_21 = $data->sum('incomplete_21') ?? 0; // รวมทั้งหมดไม่ครบ 21 ตัวแปร
                            $sum_total = $sum_complete_21 + $sum_incomplete_21; // รวมจำนวนทั้งหมด

                            $avg_sum_percent_complete_21 = $sum_percent_complete_21 > 0 ? $sum_percent_complete_21 / $data->count() : 0;
                            $avg_sum_percent_incomplete_21 = $sum_percent_incomplete_21 > 0 ? $sum_percent_incomplete_21 / $data->count() : 0;
                        @endphp
                        <tr class="table-secondary border-dark fw-bold">
                            <td colspan="4" class="text-end"><b>รวมทั้งหมด</b></td>
                            <td class="text-end">{{ number_format($sum_total) }}</td>
                            <td class="text-end">{{ number_format($sum_complete_21) }}</td>
                            <td class="text-end">{{ number_format($sum_incomplete_21) }}</td>
                            <td class="{{ bg_percent($avg_sum_percent_complete_21) }} text-end">{{ number_format_percent($avg_sum_percent_complete_21) }}</td>
                            <td class="text-end">{{ number_format_percent($avg_sum_percent_incomplete_21) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="dashboard-empty">
                <div class="d-block mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-3 p-3 fs-5"><i class="fa-solid fa-chart-column"></i></span>
                </div>
                คลิกปุ่ม <b><i class="fa-solid fa-magnifying-glass-chart small"></i> ค้นหา</b> เพื่อเรียกดูข้อมูล
            </div>
        @endif
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            var is_onload_hospitals = true;

            initializeSelect2('#health_zone');
            initializeSelect2('#province');
            initializeSelect2('#hospital');

            $("#date_start").val(localStorage.getItem('date_start'));
            $("#date_end").val(localStorage.getItem('date_end'));
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

                safeSetLocalStorage('date_start', '#date_start');
                safeSetLocalStorage('date_end', '#date_end');
                safeSetLocalStorage('health_zone', '#health_zone');
                safeSetLocalStorage('province', '#province');
                safeSetLocalStorage('hospital', '#hospital');

            });

            $("#clear_filter").on("click", function() {
                localStorage.clear();
                location.href = "{{ route('dashboard.hospital_21_variables') }}";
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
                    await restoreHospitalSelection(health_zone, province);
                }
                is_onload_hospitals = false; // ปลดล็อกการเลือกจังหวัดหลังโหลดค่าเดิมเสร็จ
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
                                $('#hospital').trigger('change.select2');

                                if (is_onload_hospitals == true && localStorage.getItem('hospital')) {
                                    let storedProvinces = localStorage.getItem('hospital').split(',');
                                    $('#hospital').val(storedProvinces).trigger('change');
                                }
                                is_onload_hospitals = false;
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

            function restoreHospitalSelection(health_zone, province) {
                const selected = (localStorage.getItem('hospital') || '').split(',').filter(Boolean);
                const selectedHospitals = selected.filter((value) => value !== 'ทั้งหมด');

                if (selected.includes('ทั้งหมด')) {
                    $('#hospital').append(new Option('ทั้งหมด', 'ทั้งหมด', true, true));
                }
                if (!selectedHospitals.length) {
                    $('#hospital').trigger('change');
                    return Promise.resolve();
                }

                return $.get("{{ route('dashboard.get_hospital_asm1_from_province') }}", {
                    health_zone: health_zone,
                    province: province.split(','),
                    selected: selectedHospitals
                }).done(function(response) {
                    $.each(response.results || [], function(_, hospital) {
                        $('#hospital').append(new Option(hospital.text, hospital.id, true, true));
                    });
                    $('#hospital').trigger('change');
                });
            }

            // เรียกใช้ฟังก์ชันโหลดจังหวัดเมื่อเลือกเขตสุขภาพ
            $('#health_zone').on("change", function() {
                if (!$(this).val()) { // ตรวจสอบว่า select2 ว่างหรือยัง
                    $("#province option[value]").remove(); // Clear ค่าที่เลือกไว้
                    $("#hospital option[value]").remove(); // Clear ค่าที่เลือกไว้
                }
                $('#hospital').html('<option value="ทั้งหมด">ทั้งหมด</option>');
                load_provinces($(this).val(), 'change');
            });

            // เรียกใช้ฟังก์ชันโหลดโรงพยาบาลเมื่อเลือกจังหวัด
            $('#province').on("change", function() {
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
                $('#hospital').val(null).trigger('change');
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

                const $select = $(selector);
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }

                const options = {
                    theme: 'bootstrap-5',
                    width: '100%',
                    allowClear: true,
                    placeholder: "=== กรุณาเลือก ===",
                    closeOnSelect: closeOnSelectValue, // ตั้งค่า closeOnSelect ตามเงื่อนไข
                };
                if (selector === '#hospital') {
                    options.ajax = {
                        url: "{{ route('dashboard.get_hospital_asm1_from_province') }}",
                        dataType: 'json',
                        delay: 300,
                        data: function(params) {
                            return {
                                health_zone: $('#health_zone').val(),
                                province: $('#province').val(),
                                term: params.term || '',
                                page: params.page || 1
                            };
                        },
                        processResults: function(data) {
                            return data;
                        }
                    };
                }
                $select.select2(options);

                $(document).off('select2:open.dashboardSelect2').on('select2:open.dashboardSelect2', () => {
                    document.querySelector('.select2-search__field').focus();
                });
            }
        });
    </script>

    <script>
        $('#date_start, #date_end').on('change', function() {
            let start = $('#date_start').val();
            let end = $('#date_end').val();

            if (start && end) {
                let startDate = new Date(start);
                let endDate = new Date(end);

                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });

                if (startDate > endDate) {
                    Toast.fire({
                        icon: "warning",
                        title: "วันที่เริ่มต้นต้องไม่มากกว่าวันที่สิ้นสุด"
                    });
                    $('#date_start').val(end); // ปรับให้ตรง

                } else if (endDate < startDate) {
                    Toast.fire({
                        icon: "warning",
                        title: "วันที่สิ้นสุดต้องไม่ก่อนวันที่เริ่มต้น"
                    });
                    $('#date_end').val(start); // ปรับให้ตรง
                } else {
                    // ตรวจสอบไม่ให้เกิน 90 วัน
                    const diffTime = Math.abs(endDate - startDate);
                    const diffDays = diffTime / (1000 * 60 * 60 * 24);
                    if (diffDays > 90) {
                        Toast.fire({
                            icon: "warning",
                            title: "ช่วงวันที่ต้องไม่เกิน 90 วัน"
                        });
                        $('#date_end').val('');
                    }
                }
            }
        });
    </script>
@endsection
