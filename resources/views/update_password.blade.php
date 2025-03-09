@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 style="font-weight: 500">แก้ไขรหัสผ่าน</h1>
        <form method="post" action="{{ route('submit_new_password') }}">
            @csrf
            <div class="form-group">
                <label>รหัสผ่านใหม่</label>
                <input type="password" class="form-control mb-3" placeholder="Enter password" name="password">

                <label>ยืนยันรหัสผ่าน</label>
                <input type="password" class="form-control mb-3" placeholder="Confirm Password" name="password_confirmation">

                <label>แก้ไขชื่อ</label>
                <input type="text" class="form-control mb-3" placeholder="Enter Firstname"
                    @if ($user->firstname != null) value="{{ $user->firstname }}" @endif name="firstname">

                <label>แก้ไขนามสกุล</label>
                <input type="text" class="form-control mb-3" placeholder="Enter Lastname"
                    @if ($user->lastname != null) value="{{ $user->lastname }}" @endif name="lastname">

                <label>แก้ไขเบอร์โทร</label>
                <input type="tel" pattern="[0]{1}[2,6,8,9]{1}[0-9]{8}" class="form-control mb-3"
                    placeholder="Enter Phone Number" @if ($user->phone != null) value="{{ $user->phone }}" @endif
                    name="phone">

                <label>แก้ไข Email</label>
                <input type="email" class="form-control mb-3" placeholder="Enter Email"
                    @if ($user->email != null) value="{{ $user->email }}" @endif name="email">
            </div>

            <input name="id" hidden value="{{ $user->id }}">
            <button type="submit" class="btn btn-success">
                <i class="fa-solid fa-floppy-disk me-1"></i>
                ยืนยัน
            </button>
        </form>


        @error('password')
            <div class="alert alert-danger m-2" role="alert" style="width: 50%">
                <span><strong>รหัสผ่านไม่ตรงกัน</strong></span>
            </div>
        @enderror
    </div>
@endsection
