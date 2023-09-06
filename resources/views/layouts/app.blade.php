<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Is-Checking') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai&display=swap" rel="stylesheet">
    <!-- CSS only -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/6.5.95/css/materialdesignicons.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- bootstrap-datepicker thai extension -->
    <script type="text/javascript" src="{{ asset('js/datepicker_th/bootstrap-datepicker-thai.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/datepicker_th/locales/bootstrap-datepicker.th.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/momentjs/moment-with-locales.min.js') }}"></script>



{{--    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>--}}
{{--    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />--}}
    <!-- Styles -->
{{--    <link href="assets/datepicker.css" rel="stylesheet">--}}

    <style>
        body,html {
            font-family: 'Noto Sans Thai', sans-serif;
        }
        .page-link {
            position: relative;
            display: block;
            color: black;
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #dee2e6;
            transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
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

        .icon{

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
        .select2-container .select2-selection--single{
            height: 38px;
        }
        .roboto{
            font-family: 'Noto Sans Thai', sans-serif;
        }

        .footer{

            width: 100%;
            text-align: center;
        }
        .nav-link:hover {
            background-color: #EAEAEA;
            color: #FFF;
        }
        .nav-link{
            text-align: left;
            color: #fff;
        }
        a{
            text-decoration:none;
        }
        .datepicker-days table tbody tr td{
            padding: 10px;
        }
        .datepicker-days table thead tr th{
            padding: 10px;
        }
        .datepicker-days table tbody tr td:hover{
            background-color: #006A68;
            cursor: pointer;
        }

    </style>

</head>
<body>
<div id="app" >
    @include("waiting")
    <nav class="navbar navbar-expand-md navbar-light   shadow-sm"  style="background-color: #006637;">
        <div class="container-fluid" >
            <a class="navbar-brand mb-0 h1 roboto" style="color: #FFF;font-weight: 700;size: 18px" href="{{ url('/') }}">
                IS - CHECKING
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav me-auto">

                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto d-flex align-items-center"  style="color: #FFF;" >
                    <!-- Authentication Links -->
                    @guest
                        @if (Route::has('login'))
                            <a style="color: #ffffff" class="nav-link" href="{{ route('login') }}">
                                <button type="button" class="btn btn-success" >
                                    เข้าสู่ระบบ
                                </button>
                            </a>
                        @endif

                    @else

                        <div style="margin-right: 12px; font-size: 20px" >
                            {{ Auth::user()->name }}
                        </div>

                        <a class="btn btn-danger" style="font-size: 10px" href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    @guest
        @if (Route::has('login'))
            <div>
                <br>   <br>
                @yield('content')
            </div>

        @endif

    @else
        <div class="d-flex" id="wrapper">
            <div class="border-end bg-white" id="sidebar-wrapper">
                <div class="list-group list-group-flush">
                    <img style="width: 60px;height: 60px;display: block;margin: 10px auto" src="{{ asset('storage/imgs/logo.svg') }}">
                    <a class="list-group-item list-group-item-action list-group-item-light p-3" href="{{url("/present/report")}}" ><i class="mdi mdi-clipboard-text icon"></i>หน้าหลัก</a>
                    <a class="list-group-item list-group-item-action list-group-item-light p-3" href="{{url("/reorder")}}"><i class="mdi mdi-pencil-box icon"></i>สั่งตรวจใหม่</a>
                    <a class="list-group-item list-group-item-action list-group-item-light p-3" href="{{url("/retrospective/report")}}"><i class="mdi mdi-folder icon"></i>ผลการตรวจสอบ</a>
                    <a class="list-group-item list-group-item-action list-group-item-light p-3" href="{{url("/update/password",Auth::user()->id)}}"><i class="mdi mdi-account icon"></i>แก้ไขข้อมูลส่วนตัว</a>


                    {{--                        only super admin can manage users--}}
                    @if( Auth::user()->type == 1)
                        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="{{url("/manage/users")}}"><i class="mdi mdi-account icon"></i>จัดการผู้ใช้งาน</a>
                        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="{{url("/manage/hospitals")}}"><i class="mdi mdi-account icon"></i>จัดการโรงพยาบาล</a>
                        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="{{url("/history")}}"><i class="mdi mdi-history icon"></i>ประวัติการใช้งาน</a>
                    @endif
                    <a class="list-group-item list-group-item-action list-group-item-light p-3" href="{{url("/manage/cases")}}"><i class="mdi mdi-check-all icon"></i>จัดการ case</a>

                    <div  class="footer" >
                        <p>Copyright &#169; 2021 </p>
                    </div>
                </div>
            </div>
            <div id="page-content-wrapper">
                @yield('content')
            </div>
        </div>




</div>
@endguest

</body>
</html>

<script>
    $(window).bind('beforeunload',function () {
        $("#loading-wrapper").show();
    })
    $(window).on("unload",function () {
        $("#loading-wrapper").hide();
    })
    $(window).ready(function ($) {
        $("#loading-wrapper").hide();
    })

    $(document).ready(function() {
        $('.select2').select2();
    });

    $(function(){
        $('.datepicker').datepicker({
            lang:'th',
            format: 'dd/mm/yyyy',
            endDate:new Date(),
            inputs: $('.actual_range'),
            yearOffset:543,
        });

    });

</script>
