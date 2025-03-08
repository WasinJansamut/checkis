@extends('layouts.app')

@section('content')
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        <h1 style="font-weight: 500">จัดการ Cases</h1>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 100px" scope="col">Case No.</th>
                    <th scope="col">Case Name</th>
                    <th scope="col" style="width: 120px">Error Type</th>
                    @if (\Illuminate\Support\Facades\Auth::user()->type == 1)
                        <th scope="col" style="width:100px;text-align: center">การจัดการ</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @if ($cases->isNotEmpty())
                    @foreach ($cases as $case)
                        <tr>
                            <td>{{ $case->number }}</td>
                            <td style="text-align: left">{{ $case->name }}</td>
                            <td style="text-align: center">{{ $error_type[$case->errorType] }}</td>

                            @if (Auth::user()->type == 1)
                                <td><a href="{{ url("/update/case/{$case->id}") }}}">
                                        <button type="button" class="btn btn-outline-warning">
                                            <i class="fa-solid fa-pen-to-square me-1"></i>
                                            แก้ไข
                                        </button>
                                    </a>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @else
                    <div>
                        <h2>ไม่พบ Cases</h2>
                    </div>
                @endif
            </tbody>
        </table>

        @if ($cases->links()->paginator->hasPages())
            <div class="mt-4 p-4 box has-text-centered text-center">
                {{ $cases->links() }}
            </div>
        @endif

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th scope="col">รายการแก้ไข</th>
                    <th scope="col">วันที่และเวลา ที่ทำการแก้ไข</th>
                    <th scope="col">Error Type</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr>
                        <td> Case NO. {{ $log['detail']['number'] }} </td>
                        <td>{{ $log['updated_at']->addyear(543)->format('d-m-Y H:i:s') }}</td>
                        <td>{{ $error_type[$log['detail']['errorType']] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
