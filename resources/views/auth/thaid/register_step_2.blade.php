<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Register from ThaID') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- Font Awesome Css -->
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free-6.4.0-web/css/all.min.css') }}" />

    <!-- Sweetalert2 -->
    <link href="{{ asset('assets/sweetalert2/css/sweetalert2.min.css') }}" rel="stylesheet" />

    <!-- Select2 -->
    <link href="{{ asset('assets/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/select2/css/select2-bootstrap-5-theme.min.css.css') }}" rel="stylesheet" />

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap');

        * {
            font-family: "Noto Sans Thai", sans-serif;
        }

        @media (max-width: 767px) {
            .btn-custom {
                /* ให้เต็มจอเมื่อเป็นจอเล็ก */
                width: 100%;
            }
        }

        #btn_logout {
            position: absolute !important;
            top: 10px !important;
            right: 10px !important;
        }

        .container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>

<body>
    <a id="btn_logout" href="{{ route('logout') }}" class="btn btn-sm btn-danger"
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fa-solid fa-arrow-right-from-bracket me-1"></i>
        ออกจากระบบ
    </a>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <div class="row m-0 align-items-center justify-content-center vh-100">
        <div class="col-sm-12 col-md-9 col-lg-7 col-xl-5">
            <div class="text-center mb-3">
                <img src="{{ asset('storage/imgs/logo.svg') }}" class="mb-3" height="150">
                <h4>{{ config('app.name') }}</h4>
            </div>
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">กรอกข้อมูลให้ครบถ้วน</h5>
                </div>
                <div class="card-body">
                    <form id="form" action="{{ route('thaid.update_register_step_2') }}" method="post">
                        @method('POST')
                        @csrf
                        <div class="col-12 mb-3">
                            <label for="hospcode">สถานบริการ / หน่วยงาน</label>
                            <span class="text-danger">*</span>
                            <select name="hospcode" id="hospcode"
                                class="form-select select2 @error('hospcode') is-invalid @enderror" required>
                                <option value="">=== กรุณาเลือก ===</option>
                                @foreach ($hospitals as $hospital)
                                    {{-- <option value="{{ $hospital->id }}"
                                        {{ old('hospcode', Auth::user()->hospcode) == $hospital->hospcode ? 'selected' : '' }}>
                                        {{ $hospital->full_name }}
                                    </option> --}}
                                    <option value="{{ $hospital->hospcode }}"
                                        {{ old('hospcode') == $hospital->hospcode ? 'selected' : '' }}>
                                        {{ $hospital->full_name ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('hospcode')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-success btn-custom">
                                <i class="fa-solid fa-floppy-disk me-1"></i>
                                ตกลง
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-12 text-center">
                <p class="mb-0">
                    หากไม่มีโรงพยาบาลของท่าน กรุณาติดต่อ Line:
                    <a href="https://lin.ee/qzzSV3f" target="_blank" class="text-decoration-none">@rtiddc</a> หรือ
                    <span id="line_qr_code" role="button" class="text-primary ms-1">
                        <i class="fa-solid fa-qrcode"></i> QR Code
                    </span>
                </p>
            </div>
        </div>
    </div>
</body>

<script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}" defer></script>

<!-- Sweetalert2 -->
<script src="{{ asset('assets/sweetalert2/js/sweetalert2.all.min.js') }}"></script>

<!-- Select2 -->
<script src="{{ asset('assets/select2/js/bootstrap.bundle.min.js') }}"
    integrity="sha384-sqIwnO0uI2Yo5qjwGXu2CgQyxB4G2c5xH9beSHsQuUC6wJO3aMSszc7u" crossorigin="anonymous"></script>
<script src="{{ asset('assets/select2/js/select2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
        });
        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });
    });
</script>

<script>
    $(document).ready(function() {
        $("#form").submit(function(e) {
            e.preventDefault(); // ยกเลิกการส่งฟอร์มถ้าผู้ใช้กด 'ยกเลิก'

            Swal.fire({
                title: "แน่ใจหรือไม่?",
                text: "คุณต้องการยืนยันการส่งข้อมูลใช่หรือไม่?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3a57e8",
                cancelButtonColor: "#c03221",
                confirmButtonText: '<i class="fa-solid fa-check me-1"></i> ยืนยัน',
                cancelButtonText: '<i class="fa-solid fa-xmark me-1"></i> ยกเลิก',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "สำเร็จ!",
                        text: "ยืนยันการส่งข้อมูลเรียบร้อย",
                        icon: "success",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });

                    setTimeout(function() {
                        // ปลด event แล้ว submit ฟอร์มจริง
                        $("#form").off("submit").submit();
                    }, 1000);
                }
            });
        });
    });
</script>

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

</html>
