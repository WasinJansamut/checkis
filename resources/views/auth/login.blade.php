<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Login') }}</title>

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

        .btn_login {
            color: white;
            background-color: #111372;
            transition: transform .3s;
        }

        .btn_login:hover {
            color: white;
            background-color: #0f1187;
            transform: scale(1.05);
        }

        .alert_danger {
            background-color: #f8d7da;
            border: 2px solid #f5c2c7;
            color: #842029;
            padding: .8rem 1rem;
        }
    </style>
</head>

<body>
    <div class="row m-0 align-items-center vh-100">
        <div class="justify-content-center text-center">
            <img class="mb-3 mx-auto" style="width: 120px; height: 120px;" src="{{ asset('storage/imgs/logo.svg') }}">
            <h2 class="mb-3" style="font-size: 36px; color: #748080;">
                <b>IS - CHECKING</b>
            </h2>
            {{-- <form method="POST" action="{{ route('login') }}" class="mb-3">
                    @csrf
                    <input id="username" type="text" class="form-control @error('username') is-invalid @enderror mb-3"
                        name="username" value="{{ old('username') }}" required autocomplete="username"
                        placeholder="ชื่อผู้ใช้งาน">

                    @error('username')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror mb-3"
                        name="password" required autocomplete="current-password" placeholder="รหัสผ่าน">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">
                            {{ __('เข้าสู่ระบบ') }}
                        </button>
                    </div>
                </form> --}}

            <a id="login_thaid" href="{{ route('login.thaid') }}" class="btn py-3 px-5 fs-3 mb-3 btn_login">
                <img src="{{ asset('storage/imgs/thaid.png') }}" class="rounded me-1" width="52">
                <b>
                    เข้าสู่ระบบด้วย Tha<span style="color: #fdb904;">ID</span>
                </b>
            </a>

            <div class="col-12 text-center">
                <p>
                    หากเข้าสู่ระบบไม่ได้ กรุณาติดต่อ Line:
                    <a href="https://lin.ee/qzzSV3f" target="_blank">@rtiddc</a>
                </p>
                <p>
                    <img src="https://rti.moph.go.th/pher-plus/report/public/assets/images/qrcode_line.png"
                        alt="QR Code Line" style="width:150px; height:auto;">
                </p>
            </div>
        </div>
    </div>
</body>

<!-- jQuery 3.7.1 -->
<script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<!-- Sweetalert2 -->
<script src="{{ asset('assets/sweetalert2/js/sweetalert2.all.min.js') }}"></script>

@if (Session::has('danger'))
    <script>
        Swal.fire({
            title: "เกิดข้อผิดพลาด",
            text: "{{ Session::get('danger') }}",
            icon: "error",
            confirmButtonText: 'ตกลง',

        });
    </script>
@endif

<script>
    $("#login_thaid").on("click", function() {
        Swal.fire({
            title: 'ระบบกำลังเชื่อมต่อกับ ThaID',
            html: '<h4 class="mb-2 text-gray">กรุณารอสักครู่...</h4>',
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            padding: "0px 15px 20px",
            didOpen: () => {
                Swal.showLoading();
            },
        });
    });
</script>

</html>
