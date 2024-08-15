@extends('layouts.app')

@section('content')

    <div class="container">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        <h1 class="roboto" style="font-weight: 500">จัดการ Hospitals </h1>


        <form action="{{ url('/search/hospital') }}" method="GET">
            <select class="custom-select form-control select2" style="width: 300px;" tabindex="-1" aria-hidden="true" name="search" type="text" required>
                <option selected="selected" value="">โรงพยาบาล</option>
                @foreach($hospitals_all as $hospital)
                    <option @if($hospital_name == $hospital->full_name) selected @endif value={{$hospital->hospcode}}>{{$hospital->full_name}}</option>
                @endforeach
            </select>
            <button class="btn btn-secondary" style="margin-left: 20px" type="submit" ><i class="mdi mdi-magnify icon"></i>ค้นหา</button>
        </form>
            <a href="{{url("/edit/hospital")}}">
                <button class="btn btn-success m-3" style="margin-left: 20px" type="submit" ><i class="mdi mdi-plus-circle"></i> เพิ่มโรงพยาบาล</button>
            </a>

        <br>
            <div style="width: 100%;overflow:scroll ">
        <table class="table table-bordered table-hover">
            <thead>
            <tr>
                <th scope="col">health_district</th>
                <th scope="col" >code_district</th>
                <th scope="col">hospcode9</th>
                <th scope="col" >hospcode</th>
                <th scope="col" style="padding-right: 200px;">name</th>
                <th scope="col" style="padding-right: 200px;">full_name</th>
                <th scope="col" style="padding-right: 100px;">type</th>
                <th scope="col">type_code</th>
                <th scope="col">bed_amount</th>
                <th scope="col">province_code</th>
                <th scope="col">province_name</th>
                <th scope="col">district_id</th>
                <th scope="col">district_name</th>
                <th scope="col">sub_district_id</th>
                <th scope="col">sub_district_name</th>
                <th scope="col">moo</th>
                <th scope="col">area_code</th>
                <th scope="col">area_gorverment</th>
                <th scope="col" style="padding-right: 100px;">hosptype</th>
                <th scope="col">action</th>


            </tr>
            </thead>
            <tbody>

            @if($hospitals->isNotEmpty())
                @foreach($hospitals as $hospital)
                    <tr>
                        <td >{{$hospital->health_district}}</td>
                        <td >{{$hospital->code_district}}</td>
                        <td >{{$hospital->hospcode9}}</td>
                        <td >{{$hospital->hospcode}}</td>
                        <td style=" text-align: left">{{$hospital->name}}</td>
                        <td style=" text-align: left">{{$hospital->full_name}}</td>
                        <td >{{$hospital->type}}</td>
                        <td >{{$hospital->type_code}}</td>
                        <td >{{$hospital->bed_amount}}</td>
                        <td >{{$hospital->province_code}}</td>
                        <td >{{$hospital->province_name}}</td>
                        <td >{{$hospital->district_id}}</td>
                        <td >{{$hospital->district_name}}</td>
                        <td >{{$hospital->sub_district_id}}</td>
                        <td >{{$hospital->sub_district_name}}</td>
                        <td >{{$hospital->moo}}</td>
                        <td >{{$hospital->area_code}}</td>
                        <td >{{$hospital->area_gorverment}}</td>
                        <td >{{$hospital->hosptype}}</td>
                        <td>
                            <a href="{{url("/edit/hospital/{$hospital->hospcode}")}}"><button type="button" class="btn btn-outline-warning">แก้ไข</button></a>
                            <a href="{{url("/delete/hospital/{$hospital->hospcode}")}}"><button type="button" class="btn btn-outline-danger">ลบ</button></a>
                        </td>



{{--                        <td><a href="{{url("/update/case/{$hospital->id}")}}}"><button type="button" class="btn btn-outline-warning"><i class="mdi mdi-pencil icon"></i>แก้ไข</button></a></td>--}}

                    </tr>
                @endforeach

            @else
                <div>
                    <h2>No Cases found</h2>
                </div>
            @endif

            </tbody>
        </table>
            </div>


        @if ($hospitals->links()->paginator->hasPages())
            <div class="mt-4 p-4 box has-text-centered text-center">
                {{ $hospitals->links() }}
            </div>
        @endif



    </div>
@endsection
