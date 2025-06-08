@php
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
@endphp
@extends('layouts.app')

@section('content')

    <style>
        .table-report table {
            table-layout: fixed;
            font-weight: 400;
            font-size: 12px;
        }

        .table-report th {
            background-color: #198754;
            /* light gray */
            color: white;
            width: 115px;
            font-weight: bold;
            vertical-align: middle;
        }
    </style>
    <div class="container-fluid px-4">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @else
            <h1>ผลการตรวจสอบ</h1>
            <form action="{{ route('search_report') }}" method="GET">
                @if (Auth::user()->type == 1 || Auth::user()->type == 2 || Auth::user()->type == 3)
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                            <select class="custom-select form-control select2" tabindex="-1" aria-hidden="true" name="hosp_search">
                                <option selected="selected" value="">=== กรุณาเลือกโรงพยาบาล ===</option>
                                {{-- <option value="all_hosp">โรงพยาบาลทั้งหมด</option> --}}
                                @foreach ($hosps as $hosp)
                                    <option @if ($hosp->hospcode == $hospCode) selected @endif value={{ $hosp->hospcode }}>
                                        {{ $hosp->full_name }}({{ $hosp->hospcode }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                            <select class="custom-select form-control select2" name="area_code">
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

                <div class="row">
                    <div class="col-sm-12 col-md-8 mb-3">
                        <div class="input-group input-daterange date align-items-center">
                            <input class="form-control datepicker" data-provide="datepicker" id="start_date" data-date-language="th-th" class="form-control " name="start_date"
                                value="{{ $start }}">
                            <span class="input-group-text" id="inputGroup-sizing-sm">ถึง</span>
                            <input data-provide="datepicker" id="end_date"
                                data-date-language="th-th" class="form-control datepicker" name="end_date"
                                value="{{ $end }}">
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-4 mb-3">
                        <button class="btn btn-success" type="submit">
                            <i class="fa-solid fa-magnifying-glass me-1"></i>
                            ค้นหาข้อมูลที่ตรวจไปแล้ว
                        </button>
                    </div>
                </div>
            </form>

            <form action="{{ route('retrospective_get_report') }}" method="GET" class="mb-3">
                <input name="page" hidden value="{{ request()->page }}">
                <div class="text-end">
                    <button type="submit" class="btn btn-info" title="ส่งไฟล์รายงานผ่าน E-Mail">
                        <i class="fa-solid fa-paper-plane me-1"></i>
                        ส่งไฟล์หน้านี้ด้วย E-Mail
                    </button>
                </div>
            </form>

            @if (Session::has('success email'))
                <div class="alert alert-success" role="alert">
                    <span><strong>ส่งอีเมลเสร็จสิ้น กรุณาเช็คกล่องข้อความ ผ่านอีเมลที่ลงทะเบียน</strong> </span>
                </div>
            @endif

            @if (Session::has('error'))
                <div class="alert alert-danger" role="alert">
                    <span><strong>เกิดข้อผิดพลาด ไม่สามารถอ่านไฟล์ได้</strong> </span>
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
                                <th rowspan="2">สถานะงาน</th>
                                <th rowspan="2" style="width: 120px">รายงาน</th>
                                <th colspan="2" style="width: 230px">
                                    ร้อยละความถูกต้องของแต่ละด้าน
                                    <i class="fa-solid fa-circle-info" data-bs-toggle="modal" data-bs-target="#colorLegendModal" title="ดูคำอธิบายสี"></i>

                                </th>
                                @if (Auth::user()->type == 1)
                                    <th rowspan="2">สถานะการส่ง E-Mail</th>
                                @endif
                                @if (Auth::user()->type == 1)
                                    <th rowspan="2">ประมวลผลโดย</th>
                                @endif
                                @if (Auth::user()->type >= 1)
                                    <th rowspan="2" style="width: 200px">ชื่อโรงพยาบาล</th>
                                @endif
                            </tr>
                            <tr>
                                <th scope="col">
                                    ความสมบูรณ์ (Completeness)
                                </th>
                                <th scope="col">
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
                                        <td class="fw-bold" style="background-color: {{ $job->status == 'checked' ? '#d4edda' : '#e2e3e5' }};">
                                            @if ($job->status == 'checked')
                                                ตรวจสอบเสร็จสิ้น
                                                @if ($job->email_status != null)
                                                    <img src="{{ asset('assets/mail.png') }}" width="25">
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
                                        <td style="background-color: {{ getColorByPercentage($job->type_1P) }}; color: white;">
                                            {{ $job->type_1P }}%
                                        </td>
                                        <td style="background-color: {{ getColorByPercentage($job->type_2P) }}; color: white;">
                                            {{ $job->type_2P }}%
                                        </td>
                                        @if (Auth::user()->type == 1)
                                            <td>{{ $job->email_status ?? '-' }}</td>
                                        @endif
                                        @if (Auth::user()->type == 1)
                                            <td>{{ $job->user ? $job->user->name : '' }}</td>
                                        @endif
                                        @if (Auth::user()->type >= 1)
                                            <td>{{ $job->getHospName->full_name ?? '' }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <div>
                                    <h2>ไม่พบรายงาน</h2>
                                </div>
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
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="colorLegendModalLabel">คำอธิบายสีร้อยละ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                    </div>
                    <div class="modal-body">
                        @for ($i = 0; $i < 10; $i++)
                            @php
                                $rangeStart = $i * 10 + 1;
                                $rangeEnd = ($i + 1) * 10;
                                $color = getColorByPercentage($rangeEnd);
                            @endphp
                            <div class="d-flex align-items-center mb-2">
                                <div style="width: 30px; height: 20px; background-color: {{ $color }}; margin-right: 10px;"></div>
                                <div>{{ $rangeStart }}% - {{ $rangeEnd }}%</div>
                            </div>
                        @endfor
                        <div class="d-flex align-items-center mb-2">
                            <div style="width: 30px; height: 20px; background-color: {{ getColorByPercentage(0) }}; margin-right: 10px;"></div>
                            <div>0%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    {{-- <script>
        $(document).ready(function() {
            $('#start_date').datepicker({
                language: 'th-th',
                format: 'dd/mm/yyyy'
            });
            $('#end_date').datepicker({
                language: 'th-th',
                format: 'dd/mm/yyyy'
            });
        })
    </script> --}}
@endsection
