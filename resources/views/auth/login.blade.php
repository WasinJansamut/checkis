{{-- @extends('layouts.app') --}}

{{-- @section('content') --}}
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

            <a href="{{ route('login.thaid') }}" class="btn btn-primary py-3 px-5 fs-3 mb-3">
                <img src="{{ asset('storage/imgs/thaid.png') }}" class="rounded me-1" width="52">
                เข้าสู่ระบบด้วย ThaID
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

</html>
{{-- @endsection --}}
