<table>
    <tbody>
        <tr>
            <td colspan="2" style="background-color: #33FF33; font-weight: bold;">จำนวนข้อมูลทั้งหมด</td>
            <td style="background-color: #33FF33; font-weight: bold;">{{ $datas->count }}</td>
        </tr>

        <tr>
            <td colspan="3" style="background-color: #99FFFF; font-weight: bold;">ความสมบูรณ์ (Completeness)</td>
        </tr>
        <tr>
            <td colspan="2" style="width: 250px">จำนวนข้อมูลที่มี ความสมบูรณ์ ครบ</td>
            <td>{{ $datas->type_1 }}</td>
        </tr>
        <tr>
            <td colspan="2" style="width: 250px">จำนวนข้อมูลที่มี ความสมบูรณ์ ไม่ครบ</td>
            <td>{{ $datas->count - $datas->type_1 }}</td>
        </tr>
        <tr>
            <td colspan="2" style="width: 250px">ร้อยละ ความสมบูรณ์ ของข้อมูล</td>
            <td>{{ $datas->type_1P }} %</td>
        </tr>


        <tr>
            <td colspan="3" style="background-color: #99FFFF; font-weight: bold;">ความสอดคล้อง (Consistency)</td>
        </tr>
        <tr>
            <td colspan="2" style="width: 250px">จำนวนข้อมูลที่มี ความสมบูรณ์ ครบ</td>
            <td>{{ $datas->type_2 }}</td>
        </tr>
        <tr>
            <td colspan="2" style="width: 250px">จำนวนข้อมูลที่มี ความสมบูรณ์ ไม่ครบ</td>
            <td>{{ $datas->count - $datas->type_2 }}</td>
        </tr>
        <tr>
            <td colspan="2" style="width: 250px">ร้อยละ ความสมบูรณ์ ของข้อมูล</td>
            <td>{{ $datas->type_2P }} %</td>
        </tr>

    </tbody>
</table>
