@extends('layouts.app')
@section('content')
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success fw-bold" role="alert">
                <i class="fa-solid fa-circle-info me-1"></i>
                {{ session('status') }}
            </div>
        @endif
        {{-- @if (session('danger'))
            <div class="alert alert-danger fw-bold" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                {{ session('danger') }}
            </div>
        @endif --}}

        <h1 style="fw-bold">หน้าหลัก</h1>
        <hr>
        <h4 class="fw-bold">ตรวจปริมาณข้อมูล และรายงานที่สั่งตรวจล่าสุดของหน่วยงาน</h4>
        <form action="{{ route('present_report') }}" method="post">
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
                    @if (Auth::user()->type === 0) required @endif>
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

        @if (!empty($hosp_stats))
            <h5 class="fw-bold">
                <small><i class="fa-solid fa-chart-column me-1"></i></small>
                ปริมาณข้อมูล {{ $hosp_stats->filter->hospname ?? '-' }} ที่อยู่ในฐานข้อมูลส่วนกลาง
                <span style="font-size: 15px;">
                    (หากไม่ตรงกับฐานข้อมูลที่โรงพยาบาล ให้ตรวจสอบการส่งข้อมูลมาอีกครั้ง)
                </span>
            </h5>
            <div class="col-12 mb-2">
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

        @if (!empty($datas))
            <h5 class="mb-1 fw-bold">
                <small><i class="fa-solid fa-newspaper me-1"></i></small>
                รายงานที่สั่งตรวจล่าสุด
            </h5>
            <p class="mb-0">
                ข้อมูลในช่วง
                {{ $datas->start_date->format('d/m/') . ($datas->start_date->format('Y') + 543) }}
                ถึง
                {{ $datas->end_date->format('d/m/') . ($datas->end_date->format('Y') + 543) }}
                <small>
                    (ประมวลผลเมื่อ
                    {{ $datas->start_time->format('d/m/') . ($datas->start_time->format('Y') + 543) }}
                    {{ $datas->start_time->format('H:i:s') }} น.)
                </small>
            </p>

            <p class="fw-bold mt-2 mb-0" style="float: left; font-size: 20px">
                {{ $datas->getHospName->full_name ?? '' }}
            </p>

            @if (isset($datas->id))
                <a href="{{ route('download_report', $datas->id) }}" target="_blank">
                    <button type="button" class="btn btn-sm btn-outline-success mb-2" style="float: right">
                        <i class="fa-solid fa-download me-1"></i>
                        ดาวน์โหลด Excel
                    </button>
                </a>
            @endif

            <table class="table table-bordered table-hover">
                <tbody>
                    <tr>
                        <td class="fw-bold bg-secondary text-white">
                            จำนวนข้อมูลทั้งหมด
                        </td>
                        <td class="text-end bg-secondary text-white">
                            {{ number_format($datas->count ?? 0) }} ราย
                        </td>
                    </tr>
                    @php
                        $error_types = \Illuminate\Support\Facades\DB::table('error_types_bk')
                            ->where('is_using', true)
                            ->get();
                    @endphp
                    @foreach ($error_types as $error_type)
                        @php
                            // ตัดคำเช่น "ความถูกต้อง (Accuracy)" เป็น "ความถูกต้อง"
                            $error_type_name_short = substr($error_type->name, 0, strpos($error_type->name, ' '));

                            // สร้างชื่อฟิลด์ที่เชื่อมโยงกับ error_type->id เช่น type_1, type_1P
                            $data_type_x = 'type_' . $error_type->id;
                            $data_type_xP = 'type_' . $error_type->id . 'P';
                        @endphp
                        <tr>
                            <td colspan="2" class="table-secondary">{{ $error_type->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <td>จำนวนข้อมูลที่มี {{ $error_type_name_short }} ครบ</td>
                            <td class="text-end">
                                {{ number_format($datas->$data_type_x ?? 0) }} ราย
                            </td>
                        </tr>
                        <tr>
                            <td>จำนวนข้อมูลที่มี {{ $error_type_name_short }} ไม่ครบ</td>
                            <td class="text-end">
                                {{ number_format(($datas->count ?? 0) - ($datas->$data_type_x ?? 0)) }} ราย
                            </td>
                        </tr>
                        <tr>
                            <td>ร้อยละ {{ $error_type_name_short }} ของข้อมูล</td>
                            <td class="text-end">
                                {{ number_format($datas->$data_type_xP ?? 0, 2) }}%
                            </td>
                        </tr>
                    @endforeach

                    {{-- <tr>
                        <td colspan="2" class="table-secondary">ความถูกต้อง</td>
                    </tr>
                    <tr>
                        <td>จำนวนข้อมูลที่มี ความถูกต้อง ครบ</td>
                        <td class="text-end">
                            {{ number_format($datas->type_1 ?? 0) }}
                        </td>
                    </tr>
                    <tr>
                        <td>จำนวนข้อมูลที่มี ความถูกต้อง ไม่ครบ</td>
                        <td class="text-end">
                            {{ number_format(($datas->count ?? 0) - ($datas->type_1 ?? 0)) }}
                        </td>
                    </tr>
                    <tr>
                        <td>ร้อยละ ความถูกต้อง ของข้อมูล</td>
                        <td class="text-end">
                            {{ number_format($datas->type_1P ?? 0, 2) }}%
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="table-secondary">ความสมบูรณ์</td>
                    </tr>
                    <tr>
                        <td>จำนวนข้อมูลที่มี ความสมบูรณ์ ครบ</td>
                        <td class="text-end">
                            {{ number_format($datas->type_2 ?? 0) }}
                        </td>
                    </tr>
                    <tr>
                        <td>จำนวนข้อมูลที่มี ความสมบูรณ์ ไม่ครบ</td>
                        <td class="text-end">
                            {{ number_format(($datas->count ?? 0) - ($datas->type_2 ?? 0)) }}
                        </td>
                    </tr>
                    <tr>
                        <td>ร้อยละ ความสมบูรณ์ ของข้อมูล</td>
                        <td class="text-end">
                            {{ number_format($datas->type_2P ?? 0, 2) }}%
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="table-secondary">ความเที่ยงตรง</td>
                    </tr>
                    <tr>
                        <td>จำนวนข้อมูลที่มี ความเที่ยงตรง ครบ</td>
                        <td class="text-end">
                            {{ number_format($datas->type_3 ?? 0) }}
                        </td>
                    </tr>
                    <tr>
                        <td>จำนวนข้อมูลที่มี ความเที่ยงตรง ไม่ครบ</td>
                        <td class="text-end">
                            {{ number_format(($datas->count ?? 0) - ($datas->type_3 ?? 0)) }}
                        </td>
                    </tr>
                    <tr>
                        <td>ร้อยละ ความเที่ยงตรง ของข้อมูล</td>
                        <td class="text-end">
                            {{ number_format($datas->type_3P ?? 0, 2) }}%
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="table-secondary">ความตรงตามกาล</td>
                    </tr>
                    <tr>
                        <td>จำนวนข้อมูลที่มี ความตรงตามกาล ครบ</td>
                        <td class="text-end">
                            {{ number_format($datas->type_4 ?? 0) }}
                        </td>
                    </tr>
                    <tr>
                        <td>จำนวนข้อมูลที่มี ความตรงตามกาล ไม่ครบ</td>
                        <td class="text-end">
                            {{ number_format(($datas->count ?? 0) - ($datas->type_4 ?? 0)) }}
                        </td>
                    </tr>
                    <tr>
                        <td>ร้อยละ ความตรงตามกาล ของข้อมูล</td>
                        <td class="text-end">
                            {{ number_format($datas->type_4P ?? 0, 2) }}%
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="table-secondary">ความเป็นเอกลักษณ์</td>
                    </tr>
                    <tr>
                        <td>จำนวนข้อมูลที่มี ความเป็นเอกลักษณ์ ครบ</td>
                        <td class="text-end">
                            {{ number_format($datas->type_5 ?? 0) }}
                        </td>
                    </tr>
                    <tr>
                        <td>จำนวนข้อมูลที่มี ความเป็นเอกลักษณ์ ไม่ครบ</td>
                        <td class="text-end">
                            {{ number_format(($datas->count ?? 0) - ($datas->type_5 ?? 0)) }}
                        </td>
                    </tr>
                    <tr>
                        <td>ร้อยละ ความเป็นเอกลักษณ์ ของข้อมูล</td>
                        <td class="text-end">
                            {{ number_format($datas->type_5P ?? 0, 2) }}%
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="table-secondary">ความแม่นยำ</td>
                    </tr>
                    <tr>
                        <td>จำนวนข้อมูลที่มี ความแม่นยำ ครบ</td>
                        <td class="text-end">
                            {{ number_format($datas->type_6 ?? 0) }}
                        </td>
                    </tr>
                    <tr>
                        <td>จำนวนข้อมูลที่มี ความแม่นยำ ไม่ครบ</td>
                        <td class="text-end">
                            {{ number_format(($datas->count ?? 0) - ($datas->type_6 ?? 0)) }}
                        </td>
                    </tr>
                    <tr>
                        <td>ร้อยละ ความแม่นยำ ของข้อมูล</td>
                        <td class="text-end">
                            {{ number_format($datas->type_6P ?? 0, 2) }}%
                        </td>
                    </tr> --}}
                </tbody>
            </table>
        @else
            <div>
                <h3 class="fw-bold text-center text-danger mb-3">
                    <small><i class="fa-solid fa-minus"></i></small>
                    ยังไม่ผ่านการตรวจข้อมูลในระบบ
                    <small><i class="fa-solid fa-minus"></i></small>
                </h3>
                <a href="{{ route('reorder') }}" class="text-center">
                    <h4>
                        <small><i class="fa-solid fa-angles-right"></i></small>
                        ตรวจสอบที่นี่
                        <small><i class="fa-solid fa-angles-left"></i></small>
                    </h4>
                </a>
            </div>
        @endif
        <hr>
        <h5 class="fw-bold">พบปัญหาในการใช้งาน /
            แจ้งเงื่อนไขในการตรวจสอบคุณภาพข้อมูลเพิ่มเติมแจ้งได้ที่ Line :
            <a href="https://lin.ee/qzzSV3f" target="_blank" class="text-decoration-none">@rtiddc</a> หรือ
            <span id="line_qr_code" role="button" class="text-primary ms-1">
                <i class="fa-solid fa-qrcode"></i> QR Code
            </span>
        </h5>
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

    @if (session('danger'))
        <script>
            Swal.fire({
                icon: "error",
                title: "เกิดข้อผิดพลาด",
                html: "{{ session('danger') ?? '' }}",
                showConfirmButton: true,
                showCancelButton: false,
                confirmButtonText: 'ตกลง',
                allowEscapeKey: false,
                allowOutsideClick: false,
            });
        </script>
    @endif

    @if (!empty($hosp_stats))
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
