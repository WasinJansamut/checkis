{{-- @extends('layouts.app') --}}

{{-- @section('content') --}}
<!doctype html>
<center lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Login') }}</title>

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        <style>
            .roboto {
                text-align: center;
                font-family: 'Roboto', sans-serif;
            }

            .container {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                font-family: 'Roboto', sans-serif;
            }
        </style>

    </head>
    <div class="container col-4">
        <center>
            {{-- <div class="row justify-content-center"> --}}
            <img style="width: 94px;height: 94px;display: block;margin: 10px auto"
                src="{{ asset('storage/imgs/logo.svg') }}">
            <h2 class="roboto" style="font-weight: 700;font-size: 36px;color: #748080;margin-bottom: 50px">IS - CHECKING
            </h2>
            {{-- <div class="col-md-8"> --}}
            {{-- <div class="card"> --}}

            {{-- <div class="card-body"> --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- <div class="row mb-3"> --}}
                {{-- <label for="username" class="col-md-4 col-form-label text-md-right">{{ __('Username') }}</label> --}}

                {{-- <div class="col-md-6"> --}}
                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror"
                    name="username" value="{{ old('username') }}" required autocomplete="username"
                    placeholder="ชื่อผู้ใช้งาน">

                @error('username')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

                &nbsp;
                {{-- </div> --}}
                {{-- </div> --}}

                {{-- <div class="row mb-3"> --}}
                {{-- <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label> --}}

                {{-- <div class="col-md-6"> --}}
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    name="password" required autocomplete="current-password" placeholder="รหัสผ่าน">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

                &nbsp;
                {{-- </div> --}}
                {{-- </div> --}}

                {{-- <div class="row mb-3"> --}}
                {{-- <div class="col-md-6 offset-md-4"> --}}
                {{-- <div class="form-check"> --}}
                {{-- <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}> --}}

                {{-- <label class="form-check-label" for="remember"> --}}
                {{-- {{ __('Remember Me') }} --}}
                {{-- </label> --}}
                {{-- </div> --}}
                {{-- </div> --}}
                {{-- </div> --}}

                {{-- <div class="row mb-0"> --}}
                {{-- <div class="col-md-8 offset-md-4"> --}}
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">
                        {{ __('เข้าสู่ระบบ') }}
                    </button>
                </div>

                {{-- @if (Route::has('password.request')) --}}
                {{-- <a class="btn btn-link" href="{{ route('password.request') }}"> --}}
                {{-- {{ __('Forgot Your Password?') }} --}}
                {{-- </a> --}}
                {{-- @endif --}}
                {{-- </div> --}}
                {{-- </div> --}}
            </form>
            {{-- </div> --}}
            {{-- </div> --}}
            {{-- </div> --}}
            {{-- </div> --}}

            <div class="col-12 text-center mt-5">
                <p>
                    หากเข้าสู่ระบบไม่ได้ กรุณาติดต่อ Line: <a href="https://lin.ee/qzzSV3f" target="_blank">@rtiddc</a>
                </p>
                <p>
                    <img src="https://rti.moph.go.th/pher-plus/report/public/assets/images/qrcode_line.png"
                        alt="QR Code Line" style="width:150px; height:auto;">
                </p>
            </div>
        </center>

    </div>

    </html>
    {{-- @endsection --}}
