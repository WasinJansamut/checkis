@extends('layouts.app')
@section('content')
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @else
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title text-center mb-4">สั่งตรวจใหม่</h3>
                            <form action="{{ route('addReport') }}" method="post" class="mb-3">
                                @csrf
                                @if (session('user_info.user_level_code', null) != 'HOSP')
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <select class="custom-select form-control select2" name="hosp" id="hosp-select">
                                                <option selected value="">=== กรุณาเลือกโรงพยาบาล ===</option>
                                                @foreach ($hosps as $hosp)
                                                    <option value="{{ $hosp->off_id }}">
                                                        {{ $hosp->name }} ({{ $hosp->off_id }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <select class="custom-select form-control select2" name="area_code" id="area_code-select">
                                                <option selected value="">=== กรุณาเลือกเขต ===</option>
                                                @foreach ($area_codes as $area_code)
                                                    <option value="{{ $area_code }}">{{ $area_code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif

                                <div class="row align-items-center">
                                    <div class="col-md-8 mb-3">
                                        <div class="input-group input-daterange date">
                                            <input class="form-control" data-provide="datepicker" data-date-language="th-th"
                                                id="start_date" name="start_date" value="{{ $start }}">
                                            <span class="input-group-text">ถึง</span>
                                            <input class="form-control" data-provide="datepicker" data-date-language="th-th"
                                                id="end_date" name="end_date" value="{{ $end }}">
                                            <span class="ms-2">
                                                <small>(มากสุดไม่เกิน 90 วัน)</small>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3 text-end">
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fa-solid fa-magnifying-glass me-1"></i>
                                            ประมวลผล
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

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
