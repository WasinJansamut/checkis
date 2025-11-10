<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>เกิดข้อผิดพลาด - {{ config('app.name', 'เกิดข้อผิดพลาด') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- Font Awesome Css -->
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free-6.4.0-web/css/all.min.css') }}" />

    <!-- Sweetalert2 -->
    <link href="{{ asset('assets/sweetalert2/css/sweetalert2.min.css') }}" rel="stylesheet" />

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap');

        * {
            font-family: "Noto Sans Thai", sans-serif;
        }
    </style>
</head>

<body>
    @php
        $alerts = [
            'primary' => 'สำเร็จ',
            'success' => 'สำเร็จ',
            'warning' => 'คำเตือน',
            'info' => 'รายละเอียด',
            'danger' => 'เกิดข้อผิดพลาด',
        ];

        $hasAlert = false;
        foreach ($alerts as $type => $title) {
            if (Session::has($type)) {
                $hasAlert = true;
                break;
            }
        }
    @endphp

    @if (!$hasAlert)
        {{-- ถ้าไม่มี message ให้ redirect ไปหน้าอื่น --}}
        <script>
            window.location.href = "https://connect.moph.go.th/pher-plus/";
        </script>
    @else
        {{-- <a href="https://connect.moph.go.th/pher-plus/" class="btn btn-success position-absolute m-3" style="top: 0; left: 0;">
            <i class="fa-solid fa-arrow-left me-1"></i> กลับสู่ Pher Plus
        </a> --}}
        <div class="row m-0 align-items-center vh-100">
            <div class="justify-content-center text-center">
                <img class="mb-3 mx-auto" style="width: 140px; height: 140px;" src="{{ asset('storage/imgs/logo.svg') }}">
                <h5 class="fw-bolder" style="color: #006838">ระบบตรวจสอบคุณภาพข้อมูลการเฝ้าระวังการบาดเจ็บ</h5>
                <h2 class="fw-bolder fs-1 text-secondary mb-3">
                    IS - CHECKING
                </h2>
                <div class="content-inner pb-0 container-fluid" id="page_layout">
                    <div class="row">
                        <div class="col-lg-12">
                            @foreach ($alerts as $type => $title)
                                @if (Session::has($type))
                                    <div class="row m-0 align-items-center">
                                        <div class="justify-content-center text-center">
                                            <div class="alert alert-left alert-{{ $type }} alert-dismissible progress-bar-striped progress-bar-animated fade show px-3 pt-4 pb-3" role="alert">
                                                @if (in_array($type, ['warning', 'danger']))
                                                    <i class="fa-solid fa-triangle-exclamation mb-3" style="font-size: 3.5rem;"></i>
                                                @else
                                                    <i class="fa-solid fa-circle-check mb-3 display-4"></i>
                                                @endif
                                                <p class="fs-2 fw-bolder mb-2">{{ $title }}</p>
                                                <p class="fs-5">
                                                    <sup><i class="fa-solid fa-quote-left"></i></sup>
                                                    {{ Session::get($type) }}
                                                    <sup><i class="fa-solid fa-quote-right"></i></sup>
                                                </p>
                                                <a class="btn btn-primary mx-1 mb-2" href="https://connect.moph.go.th/pher-plus/">
                                                    <i class="fa-solid fa-arrow-left me-1"></i>
                                                    <span class="item-name">กลับสู่ Pher Plus</span>
                                                </a>
                                                <a class="btn btn-success mx-1 mb-2" href="{{ route('dashboard.hospital_21_variables') }}">
                                                    <i class="fa-solid fa-square-poll-vertical me-1"></i>
                                                    Dashboard ตรวจสอบข้อมูล
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                {{-- <div class="col-12 text-center">
                    <p class="mb-0">
                        หากเข้าสู่ระบบไม่ได้ กรุณาติดต่อ Line:
                        <a href="https://lin.ee/qzzSV3f" target="_blank">@rtiddc</a>
                    </p>
                    <p class="mb-3">
                        <img src="https://rti.moph.go.th/pher-plus/report/public/assets/images/qrcode_line.png"
                            alt="QR Code Line"
                            style="width:150px; height:auto; -webkit-filter: grayscale(100%); filter: grayscale(100%);">
                    </p>
                </div>
                <a href="{{ route('dashboard.hospital_21_variables') }}" class="btn btn-lg btn-success">
                    <i class="fa-solid fa-square-poll-vertical me-1"></i>
                    Dashboard ตรวจสอบข้อมูล
                </a> --}}
            </div>
        </div>
    @endif
</body>

<!-- jQuery 3.7.1 -->
<script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        localStorage.clear();
    });
</script>

</html>
