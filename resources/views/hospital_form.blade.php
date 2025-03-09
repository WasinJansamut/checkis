@extends('layouts.app')

@section('content')
    <div class="container">
        @if ($hospital != null)
            <h1 style="font-weight: 500">แก้ไขโรงพยาบาล</h1>
            <form method="post" action="{{ route('update_hospital') }}">
                @csrf
                <div class="form-group">
                    <label>health_district</label>
                    <input type="number" class="form-control mb-3"
                        @if ($hospital->health_district != null) value="{{ $hospital->health_district }}" @endif
                        name="health_district">

                    <label>code_district</label>
                    <input type="number" class="form-control mb-3"
                        @if ($hospital->code_district != null) value="{{ $hospital->code_district }}" @endif
                        name="code_district">

                    <label>hospcode9</label>
                    <input type="number" class="form-control mb-3"
                        @if ($hospital->hospcode9 != null) value="{{ $hospital->hospcode9 }}" @endif name="hospcode9">

                    <label>hospcode</label>
                    <input type="number" class="form-control mb-3"
                        @if ($hospital->hospcode != null) value="{{ $hospital->hospcode }}" @endif name="hospcode">

                    <label>name</label>
                    <input type="text" class="form-control mb-3"
                        @if ($hospital->name != null) value="{{ $hospital->name }}" @endif name="name">

                    <label>full_name</label>
                    <input type="text" class="form-control mb-3"
                        @if ($hospital->full_name != null) value="{{ $hospital->full_name }}" @endif name="full_name">

                    <label>type</label>
                    <input type="text" class="form-control mb-3"
                        @if ($hospital->type != null) value="{{ $hospital->type }}" @endif name="type">

                    <label>type_code</label>
                    <input type="text" class="form-control mb-3"
                        @if ($hospital->type_code != null) value="{{ $hospital->type_code }}" @endif name="type_code">

                    <label>bed_amount</label>
                    <input type="number" class="form-control mb-3"
                        @if ($hospital->bed_amount != null) value="{{ $hospital->bed_amount }}" @endif name="bed_amount">

                    <label>province_code</label>
                    <input type="number" class="form-control mb-3"
                        @if ($hospital->province_code != null) value="{{ $hospital->province_code }}" @endif
                        name="province_code">

                    <label>province_name</label>
                    <input type="text" class="form-control mb-3"
                        @if ($hospital->province_name != null) value="{{ $hospital->province_name }}" @endif
                        name="province_name">

                    <label>district_id</label>
                    <input type="number" class="form-control mb-3"
                        @if ($hospital->district_id != null) value="{{ $hospital->district_id }}" @endif name="district_id">

                    <label>district_name</label>
                    <input type="text" class="form-control mb-3"
                        @if ($hospital->district_name != null) value="{{ $hospital->district_name }}" @endif
                        name="district_name">

                    <label>sub_district_id</label>
                    <input type="number" class="form-control mb-3"
                        @if ($hospital->sub_district_id != null) value="{{ $hospital->sub_district_id }}" @endif
                        name="sub_district_id">

                    <label>sub_district_name</label>
                    <input type="text" class="form-control mb-3"
                        @if ($hospital->sub_district_name != null) value="{{ $hospital->sub_district_name }}" @endif
                        name="sub_district_name">

                    <label>moo</label>
                    <input type="number" class="form-control mb-3"
                        @if ($hospital->moo != null) value="{{ $hospital->moo }}" @endif name="moo">

                    <label>area_code</label>
                    <input type="number" class="form-control mb-3"
                        @if ($hospital->area_code != null) value="{{ $hospital->area_code }}" @endif name="area_code">

                    <label>area_gorverment</label>
                    <input type="number" class="form-control mb-3"
                        @if ($hospital->area_gorverment != null) value="{{ $hospital->area_gorverment }}" @endif
                        name="area_gorverment">

                    <label>hosptype</label>
                    <input type="text" class="form-control mb-3"
                        @if ($hospital->hosptype != null) value="{{ $hospital->hosptype }}" @endif name="hosptype">
                </div>
                @if ($hospital->hospcode != null)
                    <input name="id" hidden value="{{ $hospital->hospcode }}">
                @endif
                <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-floppy-disk me-1"></i>
                    ยืนยัน
                </button>
            </form>
        @else
            <h1 style="font-weight: 500">เพิ่มโรงพยาบาล</h1>
            <form method="post" action="{{ route('create_hospital') }}">
                @csrf
                <div class="form-group">
                    <label>health_district</label>
                    <input type="number" class="form-control mb-3" name="health_district">

                    <label>code_district</label>
                    <input type="number" class="form-control mb-3" name="code_district">

                    <label>hospcode9</label>
                    <input type="number" class="form-control mb-3" name="hospcode9">

                    <label>hospcode</label>
                    <input type="number" class="form-control mb-3" name="hospcode">

                    <label>name</label>
                    <input type="text" class="form-control mb-3" name="name">

                    <label>full_name</label>
                    <input type="text" class="form-control mb-3" name="full_name">

                    <label>type</label>
                    <input type="text" class="form-control mb-3" name="type">

                    <label>type_code</label>
                    <input type="text" class="form-control mb-3" name="type_code">

                    <label>bed_amount</label>
                    <input type="number" class="form-control mb-3" name="bed_amount">

                    <label>province_code</label>
                    <input type="number" class="form-control mb-3" name="province_code">

                    <label>province_name</label>
                    <input type="text" class="form-control mb-3" name="province_name">

                    <label>district_id</label>
                    <input type="number" class="form-control mb-3" name="district_id">

                    <label>district_name</label>
                    <input type="text" class="form-control mb-3" name="district_name">

                    <label>sub_district_id</label>
                    <input type="number" class="form-control mb-3" name="sub_district_id">

                    <label>sub_district_name</label>
                    <input type="text" class="form-control mb-3" name="sub_district_name">

                    <label>moo</label>
                    <input type="number" class="form-control mb-3" name="moo">

                    <label>area_code</label>
                    <input type="number" class="form-control mb-3" name="area_code">

                    <label>area_gorverment</label>
                    <input type="number" class="form-control mb-3" name="area_gorverment">

                    <label>hosptype</label>
                    <input type="text" class="form-control mb-3" name="hosptype">
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-floppy-disk me-1"></i>
                    ยืนยัน
                </button>
            </form>
        @endif
    </div>
@endsection
