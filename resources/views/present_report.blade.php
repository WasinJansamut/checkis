@extends('layouts.app')
@section('content')
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                <strong>
                    <i class="fa-solid fa-circle-info me-1"></i>
                    {{ session('status') }}
                </strong>
            </div>
        @endif
        @if (session('danger'))
            <div class="alert alert-danger" role="alert">
                <strong>
                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                    {{ session('danger') }}
                </strong>
            </div>
        @endif

        <h1 style="fw-bold">หน้าหลัก</h1>
        <hr>

        {{-- @if (Auth::user()->type == 1)
            <h4 style="font-weight: 500;">รายงานที่สั่งตรวจล่าสุด</h4>
            <form action="{{ route('search_present_report') }}" method="GET">
                <div class="mb-3">
                    <select class="form-control select2" style="width: 400px;" tabindex="-1"
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
        @endif --}}

        <h4>ตรวจปริมาณข้อมูลของหน่วยงาน</h4>
        <form action="{{ route('present_report') }}" method="POST">
            @method('POST')
            @csrf
            <div class="mb-3">
                @php
                    $year_th_array = [];
                    $year_th_now = Carbon\Carbon::now()->year + 543; // ปีปัจจุบัน

                    for ($i = 0; $i < 5; $i++) {
                        $year_th_array[] = $year_th_now - $i;
                    }
                @endphp
                <select class="form-control select2" style="width: 200px;" tabindex="-1" aria-hidden="true" name="year"
                    required>
                    <option value="">=== กรุณาเลือกปี ===</option>
                    @foreach ($year_th_array as $year)
                        <option value={{ $year }} {{ request()->year == $year ? 'selected' : '' }}>
                            พ.ศ. {{ $year }}
                        </option>
                    @endforeach
                </select>
                <select class="form-control select2" style="width: 480px;" tabindex="-1" aria-hidden="true" name="hospcode"
                    required>
                    @if (Auth::user()->type > 0)
                        <option value="">=== กรุณาเลือกหน่วยงาน ===</option>
                    @endif
                    @foreach ($hospitals as $hospital)
                        <option value={{ $hospital->hospcode }}
                            {{ request()->hospcode == $hospital->hospcode ? 'selected' : '' }}>
                            {{ $hospital->full_name ?? '-' }}
                            ({{ $hospital->hospcode ?? '-' }})
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-magnifying-glass-chart me-1"></i>
                    ตรวจสอบ
                </button>
                @if (request()->isMethod('post'))
                    <a href="{{ route('present_report') }}" class="btn btn-dark">
                        <i class="fa-solid fa-hand-sparkles me-1"></i>
                        ล้างค่า
                    </a>
                @endif
            </div>
        </form>

        @if (request()->isMethod('post'))
            <h5 class="fw-bold">
                ข้อมูลของ {{ $hosp_stats->filter->hospname ?? '-' }} ที่อยู่ในฐานข้อมูลส่วนกลาง
                <span style="font-size: 15px;">
                    (หากไม่ตรงกับฐานข้อมูลที่โรงพยาบาล ให้ตรวจสอบการส่งข้อมูลมาอีกครั้ง)
                </span>
            </h5>
            <div class="col-12 mb-3">
                ปริมาณข้อมูลทั้งหมด {{ number_format($hosp_stats->count ?? 0) }} ราย
                (ข้อมูลปี พ.ศ. {{ request()->year }})
            </div>
            <div class="col-12">
                <!-- [Start] กราฟ -->
                <div id="div_show_chart" class="card">
                    <div class="card-body px-0 pt-1 pb-0">
                        <figure class="highcharts-figure">
                            <div id="highcharts-container"></div>
                            <p class="highcharts-description">
                                {{-- description --}}
                            </p>
                        </figure>
                    </div>
                </div>
                <!-- [End] กราฟ -->
            </div>
        @endif
        <hr>

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
                allowOutsideClick: false,
                didOpen: () => {
                    // เมื่อเปิด modal แล้ว ทำให้ไม่มีการโฟกัสไปที่ปุ่ม
                    Swal.getCancelButton().blur();
                }
            });
        });
    </script>

    @if (request()->isMethod('post'))
        <!-- Highcharts -->
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/modules/export-data.js"></script>
        <script src="https://code.highcharts.com/modules/accessibility.js"></script>
        <script>
            $(document).ready(function() {
                const data = {!! json_encode($hosp_stats->stats) !!};
                var data_to_chart = [];
                if (typeof data === 'object' && data !== null) {
                    const data_array = Object.values(data);
                    if (Array.isArray(data_array)) {
                        data_array.forEach((row) => {
                            // var label = row.data_yymm || '';
                            var label = row.month_th || '';
                            var count = parseInt(row.data || 0);
                            data_to_chart.push([label, count]);
                        });
                    }
                }

                if (data_to_chart.length > 0) {
                    var req_hospname = "{{ $hosp_stats->filter->hospname ?? '-' }}";
                    var req_year = "{{ request()->year ?? '-' }}";
                    var req_count = {{ $hosp_stats->count ?? 0 }};
                    Highcharts.chart('highcharts-container', {
                        lang: {
                            thousandsSep: ','
                        },
                        credits: {
                            // enabled: false,
                            text: 'ที่มาแหล่งข้อมูล : ระบบเฝ้าระวังการบาดเจ็บ (Injury Surveillance : IS) กระทรวงสาธารณสุข',
                            href: '#', // The URL for the link
                            position: {
                                align: 'center',
                                y: -5,
                                x: 5
                            },
                            style: {
                                fontSize: '12px',
                            }
                        },
                        chart: {
                            type: 'column',
                            style: {
                                fontFamily: 'Noto Sans Thai',
                            }
                        },
                        title: {
                            text: `ความสม่ำเสมอของข้อมูล ${req_hospname}`,
                        },
                        subtitle: {
                            text: `ปริมาณข้อมูลทั้งหมด ${req_count.toLocaleString()} ราย (ข้อมูลปี พ.ศ. ${req_year})`,
                        },
                        xAxis: {
                            type: 'category',
                            title: {
                                text: 'เดือน'
                            },
                            labels: {
                                autoRotation: [-45, -90],
                                style: {
                                    fontSize: '12px',
                                }
                            }
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'ปริมาณข้อมูล (ราย)'
                            }
                        },
                        legend: {
                            enabled: false
                        },
                        exporting: {
                            buttons: {
                                contextButton: {
                                    menuItems: [
                                        "viewFullscreen",
                                        "downloadPNG",
                                        "downloadJPEG",
                                        "downloadPDF",
                                        "downloadSVG"
                                    ]
                                }
                            }
                        },
                        tooltip: {
                            shared: true,
                            headerFormat: '<span style="font-size: 14px">{point.key}</span><br/>',
                            pointFormat: '<span style="color:{point.color}">&#9679;</span> {series.name}: <b>{point.y} ราย</b><br/>',
                        },
                        plotOptions: {
                            column: {
                                dataLabels: {
                                    enabled: true,
                                },
                            },
                        },
                        series: [{
                            name: 'จำนวน',
                            data: data_to_chart,
                            colorByPoint: true,
                        }]
                    });

                }
            });
        </script>
    @endif
@endsection
