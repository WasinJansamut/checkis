<!DOCTYPE html>
<html>
<head>
    <title>แจ้งเตือนรายการสั่งตรวจใหม่ {{ $details['start_date'] }} - {{$details['end_date']}}</title>
</head>
<body>
<p>เรียน {{$details['hosp_name']}}</p>
<h1>แจ้งเตือนรายการสั่งตรวจใหม่ {{ $details['start_date'] }} - {{$details['end_date']}}</h1>
<p>ระบบได้ทำการสั่งตรวจใหม่ {{ $details['start_date'] }} - {{$details['end_date']}}</p>
<p>วันที่ประมวลผล {{$details['start_time']}}</p>
@if($details['name'])
    <p>ประมวลผลโดย: {{$details['name']}} ฯลฯ</p>
@else
    <p>ประมวลผลโดย: ไม่มีชื่อผู้ดูแล</p>
@endif
<p>สถานะงาน "ทำรายการเสร็จสิ้น"</p>
<p>สามารถดูรายละเอียดงานได้ที่ -> <a href="{{url('/retrospective/report')}}">(Link)</a></p>
<br>
<p>ขอแสดงความนับถือ</p>
</body>
</html>
