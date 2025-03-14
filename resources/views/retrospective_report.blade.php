@extends('layouts.app')

@section('content')

    <style>
        .table-report table {
            table-layout: fixed;
            font-weight: 400;
            font-size: 12px;
        }

        .table-report th {
            color: #748080;
            width: 115px;
            font-weight: bold;
            vertical-align: middle
        }
    </style>
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @else
            <h1>ผลการตรวจสอบ</h1>
            <form action="{{ route('search_report') }}" method="GET">
                @if (Auth::user()->type == 1 || Auth::user()->type == 2 || Auth::user()->type == 3)
                    <div class="row d-flex align-items-center">
                        <div class="col-12 col-sm-6 col-md-4">
                            <select class="custom-select form-control select2" tabindex="-1" aria-hidden="true"
                                name="hosp_search" type="text">
                                <option selected="selected" value="">=== กรุณาเลือกโรงพยาบาล ===</option>
                                {{-- <option value="all_hosp">โรงพยาบาลทั้งหมด</option> --}}
                                @foreach ($hosps as $hosp)
                                    <option @if ($hosp->hospcode == $hospCode) selected @endif value={{ $hosp->hospcode }}>
                                        {{ $hosp->full_name }}({{ $hosp->hospcode }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
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

                <div class="row d-flex align-items-center mt-2">
                    <div class="col-4">
                        <div class="input-group input-daterange date align-items-center">
                            <input style="min-width: 150px" class="form-control datepicker" data-provide="datepicker"
                                id="start_date" data-date-language="th-th" class="form-control " name="start_date"
                                value="{{ $start }}">
                            <span class="input-group-text" id="inputGroup-sizing-sm">ถึง</span>
                            <input style="min-width: 150px" data-provide="datepicker" id="end_date"
                                data-date-language="th-th" class="form-control datepicker" name="end_date"
                                value="{{ $end }}">
                        </div>
                    </div>

                    <div class="col-auto">
                        <button class="btn btn-secondary" type="submit">
                            <i class="fa-solid fa-magnifying-glass me-1"></i>
                            ค้นหา
                        </button>
                    </div>
                </div>
            </form>
            <form action="{{ route('retrospective_get_report') }}" method="GET" class="mb-3">
                <input name="page" hidden value="{{ request()->page }}">
                <button type="submit" class="btn btn-outline-success mt-3">
                    <i class="fa-solid fa-paper-plane me-1"></i>
                    ส่งไฟล์หน้านี้ด้วย E-Mail
                </button>
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
                                <th colspan="6" style="width: 690px">ร้อยละความถูกต้องของแต่ละด้าน</th>
                                <th rowspan="2">สถานะงาน</th>
                                @if (Auth::user()->type == 1)
                                    <th rowspan="2">สถานะการส่ง E-Mail</th>
                                @endif
                                <th rowspan="2">วันที่ประมวลผล</th>
                                @if (Auth::user()->type == 1)
                                    <th rowspan="2">ประมวลผลโดย</th>
                                @endif
                                <th rowspan="2" style="width: 120px">รายงาน</th>
                                @if (Auth::user()->type >= 1)
                                    <th rowspan="2" style="width: 200px">ชื่อโรงพยาบาล</th>
                                @endif
                            </tr>
                            <tr>
                                <th scope="col">
                                    ความถูกต้อง (Accuracy)
                                </th>
                                <th scope="col">
                                    ความสมบูรณ์ (Completeness)
                                </th>
                                <th scope="col">
                                    ความเที่ยงตรง (Consistency)
                                </th>
                                <th scope="col">
                                    ความตรงตามกาล (Timeliness)
                                </th>
                                <th scope="col">
                                    ความเป็นเอกลักษณ์ (Uniqueness)
                                </th>
                                <th scope="col">
                                    ความแม่นยำ (Orderliness)
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
                                        <td>{{ $job->type_1P }}%</td>
                                        <td>{{ $job->type_2P }}%</td>
                                        <td>{{ $job->type_3P }}%</td>
                                        <td>{{ $job->type_4P }}%</td>
                                        <td>{{ $job->type_5P }}%</td>
                                        <td>{{ $job->type_6P }}%</td>
                                        <td>
                                            @if ($job->status == 'checked')
                                                ตรวจสอบเสร็จสิ้น
                                                @if ($job->email_status != null)
                                                    <img src="{{ asset('assets/mail.png') }}" width="25">
                                                @endif
                                            @else
                                                รอการตรวจสอบ
                                            @endif
                                        </td>
                                        @if (Auth::user()->type == 1)
                                            <td>{{ $job->email_status ?? '-' }}</td>
                                        @endif
                                        <td>
                                            {{ $job->start_time ? $job->start_time->addYear(543)->format('d-m-Y H:i:s') : '-' }}
                                        </td>
                                        @if (Auth::user()->type == 1)
                                            <td>{{ $job->user ? $job->user->name : '' }}</td>
                                        @endif
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
