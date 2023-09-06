@extends('layouts.app')

@section('content')
    <style>
        .input{
            width: 250px;
            margin: 5px;
        }
    </style>





    @if($hospital != null)
        <h1 class="roboto" style="font-weight: 500">แก้ไขโรงพยาบาล</h1>
            <form method="post" action="{{url("/update/hospital")}}">
                @csrf
                <div class="form-group">
                    <label class="roboto">health_district</label>
                    <input type="number" class="form-control input" @if($hospital->health_district != null ) value="{{$hospital->health_district}}" @endif  name="health_district">

                    <label class="roboto">code_district</label>
                    <input type="number" class="form-control input" @if($hospital->code_district != null ) value="{{$hospital->code_district}}" @endif name="code_district">

                    <label class="roboto">hospcode9</label>
                    <input type="number" class="form-control input" @if($hospital->hospcode9 != null ) value="{{$hospital->hospcode9}}" @endif name="hospcode9">

                    <label class="roboto">hospcode</label>
                    <input type="number" class="form-control input" @if($hospital->hospcode != null ) value="{{$hospital->hospcode}}" @endif name="hospcode">

                    <label class="roboto">name</label>
                    <input type="text" class="form-control input" @if($hospital->name != null ) value="{{$hospital->name}}" @endif name="name">

                    <label class="roboto">full_name</label>
                    <input type="text" class="form-control input" @if($hospital->full_name != null ) value="{{$hospital->full_name}}" @endif name="full_name">

                    <label class="roboto">type</label>
                    <input type="text" class="form-control input" @if($hospital->type != null ) value="{{$hospital->type}}" @endif name="type">

                    <label class="roboto">type_code</label>
                    <input type="text" class="form-control input" @if($hospital->type_code != null ) value="{{$hospital->type_code}}" @endif name="type_code">

                    <label class="roboto">bed_amount</label>
                    <input type="number" class="form-control input" @if($hospital->bed_amount != null ) value="{{$hospital->bed_amount}}" @endif name="bed_amount">

                    <label class="roboto">province_code</label>
                    <input type="number" class="form-control input" @if($hospital->province_code != null ) value="{{$hospital->province_code}}" @endif name="province_code">

                    <label class="roboto">province_name</label>
                    <input type="text" class="form-control input" @if($hospital->province_name != null ) value="{{$hospital->province_name}}" @endif name="province_name">

                    <label class="roboto">district_id</label>
                    <input type="number" class="form-control input" @if($hospital->district_id != null ) value="{{$hospital->district_id}}" @endif name="district_id">

                    <label class="roboto">district_name</label>
                    <input type="text" class="form-control input" @if($hospital->district_name != null ) value="{{$hospital->district_name}}" @endif name="district_name">

                    <label class="roboto">sub_district_id</label>
                    <input type="number" class="form-control input" @if($hospital->sub_district_id != null ) value="{{$hospital->sub_district_id}}" @endif name="sub_district_id">

                    <label class="roboto">sub_district_name</label>
                    <input type="text" class="form-control input" @if($hospital->sub_district_name != null ) value="{{$hospital->sub_district_name}}" @endif name="sub_district_name">

                    <label class="roboto">moo</label>
                    <input type="number" class="form-control input" @if($hospital->moo != null ) value="{{$hospital->moo}}" @endif name="moo">

                    <label class="roboto">area_code</label>
                    <input type="number" class="form-control input" @if($hospital->area_code != null ) value="{{$hospital->area_code}}" @endif name="area_code">

                    <label class="roboto">area_gorverment</label>
                    <input type="number" class="form-control input" @if($hospital->area_gorverment != null ) value="{{$hospital->area_gorverment}}" @endif name="area_gorverment">

                    <label class="roboto">hosptype</label>
                    <input type="text" class="form-control input" @if($hospital->hosptype != null ) value="{{$hospital->hosptype}}" @endif name="hosptype">
                </div>

        @if($hospital->hospcode != NULL)
            <input name="id" hidden value="{{$hospital->hospcode}}">
        @endif

        @else
                    <h1 class="roboto" style="font-weight: 500">เพิ่มโรงพยาบาล</h1>
            <form method="post" action="{{url("/create/hospital")}}">
                @csrf
                <div class="form-group">
                    <label class="roboto">health_district</label>
                    <input type="number" class="form-control input" name="health_district">

                    <label class="roboto">code_district</label>
                    <input type="number" class="form-control input" name="code_district">

                    <label class="roboto">hospcode9</label>
                    <input type="number" class="form-control input" name="hospcode9">

                    <label class="roboto">hospcode</label>
                    <input type="number" class="form-control input" name="hospcode">

                    <label class="roboto">name</label>
                    <input type="text" class="form-control input"  name="name">

                    <label class="roboto">full_name</label>
                    <input type="text" class="form-control input"  name="full_name">

                    <label class="roboto">type</label>
                    <input type="text" class="form-control input"  name="type">

                    <label class="roboto">type_code</label>
                    <input type="text" class="form-control input" name="type_code">

                    <label class="roboto">bed_amount</label>
                    <input type="number" class="form-control input"  name="bed_amount">

                    <label class="roboto">province_code</label>
                    <input type="number" class="form-control input"  name="province_code">

                    <label class="roboto">province_name</label>
                    <input type="text" class="form-control input" name="province_name">

                    <label class="roboto">district_id</label>
                    <input type="number" class="form-control input" name="district_id">

                    <label class="roboto">district_name</label>
                    <input type="text" class="form-control input"  name="district_name">

                    <label class="roboto">sub_district_id</label>
                    <input type="number" class="form-control input" name="sub_district_id">

                    <label class="roboto">sub_district_name</label>
                    <input type="text" class="form-control input"  name="sub_district_name">

                    <label class="roboto">moo</label>
                    <input type="number" class="form-control input"  name="moo">

                    <label class="roboto">area_code</label>
                    <input type="number" class="form-control input"  name="area_code">

                    <label class="roboto">area_gorverment</label>
                    <input type="number" class="form-control input"  name="area_gorverment">

                    <label class="roboto">hosptype</label>
                    <input type="text" class="form-control input" name="hosptype">
            </div>

        @endif

        <button type="submit" class="btn btn-success" style="margin-top: 5px" >ยืนยัน</button>
    </form>



@endsection
