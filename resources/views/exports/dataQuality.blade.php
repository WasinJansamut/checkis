
<table>
    <tbody>
    <tr>
        <td colspan="2">จำนวนข้อมูลทั้งหมด</td>
        <td>{{$datas->count}}</td>
    </tr>

    <tr>
        <td colspan="3" style="color:grey">ความถูกต้อง</td>
    </tr>
    <tr>
        <td colspan="2" style="width: 250px">จำนวนข้อมูลที่มี ความถูกต้อง ครบ</td>
        <td>{{$datas->type_1}}</td>
    </tr>
    <tr>
        <td colspan="2" style="width: 250px">จำนวนข้อมูลที่มี ความถูกต้อง ไม่ครบ</td>
        <td>{{$datas->count - $datas->type_1}}</td>
    </tr>
    <tr>
        <td colspan="2" style="width: 250px">ร้อยละ ความถูกต้อง ของข้อมูล</td>
        <td>{{$datas->type_1P}} %</td>
    </tr>


    <tr>
        <td colspan="3" style="color:gray">ความสมบูรณ์</td>
    </tr>
    <tr>
        <td colspan="2" style="width: 250px">จำนวนข้อมูลที่มี ความสมบูรณ์ ครบ</td>
        <td>{{$datas->type_2}}</td>
    </tr>
    <tr>
        <td colspan="2" style="width: 250px">จำนวนข้อมูลที่มี ความสมบูรณ์ ไม่ครบ</td>
        <td>{{$datas->count - $datas->type_2}}</td>
    </tr>
    <tr>
        <td colspan="2" style="width: 250px">ร้อยละ ความสมบูรณ์ ของข้อมูล</td>
        <td>{{$datas->type_2P}} %</td>
    </tr>


    <tr>
        <td colspan="3" style="color:gray">ความเที่ยงตรง</td>
    </tr>
    <tr>
        <td colspan="2" style="width: 250px">จำนวนข้อมูลที่มี ความเที่ยงตรง ครบ</td>
        <td>{{$datas->type_3}}</td>
    </tr>
    <tr>
        <td colspan="2" style="width: 250px">จำนวนข้อมูลที่มี ความเที่ยงตรง ไม่ครบ</td>
        <td>{{$datas->count - $datas->type_3}}</td>
    </tr>
    <tr>
        <td colspan="2" style="width: 250px">ร้อยละ ความเที่ยงตรง ของข้อมูล</td>
        <td>{{$datas->type_3P}} %</td>
    </tr>


    <tr>
        <td colspan="3" style="color:gray">ความตรงตามกาล</td>
    </tr>
    <tr>
        <td colspan="2" style="width: 250px">จำนวนข้อมูลที่มี ความตรงตามกาล ครบ</td>
        <td>{{$datas->type_4}}</td>
    </tr>
    <tr>
        <td colspan="2" style="width: 250px">จำนวนข้อมูลที่มี ความตรงตามกาล ไม่ครบ</td>
        <td>{{$datas->count - $datas->type_4}}</td>
    </tr>
    <tr>
        <td colspan="2" style="width: 250px">ร้อยละ ความตรงตามกาล ของข้อมูล</td>
        <td>{{$datas->type_4P}} %</td>
    </tr>



    <tr>
        <td colspan="3" style="color:gray">ความเป็นเอกลักษณ์</td>
    </tr>
    <tr>
        <td colspan="2" style="width: 250px">จำนวนข้อมูลที่มี ความเป็นเอกลักษณ์ ครบ</td>
        <td>{{$datas->type_5}}</td>
    </tr>
    <tr>
        <td colspan="2" style="width: 250px">จำนวนข้อมูลที่มี ความเป็นเอกลักษณ์ ไม่ครบ</td>
        <td>{{$datas->count - $datas->type_5}}</td>
    </tr>
    <tr>
        <td colspan="2" style="width: 250px">ร้อยละ ความเป็นเอกลักษณ์ ของข้อมูล</td>
        <td>{{$datas->type_5P}} %</td>
    </tr>


    <tr>
        <td colspan="3" style="color:gray">ความแม่นยำ</td>
    </tr>
    <tr>
        <td colspan="2" style="width: 250px">จำนวนข้อมูลที่มี ความแม่นยำ ครบ</td>
        <td>{{$datas->type_6}}</td>
    </tr>
    <tr>
        <td colspan="2" style="width: 250px">จำนวนข้อมูลที่มี ความแม่นยำ ไม่ครบ</td>
        <td>{{$datas->count - $datas->type_6}}</td>
    </tr>
    <tr>
        <td colspan="2" style="width: 250px">ร้อยละ ความแม่นยำ ของข้อมูล</td>
        <td>{{$datas->type_6P}} %</td>
    </tr>

    </tbody>
</table>

