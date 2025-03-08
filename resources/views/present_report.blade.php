@extends('layouts.app')
@section('content')
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <h1 style="fw-bold">หน้าหลัก</h1>
        <hr>
        @if (Auth::user()->type == 1 || Auth::user()->type == 2 || Auth::user()->type == 3)
            <a href="https://dip.ddc.moph.go.th/is-checking/check_error" target="_blank">
                <button class="btn btn-primary">
                    <i class="fa-solid fa-chart-area me-1"></i>
                    ตรวจปริมาณข้อมูลของโรงพยาบาล
                </button>
            </a>
            <hr>
            @if (Auth::user()->type == 1)
                <h1 style="font-weight: 500;">รายงานที่สั่งตรวจล่าสุด</h1>
                <form action="{{ route('search_present_report') }}" method="GET">
                    <div class="mb-3">
                        <select class="custom-select form-control select2" style="width: 300px;" tabindex="-1"
                            aria-hidden="true" name="hosp_search" type="text" required>
                            <option selected="selected" value="">โรงพยาบาล</option>
                            @foreach ($hospitals as $hospital)
                                <option value={{ $hospital->hospcode }}>{{ $hospital->full_name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-magnifying-glass me-1"></i>
                            แสดงผล
                        </button>

                    </div>
                </form>
                @if ($datas != null)
                    <p class="mt-3" style="font-size: 20px"><strong>{{ $datas->getHospName->full_name ?? '' }}</strong>
                    </p>
                @endif
            @endif
        @endif

        {{-- @if (Auth::user()->type == 0) --}}
        <h5 style="font-weight: 500">
            ข้อมูลของ {{ Auth::user()->name }} ที่อยู่ในฐานข้อมูลส่วนกลาง<br>
            <small>หากไม่ตรงกับฐานข้อมูลที่โรงพยาบาล ให้ตรวจสอบการส่งข้อมูลมาอีกครั้ง</small>
        </h5>

        <div class="text-center">
            <iframe
                src="https://dip.ddc.moph.go.th/is-checking/tracking_detail/{{ Auth::user()->username }}/{{ date('Y') }}"
                frameborder="0" width="800" height="400" scrolling="no"></iframe>
        </div>
        <hr>
        {{-- @endif --}}

        <h5 style="font-weight: 500;">พบปัญหาในการใช้งาน /
            แจ้งเงื่อนไขในการตรวจสอบคุณภาพข้อมูลเพิ่มเติมแจ้งได้ที่ Line :
            <a href="https://lin.ee/qzzSV3f" target="_blank" class="text-decoration-none">@rtiddc</a> หรือ
            <span id="line_qr_code" role="button" class="text-primary ms-1">
                <i class="fa-solid fa-qrcode"></i> QR Code
            </span>
        </h5>
        <hr>

        @if ($datas !== null)
            <h5 style="font-weight: 500">รายงานที่สั่งตรวจล่าสุด</small></h5>
            <p>
                ข้อมูลในช่วง
                {{ $datas->start_date->format('d/m/') . ($datas->start_date->format('Y') + 543) }}
                ถึง
                {{ $datas->end_date->format('d/m/') . ($datas->end_date->format('Y') + 543) }}<br>
                ประมวลผลเมื่อ
                {{ $datas->start_time->format('d/m/') . ($datas->start_time->format('Y') + 543) }}
                {{ $datas->start_time->format('H:i:s') }}
            </p>
        @endif

        @if (Session::has('no_data'))
            <div class="alert alert-warning" role="alert">
                <span>ไม่พบข้อมูล <strong>รายงาน</strong> ล่าสุด</span>
            </div>
        @endif

        @if ($datas !== null)
            @if (Auth::user()->type >= 1)
                <p class="mt-3" style="float: left; font-size: 20px">
                    <strong>{{ $datas->getHospName->full_name ?? '' }}</strong>
                </p>
            @endif

            @if (isset($datas->id))
                <a href="{{ url("/download/report/{$datas->id}") }}" target="_blank">
                    <button type="button" class="btn btn-outline-success mb-3" style="float: right">
                        <i class="fa-solid fa-download me-1"></i>
                        ดาวน์โหลด Excel
                    </button>
                </a>
            @endif

            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td colspan="2">จำนวนข้อมูลทั้งหมด</td>
                        <td>{{ $datas->count ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="color:grey">ความถูกต้อง</td>
                    </tr>
                    <tr>
                        <td colspan="2">จำนวนข้อมูลที่มี ความถูกต้อง ครบ</td>
                        <td>{{ $datas->type_1 ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">จำนวนข้อมูลที่มี ความถูกต้อง ไม่ครบ</td>
                        <td>{{ ($datas->count ?? 0) - ($datas->type_1 ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">ร้อยละ ความถูกต้อง ของข้อมูล</td>
                        <td>{{ $datas->type_1P ?? 0 }} %</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="color:gray">ความสมบูรณ์</td>
                    </tr>
                    <tr>
                        <td colspan="2">จำนวนข้อมูลที่มี ความสมบูรณ์ ครบ</td>
                        <td>{{ $datas->type_2 ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">จำนวนข้อมูลที่มี ความสมบูรณ์ ไม่ครบ</td>
                        <td>{{ ($datas->count ?? 0) - ($datas->type_2 ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">ร้อยละ ความสมบูรณ์ ของข้อมูล</td>
                        <td>{{ $datas->type_2P ?? 0 }} %</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="color:gray">ความเที่ยงตรง</td>
                    </tr>
                    <tr>
                        <td colspan="2">จำนวนข้อมูลที่มี ความเที่ยงตรง ครบ</td>
                        <td>{{ $datas->type_3 ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">จำนวนข้อมูลที่มี ความเที่ยงตรง ไม่ครบ</td>
                        <td>{{ ($datas->count ?? 0) - ($datas->type_3 ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">ร้อยละ ความเที่ยง ของข้อมูล</td>
                        <td>{{ $datas->type_3P ?? 0 }} %</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="color:gray">ความตรงตามกาล</td>
                    </tr>
                    <tr>
                        <td colspan="2">จำนวนข้อมูลที่มี ความตรงตามกาล ครบ</td>
                        <td>{{ $datas->type_4 ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">จำนวนข้อมูลที่มี ความตรงตามกาล ไม่ครบ</td>
                        <td>{{ ($datas->count ?? 0) - ($datas->type_4 ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">ร้อยละ ความตรงตามกาล ของข้อมูล</td>
                        <td>{{ $datas->type_4P ?? 0 }} %</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="color:gray">ความเป็นเอกลักษณ์</td>
                    </tr>
                    <tr>
                        <td colspan="2">จำนวนข้อมูลที่มี ความเป็นเอกลักษณ์ ครบ</td>
                        <td>{{ $datas->type_5 ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">จำนวนข้อมูลที่มี ความเป็นเอกลักษณ์ ไม่ครบ</td>
                        <td>{{ ($datas->count ?? 0) - ($datas->type_5 ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">ร้อยละ ความเป็นเอกลักษณ์ ของข้อมูล</td>
                        <td>{{ $datas->type_5P ?? 0 }} %</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="color:gray">ความแม่นยำ</td>
                    </tr>
                    <tr>
                        <td colspan="2">จำนวนข้อมูลที่มี ความแม่นยำ ครบ</td>
                        <td>{{ $datas->type_6 ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">จำนวนข้อมูลที่มี ความแม่นยำ ไม่ครบ</td>
                        <td>{{ ($datas->count ?? 0) - ($datas->type_6 ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">ร้อยละ ความแม่นยำ ของข้อมูล</td>
                        <td>{{ $datas->type_6P ?? 0 }} %</td>
                    </tr>
                </tbody>
            </table>
        @else
            <div>
                <h2 style="font-weight: 500; text-align:center;color:red;">
                    <i class="fa-solid fa-minus"></i>
                    ยังไม่ผ่านการตรวจข้อมูลในระบบ
                    <i class="fa-solid fa-minus"></i>
                </h2>
                <a href="{{ url('/reorder') }}" style=" text-align:center">
                    <h3>
                        <i class="fa-solid fa-angles-right"></i>
                        ตรวจสอบที่นี่
                        <i class="fa-solid fa-angles-left"></i>
                    </h3>
                </a>
            </div>
        @endif
    </div>
@endsection
@section('script')
    <script>
        $("#line_qr_code").on("click", function() {
            Swal.fire({
                title: "QR Code Line",
                html: '<img src="https://rti.moph.go.th/pher-plus/report/public/assets/images/qrcode_line.png" alt="QR Code Line" style="width:100%; height:auto;">',
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'ปิด',
                focusConfirm: false,
                focusCancel: false,
                width: '280px',
                backdrop: '#FFFFFF',
                allowEscapeKey: false,
                allowOutsideClick: false
            });
        });
    </script>
@endsection
