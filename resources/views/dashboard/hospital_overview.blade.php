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
    <div class="container mb-3">
        <h1>Dashboard สรุปข้อมูลโรงยาบาล</h1>
        <div class="col-12">
            <form id="form" action="{{ route('dashboard.hospital_overview') }}" method="post">
                @method('POST')
                @csrf
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
                                <i class="fa-solid fa-magnifying-glass me-1"></i>
                                ค้นหา
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        @if (request()->isMethod('post'))
            <table class="table table-bordered table-hover border-dark datatables mb-1">
                <thead>
                    <tr class="table-secondary">
                        <th rowspan="2">เขตสุขภาพ</th>
                        <th rowspan="2">จังหวัด</th>
                        <th rowspan="2">โรงพยาบาล</th>
                        <th rowspan="2">ระดับ รพ.</th>
                        <th colspan="3">จำนวน</th>
                        <th rowspan="2">ร้อยละที่ครบ</th>
                        <th rowspan="2">ร้อยละที่ไม่ครบ</th>
                    </tr>
                    <!-- ใช้สี -->
                    <!-- สีเขียว: รพ. ที่ร้อยละที่ครบ > 90% -->
                    <!-- สีส้ม: รพ. ที่ร้อยละระหว่าง 70-90% -->
                    <!-- สีแดง: รพ. ที่ร้อยละต่ำกว่า 70% -->
                    <tr class="table-secondary">
                        <th>ทั้งหมด</th>
                        <th>ที่ครบ 21 ตัวแปร</th>
                        <th>ที่ไม่ครบ 21 ตัวแปร</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($query as $row)
                        <tr>
                            <td class="text-center">{{ (int) $row->_hosp->region ?? '' }}</td>
                            <td>{{ $row->_hosp->changwat ?? '' }}</td>
                            <td>{{ $row->_hosp->name ?? '' }}</td>
                            <td class="text-center">{{ $row->_hosp->splevel ?? '' }}</td>
                            <td class="text-end">ข้อมูลทั้งหมด</td>
                            <td class="text-end">ข้อมูลครบ 21</td>
                            <td class="text-end">ข้อมูลไม่ 21</td>
                            <td class="table-success text-end">ข้อมูลร้อยละที่ครบ</td>
                            <td class="text-end">ข้อมูลร้อยละไม่ที่ครบ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center ">
                <div class="alert alert-warning py-4" role="alert">
                    คลิกปุ่ม "<b><i class="fa-solid fa-magnifying-glass small"></i> ค้นหา</b>" เพื่อเรียกดูข้อมูล
                </div>

            </div>
        @endif
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            var is_onload_hospitals = true;

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
                            url: "{{ route('dashboard.get_hospital_from_province') }}",
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
@endsection
