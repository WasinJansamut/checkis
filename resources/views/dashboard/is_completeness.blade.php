@extends('layouts.app')

@section('style')
    <style>
        .is-dashboard {
            --ink: #17365d;
            --blue: #2078c7;
            --sky: #edf7ff;
            --line: #dce8f3;
            color: var(--ink);
        }

        .is-hero {
            background: linear-gradient(118deg, #064e3b, #087a4c 58%, #31a96b);
            border-radius: 22px;
            color: #fff;
            overflow: hidden;
            position: relative;
        }

        .is-hero:after {
            content: "";
            position: absolute;
            width: 240px;
            height: 240px;
            border: 40px solid rgba(255, 255, 255, .09);
            border-radius: 50%;
            right: -68px;
            top: -120px;
        }

        .is-hero-watermark {
            position: absolute;
            right: 1rem;
            top: 1rem;
            color: rgba(255, 255, 255, .12);
            font-family: Arial, sans-serif;
            font-size: clamp(3.5rem, 6vw, 4rem);
            font-weight: 800;
            letter-spacing: -.1em;
            line-height: 1;
            pointer-events: none;
            user-select: none;
        }

        .is-filter,
        .is-panel,
        .is-stat {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(29, 74, 117, .06);
        }

        .is-filter {
            margin-top: -20px;
            position: relative;
            z-index: 1;
        }

        .is-filter .select2-container {
            width: 100% !important;
        }

        .is-filter .select2-selection--multiple {
            height: 42px !important;
            min-height: 42px !important;
            border-color: #ced4da;
            padding: 2px 6px;
            overflow: hidden;
            position: relative;
        }

        .is-filter .select2-selection--single {
            box-sizing: border-box;
            display: flex;
            align-items: center;
            height: 42px !important;
            border-color: #ced4da;
            padding: 0 2.25rem 0 .75rem !important;
        }

        .is-filter .select2-selection--single .select2-selection__rendered {
            box-sizing: border-box;
            display: flex !important;
            align-items: center;
            height: 100% !important;
            line-height: normal !important;
            padding: 0 .75rem !important;
        }

        .is-filter .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            top: 0 !important;
        }

        .is-filter .select2-selection__choice {
            display: none !important;
        }

        .is-filter .select2-selection__rendered.is-summary {
            display: block !important;
            height: 36px !important;
            overflow: hidden !important;
            padding-left: .35rem !important;
            position: relative;
        }

        .is-filter .select2-selection__rendered.is-summary::before {
            content: attr(data-summary);
            display: flex;
            align-items: center;
            position: absolute;
            inset: 0 .4rem 0 .35rem;
            color: #24557d;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .is-filter .select2-container .select2-search--inline,
        .is-filter .select2-container .select2-search__field {
            display: none !important;
        }

        .year-type-toggle .btn {
            flex: 1;
            min-width: 0;
            overflow: hidden;
            padding-inline: .45rem;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .year-type-toggle {
            border: 1px solid #198754;
            border-radius: .5rem;
            max-width: 100%;
            overflow: hidden;
        }

        .year-type-toggle .btn {
            border: 0 !important;
            border-radius: 0 !important;
        }

        .year-type-toggle label[for="year_type_fiscal"] {
            border-left: 1px solid #198754 !important;
        }

        .is-select-option {
            display: flex;
            align-items: center;
            gap: .55rem;
        }

        .is-select-option .is-checkbox {
            width: 18px;
            height: 18px;
            border: 2px solid #8a8a8a;
            border-radius: 3px;
            background: #fff;
            flex: 0 0 18px;
            position: relative;
        }

        .select2-results__option--selected .is-select-option .is-checkbox,
        .select2-results__option[aria-selected="true"] .is-select-option .is-checkbox {
            background: #1685f8;
            border-color: #1685f8;
        }

        .select2-results__option--selected .is-select-option .is-checkbox::after,
        .select2-results__option[aria-selected="true"] .is-select-option .is-checkbox::after {
            content: '';
            position: absolute;
            width: 6px;
            height: 11px;
            border: solid #fff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
            left: 4px;
            top: 0;
        }

        .is-stat {
            height: 100%;
            padding: 1.25rem;
            border: 0;
            border-left: 4px solid var(--accent);
            background: color-mix(in srgb, var(--accent) 7%, #fff);
        }

        .is-stat .icon {
            width: 43px;
            height: 43px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            color: var(--accent);
            background: color-mix(in srgb, var(--accent) 12%, white);
        }

        .is-stat .number {
            font-size: 1.72rem;
            font-weight: 700;
            color: #16395f;
            line-height: 1.1;
        }

        .is-panel {
            padding: 1.25rem;
            height: 100%;
        }

        .is-panel h2 {
            font-size: 1rem;
            font-weight: 700;
            margin: 0;
        }

        .progress {
            height: 9px;
            border-radius: 99px;
            background: #eaf0f6;
        }

        .progress-bar {
            border-radius: 99px;
        }

        .level {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .78rem 0;
            border-bottom: 1px solid #edf1f5;
        }

        .level:last-child {
            border: 0;
        }

        .level-tag {
            width: 34px;
            height: 30px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            background: #edf7ff;
            color: #1570b9;
            font-weight: 700;
            font-size: .82rem;
        }

        .table> :not(caption)>*>* {
            padding: .72rem .55rem;
            border-color: #e7eef5;
            vertical-align: middle;
        }

        .table thead th {
            background: #f1f8fe;
            color: #335575;
            font-size: .82rem;
            white-space: nowrap;
        }

        .hospital-name {
            font-weight: 600;
            color: #274766;
        }

        .quality {
            font-weight: 700;
        }

        .quality.low {
            color: #dc6a32;
        }

        .quality.mid {
            color: #bd8b16;
        }

        .quality.high {
            color: #15805d;
        }

        @media(max-width:767px) {
            .is-filter {
                margin-top: 0;
            }

            .is-hero {
                border-radius: 16px;
            }

            .is-hero-watermark {
                right: 1rem;
                bottom: -.75rem;
            }
        }
    </style>
@endsection

@section('script')
    <script>
        $(function() {
            $('.js-is-single').select2({
                theme: 'bootstrap-5',
                width: '100%',
                minimumResultsForSearch: Infinity
            });

            function formatOption(option) {
                if (!option.id) return option.text;
                return $('<span class="is-select-option"><span class="is-checkbox" aria-hidden="true"></span><span>' + option.text + '</span></span>');
            }

            function updateSelectionSummary($select) {
                var values = ($select.val() || []).filter(function(value) {
                    return value !== '__all__';
                });
                var summary = values.length ? values.map(function(value) {
                    return $select.find('option[value="' + value + '"]').text();
                }).join(', ') : $select.data('placeholder');
                $select.next('.select2-container').find('.select2-selection__rendered')
                    .attr('data-summary', summary).addClass('is-summary');
            }

            $('.js-is-multiple').each(function() {
                var $select = $(this);
                $select.select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    closeOnSelect: false,
                    placeholder: $select.data('placeholder'),
                    templateResult: formatOption
                }).on('select2:select', function(event) {
                    if (event.params.data.id !== '__all__') return;
                    $select.find('option:not([value="__all__"])').prop('selected', true);
                    $select.find('option[value="__all__"]').prop('selected', false);
                    $select.trigger('change');
                }).on('change', function() {
                    updateSelectionSummary($select);
                });
                updateSelectionSummary($select);
            });

            var yearOptions = {
                fiscal: ['2569', '2568', '2567'],
                calendar: ['2569', '2568', '2567']
            };
            var monthOptions = {
                fiscal: [
                    [10, 'ตุลาคม'],
                    [11, 'พฤศจิกายน'],
                    [12, 'ธันวาคม'],
                    [1, 'มกราคม'],
                    [2, 'กุมภาพันธ์'],
                    [3, 'มีนาคม'],
                    [4, 'เมษายน'],
                    [5, 'พฤษภาคม'],
                    [6, 'มิถุนายน'],
                    [7, 'กรกฎาคม'],
                    [8, 'สิงหาคม'],
                    [9, 'กันยายน']
                ],
                calendar: [
                    [1, 'มกราคม'],
                    [2, 'กุมภาพันธ์'],
                    [3, 'มีนาคม'],
                    [4, 'เมษายน'],
                    [5, 'พฤษภาคม'],
                    [6, 'มิถุนายน'],
                    [7, 'กรกฎาคม'],
                    [8, 'สิงหาคม'],
                    [9, 'กันยายน'],
                    [10, 'ตุลาคม'],
                    [11, 'พฤศจิกายน'],
                    [12, 'ธันวาคม']
                ]
            };

            $('input[name="year_type"]').on('change', function() {
                var type = this.value;
                var selectedMonths = $('#report_months').val() || [];
                var yearHtml = yearOptions[type].map(function(year) {
                    return '<option value="' + year + '">' + year + '</option>';
                }).join('');
                var monthHtml = '<option value="__all__">เลือกทั้งหมด</option>' + monthOptions[type].map(function(month) {
                    return '<option value="' + month[0] + '">' + month[1] + '</option>';
                }).join('');

                $('#fiscal_year').html(yearHtml).trigger('change');
                $('#report_months').html(monthHtml).val(selectedMonths).trigger('change');
            });
        });
    </script>
@endsection

@section('content')
    <main class="container-fluid is-dashboard py-4 px-lg-4">
        <section class="is-hero p-4 p-lg-5 mb-0">
            <div class="is-hero-watermark" aria-hidden="true">DASHBOARD</div>
            <div class="position-relative" style="z-index:1">
                <h1 class="h2 fw-bold mb-2">ภาพรวมความครบถ้วนข้อมูล IS</h1>
                <p class="mb-0 text-white-50">
                    ติดตามการส่งข้อมูลของโรงพยาบาลในเขตสุขภาพ เพื่อยกระดับคุณภาพข้อมูลอย่างต่อเนื่อง
                </p>
            </div>
        </section>

        @php
            $defaultMonths = [
                1 => 'มกราคม',
                2 => 'กุมภาพันธ์',
                3 => 'มีนาคม',
                4 => 'เมษายน',
                5 => 'พฤษภาคม',
                6 => 'มิถุนายน',
                7 => 'กรกฎาคม',
                8 => 'สิงหาคม',
                9 => 'กันยายน',
                10 => 'ตุลาคม',
                11 => 'พฤศจิกายน',
                12 => 'ธันวาคม',
            ];
            $healthZones = [
                '01' => 'เขตสุขภาพ 01',
                '02' => 'เขตสุขภาพ 02',
                '03' => 'เขตสุขภาพ 03',
                '04' => 'เขตสุขภาพ 04',
                '05' => 'เขตสุขภาพ 05',
                '06' => 'เขตสุขภาพ 06',
                '07' => 'เขตสุขภาพ 07',
                '08' => 'เขตสุขภาพ 08',
                '09' => 'เขตสุขภาพ 09',
                '10' => 'เขตสุขภาพ 10',
                '11' => 'เขตสุขภาพ 11',
                '12' => 'เขตสุขภาพ 12',
                '13' => 'เขตสุขภาพ 13',
            ];
        @endphp
        <form class="is-filter p-3 p-lg-4 mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-lg-2">
                    <label class="form-label small fw-bold d-block">รูปแบบปี</label>
                    <div class="btn-group year-type-toggle w-100" role="group" aria-label="รูปแบบปี">
                        <input type="radio" class="btn-check" name="year_type" id="year_type_calendar" value="calendar" checked>
                        <label class="btn btn-outline-success" for="year_type_calendar">ปฏิทิน</label>
                        <input type="radio" class="btn-check" name="year_type" id="year_type_fiscal" value="fiscal">
                        <label class="btn btn-outline-success" for="year_type_fiscal">งบประมาณ</label>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-2">
                    <label class="form-label small fw-bold" for="fiscal_year">ปี</label>
                    <select id="fiscal_year" name="fiscal_year" class="form-select js-is-single">
                        <option>2569</option>
                        <option>2568</option>
                        <option>2567</option>
                    </select>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label class="form-label small fw-bold" for="report_months">เดือน</label>
                    <select id="report_months" name="months[]" class="form-select js-is-multiple" multiple data-placeholder="เลือกเดือนรายงาน">
                        <option value="__all__">เลือกทั้งหมด</option>
                        @foreach ($defaultMonths as $number => $month)
                            <option value="{{ $number }}">{{ $month }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8 col-lg-3">
                    <label class="form-label small fw-bold" for="health_zones">เขตสุขภาพ</label>
                    <select id="health_zones" name="health_zones[]" class="form-select js-is-multiple" multiple data-placeholder="เลือกเขตสุขภาพ">
                        <option value="__all__">เลือกทั้งหมด</option>
                        @foreach ($healthZones as $code => $zone)
                            <option value="{{ $code }}">{{ $zone }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-lg-2">
                    <button type="button" class="btn btn-primary w-100">
                        <i class="fa-solid fa-filter me-2"></i>แสดง
                    </button>
                </div>
            </div>
        </form>

        <div class="row g-3 mb-4">
            @foreach ([['เป้าหมายโรงพยาบาล', '20', 'fa-hospital', '#4188cf'], ['ส่งข้อมูลแล้ว', '20', 'fa-paper-plane', '#159b72'], ['ครบ 21 ตัวแปร', '18', 'fa-circle-check', '#7b63c7'], ['คุณภาพข้อมูล', '92.89%', 'fa-chart-line', '#e39428']] as [$label, $value, $icon, $color])
                <div class="col-sm-6 col-xl-3">
                    <article class="is-stat" style="--accent:{{ $color }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">{{ $label }}</div>
                                <div class="number">{{ $value }}</div>
                            </div>
                            <div class="icon">
                                <i class="fa-solid {{ $icon }}"></i>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>

        <div class="row g-3 mb-3">
            <div class="col-lg-7">
                <section class="is-panel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2>ความครบถ้วนแยกตามระดับโรงพยาบาล</h2>
                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary">20 แห่ง</span>
                    </div>
                    @foreach ([['A', 1, 1, 100, '#2185d0'], ['M2', 4, 4, 100, '#2185d0'], ['F1', 1, 1, 100, '#2185d0'], ['F2', 11, 10, 90.91, '#e2a11d'], ['F3', 3, 2, 66.67, '#dd713a']] as [$level, $sentCount, $completeCount, $percent, $color])
                        <div class="level">
                            <span class="level-tag">{{ $level }}</span>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>ส่งข้อมูล {{ $sentCount }} แห่ง · ครบ {{ $completeCount }} แห่ง</span>
                                    <strong>{{ number_format($percent, 2) }}%</strong>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" style="width:{{ $percent }}%;background:{{ $color }}"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </section>
            </div>
            <div class="col-lg-5">
                <section class="is-panel">
                    <h2 class="mb-3">สรุประดับจังหวัด</h2>
                    <div class="text-center py-2">
                        <div style="font-size:3.1rem;font-weight:800;color:#1776bf;line-height:1">
                            92.89<small style="font-size:1.2rem">%</small>
                        </div>
                        <div class="text-muted small mt-2">ค่าเฉลี่ยความครบถ้วนข้อมูล</div>
                    </div>
                    <div class="row text-center border-top mt-3 pt-3">
                        <div class="col-4">
                            <strong class="d-block fs-5">1</strong>
                            <small class="text-muted">ถึงเป้าหมาย</small>
                        </div>
                        <div class="col-4 border-start">
                            <strong class="d-block fs-5">0</strong>
                            <small class="text-muted">ต่ำกว่าเป้าหมาย</small>
                        </div>
                        <div class="col-4 border-start">
                            <strong class="d-block fs-5">41,311</strong>
                            <small class="text-muted">รายการบันทึก</small>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-lg-5">
                <section class="is-panel">
                    <h2 class="mb-3">ผลการดำเนินงานตามเป้าหมาย</h2>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background:#eff9f5">
                                <small class="text-muted d-block">จังหวัดที่ถึงเป้าหมาย</small>
                                <strong class="fs-3 text-success">1</strong>
                                <div class="small text-success">ร้อยเอ็ด</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background:#fff6ed">
                                <small class="text-muted d-block">จังหวัดต่ำกว่าเป้าหมาย</small>
                                <strong class="fs-3" style="color:#d67b25">0</strong>
                                <div class="small text-muted">ไม่มีรายการ</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background:#eff9f5">
                                <small class="text-muted d-block">รพ. ที่ถึงเป้าหมาย</small>
                                <strong class="fs-3 text-success">18</strong>
                                <div class="small text-muted">จาก 20 แห่ง</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background:#fff6ed">
                                <small class="text-muted d-block">รพ. ต่ำกว่าเป้าหมาย</small>
                                <strong class="fs-3" style="color:#d67b25">2</strong>
                                <div class="small text-muted">ต้องติดตาม</div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-lg-7">
                <section class="is-panel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2>สรุปการบันทึกข้อมูลรายจังหวัด</h2>
                        <span class="small text-muted">เขตสุขภาพ 07</span>
                    </div>
                    <div class="is-datatable">
                        <table class="table mb-0" data-toggle="data-table" data-page-length="5">
                            <thead>
                                <tr>
                                    <th>เขต</th>
                                    <th>จังหวัด</th>
                                    <th class="text-end">จำนวนที่บันทึก</th>
                                    <th class="text-end">ครบ 21 ตัวแปร</th>
                                    <th class="text-end">ร้อยละ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>07</td>
                                    <td class="hospital-name">ร้อยเอ็ด</td>
                                    <td class="text-end">41,311</td>
                                    <td class="text-end">38,372</td>
                                    <td class="text-end quality high">92.89%</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td></td>
                                    <td>รวมทั้งหมด</td>
                                    <td class="text-end">41,311</td>
                                    <td class="text-end">38,372</td>
                                    <td class="text-end quality high">92.89%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>

        <section class="is-panel">
            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
                <div>
                    <h2>รายชื่อโรงพยาบาล</h2>
                    <small class="text-muted">เรียงตามคุณภาพข้อมูลจากน้อยไปมาก เพื่อให้ติดตามได้ทันที</small>
                </div>
                <button class="btn btn-outline-primary btn-sm" type="button">
                    <i class="fa-solid fa-download me-1"></i>ส่งออก Excel
                </button>
            </div>
            <div class="is-datatable">
                <table class="table table-hover mb-0" data-toggle="data-table" data-page-length="10">
                    <thead>
                        <tr>
                            <th>เขต</th>
                            <th>จังหวัด</th>
                            <th>ระดับ</th>
                            <th>โรงพยาบาล</th>
                            <th class="text-end">จำนวนบันทึก</th>
                            <th class="text-end">ครบ 21 ตัวแปร</th>
                            <th style="min-width:140px">คุณภาพข้อมูล</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ([['07', 'ร้อยเอ็ด', 'F2', 'โรงพยาบาลธวัชบุรี', '196', '130', 66.33], ['07', 'ร้อยเอ็ด', 'F2', 'โรงพยาบาลศรีสมเด็จ', '2,447', '2,060', 84.18], ['07', 'ร้อยเอ็ด', 'F1', 'โรงพยาบาลพนมไพร', '211', '180', 85.31], ['07', 'ร้อยเอ็ด', 'F2', 'โรงพยาบาลโพนทอง', '3,055', '2,658', 87.0], ['07', 'ร้อยเอ็ด', 'F2', 'โรงพยาบาลเกษตรวิสัย', '2,126', '1,821', 85.65], ['07', 'ร้อยเอ็ด', 'F2', 'โรงพยาบาลจตุรพักตรพิมาน', '637', '606', 95.13], ['07', 'ร้อยเอ็ด', 'F2', 'โรงพยาบาลอาจสามารถ', '826', '766', 92.74], ['07', 'ร้อยเอ็ด', 'F2', 'โรงพยาบาลจังหาร', '647', '566', 87.48], ['07', 'ร้อยเอ็ด', 'A', 'โรงพยาบาลร้อยเอ็ด', '15,856', '15,738', 99.26]] as [$zone, $province, $level, $hospital, $all, $complete, $percent])
                            <tr>
                                <td>{{ $zone }}</td>
                                <td>{{ $province }}</td>
                                <td>
                                    <span class="level-tag">{{ $level }}</span>
                                </td>
                                <td class="hospital-name">{{ $hospital }}</td>
                                <td class="text-end">{{ $all }}</td>
                                <td class="text-end">{{ $complete }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar {{ $percent < 70 ? 'bg-danger' : ($percent < 90 ? 'bg-warning' : 'bg-success') }}" style="width:{{ $percent }}%"></div>
                                        </div>
                                        <span class="quality {{ $percent < 70 ? 'low' : ($percent < 90 ? 'mid' : 'high') }}">
                                            {{ number_format($percent, 2) }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </main>
@endsection
