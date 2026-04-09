<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>กำลังเชื่อมต่อ PHER PLUS</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;700&display=swap');

        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #e9f7ef 0%, #f7fbf8 100%);
            font-family: "Sarabun", sans-serif;
            color: #124734;
        }

        .loading-card {
            width: min(92vw, 520px);
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 22px 60px rgba(4, 104, 57, 0.12);
            padding: 40px 28px;
            text-align: center;
        }

        .logo {
            width: 88px;
            margin-bottom: 18px;
        }

        .spinner-wrap {
            width: 74px;
            height: 74px;
            margin: 30px auto 0;
            border-radius: 50%;
            border: 12px solid #d7eee1;
            border-top-color: #046839;
            animation: spin 0.9s linear infinite;
        }

        .title {
            font-size: 1.65rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .subtitle {
            font-size: 1rem;
            line-height: 1.7;
            color: #426b5a;
            margin-bottom: 0;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="loading-card">
        <img src="{{ asset('storage/imgs/logo.svg') }}" alt="Logo" class="logo">
        <div class="title">กำลังเชื่อมต่อ PHER PLUS</div>
        <p class="subtitle">
            กรุณารอสักครู่ ระบบกำลังตรวจสอบสิทธิ์และเชื่อมต่อข้อมูลผู้ใช้งาน
        </p>
        <div class="spinner-wrap" aria-hidden="true"></div>

        <form id="auth-process-form" action="{{ route('process_auth_callback') }}" method="get"></form>

        <noscript>
            <div class="mt-4">
                <button type="submit" form="auth-process-form" class="btn btn-success">ดำเนินการต่อ</button>
            </div>
        </noscript>
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('auth-process-form').submit();
            }, 400);
        });
    </script>
</body>

</html>
