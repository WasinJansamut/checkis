@extends('layouts.app')

@section('content')
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        <h1 style="font-weight: 500">จัดการ Cases</h1>
        <table class="table table-bordered table-hover " data-toggle="data-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th scope="col">Case Name</th>
                    <th scope="col" style="width: 120px">Error Type</th>
                    @if (user_info('user_level_code') == 'MOPH' && user_info('user_type') == 'SUPER ADMIN')
                        <th scope="col" style="width:100px;text-align: center">การจัดการ</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @if ($cases->isNotEmpty())
                    @foreach ($cases as $case)
                        <tr>
                            <td class="text-center">{{ $case->number }}</td>
                            <td style="text-align: left">{{ $case->name ?? '' }}
                                <div>
                                    <small>
                                        - {{ $case->description ?? '' }}
                                    </small>
                                </div>

                            </td>
                            <td style="text-align: center">{{ $case->_error_type->name ?? '' }}</td>

                            @if (user_info('user_level_code') == 'MOPH' && user_info('user_type') == 'SUPER ADMIN')
                                <td>
                                    <a href="{{ route('update_case_controller', $case->id) }}">
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

        {{-- @if ($cases->links()->paginator->hasPages())
            <div class="pagination pagination-sm justify-content-center">
                {{ $cases->links() }}
            </div>
        @endif --}}

        <hr class="mt-5 mb-4">
        <h2>รายการแก้ไข Cases</h2>
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
