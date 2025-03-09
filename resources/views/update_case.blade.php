@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 style="font-weight: 500">แก้ไข Case</h1>
        <form method="post" action="{{ route('submit_new_case') }}">
            @csrf
            <div class="mb-3">
                {{--            <label>number</label> --}}
                {{--            <input type="text" class="form-control mb-3"  value="{{$case->number}}" placeholder="Enter password" name="number" required> --}}

                <label>name</label>
                <textarea class="form-control mb-3" placeholder="Confirm Password" name="name" style="width: 500px; height: 200px;"
                    required>{{ $case->name }}</textarea>

                <label>errorType</label><br>
                <select class="custom-select form-control select2" style="width: 250px"name="error_type" type="text"
                    required>
                    {{-- <option value="">error type</option> --}}
                    <option @if ($case->errorType == 1) selected @endif value="1">ความถูกต้อง (Accuracy)</option>
                    <option @if ($case->errorType == 2) selected @endif value="2">ความสมบูรณ์ (Completeness)
                    </option>
                    <option @if ($case->errorType == 3) selected @endif value="3">ความเที่ยงตรง (Consistency)
                    </option>
                    <option @if ($case->errorType == 4) selected @endif value="4">ความตรงตามกาล (Timeliness)
                    </option>
                    <option @if ($case->errorType == 5) selected @endif value="5">ความเป็นเอกลักษณ์ (Uniqueness)
                    </option>
                    <option @if ($case->errorType == 6) selected @endif value="6">ความแม่นยำ (Orderliness)
                    </option>
                </select>
                {{-- <input type="text" class="form-control mb-3"  value="{{$case->errorType}}" placeholder="Enter password" name="error_type" required> --}}
            </div>

            <input name="id" hidden value="{{ $case->id }}">
            <button type="submit" class="btn btn-success">
                <i class="fa-solid fa-floppy-disk me-1"></i>
                ยืนยัน
            </button>
        </form>

        @if (Session::has('duplicated case'))
            this case was created
        @endif

        @error('number')
            <div class="alert alert-danger m-2" role="alert" style="width: 50%">
                <span><strong>Number ต้องเป็น ตัวเลขเท่านั้น</strong></span>
            </div>
        @enderror
    </div>
@endsection
