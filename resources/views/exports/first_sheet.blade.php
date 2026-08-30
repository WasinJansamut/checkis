<style>

</style>
<table>
    <tbody>
        <tr>
            <th style="width: 160px">ชื่อโรงพยาบาล:</th>
            <td>{{ $job->getHospName->full_name ?? '' }}</td>
        </tr>
        <tr>
            <th>รหัสโรงพยาบาล</th>
            <td>{{ $job->hosp ?? '' }}</td>
        </tr>
        <tr>
            <th>วันที่ start_date - end_date</th>
            <td>
                {{ $job->start_date->addyear(543)->format('d-m-Y') }} -
                {{ $job->end_date->addyear(543)->format('d-m-Y') }}
            </td>
        </tr>
        <tr>
            <th>วันที่สั่งตรวจข้อมูล</th>
            <td>{{ $job->start_time->addyear(543)->format('d-m-Y H:i:s') }}</td>
        </tr>
        <tr>
            <th>จำนวนข้อมูล</th>
            <td>{{ $job->count ?? '' }}</td>
        </tr>
        <tr>
            <th>ชื่อผู้รับผิดชอบ</th>
            <td>{{ $job->getUser->firstname ?? '' }} {{ $job->getUser->lastname ?? '' }}</td>
        </tr>
    </tbody>
</table>
