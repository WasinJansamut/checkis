@php
    if (!function_exists('getColorByPercentage')) {
        function getColorByPercentage($percent)
        {
            $percent = (int) $percent;
            if ($percent <= 10) {
                return '#ff4d4d';
            }
            if ($percent <= 20) {
                return '#ff6666';
            }
            if ($percent <= 30) {
                return '#ff8533';
            }
            if ($percent <= 40) {
                return '#ffaa00';
            }
            if ($percent <= 50) {
                return '#ffcc00';
            }
            if ($percent <= 60) {
                return '#e6e600';
            }
            if ($percent <= 70) {
                return '#b3d900';
            }
            if ($percent <= 80) {
                return '#66cc00';
            }
            if ($percent <= 90) {
                return '#33b300';
            }
            return '#009900';
        }
    }
@endphp
@extends('layouts.app')

@section('content')

    <style>
        .table-report table {
            min-width: 1320px;
            font-weight: 400;
            font-size: 13px;
        }

        .table-report th {
            font-weight: bold;
            vertical-align: middle;
            line-height: 1.45;
        }
    </style>
    <div class="container-fluid px-4">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @else
            <h1>ผลการตรวจสอบ</h1>
            <p class="text-muted mb-4">ค้นหา ติดตาม และดาวน์โหลดผลการตรวจสอบย้อนหลัง</p>
            <form action="{{ route('search_report') }}" method="GET" class="mb-4">
                @if (in_array(user_info('user_level_code'), ['MOPH', 'PROV']))
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-8">
                            <label for="hosp_search" class="form-label fw-semibold">โรงพยาบาล</label>
                            <select id="hosp_search" class="custom-select form-control select2-data-hosp-select" name="hosp_search" data-selected-hospcode="{{ user_info('hosp_code') }}">
                                <option value=""></option>
                                {{-- <option value="all_hosp">โรงพยาบาลทั้งหมด</option> --}}
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="retrospective-area-code" class="form-label fw-semibold">เขตสุขภาพ</label>
                            <select id="retrospective-area-code" class="custom-select form-control select2" name="area_code">
                                <option selected="selected" value="">=== กรุณาเลือกเขต ===</option>
                                @foreach ($area_codes as $area_code)
                                    <option @if ($area_code == $code) selected @endif value="{{ $area_code }}">
                                        {{ $area_code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif

                <div class="row g-3">
                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="start_date" class="form-label fw-semibold">วันที่เริ่มต้น</label>
                        <input class="form-control datepicker" data-provide="datepicker" id="start_date" data-date-language="th-th" name="start_date" value="{{ $start }}">
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="end_date" class="form-label fw-semibold">วันที่สิ้นสุด</label>
                        <input data-provide="datepicker" id="end_date" data-date-language="th-th" class="form-control datepicker" name="end_date" value="{{ $end }}">
                    </div>
                    <div class="col-12 col-lg-4 d-flex align-items-end">
                        <button class="btn btn-success w-100" type="submit">
                            <i class="fa-solid fa-magnifying-glass me-1"></i>
                            ค้นหาข้อมูลที่ตรวจไปแล้ว
                        </button>
                    </div>
                </div>
            </form>

            <div class="d-flex justify-content-end border-top pt-3 mb-3">
                <form action="{{ route('retrospective_get_report') }}" method="GET">
                    <input name="page" hidden value="{{ request()->page }}">
                    <button type="submit" class="btn btn-info" title="ส่งไฟล์รายงานผ่าน E-Mail">
                        <i class="fa-solid fa-paper-plane me-1"></i>
                        ส่งไฟล์หน้านี้ด้วย E-Mail
                    </button>
                </form>
            </div>

            @if (Session::has('success email'))
                <div class="alert alert-success" role="alert">
                    <span><strong>ส่งอีเมลเสร็จสิ้น กรุณาเช็คกล่องข้อความที่ {{ Session::get('success_email_to') ?? user_info('email') }}</strong> </span>
                </div>
            @endif

            @if (Session::has('error'))
                <div class="alert alert-danger" role="alert">
                    <span><strong>เกิดข้อผิดพลาด ไม่สามารถอ่านไฟล์ได้</strong> </span>
                </div>
            @endif

            @if (Session::has('resend_error'))
                <div class="alert alert-danger" role="alert">
                    <span><strong>{{ Session::get('resend_error') }}</strong> </span>
                </div>
            @endif

            @if (Session::has('no email'))
                <div class="alert alert-warning" role="alert">
                    <span>ผู้ใช้ยังไม่ได้ลงทะเบียน E-Mail <strong>กรุณาลงทะเบียน E-Mail ก่อนทำการส่งไฟล์ </strong> </span>
                </div>
            @endif

            @if (Session::has('no data'))
                <div class="alert alert-warning" role="alert">
                    <span>ไม่พบข้อมูล <strong>รายงาน</strong> ที่ค้นหา</span>
                </div>
            @endif

            @if (Session::has('wrong hosp'))
                <div class="alert alert-warning" role="alert" style="width: 50%">
                    <span>โรงพยาบาล และ เขต ไม่ตรงกัน</span>
                </div>
            @endif

            <div class="table-report w-100">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" style="text-align: center;">
                        <thead>
                            <tr>
                                <th rowspan="2" style="width: 100px">วันที่เริ่มต้น</th>
                                <th rowspan="2" style="width: 100px">วันที่สิ้นสุด</th>
                                <th rowspan="2" style="width: 80px">จำนวน</th>
                                <th rowspan="2">วันที่ประมวลผล</th>
                                <th rowspan="2" style="width: 135px">สถานะงาน</th>
                                <th rowspan="2" style="width: 120px">รายงาน</th>
                                <th colspan="2" style="width: 230px">
                                    ร้อยละความถูกต้องของแต่ละด้าน
                                    <i class="fa-solid fa-circle-info" data-bs-toggle="modal" data-bs-target="#colorLegendModal" title="ดูคำอธิบายสี"></i>
                                </th>
                                {{-- @if (user_info('user_level_code') == 'MOPH' && user_info('user_type') == 'SUPER ADMIN') --}}
                                <th rowspan="2" style="width: 105px">สถานะการส่ง E-Mail</th>
                                <th rowspan="2">ประมวลผลโดย</th>
                                <th rowspan="2" style="width: 200px">ชื่อโรงพยาบาล</th>
                                {{-- @endif --}}
                            </tr>
                            <tr>
                                <th scope="col" style="width: 135px">
                                    ความสมบูรณ์ (Completeness)
                                </th>
                                <th scope="col" style="width: 135px">
                                    ความสอดคล้อง (Consistency)
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($jobs->isNotEmpty())
                                @foreach ($jobs as $job)
                                    <tr>
                                        <td>{{ $job->start_date->addYear(543)->format('d-m-Y') }}</td>
                                        <td>{{ $job->end_date->addYear(543)->format('d-m-Y') }}</td>
                                        <td>{{ number_format($job->count ?? 0) }}</td>
                                        <td>
                                            {{ $job->start_time ? $job->start_time->addYear(543)->format('d-m-Y H:i:s') : '-' }}
                                        </td>
                                        <td class="fw-bold {{ $job->status == 'checked' && $job->email_status != null ? 'js-resend-email' : '' }}"
                                            style="background-color: {{ $job->status == 'checked' ? '#d4edda' : '#e2e3e5' }}; {{ $job->status == 'checked' && $job->email_status != null ? 'cursor: pointer;' : '' }}"
                                            @if ($job->status == 'checked' && $job->email_status != null) data-form-id="resend-email-form-{{ $job->id }}" title="คลิกเพื่อส่งอีเมลอีกครั้ง" @endif>
                                            @if ($job->status == 'checked')
                                                ตรวจสอบเสร็จสิ้น
                                                @if ($job->email_status != null)
                                                    <br>
                                                    <img src="{{ asset('assets/mail.png') }}" width="25">
                                                    <form id="resend-email-form-{{ $job->id }}" action="{{ route('retrospective_resend_email', $job->id) }}" method="POST" class="d-none">
                                                        @csrf
                                                    </form>
                                                @endif
                                            @else
                                                รอการตรวจสอบ
                                            @endif
                                        </td>
                                        <td>
                                            @if ($job->status == 'checked')
                                                <a href="{{ route('download_report', $job->id) }}" target="_blank">
                                                    <button type="button" class="btn btn-sm btn-outline-success">
                                                        <small>
                                                            <i class="fa-solid fa-download me-1"></i>
                                                            ดาวน์โหลด
                                                        </small>
                                                    </button>
                                                </a>
                                            @else
                                                <a href="{{ route('selected_check', $job->id) }}">
                                                    <button type="button" class="btn btn-sm btn-outline-warning">
                                                        <small>
                                                            <i class="fa-solid fa-magnifying-glass me-1"></i>
                                                            ตรวจงาน
                                                        </small>
                                                    </button>
                                                </a>
                                            @endif
                                        </td>
                                        <td class="fs-6 fw-bold" style="background-color: {{ getColorByPercentage($job->type_1P) }}; color: white;">
                                            {{ $job->type_1P }}%
                                        </td>
                                        <td class="fs-6 fw-bold" style="background-color: {{ getColorByPercentage($job->type_2P) }}; color: white;">
                                            {{ $job->type_2P }}%
                                        </td>
                                        {{-- @if (user_info('user_level_code') == 'MOPH' && user_info('user_type') == 'SUPER ADMIN') --}}
                                        <td>{{ $job->email_status ?? '-' }}</td>
                                        <td>{{ optional($job->_user_session)->name ?? optional($job->user)->name }}</td>
                                        <td>{{ $job->getHospName->full_name ?? '' }}</td>
                                        {{-- @endif --}}
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="11" class="text-center">ไม่พบข้อมูล</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if ($jobs->links()->paginator->hasPages())
            <div class="mt-4 p-4 box has-text-centered text-center">
                {{ $jobs->appends(request()->query())->links() }}
            </div>
        @endif
        <!-- Modal for color legend -->
        <div class="modal fade" id="colorLegendModal" tabindex="-1" aria-labelledby="colorLegendModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-success bg-opacity-10 border-0 px-4 pt-4 pb-3">
                        <h5 class="modal-title d-flex align-items-center gap-2 fw-bold" id="colorLegendModalLabel">
                            <span class="d-inline-flex align-items-center justify-content-center bg-white text-success rounded-circle p-2 shadow-sm">
                                <i class="fa-solid fa-palette"></i>
                            </span>
                            คำอธิบายสีร้อยละ
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                    </div>
                    <div class="modal-body px-4 pt-3 pb-2">
                        <p class="small text-muted mb-3">สีแสดงระดับความถูกต้องของข้อมูล</p>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="d-flex align-items-center gap-2 border rounded-3 px-2 py-2 h-100">
                                    <span class="rounded-2 flex-shrink-0" style="width:28px;height:28px;background-color:{{ getColorByPercentage(0) }}"></span>
                                    <span class="small fw-semibold">0%</span>
                                </div>
                            </div>
                            @for ($i = 0; $i < 10; $i++)
                                @php
                                    $rangeStart = $i * 10 + 1;
                                    $rangeEnd = ($i + 1) * 10;
                                    $color = getColorByPercentage($rangeEnd);
                                @endphp
                                <div class="col-6">
                                    <div class="d-flex align-items-center gap-2 border rounded-3 px-2 py-2 h-100">
                                        <span class="rounded-2 flex-shrink-0" style="width:28px;height:28px;background-color:{{ $color }}"></span>
                                        <span class="small fw-semibold">{{ $rangeStart }}–{{ $rangeEnd }}%</span>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pt-2 pb-4">
                        <button type="button" class="btn btn-outline-secondary w-100" data-bs-dismiss="modal">ปิดหน้าต่าง</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        const $hospitalSelect = $('#hosp_search');
        const selectedHospcode = $hospitalSelect.data('selected-hospcode');

        function initHospitalSelect() {
            $hospitalSelect.select2({
            theme: 'bootstrap-5',
            width: '100%',
            allowClear: true,
            placeholder: '=== กรุณาเลือกโรงพยาบาล ===',
            ajax: {
                url: "{{ route('retrospective-hospitals') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return data;
                },
                cache: false
            }
            });
        }

        if (selectedHospcode) {
            $.get("{{ route('retrospective-hospitals') }}", { selected: selectedHospcode })
                .done(function(data) {
                    const hospital = data.results && data.results[0];
                    if (hospital) {
                        $hospitalSelect.append(new Option(hospital.text, hospital.id, true, true)).val(hospital.id);
                    }
                    initHospitalSelect();
                })
                .fail(initHospitalSelect);
        } else {
            initHospitalSelect();
        }

        document.querySelectorAll('.js-resend-email').forEach(function(element) {
            element.addEventListener('click', function() {
                const formId = this.dataset.formId;

                AppSwal.confirmAction({
                    title: 'ส่งอีเมลอีกครั้ง?',
                    text: 'ระบบจะส่งอีเมลแจ้งผลการตรวจสอบซ้ำอีกครั้ง',
                    confirmButtonText: 'ส่งอีเมล'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(formId).submit();
                    }
                });
            });
        });
    </script>
@endsection
