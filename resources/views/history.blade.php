@extends('layouts.app')

@section('content')
    <style>
        .detail-scroll tbody tr {
            max-height: 100px !important;
            overflow: scroll !important;
        }
    </style>
    <div class="container">
        <h1>ประวัติการใช้งาน</h1>
        <div style="width: 100%;overflow:scroll ">
            <table class="table table-bordered table-hover detail-scroll">
                <thead>
                    <tr>
                        <th scope="col">user_id</th>
                        <th scope="col">Name</th>
                        <th scope="col">hospital</th>
                        <th scope="col">ip</th>
                        <th scope="col">action</th>
                        <th scope="col">detail</th>
                        <th scope="col">time</th>

                    </tr>
                </thead>
                <tbody>
                    @if ($rows->isNotEmpty())
                        @foreach ($rows as $row)
                            <tr>
                                <td>{{ $row->user_id }}</td>
                                <td style="min-width: 150px">{{ $row->firstname }} {{ $row->lastname }}</td>
                                <td style="min-width: 250px">{{ $row->hospital }}</td>
                                <td>{{ $row->ip }}</td>
                                <td>{{ $row->action }}</td>
                                <td>
                                    <div style="max-height: 250px;min-width: 300px;overflow: auto">
                                        @if (isset($row->detail))
                                            @foreach ((array) $row->detail as $key => $value)
                                                {{ "$key: $value" }}<br>
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </div>

                                </td>
                                <td style="min-width: 120px">{{ $row->created_at->addyear(543)->format('d/m/Y H:i:s') }}
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <h2>No History Found</h2>
                    @endif

                </tbody>
            </table>
        </div>
    </div>

    @if ($rows->links()->paginator->hasPages())
        <div class="mt-4 p-4 box has-text-centered">
            {{ $rows->links() }}
        </div>
    @endif

@endsection
