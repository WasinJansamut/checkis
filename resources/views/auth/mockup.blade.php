<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Mockup ระบบตรวจสอบ Token</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('assets/css/custom-ui.css') }}">
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
            padding: 50px;
        }

        .step {
            font-size: 1.5rem;
            margin: 20px 0;
            display: none;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        pre {
            background: #f8f8f8;
            padding: 10px;
            border-radius: 5px;
            text-align: left;
            display: inline-block;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let steps = document.querySelectorAll('.step');
            steps.forEach((el, i) => {
                setTimeout(() => {
                    el.style.display = 'block';
                    const spinner = el.querySelector('.spinner');
                    if (spinner) {
                        setTimeout(() => {
                            spinner.style.display = 'none';
                        }, 2800); // ซ่อน spinner ก่อนแสดงถัดไปเล็กน้อย
                    }
                }, 3000 * i);
            });
        });
    </script>
</head>

<body>
    <h1>Mockup: ระบบตรวจสอบ Token</h1>
    <p style="color: gray;">* นี่เป็นระบบ Mockup เท่านั้น การใช้งานจริงไม่มี UI สร้างขึ้นเพื่อให้เข้าใจร่วมกันถึง Process การทำงาน</p>
    <div class="step">
        <div class="spinner"></div>
        📡 กำลังส่ง Token: <strong>{{ $token }}</strong> ไปยังระบบหลัก...
    </div>
    <div class="step">
        <div class="spinner"></div>
        🔍 กำลังตรวจสอบ Token...
    </div>
    <div class="step">
        ✅ Token นี้ถูกต้อง<br>
        <pre>{
  "valid": true,
  "name": "นายทดสอบ ระบบแปลงToken",
  "role": "Developer",
  "organization": "กรมควบคุมโรค"
}</pre>

    </div>
</body>

</html>
