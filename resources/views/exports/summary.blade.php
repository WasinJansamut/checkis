<table>
    <thead>
        <tr>
            <th style="width: 45px">#</th>
            <th style="width: 460px">ชื่อ case</th>
            <th style="width: 215px">ตัวแปรที่ใช้ตรวจสอบ</th>
            <th style="width: 60px">จำนวน</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $row)
            <tr>
                <td>{{ $row->number }}</td>
                <td>{{ $row->name }}</td>
                <td>{{ $row->check_fields }}</td>
                <td>{{ $row->count }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
