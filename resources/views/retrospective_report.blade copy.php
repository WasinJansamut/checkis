@extends('layouts.app')

@section('content')

    <style>
        .table-report table{
            table-layout: fixed;
            font-weight: 400;
            font-size: 12px;
        }
        .table-report th{
            color: #748080;
            width: 115px;
            font-weight: 400;
            vertical-align : middle
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

            @if(Auth::user()->type == 1)

                    <div class="row d-flex align-items-center">
                        <div class="col-12 col-sm-6 col-md-4">

                            <select class="custom-select form-control select2" style="width: 300px;" tabindex="-1" aria-hidden="true" name="hosp_search" type="text">
                                <option selected="selected" value="">โรงพยาบาล</option>
                                {{--                                <option value="all_hosp">โรงพยาบาลทั้งหมด</option>--}}
                                @foreach($hosps as $hosp)
                                    <option @if($hosp->hospcode == $hospCode) selected @endif value={{$hosp->hospcode}}>{{$hosp->full_name}}({{$hosp->hospcode}})</option>
                                @endforeach
                            </select>

                        </div>
                        <div class="col-12 col-sm-6 col-md-4">

                            <select class="custom-select form-control" style="width: 200px;" name="area_code">
                                <option  selected="selected" value="">เขต</option>
                                @foreach($area_codes as $area_code)
                                    <option @if($area_code == $code) selected @endif value="{{$area_code}}">
                                        {{$area_code}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

            @endif

                <div class="row d-flex align-items-center mt-2">
                    <div class="col-4">
                        <div class="input-group input-daterange date align-items-center">
                            <input style="min-width: 150px" class="form-control m-0" data-provide="datepicker" id="start_date" data-date-language="th-th"  class="form-control " name="start_date" value="{{$start}}" >
                            <span class="input-group-text" id="inputGroup-sizing-sm">ถึง</span>
                            <input style="min-width: 150px" data-provide="datepicker" id="end_date" data-date-language="th-th"  class="form-control " name="end_date"  value="{{$end}}" >
                        </div>
                    </div>

                    <div class="col-auto">
                            <button class="btn btn-secondary" style="margin-left: 20px" type="submit"><i class="mdi mdi-magnify icon"></i>ค้นหา</button>
                    </div>

                </div>

            </form>
            <form action="{{ route('retrospective_get_report') }}" method="GET">
                <input name="page" hidden value="{{request()->page}}">
                <button type="submit"  class="btn btn-outline-success mt-3">ส่งไฟล์หน้านี้ด้วย Email</button>
            </form>

            @if(Session::has('success email'))
                <div class="alert alert-success m-2" role="alert" style="width: 30%; float: left">
                    <span><strong>ส่งอีเมลเสร็จสิ้น กรุณาเช็คกล่องข้อความ ผ่านอีเมลที่ลงทะเบียน</strong> </span>
                </div>
                <br>
            @endif

            @if(Session::has('error'))
                <div class="alert alert-danger m-2" role="alert" style="width: 30%; float: left">
                    <span><strong>เกิดข้อผิดพลาด ไม่สามารถอ่านไฟล์ได้</strong> </span>
                </div>
                <br>
            @endif

            @if(Session::has('no email'))
                <div class="alert alert-warning m-2" role="alert" style="width: 30%; float: left">
                    <span>ผู้ใช้ยังไม่ได้ลงทะเบียน Email <strong>กรุณาลงทะเบียน Email ก่อนทำการส่งไฟล์ </strong> </span>
                </div>
                <br>
            @endif

            @if(Session::has('no data'))
                <div class="alert alert-warning m-2" role="alert" style="width: 30%; float: left">
                    <span>ไม่พบข้อมูล <strong>รายงาน</strong> ที่ค้นหา</span>
                </div>
                <br>
            @endif

            @if(Session::has('wrong hosp'))
                <div class="alert alert-warning m-2" role="alert" style="width: 50%">
                    <span>โรงพยาบาล และ เขต ไม่ตรงกัน</span>
                </div>
            @endif

        <br>
            <div class="table-report" style="width: 100%;overflow:scroll ">

                <table class="table table-bordered table-hover " style="text-align: center;">
                <thead>
                <tr>
                    <th rowspan="2" style="width: 100px" >start_date</th>
                    <th rowspan="2" style="width: 100px" >end_date</th>
                    <th rowspan="2" style="width: 80px">จำนวน</th>
                    <th colspan="6" style="width: 690px">ร้อยละความถูกต้องของแต่ละด้าน</th>
                    <th rowspan="2">สถานะงาน</th>
                    <th rowspan="2">สถานะการส่ง email</th>
                    <th rowspan="2">วันทีประมวลผล</th>
                    <th rowspan="2">ประมวลผลโดย</th>
                    <th rowspan="2" style="width: 120px">รายงาน</th>
                    @if(Auth::user()->type == 1)
                    <th rowspan="2" style="width: 200px">ชื่อโรงพยาบาล</th>
                        @endif
                </tr>
                <tr>
                    <th scope="col">ความถูกต้อง
                        (Accuracy)</th>
                    <th scope="col">ความสมบูรณ์
                        (Completeness)</th>
                    <th scope="col">ความเที่ยงตรง
                        (Consistency)</th>
                    <th scope="col">ความตรงตามกาล
                        (Timeliness)</th>
                    <th scope="col">ความเป็นเอกลักษณ์
                        (Uniqueness)</th>
                    <th scope="col">ความแม่นยำ
                        (Orderliness)</th>
                </tr>
                </thead>
                <tbody>

                @if($jobs->isNotEmpty())

                    @foreach($jobs as $job)
                        <tr>
                            <td>{{$job->start_date->addYear(543)->format('d-m-Y')}}</td>
                            <td>{{$job->end_date->addYear(543)->format('d-m-Y')}}</td>
                            <td>{{$job->count}}</td>
                            <td>{{$job->type_1P}}%</td>
                            <td>{{$job->type_2P}}%</td>
                            <td>{{$job->type_3P}}%</td>
                            <td>{{$job->type_4P}}%</td>
                            <td>{{$job->type_5P}}%</td>
                            <td>{{$job->type_6P}}%</td>

                            <td>
                                @if($job->status == "checked")
                                    ตรวจสอบเสร็จสิ้น
                                @else รอการตรวจสอบ
                                @endif
                            </td>
                            <td>
                                @if($job->email_status != null)
                                    {{$job->email_status}}
                                @else -
                                @endif
                            </td>
                            <td>
                                @if($job->start_time !== null)
                                    {{$job->start_time->addYear(543)->format('d-m-Y H:i:s')}}
{{--                                {{date('d/m/Y', strtotime($job->start_time))}}--}}
                                @else -
                                @endif
                            </td>
                            <td>{{$job->user? $job->user->name : ''}}</td>
                            <td>
                                @if($job->status == "checked")
                                    <a href="{{url("/download/report/{$job->id}")}}" target="_blank">
                                        <button type="button" class="btn btn-outline-success" style="font-size: 10px " ><i class="mdi mdi-arrow-down-bold-circle icon"></i>ดาวน์โหลด</button>
                                    </a>
                                @else
                                    <a href="{{url("/check/job/{$job->id}")}}">
                                        <button type="button" class="btn btn-outline-warning" style="font-size: 10px " >ตรวจงาน</button>
                                    </a>
                                @endif
                            </td>
                            @if(Auth::user()->type == 1)
                            <td>{{$job->getHospName->full_name}}</td>
                            @endif

                        </tr>

                    @endforeach
                    @else
                        <div>
                            <h2>No Report Found</h2>
                        </div>
                    @endif

                </tbody>
            </table>

            </div>
        @endif

            @if ($jobs->links()->paginator->hasPages())
                <div class="mt-4 p-4 box has-text-centered">
                    {{ $jobs->appends(request()->query())->links() }}
                </div>
            @endif



    </div>
<script>
    $(document).ready(function () {
        $('#start_date').datepicker({language:'th-th',format:'dd/mm/yyyy'});
        $('#end_date').datepicker({language:'th-th',format:'dd/mm/yyyy'});
    })

</script>
@endsection

