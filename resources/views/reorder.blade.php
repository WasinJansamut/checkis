@extends('layouts.app')

@section('content')
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @else
            <h1>สั่งตรวจใหม่</h1>
            <form action="{{ route('addReport') }}" method="post" class="mb-3">
                @csrf
                @if (Auth::user()->type > 0)
                    <div class="row d-flex align-items-center">
                        <div class="col-12 col-sm-6 col-md-6 mb-3">
                            <select class="custom-select form-control select2" tabindex="-1" aria-hidden="true"
                                name="hosp" type="text" id="hosp-select">
                                <option selected="selected" value="">=== กรุณาเลือกโรงพยาบาล ===</option>
                                @foreach ($hosps as $hosp)
                                    <option value={{ $hosp->hospcode }}>{{ $hosp->full_name }} ({{ $hosp->hospcode }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 mb-3">
                            <select class="custom-select form-control select2" name="area_code" id="area_code-select">
                                <option selected="selected" value="">=== กรุณาเลือกเขต ===</option>
                                @foreach ($area_codes as $area_code)
                                    <option value="{{ $area_code }}">
                                        {{ $area_code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
                <div class="row d-flex align-items-center">
                    <div class="col-12 col-sm-12 col-md-12 mb-3">
                        <div class="input-group input-daterange date d-flex align-items-center">
                            <input class="form-control" data-provide="datepicker" id="start_date" data-date-language="th-th"
                                class="form-control" name="start_date" value="{{ $start }}">
                            <span class="input-group-text" id="inputGroup-sizing-sm">ถึง</span>
                            <input data-provide="datepicker" id="end_date" data-date-language="th-th" class="form-control"
                                name="end_date" value="{{ $end }}">
                            <span class="ms-1">
                                <small>(มากสุดไม่เกิน 90 วัน)</small>
                            </span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-magnifying-glass me-1"></i>
                            ประมวลผล
                        </button>
                    </div>
                </div>

                {{-- <input class="form-control" type="text" onfocus="(this.type='date')" max="{{ $now }}">
                <div class="form-group">
                    <select class="custom-select form-control" style="width: 100px;float: left" name="month"
                        type="text" required>
                        <option selected value="">เดือน</option>
                        <option value="1">มกราคม</option>
                        <option value="2">กุมภาพันธ์</option>
                        <option value="3">มีนาคม</option>
                        <option value="4">เมษายน</option>
                        <option value="5">พฤษภาคม</option>
                        <option value="6">มิถุนายน</option>
                        <option value="7">กรกฎาคม</option>
                        <option value="8">สิงหาคม</option>
                        <option value="9">กันยายน</option>
                        <option value="10">ตุลาคม</option>
                        <option value="11">พฤศจิกายน</option>
                        <option value="12">ธันวาคม</option>
                    </select>
                    <select class="custom-select form-control" style="width: 100px;float: left"name="year" type="text"
                        required>
                        <option selected value="">ปี</option>
                        <option value="2021">2564</option>
                        <option value="2020">2563</option>
                        <option value="2019">2562</option>
                        <option value="2018">2561</option>
                        <option value="2017">2560</option>
                    </select>
                    <div class="row">
                        <div class="col-3">
                            <div class="input-group date d-flex align-items-center" id="datepicker">
                                <input type="text" class="form-control" id="date" />
                                <span class="input-group-append">
                                    <span class="input-group-text bg-light d-block">
                                        <i class="mdi mdi-calendar icon"></i>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <div class="input-group date d-flex align-items-center" id="datepicker">
                                <input type="text" class="form-control" id="date" />
                                <span class="input-group-append">
                                    <span class="input-group-text bg-light d-block">
                                        <i class="mdi mdi-calendar icon"></i>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </form>

            {{-- <a href="{{ route('check') }}">
                <button type="button" class="btn btn-outline-success" style="margin-top: 20px">
                    ตรวจงาน ทั้งหมด
                </button>
            </a>
            @error('month')
                <div class="alert alert-danger" role="alert">
                    กรุณาเลือก <strong>เดือน</strong>
                </div>
            @enderror
            @error('year')
                <div class="alert alert-danger" role="alert">
                    กรุณาเลือก <strong>ปี</strong>
                </div>
            @enderror
            @error('hosp')
                <div class="alert alert-danger" role="alert">
                    กรุณาเลือก <strong>โรงพยาบาล</strong>
                </div>
            @enderror --}}

            @if (Session::has('time range too long'))
                <div class="alert alert-warning mb-3" role="alert" style="width: 50%">
                    <span>start_date / end_date ต้องห่างกันไม่เกิน 1 ปี</span>
                </div>
            @endif

            @if (Session::has('wrong hosp'))
                <div class="alert alert-warning mb-3" role="alert" style="width: 50%">
                    <span>โรงพยาบาล และ เขต ไม่ตรงกัน</span>
                </div>
            @endif

            @if (Session::has('incomplete value'))
                <div class="alert alert-warning mb-3" role="alert" style="width: 50%">
                    <span>เลือกข้อมูลอย่างน้อย 1 ตัวเลือก</span>
                </div>
            @endif

            @if (Session::has('no data'))
                <div class="alert alert-warning mb-3" role="alert" style="width: 50%">
                    <span>
                        ไม่พบข้อมูล
                        <strong>ของช่วงนี้</strong>
                        ที่ต้องการค้นหา กรุณาเลือก
                        <strong>ช่วง</strong>
                        อีกครั้ง
                    </span>
                </div>
            @endif

            @if (Session::has('duplicate job'))
                <div class="alert alert-warning mb-3" role="alert" style="width: 30%">
                    <span>มีรายการสั่งตรวจใหม่นี้อยู่แล้ว กรุณาใหม่เลือกอีกครั้ง</span>
                </div>
            @endif
        @endif
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#start_date').datepicker({
                language: 'th-th',
                format: 'dd/mm/yyyy'
            });
            $('#end_date').datepicker({
                language: 'th-th',
                format: 'dd/mm/yyyy'
            });
            $("#start_date").on('change', function() {
                const startDate = moment($('#start_date').val(), 'DD/MM/YYYY').format('YYYY-MM-DD');
                const endDate = moment($('#end_date').val(), 'DD/MM/YYYY').format('YYYY-MM-DD');
                let d = new Date(startDate);
                let e = new Date(endDate);



                var m_day = d.getDate();
                if (m_day < 10) {
                    m_day = "0" + m_day;
                }
                var m_month = d.getMonth() + 1;
                if (m_month < 10) {
                    m_month = "0" + m_month;
                }
                var m_year = d.getFullYear();
                var min = m_year + '-' + m_month + '-' + m_day;
                document.getElementById("end_date").setAttribute("min", min);
                if (e < d) {
                    $('#end_date').val(moment(min, 'YYYY-MM-DD').format('DD/MM/YYYY'));
                }

                d.setDate(d.getDate() + 90);


                if (e > d || e == null) {
                    var e_day = d.getDate();
                    if (e_day < 10) {
                        e_day = "0" + e_day;
                    }
                    var e_month = d.getMonth() + 1;
                    if (e_month < 10) {
                        e_month = "0" + e_month;
                    }
                    var e_year = d.getFullYear();
                    var e_max = e_year + '-' + e_month + '-' + e_day;
                    $('#end_date').val(moment(e_max, 'YYYY-MM-DD').format('DD/MM/YYYY'));
                }



                var curr_day = d.getDate();
                if (curr_day < 10) {
                    curr_day = "0" + curr_day;
                }
                var curr_month = d.getMonth() + 1;
                if (curr_month < 10) {
                    curr_month = "0" + curr_month;
                }
                var curr_year = d.getFullYear();

                var end = curr_year + '-' + curr_month + '-' + curr_day;

                document.getElementById("end_date").setAttribute("max", end);
            })
        });




        $('#hosp-select').on('change', function() {
            $.ajax({
                url: "{!! url('reorder/sort/hosp?hosp=') !!}" + this.value,
                type: "GET",
                contentType: "application/json",
                success: function(data) {
                    $('#area_code-select').empty().append(data);

                }
            });
        })

        $('#area_code-select').on('change', function() {
            $.ajax({
                url: "{!! url('reorder/sort/area_code?code=') !!}" + this.value,
                type: "GET",
                contentType: "application/json",
                success: function(data) {
                    $('#hosp-select').empty().append(data);
                    console.log(data)

                }
            });
        })
    </script>
@endsection
