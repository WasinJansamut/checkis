
    <table>
        <thead>
        <tr>
            <th>#</th>
            <th style="width: 400px">ชื่อ case</th>
            <th>จำนวน</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $row)
            <tr>
                <td>{{ $row->number }}</td>
                <td>{{ $row->name }}</td>
                <td>{{ $row->count }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

