<style>

</style>
    <table>

        <tbody>

            <tr>
                <th style="width: 100px">ชื่อโรงพยาบาล:</th>
                <td style="width: 100px">{{$job->getHospName->full_name}}</td>
            </tr>
            <tr>
                <th style="width: 100px">รหัสโรงพยาบาล</th>
                <td style="width: 100px">{{ $job->hosp }}</td>
            </tr>
            <tr>
                <th style="width: 100px">วันที่ start_date - end_date</th>
                <td style="width: 100px">{{ $job->start_date->addyear(543)->format('d-m-Y')}} - {{$job->end_date->addyear(543)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <th style="width: 100px">วันที่สั่งตรวจข้อมูล</th>
                <td style="width: 100px">{{$job->start_time->addyear(543)->format('d-m-Y H:i:s')}}</td>
            </tr>
            <tr>
                <th style="width: 100px">จำนวนข้อมูล</th>
                <td style="width: 100px">{{$job->count}}</td>
            </tr>

            <tr>
                <th style="width: 100px">ชื่อผู้รับผิดชอบ</th>
                <td style="width: 100px">{{$job->getUser->firstname}} {{$job->getUser->lastname}}</td>
            </tr>

        </tbody>
    </table>

