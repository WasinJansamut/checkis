@extends('layouts.app')

@section('content')
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        <h1 style="font-weight: 500">จัดการผู้ใช้งาน</h1>
        <form action="{{ route('search_user') }}" method="GET" class="mb-3">
            <select class="custom-select form-control select2" tabindex="-1" aria-hidden="true" name="search" type="text"
                required>
                <option selected="selected" value="">โรงพยาบาล</option>
                @foreach ($usersAll as $user)
                    <option @if ($username == $user->username) selected @endif value={{ $user->username }}>
                        {{ $user->name }} ({{ $user->username }})</option>
                @endforeach
            </select>
            <button class="btn btn-secondary" type="submit">
                <i class="fa-solid fa-magnifying-glass me-1"></i>
                ค้นหา
            </button>
        </form>
        @if (Session::has('no data'))
            <div class="alert alert-warning m-2" role="alert" style="width: 30%;">
                <span>ไม่พบข้อมูล <strong>ผู้ใช้งาน</strong> ที่ค้นหา</span>
            </div>
            <br>
        @endif

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 120px" scope="col">รหัสโรงพยาบาล</th>
                    <th scope="col">ชื่อโรงพยาบาล</th>
                    {{-- <th scope="col" style="width: 150px">การจัดการ</th> --}}
                </tr>
            </thead>
            <tbody>

                @if ($users != null)
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->username }}</td>
                            <td style=" text-align: left">{{ $user->name }}</td>
                            {{-- <td>
                                <a href="{{ route('update_password_controller', $user->id) }}">
                                    <button type="button" class="btn btn-outline-warning">
                                        <i class="fa-solid fa-pen-to-square me-1"></i>
                                        แก้ไข
                                    </button>
                                </a>
                            </td> --}}
                        </tr>
                    @endforeach
                @else
                    <div>
                        <h2>No users found</h2>
                    </div>
                @endif

            </tbody>
        </table>


        @if ($users->links()->paginator->hasPages())
            <div class="mt-4 p-4 box has-text-centered text-center">
                {{ $users->links() }}
            </div>
        @endif

        @if (Session::has('success'))
            success
        @endif

    </div>
@endsection
