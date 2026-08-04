<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'IS-Checking') }}</title>

    <!-- Font Awesome Css -->
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free-6.4.0-web/css/all.min.css') }}" />

    <!-- Sweetalert2 -->
    <link href="{{ asset('assets/sweetalert2/css/sweetalert2.min.css') }}" rel="stylesheet" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> --}}


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

    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/dataTables/css/jquery.dataTables.min.css') }}">

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

        #wrapper {
            display: flex;
        }

        #sidebar-wrapper {
            min-width: 240px;
            max-width: 240px;
            background-color: #fff;
            border-right: 1px solid #dee2e6;
        }

        #page-content-wrapper {
            flex-grow: 1;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
            overflow-x: auto;
        }

        svg {
            /* width: 50px; */
        }

        .container {
            margin-left: 0;
            margin-right: 0;
            width: 100%;
        }

        .list-group-item.active {
            background-color: #006637 !important;
            color: #ffffff !important;
            border-color: #006637 !important;
        }

        #dashboardSubmenu .list-group-item {
            padding-left: 2.5rem !important;
        }
    </style>

    <style>
        .highcharts-container,
        .highcharts-root {
            color-scheme: light !important;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('assets/css/custom-ui.css') }}">

    @yield('style')

</head>

<body>
    <div id="app">
        @include('waiting')
        <nav class="navbar navbar-expand-md navbar-light shadow-sm sticky-top app-navbar">
            <div class="container-fluid px-4">
                <a class="navbar-brand app-navbar__brand" href="{{ route('present_report') }}">
                    <span class="app-navbar__brand-icon"><i class="fa-solid fa-shield-heart"></i></span>
                    <span>
                        <strong>IS-CHECKING</strong>
                        <small>ระบบตรวจสอบคุณภาพข้อมูล</small>
                    </span>
                </a>
                @if (user_info())
                    <button id="mobile-sidebar-toggle" class="navbar-toggler btn btn-light bg-opacity-10 border border-white border-opacity-25 text-white rounded-3 shadow-sm ms-auto px-3 py-2" type="button"
                        aria-controls="sidebar-wrapper"
                        aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <i class="fa-solid fa-bars fs-5"></i>
                    </button>
                @endif

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    {{-- <ul class="navbar-nav me-auto">
                    </ul> --}}

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto d-flex align-items-center gap-2">
                        <!-- Authentication Links -->
                        @if (Route::has('login'))
                            <a style="color: #ffffff" class="nav-link" href="{{ route('login') }}">
                                <button type="button" class="btn btn-success">
                                    เข้าสู่ระบบ
                                </button>
                            </a>
                        @endif
                        @if (user_info())
                            <div class="app-navbar__user">
                                <span class="app-navbar__user-icon"><i class="fa-solid fa-hospital"></i></span>
                                <span>
                                    <strong>{{ user_info('name') ?? '-' }}</strong>
                                    <small>{{ user_info('hosp_name') ?? '-' }}</small>
                                </span>
                            </div>
                            <button id="btn_logout" class="btn btn-outline-light btn-sm btn-logout app-navbar__logout">
                                <i class="fa-solid fa-arrow-right-from-bracket me-1"></i>
                                ออกจากระบบ
                            </button>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
        @if (user_info())
            <div class="d-flex" id="wrapper">
                <div class="border-end bg-white collapse" id="sidebar-wrapper">
                    <div class="list-group list-group-flush">
                        <img style="display: block;" src="{{ asset('storage/imgs/logo.svg') }}">
                        <div class="d-md-none px-3 pb-3 mb-2 border-bottom text-center">
                            <div class="fw-bold text-dark">
                                <i class="fa-solid fa-hospital me-1"></i>{{ user_info('name') ?? '-' }}
                            </div>
                            <small class="text-muted d-block">{{ user_info('hosp_name') ?? '-' }}</small>
                            <button type="button" class="btn btn-outline-danger btn-sm btn-logout w-100 mt-2">
                                <i class="fa-solid fa-arrow-right-from-bracket me-1"></i>ออกจากระบบ
                            </button>
                        </div>
                        <a class="list-group-item list-group-item-action list-group-item-light p-3 {{ Request::is('') || Request::is('present/report') ? 'active' : '' }}"
                            href="{{ route('present_report') }}">
                            <i class="fa-solid fa-house fa-fw icon"></i>
                            หน้าหลัก
                        </a>
                        <a class="list-group-item list-group-item-action list-group-item-light p-3 {{ Request::is('reorder') ? 'active' : '' }}"
                            href="{{ route('reorder') }}">
                            <i class="fa-solid fa-pen-to-square fa-fw icon"></i>
                            สั่งตรวจใหม่
                        </a>
                        <a class="list-group-item list-group-item-action list-group-item-light p-3 {{ Request::is('retrospective/report') || Request::is('search/report*') ? 'active' : '' }}"
                            href="{{ route('retrospective_report') }}">
                            <i class="fa-solid fa-folder-open fa-fw icon"></i>
                            ผลการตรวจสอบ
                        </a>
                        {{-- <a class="list-group-item list-group-item-action list-group-item-light p-3"
                            href="{{ route('update_password_controller', Auth::user()->id) }}">
                            <i class="fa-solid fa-user-gear fa-fw icon"></i>
                            แก้ไขข้อมูลส่วนตัว
                        </a> --}}

                        {{-- only super admin can manage users --}}
                        {{-- @if (session('user_info.user_level_code', null) == 'MOPH' && session('user_info.user_type', null) == 'SUPER ADMIN')
                            <a class="list-group-item list-group-item-action list-group-item-light p-3 {{ Request::is('manage/users') || Request::is('edit/user*') || Request::is('search/user*') ? 'active' : '' }}"
                                href="{{ route('manage_users') }}">
                                <i class="fa-solid fa-users fa-fw icon"></i>
                                จัดการผู้ใช้งาน
                            </a>
                            <a class="list-group-item list-group-item-action list-group-item-light p-3 {{ Request::is('manage/hospitals') || Request::is('edit/hospital*') || Request::is('search/hospital*') ? 'active' : '' }}"
                                href="{{ route('manage_hospitals') }}">
                                <i class="fa-solid fa-hospital fa-fw icon"></i>
                                จัดการโรงพยาบาล
                            </a>
                            <a class="list-group-item list-group-item-action list-group-item-light p-3 {{ Request::is('history') ? 'active' : '' }}"
                                href="{{ route('history') }}">
                                <i class="fa-solid fa-clock-rotate-left fa-fw icon"></i>
                                ประวัติการใช้งาน
                            </a>
                        @endif --}}
                        {{-- <a class="list-group-item list-group-item-action list-group-item-light p-3 {{ Request::is('dashboard/hospital-21-variables') ? 'active' : '' }}"
                            href="{{ route('dashboard.hospital_21_variables') }}">
                            <i class="fa-solid fa-clock-rotate-left fa-fw icon"></i>
                            Dashboard 21 ตัวแปร
                        </a>
                        <a class="list-group-item list-group-item-action list-group-item-light p-3 {{ Request::is('dashboard/hospital-overview') ? 'active' : '' }}"
                            href="{{ route('dashboard.hospital_overview') }}">
                            <i class="fa-solid fa-clock-rotate-left fa-fw icon"></i>
                            Dashboard ติดตามการส่งข้อมูล
                        </a> --}}
                        <!-- Dashboard (หัวข้อหลัก) -->
                        <a id="dashboard-submenu-toggle" class="list-group-item list-group-item-action list-group-item-light p-3 d-flex justify-content-between align-items-center"
                            href="#dashboardSubmenu" role="button" aria-expanded="{{ Request::is('dashboard*') ? 'true' : 'false' }}" aria-controls="dashboardSubmenu">
                            <span><i class="fa-solid fa-table-columns fa-fw me-1"></i> Dashboard</span>
                            <i class="fa-solid fa-chevron-down fa-fw icon"></i>
                        </a>

                        <!-- Submenu -->
                        <div class="collapse {{ Request::is('dashboard*') ? 'show' : '' }}" id="dashboardSubmenu">
                            <a class="list-group-item list-group-item-action list-group-item-light p-3 {{ Request::is('dashboard/hospital-21-variables') ? 'active' : '' }}" style="padding-left: 1.5rem !important;"
                                href="{{ route('dashboard.hospital_21_variables') }}">
                                <i class="fa-solid fa-magnifying-glass-chart fa-fw icon"></i>
                                สรุป 21 ตัวแปร
                            </a>
                            <a class="list-group-item list-group-item-action list-group-item-light p-3 {{ Request::is('dashboard/hospital-overview') ? 'active' : '' }}" style="padding-left: 1.5rem !important;"
                                href="{{ route('dashboard.hospital_overview') }}">
                                <i class="fa-solid fa-chart-line fa-fw icon"></i>
                                ติดตามการส่งข้อมูล
                            </a>
                        </div>
                        <a class="list-group-item list-group-item-action list-group-item-light p-3 {{ Request::is('manage/cases') ? 'active' : '' }}"
                            href="{{ route('manage_cases') }}">
                            <i class="fa-solid fa-list-check fa-fw icon"></i>
                            จัดการ case
                        </a>
                        <a class="list-group-item list-group-item-action list-group-item-light p-3"
                            href="https://connect.moph.go.th/pher-plus/">
                            <i class="fa-solid fa-arrow-left icon"></i>
                            กลับสู่ Pher Plus
                        </a>
                    </div>
                </div>
                <div id="page-content-wrapper" class="d-flex flex-column" style="flex-grow: 1;">
                    <div class="p-4">
                        <div class="page-context">
                            <span class="page-context__product">IS-CHECKING</span>
                            <span class="page-context__divider"></span>
                            <span>ระบบตรวจสอบคุณภาพข้อมูล</span>
                        </div>
                        <main class="app-page flex-grow-1">
                            @yield('content')
                        </main>
                    </div>
                    <footer class="border-top bg-white mt-auto py-3">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 px-3 small">
                            <div class="d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle p-2">
                                    <i class="fa-solid fa-headset"></i>
                                </span>
                                <div>
                                    <span class="text-muted">พบปัญหาในการใช้งาน?</span>
                                    <a href="https://lin.ee/qzzSV3f" target="_blank" class="fw-semibold text-decoration-none ms-1">ติดต่อ Line @rtiddc</a>
                                    <button id="line_qr_code" type="button" class="btn btn-link btn-sm p-0 ms-1 align-baseline text-decoration-none">
                                        <i class="fa-solid fa-qrcode me-1"></i>QR Code
                                    </button>
                                </div>
                            </div>
                            <span class="text-muted">Copyright &copy; 2021</span>
                        </div>
                    </footer>
                </div>
            </div>
        @else
            <div id="page-content-wrapper" class="d-flex flex-column" style="flex-grow: 1;">
                <div class="p-4">
                    <div class="page-context">
                        <span class="page-context__product">IS-CHECKING</span>
                        <span class="page-context__divider"></span>
                        <span>ระบบตรวจสอบคุณภาพข้อมูล</span>
                    </div>
                    <main class="app-page flex-grow-1">
                        @yield('content')
                    </main>
                </div>
                <footer class="border-top bg-white mt-auto py-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 px-3 small">
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle p-2">
                                <i class="fa-solid fa-headset"></i>
                            </span>
                            <div>
                                <span class="text-muted">พบปัญหาในการใช้งาน?</span>
                                <a href="https://lin.ee/qzzSV3f" target="_blank" class="fw-semibold text-decoration-none ms-1">ติดต่อ Line @rtiddc</a>
                                <button id="line_qr_code" type="button" class="btn btn-link btn-sm p-0 ms-1 align-baseline text-decoration-none">
                                    <i class="fa-solid fa-qrcode me-1"></i>QR Code
                                </button>
                            </div>
                        </div>
                        <span class="text-muted">Copyright &copy; 2021</span>
                    </div>
                </footer>
            </div>
        @endif
    </div>
</body>

<!-- jQuery -->
{{-- <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<!-- รวม jQuery UI (ต้องใช้ jQuery UI เพื่อใช้ datepicker) -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<!-- Sweetalert2 -->
<script src="{{ asset('assets/sweetalert2/js/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('assets/js/swal2-helpers.js') }}"></script>

{{-- <!-- bootstrap-datepicker thai extension -->
<script type="text/javascript" src="{{ asset('js/datepicker_th/bootstrap-datepicker-thai.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/datepicker_th/locales/bootstrap-datepicker.th.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/momentjs/moment-with-locales.min.js') }}"></script> --}}

<!-- Bootstrap Datepicker -->
<script src="{{ asset('assets/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/bootstrap-datepicker/js/bootstrap-datepicker-thai.js') }}"></script>
<script src="{{ asset('assets/bootstrap-datepicker/js/locales/bootstrap-datepicker.th.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>

{{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}

<!-- Select2 -->
{{-- <script src="{{ asset('assets/select2/js/bootstrap.bundle.min.js') }}"
    integrity="sha384-sqIwnO0uI2Yo5qjwGXu2CgQyxB4G2c5xH9beSHsQuUC6wJO3aMSszc7u" crossorigin="anonymous"></script> --}}
<script src="{{ asset('assets/select2/js/bootstrap.bundle.min.js') }}" crossorigin="anonymous"></script>
<script src="{{ asset('assets/select2/js/select2.min.js') }}"></script>
<script>
    $.fn.select2.defaults.set('theme', 'bootstrap-5');
</script>

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
</script>

<script>
    $(document).ready(function() {
        $('.select2').each(function() {
            const isMultiple = $(this).prop('multiple'); // ตรวจสอบว่ามี attribute multiple หรือไม่
            const closeOnSelectValue = isMultiple ? false : true; // ถ้าเป็น multiple ให้ปิด closeOnSelect
            $(this).select2({
                theme: 'bootstrap-5',
                width: '100%',
                allowClear: true,
                placeholder: "=== กรุณาเลือก ===",
                closeOnSelect: !isMultiple // ถ้า multiple ให้ false, ถ้าไม่ใช่ให้ true
            });
        });
        $(document).on('select2:open', () => {
            $('.select2-container--open .select2-search__field').last().trigger('focus');
        });

        // $('#select2-checkbox').select2({
        //     closeOnSelect: false,
        //     placeholder: "Select items",
        //     allowClear: true,
        //     templateResult: function(data) {
        //         console.log('templateResult data:', data);

        //         if (!data.id) {
        //             return data.text;
        //         }
        //         var $result = $(
        //             '<span><input type="checkbox" style="margin-right: 5px;" /> ' + data.text + '</span>'
        //         );
        //         return $result;
        //     },
        //     templateSelection: function(data) {
        //         return data.length + " selected";
        //     }
        // });

        // $('#select2-checkbox').on('select2:select select2:unselect', function(e) {
        //     // update checkbox manually (because select2 doesn't manage checkboxes)
        //     var selectedVals = $(this).val() || [];
        //     $('#select2-checkbox').find('option').each(function() {
        //         var optionVal = $(this).val();
        //         var isSelected = selectedVals.includes(optionVal);
        //         var checkbox = $('.select2-results__option').find('input[value="' + optionVal + '"]');
        //         checkbox.prop('checked', isSelected);
        //     });
        // });
    });
</script>
<script>
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

<script>
    $(".btn-logout").on("click", function() {
        AppSwal.confirmLogout().then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('logout') }}";
            }
        });
    });
</script>

<!-- DataTables -->
<script src="{{ asset('assets/dataTables/js/jquery.dataTables.min.js') }}"></script>

<script>
    $(document).ready(function() {
        if ($('[data-toggle="data-table"]').length) {
            $('[data-toggle="data-table"]').each(function() {
                const $table = $(this);
                const pageLength = $table.data('page-length') || 10; // ใช้ค่าใน data-page-length หรือค่า default = 10

                $table.DataTable({
                    // "dom": 'B<"row align-items-center"<"col-md-6" l> <"col-md-6"  f>><"table-responsive border-bottom my-3" rt><"row align-items-center" <"col-md-6" i><"col-md-6" p>><"clear">',
                    "dom": 'B<"row align-items-center"<"col-md-6" l> <"col-md-6" f>>' +
                        '<"table-responsive border-bottom w-100 mb-1" rt>' +
                        '<"row align-items-center"<"col-md-6" i><"col-md-6" p>>' +
                        '<"clear">',
                    "aaSorting": [],
                    "pageLength": pageLength,
                    "lengthMenu": [
                        [-1, 5, 10, 30, 50, 100, 200, 500],
                        ['All', 5, 10, 30, 50, 100, 200, 500]
                    ],
                    "buttons": [{
                            extend: 'excelHtml5',
                            className: 'btn-export',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'print', // เพิ่มปุ่ม Print
                            className: 'btn-print',
                            exportOptions: {
                                columns: ':visible' // ให้แสดงเฉพาะคอลัมน์ที่มองเห็น
                            }
                        }
                    ],
                    language: {
                        url: "{{ asset('assets/dataTables/lang/th.json') }}"
                    }
                });
            });
        }
    });
</script>

<script>
    $(document).on('show.bs.modal', '.modal', function() {
        $('.select2-hidden-accessible').select2('close');
    });

    $(document).on('click', '#mobile-sidebar-toggle', function() {
        const $sidebar = $('#sidebar-wrapper');
        const isOpen = $sidebar.toggleClass('show').hasClass('show');
        $(this).attr('aria-expanded', isOpen);
    });

    $(document).on('click', '#dashboard-submenu-toggle', function(event) {
        event.preventDefault();
        const $submenu = $('#dashboardSubmenu');
        const isOpen = $submenu.toggleClass('show').hasClass('show');
        $(this).attr('aria-expanded', isOpen);
    });
</script>

@yield('script')

</html>
