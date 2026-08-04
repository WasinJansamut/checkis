<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แจ้งเตือนรายการสั่งตรวจใหม่ {{ $details['start_date'] }} - {{ $details['end_date'] }}</title>
</head>

<body style="margin:0;padding:0;background:#f3f7f4;color:#263a30;font-family:Arial,'Noto Sans Thai',sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f3f7f4;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:640px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 8px 28px rgba(0,72,39,.10);">
                    <tr>
                        <td style="padding:24px 32px;background:#006637;color:#ffffff;">
                            <div style="font-size:13px;font-weight:700;letter-spacing:.08em;opacity:.78;">IS - CHECKING</div>
                            <div style="margin-top:6px;font-size:23px;font-weight:700;line-height:1.35;">แจ้งเตือนรายการสั่งตรวจใหม่</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px 32px 12px;">
                            <p style="margin:0 0 5px;font-size:16px;line-height:1.7;">เรียน {{ $details['hosp_name'] }}</p>
                            <p style="margin:0;font-size:16px;line-height:1.7;color:#50665a;">ระบบได้ดำเนินการสั่งตรวจข้อมูลเรียบร้อยแล้ว</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:12px 32px 24px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #dce9e0;border-radius:10px;overflow:hidden;">
                                <tr>
                                    <td style="padding:12px 16px;background:#eff7f1;color:#527060;font-size:14px;width:110px;">ช่วงข้อมูล</td>
                                    <td style="padding:12px 16px;color:#183b29;font-size:14px;font-weight:700;">{{ $details['start_date'] }} - {{ $details['end_date'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 16px;border-top:1px solid #e7f0e9;background:#eff7f1;color:#527060;font-size:14px;">วันที่ประมวลผล</td>
                                    <td style="padding:12px 16px;border-top:1px solid #e7f0e9;color:#183b29;font-size:14px;font-weight:700;">{{ $details['start_time'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 16px;border-top:1px solid #e7f0e9;background:#eff7f1;color:#527060;font-size:14px;">ประมวลผลโดย</td>
                                    <td style="padding:12px 16px;border-top:1px solid #e7f0e9;color:#183b29;font-size:14px;font-weight:700;">{{ $details['name'] ?: 'ไม่มีชื่อผู้ดูแล' }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 32px 24px;">
                            <div style="padding:13px 16px;background:#e8f5ec;border-radius:9px;color:#17633b;font-size:14px;font-weight:700;">✓ สถานะงาน: ทำรายการเสร็จสิ้น</div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:0 32px 32px;">
                            <a href="{{ url('/retrospective/report') }}" style="display:inline-block;padding:13px 24px;background:#006637;border-radius:8px;color:#ffffff;font-size:15px;font-weight:700;text-decoration:none;">ดูรายละเอียดผลการตรวจ</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 32px;background:#f7faf8;border-top:1px solid #e5eee8;color:#788a7f;font-size:12px;line-height:1.6;">
                            ขอแสดงความนับถือ<br>ระบบ IS - CHECKING
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
