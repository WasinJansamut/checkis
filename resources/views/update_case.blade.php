@extends('layouts.app')

@section('content')
    <style>
        .input{
            width: 250px;
            margin: 5px;
        }

    </style>

    <h1 class="roboto" style="font-weight: 500">แก้ไข case</h1>


    <form method="post" action="{{url("/update/case")}}">
        @csrf
        <div class="form-group">
{{--            <label class="roboto">number</label>--}}
{{--            <input type="text" class="form-control input"  value="{{$case->number}}" placeholder="Enter password" name="number" required>--}}

            <label class="roboto">name</label>
            <textarea class="form-control input" placeholder="Confirm Password" name="name" style="width: 500px;height: 200px;" required>{{$case->name}}</textarea>

            <label class="roboto">errorType</label>
            <select class="custom-select form-control" style="width: 250px"name="error_type" type="text" required>
{{--                <option value="">error type</option>--}}
                <option @if($case->errorType == 1) selected @endif value="1">ความถูกต้อง (Accuracy)</option>
                <option @if($case->errorType == 2) selected @endif value="2">ความสมบูรณ์ (Completeness)</option>
                <option @if($case->errorType == 3) selected @endif value="3">ความเที่ยงตรง (Consistency)</option>
                <option @if($case->errorType == 4) selected @endif value="4">ความตรงตามกาล (Timeliness)</option>
                <option @if($case->errorType == 5) selected @endif value="5">ความเป็นเอกลักษณ์ (Uniqueness)</option>
                <option @if($case->errorType == 6) selected @endif value="6">ความแม่นยำ (Orderliness)</option>

            </select>
{{--            <input type="text" class="form-control input"  value="{{$case->errorType}}" placeholder="Enter password" name="error_type" required>--}}
        </div>

        <input name="id" hidden value="{{$case->id}}">
        <button type="submit" class="btn btn-success" style="margin-top: 5px" >ยืนยัน</button>
    </form>

    @if(Session::has('duplicated case'))
        this case was created
    @endif

    @error('number')
    <div class="alert alert-danger m-2" role="alert" style="width: 50%">
        <span><strong>Number ต้องเป็น ตัวเลขเท่านั้น</strong></span>
    </div>
    @enderror


@endsection
