@extends('layouts.app')

@section('content')
    <style>
        .input{
            width: 250px;
            margin: 5px;
        }
    </style>


    <h1 class="roboto" style="font-weight: 500">แก้ไขรหัสผ่าน</h1>

    <form method="post" action="{{ url('/update/password') }}">
        @csrf
        <div class="form-group">
            <label class="roboto">รหัสผ่านใหม่</label>
            <input type="password" class="form-control input"  placeholder="Enter password" name="password">

            <label class="roboto">ยืนยันรหัสผ่าน</label>
            <input type="password" class="form-control input" placeholder="Confirm Password" name="password_confirmation">

            <label class="roboto">แก้ไขชื่อ</label>
            <input type="text" class="form-control input" placeholder="Enter Firstname" @if($user->firstname != null ) value="{{$user->firstname}}" @endif name="firstname">

            <label class="roboto">แก้ไขนามสกุล</label>
            <input type="text" class="form-control input" placeholder="Enter Lastname" @if($user->lastname != null ) value="{{$user->lastname}}" @endif name="lastname">

            <label class="roboto">แก้ไขเบอร์โทร</label>
            <input type="tel" pattern="[0]{1}[2,6,8,9]{1}[0-9]{8}" class="form-control input" placeholder="Enter Phone Number" @if($user->phone != null ) value="{{$user->phone}}" @endif name="phone">

            <label class="roboto">แก้ไข Email</label>
            <input type="email" class="form-control input" placeholder="Enter Email" @if($user->email != null ) value="{{$user->email}}" @endif name="email">
        </div>

        <input name="id" hidden value="{{$user->id}}">
        <button type="submit" class="btn btn-success" style="margin-top: 5px" >ยืนยัน</button>
    </form>


    @error('password')
    <div class="alert alert-danger m-2" role="alert" style="width: 50%">
        <span><strong>รหัสผ่านไม่ตรงกัน</strong></span>
    </div>
    @enderror


@endsection
