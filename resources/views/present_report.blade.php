@extends('layouts.app')

@section('content')

<div class="container">

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>

                    @endif




                    <h1 class="roboto" style="font-weight: 500;margin-top:20px;">หน้าหลัก </h1>
<hr>
                        @if(Auth::user()->type == 1 || Auth::user()->type == 2 || Auth::user()->type == 3)

                        <a href="https://dip.ddc.moph.go.th/is-checking/check_error" target="_blank">
                            <button class="btn btn-white btn-info btn-bold">
                         <i class="ace-icon fa fa-area-chart bigger-120 blue"></i> ตรวจปริมาณข้อมูลของโรงพยาบาล
                         </button>
                            </a>
                        <hr>
                        <h1 class="roboto" style="font-weight: 500;">ติดปัญหาในการใช้งาน/แจ้งเงื่อนไขเพิ่มเติมแจ้งได้ที่ Line : @RTIDDC  </h1>

                        <hr>
 @if(Auth::user()->type == 1 )
  <h1 class="roboto" style="font-weight: 500;">รายงานที่สั่งตรวจล่าสุด </h1>

                            <form action="{{ route('search_present_report') }}" method="GET">
                                <select class="custom-select form-control select2" style="width: 300px;" tabindex="-1" aria-hidden="true" name="hosp_search" type="text" required>
                                    <option selected="selected" value="">โรงพยาบาล</option>
                                    @foreach($hosps as $hosp)
                                        <option value={{$hosp->hospcode}}>{{$hosp->full_name}}</option>
                                    @endforeach
                                </select>

                                <input type="submit" value="แสดงผล" class="btn btn-success" >
                            </form>
							
							
							
                            @if($datas != null)
                                <p class="mt-3" style="font-size: 20px"><strong>{{$datas->getHospName->full_name}}</strong></p>
                            @endif
							@endif
                        @endif

@if(Auth::user()->type == 0 )
                     <h5 class="roboto" style="font-weight: 500">ข้อมูลของ {{ Auth::user()->name  }} ที่อยู่ในฐานข้อมูลส่วนกลาง <small> หากไม่ตรงกับฐานข้อมูลที่โรงพยาบาล ให้ตรวจสอบการส่งข้อมูลมาอีกครั้ง</small></h5>

                       <p align="center">     <iframe src="https://dip.ddc.moph.go.th/is-checking/tracking_detail/{{ Auth::user()->username }}/{{date('Y')}}" frameborder="0" width="800" height="400" scrolling="no" ></iframe></p>
                        <hr>
                        @endif

                        <h3 class="roboto" style="font-weight: 500;">พบปัญหาในการใช้งาน / แจ้งเงื่อนไขในการตรวจสอบคุณภาพข้อมูลเพิ่มเติมแจ้งได้ที่ Line : @RTIDDC  </h3>
                        <hr>

                        @if($datas != null)



                        <h5 class="roboto" style="font-weight: 500">รายงานที่สั่งตรวจล่าสุด</small></h5>
                        <p>ข้อมูลในช่วง {{$datas->start_date->addyear(543)->format('d-m-Y')}} ถึง {{$datas->end_date->addyear(543)->format('d-m-Y')}} <br>
                        ประมวลผลเมื่อ {{$datas->start_time->addYear(543)->format('d-m-Y H:i:s')}}
                        </p>
                            @endif


                        @if(Session::has('no data'))
                            <div class="alert alert-warning m-2" role="alert" style="width:30%">
                                <span>ไม่พบข้อมูล <strong>รายงาน</strong> ล่าสุด</span>
                            </div>
                        @endif


                @if($datas !== null)
                   @if(Auth::user()->type >= 1)
     <p class="mt-3" style="float: left;font-size: 20px"><strong>{{$datas->getHospName->full_name}}</strong></p>
                    @endif



                            <a href="{{url("/download/report/{$datas->id}")}}" target="_blank"><button type="button" class="btn btn-outline-success mb-3" style="float: right"><i class="mdi mdi-arrow-down-bold-circle icon"></i>ดาวน์โหลด excel</button></a>

                    <table class="table table-bordered">
                        <tbody class="roboto">
                        <tr>
                            <td colspan="2">จำนวนข้อมูลทั้งหมด</td>
                            <td>{{$datas->count}}</td>
                        </tr>

                            <tr>
                                <td colspan="3" style="color:grey">ความถูกต้อง</td>
                            </tr>
                            <tr>
                                <td colspan="2">จำนวนข้อมูลที่มี ความถูกต้อง ครบ</td>
                                <td>{{$datas->type_1}}</td>
                            </tr>
                            <tr>
                                <td colspan="2">จำนวนข้อมูลที่มี ความถูกต้อง ไม่ครบ</td>
                                <td>{{$datas->count - $datas->type_1}}</td>
                            </tr>
                            <tr>
                                <td colspan="2">ร้อยละ ความถูกต้อง ของข้อมูล</td>
                                <td>{{$datas->type_1P}} %</td>
                            </tr>


                        <tr>
                            <td colspan="3" style="color:gray">ความสมบูรณ์</td>
                        </tr>
                        <tr>
                            <td colspan="2">จำนวนข้อมูลที่มี ความสมบูรณ์ ครบ</td>
                            <td>{{$datas->type_2}}</td>
                        </tr>
                        <tr>
                            <td colspan="2">จำนวนข้อมูลที่มี ความสมบูรณ์ ไม่ครบ</td>
                            <td>{{$datas->count - $datas->type_2}}</td>
                        </tr>
                        <tr>
                            <td colspan="2">ร้อยละ ความสมบูรณ์ ของข้อมูล</td>
                            <td>{{$datas->type_2P}} %</td>
                        </tr>


                        <tr>
                            <td colspan="3" style="color:gray">ความเที่ยงตรง</td>
                        </tr>
                        <tr>
                            <td colspan="2">จำนวนข้อมูลที่มี ความเที่ยงตรง ครบ</td>
                            <td>{{$datas->type_3}}</td>
                        </tr>
                        <tr>
                            <td colspan="2">จำนวนข้อมูลที่มี ความเที่ยงตรง ไม่ครบ</td>
                            <td>{{$datas->count - $datas->type_3}}</td>
                        </tr>
                        <tr>
                            <td colspan="2">ร้อยละ ความเที่ยง ของข้อมูล</td>
                            <td>{{$datas->type_3P}} %</td>
                        </tr>


                        <tr>
                            <td colspan="3" style="color:gray">ความตรงตามกาล</td>
                        </tr>
                        <tr>
                            <td colspan="2">จำนวนข้อมูลที่มี ความตรงตามกาล ครบ</td>
                            <td>{{$datas->type_4}}</td>
                        </tr>
                        <tr>
                            <td colspan="2">จำนวนข้อมูลที่มี ความตรงตามกาล ไม่ครบ</td>
                            <td>{{$datas->count - $datas->type_4}}</td>
                        </tr>
                        <tr>
                            <td colspan="2">ร้อยละ ความตรงตามกาล ของข้อมูล</td>
                            <td>{{$datas->type_4P}} %</td>
                        </tr>



                        <tr>
                            <td colspan="3" style="color:gray">ความเป็นเอกลักษณ์</td>
                        </tr>
                        <tr>
                            <td colspan="2">จำนวนข้อมูลที่มี ความเป็นเอกลักษณ์ ครบ</td>
                            <td>{{$datas->type_5}}</td>
                        </tr>
                        <tr>
                            <td colspan="2">จำนวนข้อมูลที่มี ความเป็นเอกลักษณ์ ไม่ครบ</td>
                            <td>{{$datas->count - $datas->type_5}}</td>
                        </tr>
                        <tr>
                            <td colspan="2">ร้อยละ ความเป็นเอกลักษณ์ ของข้อมูล</td>
                            <td>{{$datas->type_5P}} %</td>
                        </tr>


                        <tr>
                            <td colspan="3" style="color:gray">ความแม่นยำ</td>
                        </tr>
                        <tr>
                            <td colspan="2">จำนวนข้อมูลที่มี ความแม่นยำ ครบ</td>
                            <td>{{$datas->type_6}}</td>
                        </tr>
                        <tr>
                            <td colspan="2">จำนวนข้อมูลที่มี ความแม่นยำ ไม่ครบ</td>
                            <td>{{$datas->count - $datas->type_6}}</td>
                        </tr>
                        <tr>
                            <td colspan="2">ร้อยละ ความแม่นยำ ของข้อมูล</td>
                            <td>{{$datas->type_6P}} %</td>
                        </tr>

                        </tbody>
                    </table>

                        @else
                    <div>
                        <h2 class="roboto" style="font-weight: 500; text-align:center;color:red;">---ยังไม่ผ่านการตรวจข้อมูลในระบบ---</h2>
                        <a href="{{url("/reorder")}}" style=" text-align:center"> <h3> >>ตรวจสอบที่นี่<< </h3></a>

                    </div>

                        @endif
</div>

<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@endsection
