<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Is-Checking') }}</title>

    <!-- Font Awesome Css -->
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free-6.4.0-web/css/all.min.css') }}" />

    <!-- Sweetalert2 -->
    <link href="{{ asset('assets/sweetalert2/css/sweetalert2.min.css') }}" rel="stylesheet" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai&display=swap" rel="stylesheet">

    <!-- CSS only -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/6.5.95/css/materialdesignicons.min.css"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- Select2 -->
    <link href="{{ asset('assets/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/select2/css/select2-bootstrap-5-theme.min.css.css') }}" rel="stylesheet" />
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}

    {{-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> --}}
    {{-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> --}}

    <!-- Styles -->
    {{-- <link href="{{ asset('assets/datepicker.css') }}" rel="stylesheet"> --}}

    <!-- Bootstrap Datepicker -->
    <link href="{{ asset('assets/bootstrap-datepicker/css/datepicker.css') }}" rel="stylesheet">

    <style>
        body,
        html {
            font-family: 'Noto Sans Thai', sans-serif;
        }

        .page-link {
            position: relative;
            display: block;
            color: black;
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #dee2e6;
            transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .page-link:hover {
            color: #006637;
        }

        .page-item.active .page-link {
            z-index: 3;
            color: #006637;
            background-color: #fff;
            border-color: #006637;
        }

        .icon {

            margin-right: 5px;
            margin-bottom: 1px;
        }

        .btn-outline-warning {
            color: #E77E02;
            border-color: #E77E02;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
            position: absolute;
            top: 1px;
            right: 9px;
            width: 20px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #444;
            line-height: 34px;
        }

        .select2-container .select2-selection--single {
            height: 38px;
        }

        .footer {

            width: 100%;
            text-align: center;
        }

        .nav-link:hover {
            background-color: #EAEAEA;
            color: #FFF;
        }

        .nav-link {
            text-align: left;
            color: #fff;
        }

        a {
            text-decoration: none;
        }

        .datepicker-days table tbody tr td {
            padding: 10px;
        }

        .datepicker-days table thead tr th {
            padding: 10px;
        }

        .datepicker-days table tbody tr td:hover {
            background-color: #006A68;
            cursor: pointer;
        }

        svg {
            /* width: 50px; */
        }
    </style>

</head>

<body>
    <div id="app">
        @include('waiting')
        <nav class="navbar navbar-expand-md navbar-light shadow-sm sticky-top" style="background-color: #006637;">
            <div class="container-fluid">
                <a class="navbar-brand mb-0 h1 text-white fw-bold" href="{{ route('home') }}">
                    IS - CHECKING
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    {{-- <ul class="navbar-nav me-auto">
                    </ul> --}}

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto d-flex align-items-center" style="color: #FFF;">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <a style="color: #ffffff" class="nav-link" href="{{ route('login') }}">
                                    <button type="button" class="btn btn-success">
                                        เข้าสู่ระบบ
                                    </button>
                                </a>
                            @endif
                        @else
                            <div class="me-2" style="font-size: 16px">
                                <i class="fa-solid fa-hospital me-1"></i>
                                {{ Auth::user()->name ?? '-' }}
                                <small>({{ Auth::user()->username ?? '-' }})</small>
                            </div>
                            <form action="{{ route('logout') }}" method="post">
                                @method('POST')
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm" style="font-size: 14px">
                                    <i class="fa-solid fa-arrow-right-from-bracket me-1"></i>
                                    ออกจากระบบ
                                </button>
                            </form>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        @guest
            @if (Route::has('login'))
                <div>
                    <br><br>
                    @yield('content')
                </div>
            @endif
        @else
            <div class="d-flex" id="wrapper">
                <div class="border-end bg-white" id="sidebar-wrapper">
                    <div class="list-group list-group-flush">
                        <img style="width: 70px; height: 70px; display: block; margin: 10px auto"
                            src="{{ asset('storage/imgs/logo.svg') }}">
                        <a class="list-group-item list-group-item-action list-group-item-light p-3"
                            href="{{ route('present_report') }}">
                            <i class="mdi mdi-home icon"></i>
                            หน้าหลัก
                        </a>
                        <a class="list-group-item list-group-item-action list-group-item-light p-3"
                            href="{{ route('reorder') }}">
                            <i class="mdi mdi-pencil-box icon"></i>
                            สั่งตรวจใหม่
                        </a>
                        <a class="list-group-item list-group-item-action list-group-item-light p-3"
                            href="{{ route('retrospective_report') }}">
                            <i class="mdi mdi-folder icon"></i>
                            ผลการตรวจสอบ
                        </a>
                        {{-- <a class="list-group-item list-group-item-action list-group-item-light p-3"
                            href="{{ route('update_password_controller', Auth::user()->id) }}">
                            <i class="mdi mdi-account-cog icon"></i>
                            แก้ไขข้อมูลส่วนตัว
                        </a> --}}

                        {{-- only super admin can manage users --}}
                        @if (Auth::user()->type == 1)
                            <a class="list-group-item list-group-item-action list-group-item-light p-3"
                                href="{{ route('manage_users') }}">
                                <i class="mdi mdi-account-multiple icon"></i>
                                จัดการผู้ใช้งาน
                            </a>
                            <a class="list-group-item list-group-item-action list-group-item-light p-3"
                                href="{{ route('manage_hospitals') }}">
                                <i class="mdi mdi-hospital-building icon"></i>
                                จัดการโรงพยาบาล
                            </a>
                            <a class="list-group-item list-group-item-action list-group-item-light p-3"
                                href="{{ route('history') }}">
                                <i class="mdi mdi-history icon"></i>
                                ประวัติการใช้งาน
                            </a>
                        @endif
                        <a class="list-group-item list-group-item-action list-group-item-light p-3"
                            href="{{ route('manage_cases') }}">
                            <i class="mdi mdi-check-all icon"></i>
                            จัดการ case
                        </a>
                        <div class="footer mt-3">
                            <small>
                                Copyright &copy; 2021
                            </small>
                        </div>
                    </div>
                </div>
                <div id="page-content-wrapper" class="py-3 pe-2">
                    @yield('content')
                </div>
            </div>
        @endguest
    </div>

</body>

</html>

<!-- jQuery -->
{{-- <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<!-- รวม jQuery UI (ต้องใช้ jQuery UI เพื่อใช้ datepicker) -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<!-- Sweetalert2 -->
<script src="{{ asset('assets/sweetalert2/js/sweetalert2.all.min.js') }}"></script>

{{-- <!-- bootstrap-datepicker thai extension -->
<script type="text/javascript" src="{{ asset('js/datepicker_th/bootstrap-datepicker-thai.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/datepicker_th/locales/bootstrap-datepicker.th.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/momentjs/moment-with-locales.min.js') }}"></script> --}}

<!-- Bootstrap Datepicker -->
<script src="{{ asset('assets/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/bootstrap-datepicker/js/bootstrap-datepicker-thai.js') }}"></script>
<script src="{{ asset('assets/bootstrap-datepicker/js/locales/bootstrap-datepicker.th.js') }}"></script>

{{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}

<!-- Select2 -->
{{-- <script src="{{ asset('assets/select2/js/bootstrap.bundle.min.js') }}"
    integrity="sha384-sqIwnO0uI2Yo5qjwGXu2CgQyxB4G2c5xH9beSHsQuUC6wJO3aMSszc7u" crossorigin="anonymous"></script> --}}
<script src="{{ asset('assets/select2/js/bootstrap.bundle.min.js') }}" crossorigin="anonymous"></script>
<script src="{{ asset('assets/select2/js/select2.min.js') }}"></script>

<script>
    $(window).bind('beforeunload', function() {
        $("#loading-wrapper").show();
    })
    $(window).on("unload", function() {
        $("#loading-wrapper").hide();
    })
    $(window).ready(function($) {
        $("#loading-wrapper").hide();
    })

    $(document).ready(function() {
        $('.select2').select2();
        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });
    });

    $(function() {
        // $('.datepicker').datepicker({
        //     language: 'th-th',
        //     lang: 'th-th',
        //     format: 'dd/mm/yyyy',
        //     endDate: new Date(),
        //     inputs: $('.actual_range'),
        //     yearOffset: 543,
        // });

        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy', // รูปแบบวันที่
            language: 'th-th', // ภาษาไทย
            autoclose: true, // ปิดปฏิทินอัตโนมัติเมื่อเลือกวันที่
            todayHighlight: true, // ไฮไลต์วันที่ปัจจุบัน
        });
    });
</script>

@yield('script')
