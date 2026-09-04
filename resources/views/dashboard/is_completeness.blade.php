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

        .is-dashboard.is-loading .is-stat,
        .is-dashboard.is-loading .is-panel {
            opacity: .45;
            pointer-events: none;
        }

        .is-dashboard.is-loading .is-stat::after,
        .is-dashboard.is-loading .is-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            margin: auto;
            width: 1.5rem;
            height: 1.5rem;
            border: 3px solid #198754;
            border-right-color: transparent;
            border-radius: 50%;
            animation: is-spin .7s linear infinite;
        }

        .is-stat,
        .is-panel {
            position: relative;
        }

        .is-page-loading {
            position: fixed;
            inset: 0;
            z-index: 4000;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 54, 29, .34);
            backdrop-filter: blur(4px);
        }

        .is-loading-card {
            width: min(88vw, 320px);
            padding: 2.4rem;
            border: 1px solid rgba(255, 255, 255, .7);
            border-radius: 20px;
            background: rgba(255, 255, 255, .96);
            box-shadow: 0 20px 50px rgba(0, 35, 19, .25);
            animation: is-loading-card-enter .2s ease-out;
        }

        .is-loading-mark {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 72px;
            height: 72px;
            margin-bottom: 1.25rem;
            color: #006637;
            font-size: 1.45rem;
        }

        .is-loading-mark::before {
            position: absolute;
            inset: 12px;
            border-radius: 50%;
            background: #e8f5ed;
            content: '';
        }

        .is-loading-mark i {
            position: relative;
        }

        .is-loading-spinner {
            position: absolute;
            inset: 0;
            border: 4px solid #d8ebe0;
            border-top-color: #006637;
            border-radius: 50%;
            animation: is-spin .7s linear infinite;
        }

        .is-target-card {
            position: relative;
            min-height: 142px;
            padding: 1.25rem;
            overflow: hidden;
            border: 1px solid #e4ece8;
            border-left: 4px solid var(--target-color);
            border-radius: 1rem;
            background: var(--target-bg);
        }

        .is-target-card::after {
            position: absolute;
            right: -1.4rem;
            bottom: -2rem;
            width: 6.5rem;
            height: 6.5rem;
            content: '';
            border: 1rem solid var(--target-color);
            border-radius: 50%;
            opacity: .08;
        }

        .is-target-card small,
        .is-target-card strong,
        .is-target-card div {
            position: relative;
            z-index: 1;
        }

        .is-target-card .target-value {
            display: block;
            margin-top: .55rem;
            color: var(--target-color);
            font-size: 2.35rem;
            font-weight: 800;
            line-height: 1;
        }

        @keyframes is-spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes is-loading-card-enter {
            from {
                opacity: 0;
                transform: translateY(8px) scale(.98);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .is-empty-state {
            padding: 4rem 1.5rem;
            text-align: center;
        }

        .is-empty-state .empty-icon {
            display: inline-grid;
            place-items: center;
            width: 4rem;
            height: 4rem;
            border-radius: 50%;
            background: #edf8f2;
            color: #168754;
            font-size: 1.5rem;
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
            height: 12px;
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

        .table tr.align-top>td {
            vertical-align: top;
        }

        .table thead th {
            background: #f1f8fe;
            color: #335575;
            font-size: .82rem;
            white-space: nowrap;
        }

        #province-table,
        #hospital-table {
            table-layout: fixed;
        }

        #province-table tbody td:nth-child(n+3),
        #hospital-table tbody td:nth-child(5),
        #hospital-table tbody td:nth-child(6) {
            text-align: right !important;
        }

        #hospital-table tbody td {
            vertical-align: top;
        }

        .is-summary-tabs {
            border-bottom: 1px solid #d9e8df;
        }

        .is-summary-tabs .nav-link {
            color: #61717d;
            border: 0;
            border-bottom: 3px solid transparent;
            font-weight: 700;
            text-align: center;
        }

        .is-summary-tabs .nav-link.active {
            color: #087a46;
            background: transparent;
            border-bottom-color: #087a46;
        }

        .is-summary-pane {
            padding: .25rem;
        }

        .is-summary-score {
            padding: 1.2rem;
            text-align: center;
            border: 1px solid #dbeae1;
            border-radius: 1rem;
            background: radial-gradient(circle at top right, #e9f8ee, #f8fcfa 62%);
        }

        .is-summary-score .score-value {
            color: #1776bf;
            font-size: 3.25rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -.06em;
        }

        .is-summary-metrics {
            margin-top: .9rem;
            overflow: hidden;
            border: 1px solid #e2ece6;
            border-radius: .85rem;
            background: #fff;
        }

        .is-summary-metric {
            padding: .8rem .35rem;
        }

        .is-summary-metric+.is-summary-metric {
            border-left: 1px solid #e2ece6;
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
                }).on('select2:selecting', function(event) {
                    if (event.params.args.data.id !== '__all__') return;
                    event.preventDefault();
                    $select.find('option').prop('selected', true);
                    $select.trigger('change');
                }).on('select2:unselecting', function(event) {
                    if (event.params.args.data.id === '__all__') {
                        event.preventDefault();
                        $select.val(null).trigger('change');
                    }
                }).on('select2:unselect', function(event) {
                    if (event.params.data.id === '__all__') return;
                    $select.val(($select.val() || []).filter(function(value) {
                        return value !== '__all__';
                    })).trigger('change');
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
                $('.year-type-toggle label').removeClass('active');
                $(this).next('label').addClass('active');
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

            $('input[name="year_type"]:checked').next('label').addClass('active');

            var summaryRequest = null;
            var cacheStatusRequest = null;

            function loadDashboard() {
                if (summaryRequest) summaryRequest.abort();
                if (cacheStatusRequest) cacheStatusRequest.abort();
                var $dashboard = $('.is-dashboard').addClass('is-loading');
                var $button = $('#is-filter-form button[type="submit"]').prop('disabled', true);
                var filters = $('#is-filter-form').serialize();

                function requestSummary() {
                    var request = $.getJSON('{{ route('dashboard.is_completeness.summary') }}', filters);
                    summaryRequest = request;
                    request
                        .done(function(data) {
                            var kpis = [data.targetByLevel ? Object.values(data.targetByLevel).reduce(function(total, value) {
                                return total + Number(value);
                            }, 0) : 0, data.hospitalRows.length, data.hospitalRows.filter(function(row) {
                                return Number(row.records) > 0 && Number(row.complete_records) === Number(row.records);
                            }).length, Number(data.quality).toFixed(2) + '%'];
                            $('[data-kpi]').each(function() {
                                $(this).text(kpis[$(this).data('kpi')]);
                            });
                            $('#sent-hospital-count').text(data.hospitalRows.length + ' แห่ง');
                            $('#quality-value').text(Number(data.quality).toFixed(2));
                            var reachedProvinces = data.provinceData.filter(function(row) {
                                return row.records && (row.complete / row.records * 100) >= 90;
                            }).length;
                            $('#province-reached').text(reachedProvinces);
                            $('#province-below-target').text(data.provinceData.length - reachedProvinces);
                            $('#record-total').text(Number(data.recordTotal).toLocaleString());
                            var completeHospitals = data.hospitalRows.filter(function(row) {
                                return Number(row.records) > 0 && Number(row.complete_records) === Number(row.records);
                            }).length;
                            $('#target-province-reached').text(reachedProvinces);
                            $('#target-province-below').text(data.provinceData.length - reachedProvinces);
                            $('#target-hospital-reached').text(completeHospitals);
                            $('#target-hospital-total').text('จาก ' + data.hospitalRows.length + ' แห่งที่ส่งข้อมูล');
                            $('#target-hospital-below').text(data.hospitalRows.length - completeHospitals);
                            $('#hospital-quality-value').text(Number(data.quality).toFixed(2));
                            $('#hospital-reached-summary').text(completeHospitals);
                            $('#hospital-below-summary').text(data.hospitalRows.length - completeHospitals);
                            $('#hospital-sent-summary').text(data.hospitalRows.length);
                            $('#level-data').html(data.levelData.filter(function(item) {
                                return item.target || item.sent;
                            }).map(function(item) {
                                return '<div class="level"><span class="level-tag">' + item.level + '</span><div class="flex-grow-1"><div class="d-flex justify-content-between small mb-1"><span>ส่งข้อมูล ' + item.sent + ' แห่ง · ครบ ' + item.complete + ' แห่ง</span><strong>' + Number(item.percent).toFixed(2) + '%</strong></div><div class="progress"><div class="progress-bar" style="width:' + item.percent + '%;background:' + item.color + '"></div></div></div></div>';
                            }).join(''));
                            $('#province-table').DataTable().clear().rows.add(data.provinceData.map(function(row) {
                                var percent = row.records ? row.complete / row.records * 100 : 0;
                                return [row.region, row.province, Number(row.records).toLocaleString(), Number(row.complete).toLocaleString(), percent.toFixed(2) + '%'];
                            })).draw();
                            var provinceAverage = data.provinceData.length ? data.provinceData.reduce(function(total, row) {
                                return total + (row.records ? row.complete / row.records * 100 : 0);
                            }, 0) / data.provinceData.length : 0;
                            $('#province-record-sum').text(Number(data.recordTotal).toLocaleString());
                            $('#province-complete-sum').text(Number(data.completeTotal).toLocaleString());
                            $('#province-percent-average').text(provinceAverage.toFixed(2) + '%');
                            $('#hospital-table').DataTable().clear().rows.add(data.hospitalRows.map(function(row) {
                                var percent = row.records ? row.complete_records / row.records * 100 : 0;
                                var qualityClass = percent < 70 ? 'bg-danger' : (percent < 90 ? 'bg-warning' : 'bg-success');
                                var qualityText = percent < 70 ? 'low' : (percent < 90 ? 'mid' : 'high');
                                var qualityCell = '<div class="d-flex align-items-center gap-2"><div class="progress flex-grow-1"><div class="progress-bar ' + qualityClass + '" style="width:' + percent + '%"></div></div><span class="quality ' + qualityText + '">' + percent.toFixed(2) + '%</span></div>';
                                return [row.region, row.province, '<span class="level-tag">' + row.splevel + '</span>', row.hospital, Number(row.records).toLocaleString(), Number(row.complete_records).toLocaleString(), qualityCell];
                            })).draw();
                            var hospitalAverage = data.hospitalRows.length ? data.hospitalRows.reduce(function(total, row) {
                                return total + (row.records ? row.complete_records / row.records * 100 : 0);
                            }, 0) / data.hospitalRows.length : 0;
                            $('#hospital-record-sum').text(Number(data.recordTotal).toLocaleString());
                            $('#hospital-complete-sum').text(Number(data.completeTotal).toLocaleString());
                            $('#hospital-percent-average').text(hospitalAverage.toFixed(2) + '%');
                            $('#last-updated').toggleClass('d-none', !data.lastUpdatedAt).find('span').text(data.lastUpdatedAt || '');
                            $('#dashboard-empty-state').addClass('d-none');
                            $('#dashboard-results').removeClass('d-none');
                        }).always(function() {
                            if (summaryRequest === request) {
                                $dashboard.removeClass('is-loading');
                                $('#is-page-loading').addClass('d-none');
                                $button.prop('disabled', false);
                                summaryRequest = null;
                            }
                        });
                }

                var cacheRequest = $.getJSON('{{ route('dashboard.is_completeness.cache_status') }}', filters);
                cacheStatusRequest = cacheRequest;
                cacheRequest.done(function(status) {
                    if (cacheStatusRequest !== cacheRequest) return;
                    if (!status.cached) $('#is-page-loading').removeClass('d-none');
                    requestSummary();
                }).fail(function() {
                    if (cacheStatusRequest !== cacheRequest) return;
                    $('#is-page-loading').removeClass('d-none');
                    requestSummary();
                });
            }

            $('#is-filter-form').on('submit', function(event) {
                event.preventDefault();
                loadDashboard();
            });
            $('[data-summary-tab]').on('click', function() {
                var tab = $(this).data('summary-tab');
                $('[data-summary-tab]').removeClass('active');
                $(this).addClass('active');
                $('[data-summary-pane]').addClass('d-none');
                $('[data-summary-pane="' + tab + '"]').removeClass('d-none');
            });
            var $targetCards = $('#target-results-panel > .row > .col-6');
            $('#province-target-cards').append($targetCards.slice(0, 2));
            $('#hospital-target-cards').append($targetCards.slice(2));
            $('#level-data').closest('.col-lg-7').removeClass('col-lg-7').addClass('col-lg-6');
            $('[data-summary-pane]').closest('.col-lg-5').removeClass('col-lg-5').addClass('col-lg-6');
            $('#target-results-panel').closest('.col-lg-5').remove();
            $('#target-results-row > .col-lg-7').removeClass('col-lg-7').addClass('col-12');
            $('#province-table').DataTable().order([4, 'desc']).draw();
            $('.is-dashboard').removeClass('is-loading');
        });
    </script>
@endsection

@section('content')
    <main class="container-fluid is-dashboard py-4 px-lg-4 is-loading">
        <div id="is-page-loading" class="is-page-loading d-none" role="status">
            <div class="is-loading-card text-center">
                <div class="is-loading-mark" aria-hidden="true">
                    <div class="is-loading-spinner"></div>
                    <i class="fa-solid fa-heart-pulse"></i>
                </div>
                <h2 class="h6 fw-bold mb-1">กำลังประมวลผลข้อมูล</h2>
                <small class="text-muted">อาจใช้เวลาสักครู่</small>
            </div>
        </div>
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
            $hospitalLevels = ['A', 'S', 'M1', 'M2', 'F1', 'F2', 'F3'];
        @endphp
        <form id="is-filter-form" action="{{ route('dashboard.is_completeness') }}" method="get" class="is-filter p-3 p-lg-4 mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-lg-3">
                    <label class="form-label small fw-bold d-block">รูปแบบปี</label>
                    <div class="btn-group year-type-toggle w-100" role="group" aria-label="รูปแบบปี">
                        <input type="radio" class="btn-check" name="year_type" id="year_type_calendar" value="calendar" {{ $yearType === 'calendar' ? 'checked' : '' }}>
                        <label class="btn btn-outline-success" for="year_type_calendar">ปฏิทิน</label>
                        <input type="radio" class="btn-check" name="year_type" id="year_type_fiscal" value="fiscal" {{ $yearType === 'fiscal' ? 'checked' : '' }}>
                        <label class="btn btn-outline-success" for="year_type_fiscal">งบประมาณ</label>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label class="form-label small fw-bold" for="fiscal_year">ปี</label>
                    <select id="fiscal_year" name="fiscal_year" class="form-select js-is-single">
                        @foreach ([2569, 2568, 2567] as $year)
                            <option value="{{ $year }}" @selected($selectedYear === $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 col-lg-6">
                    <label class="form-label small fw-bold" for="report_months">เดือน</label>
                    <select id="report_months" name="months[]" class="form-select js-is-multiple" multiple data-placeholder="เลือกเดือนรายงาน">
                        <option value="__all__">เลือกทั้งหมด</option>
                        @foreach ($defaultMonths as $number => $month)
                            <option value="{{ $number }}" @selected(in_array($number, $selectedMonths))>{{ $month }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row g-3 align-items-end mt-0">
                <div class="col-md-6 col-lg-6">
                    <label class="form-label small fw-bold" for="health_zones">เขตสุขภาพ</label>
                    <select id="health_zones" name="health_zones[]" class="form-select js-is-multiple" multiple data-placeholder="เลือกเขตสุขภาพ">
                        <option value="__all__">เลือกทั้งหมด</option>
                        @foreach ($healthZones as $code => $zone)
                            <option value="{{ $code }}" @selected(in_array($code, $selectedZones))>{{ $zone }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-lg-4">
                    <label class="form-label small fw-bold" for="hospital_levels">ระดับโรงพยาบาล</label>
                    <select id="hospital_levels" name="hospital_levels[]" class="form-select js-is-multiple" multiple data-placeholder="เลือกระดับโรงพยาบาล">
                        <option value="__all__">เลือกทั้งหมด</option>
                        @foreach ($hospitalLevels as $level)
                            <option value="{{ $level }}" @selected(in_array($level, $selectedLevels))>{{ $level }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-lg-2">
                    <button type="submit" class="btn btn-primary w-100" style="height: 42px;">
                        {{-- <i class="fa-solid fa-filter me-1"></i> แสดง --}}
                        <i class="fa-solid fa-magnifying-glass me-1"></i> แสดง
                    </button>
                </div>
            </div>
        </form>

        <section id="dashboard-empty-state" class="is-panel is-empty-state">
            <div class="empty-icon"><i class="fa-solid fa-sliders"></i></div>
            <h2 class="h5 fw-bold mt-3 mb-2">เลือกเงื่อนไขเพื่อแสดงผล dashboard</h2>
            <p class="text-muted mb-0">กำหนดตัวกรองตามต้องการ แล้วกดปุ่ม “แสดง” เพื่อโหลดข้อมูล</p>
        </section>

        <div id="dashboard-results" class="d-none">
            <div class="row g-3 mb-4">
                @foreach ([['เป้าหมายโรงพยาบาล', $targetByLevel->sum(), 'fa-hospital', '#4188cf'], ['ส่งข้อมูลแล้ว', $hospitalRows->count(), 'fa-paper-plane', '#7b63c7'], ['ครบ 21 ตัวแปร', $hospitalRows->filter(fn($row) => $row->complete_records > 0)->count(), 'fa-circle-check', '#159b72'], ['คุณภาพข้อมูล', number_format($quality, 2) . '%', 'fa-chart-line', '#e39428']] as $index => [$label, $value, $icon, $color])
                    <div class="col-sm-6 col-xl-3">
                        <article class="is-stat" style="--accent:{{ $color }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="text-muted small mb-2">{{ $label }}</div>
                                    <div class="number" data-kpi="{{ $index }}">{{ $value }}</div>
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
                            <span id="sent-hospital-count" class="badge rounded-pill bg-primary bg-opacity-10 text-primary">{{ $hospitalRows->count() }} แห่ง</span>
                        </div>
                        <div id="level-data">
                            @foreach ($levelData->filter(fn($item) => $item->target || $item->sent) as $item)
                                <div class="level">
                                    <span class="level-tag">{{ $item->level }}</span>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span>ส่งข้อมูล {{ $item->sent }} แห่ง · ครบ {{ $item->complete }} แห่ง</span>
                                            <strong>{{ number_format($item->percent, 2) }}%</strong>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar" style="width:{{ $item->percent }}%;background:{{ $item->color }}"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                </div>
                <div class="col-lg-5">
                    <section class="is-panel">
                        <ul class="nav nav-tabs nav-fill is-summary-tabs mb-3" aria-label="รูปแบบสรุป">
                            <li class="nav-item"><button type="button" class="nav-link active w-100" data-summary-tab="province">สรุประดับจังหวัด</button></li>
                            <li class="nav-item"><button type="button" class="nav-link w-100" data-summary-tab="hospital">ระดับ รพ.</button></li>
                        </ul>
                        <div data-summary-pane="province" class="is-summary-pane">
                            <div class="is-summary-score">
                                <div class="score-value">
                                    <span id="quality-value">{{ number_format($quality, 2) }}</span><small style="font-size:1.2rem">%</small>
                                </div>
                                <div class="text-muted small mt-2">ค่าเฉลี่ยความครบถ้วนข้อมูล</div>
                            </div>
                            <div class="row text-center is-summary-metrics g-0">
                                <div class="col-4 is-summary-metric">
                                    <strong id="province-reached" class="d-block fs-5">{{ $provinceData->filter(fn($row) => ($row->complete / max($row->records, 1)) * 100 >= 90)->count() }}</strong>
                                    <small class="text-muted">ถึงเป้าหมาย</small>
                                </div>
                                <div class="col-4 is-summary-metric">
                                    <strong id="province-below-target" class="d-block fs-5">{{ $provinceData->filter(fn($row) => ($row->complete / max($row->records, 1)) * 100 < 90)->count() }}</strong>
                                    <small class="text-muted">ต่ำกว่าเป้าหมาย</small>
                                </div>
                                <div class="col-4 is-summary-metric">
                                    <strong id="record-total" class="d-block fs-5">{{ number_format($recordTotal) }}</strong>
                                    <small class="text-muted">รายการบันทึก</small>
                                </div>
                            </div>
                            <div id="province-target-cards" class="row g-3 mt-1"></div>
                        </div>
                        <div data-summary-pane="hospital" class="is-summary-pane d-none">
                            <div class="is-summary-score">
                                <div class="score-value">
                                    <span id="hospital-quality-value">{{ number_format($quality, 2) }}</span><small style="font-size:1.2rem">%</small>
                                </div>
                                <div class="text-muted small mt-2">ค่าเฉลี่ยความครบถ้วนข้อมูล รพ.</div>
                            </div>
                            <div class="row text-center is-summary-metrics g-0">
                                <div class="col-4 is-summary-metric">
                                    <strong id="hospital-reached-summary" class="d-block fs-5">0</strong>
                                    <small class="text-muted">ถึงเป้าหมาย</small>
                                </div>
                                <div class="col-4 is-summary-metric">
                                    <strong id="hospital-below-summary" class="d-block fs-5">0</strong>
                                    <small class="text-muted">ต่ำกว่าเป้าหมาย</small>
                                </div>
                                <div class="col-4 is-summary-metric">
                                    <strong id="hospital-sent-summary" class="d-block fs-5">0</strong>
                                    <small class="text-muted">ส่งข้อมูล</small>
                                </div>
                            </div>
                            <div id="hospital-target-cards" class="row g-3 mt-1"></div>
                        </div>
                    </section>
                </div>
            </div>

            <div id="target-results-row" class="row g-3 mb-3">
                <div class="col-lg-5">
                    <section id="target-results-panel" class="is-panel">
                        <h2 class="mb-3">ผลการดำเนินงานตามเป้าหมาย</h2>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="is-target-card" style="--target-color:#168b5a;--target-bg:#eff9f5">
                                    <small class="text-muted d-block fw-semibold">จังหวัดที่ถึงเป้าหมาย</small>
                                    <strong id="target-province-reached" class="target-value">{{ $provinceData->filter(fn($row) => ($row->complete / max($row->records, 1)) * 100 >= 90)->count() }}</strong>
                                    <div class="small text-success">คุณภาพข้อมูลตั้งแต่ 90%</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="is-target-card" style="--target-color:#d67b25;--target-bg:#fff6ed">
                                    <small class="text-muted d-block fw-semibold">จังหวัดต่ำกว่าเป้าหมาย</small>
                                    <strong id="target-province-below" class="target-value">{{ $provinceData->filter(fn($row) => ($row->complete / max($row->records, 1)) * 100 < 90)->count() }}</strong>
                                    <div class="small text-muted">คุณภาพข้อมูลต่ำกว่า 90%</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="is-target-card" style="--target-color:#287fc5;--target-bg:#eff7fd">
                                    <small class="text-muted d-block fw-semibold">รพ. ที่ถึงเป้าหมาย</small>
                                    <strong id="target-hospital-reached" class="target-value">{{ $hospitalRows->filter(fn($row) => $row->records > 0 && $row->complete_records == $row->records)->count() }}</strong>
                                    <div id="target-hospital-total" class="small text-muted">จาก {{ $hospitalRows->count() }} แห่งที่ส่งข้อมูล</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="is-target-card" style="--target-color:#9b6ad0;--target-bg:#f6f1fc">
                                    <small class="text-muted d-block fw-semibold">รพ. ต่ำกว่าเป้าหมาย</small>
                                    <strong id="target-hospital-below" class="target-value">{{ $hospitalRows->filter(fn($row) => $row->records > 0 && $row->complete_records < $row->records)->count() }}</strong>
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
                            <span class="small text-muted">{{ $selectedZones ? 'เขตสุขภาพ ' . implode(', ', $selectedZones) : 'ทุกเขตสุขภาพ' }}</span>
                        </div>
                        <div class="is-datatable">
                            <table id="province-table" class="table mb-0" data-toggle="data-table" data-page-length="5" data-auto-width="false">
                                <colgroup>
                                    <col style="width:75px; min-width:75px; max-width:75px;">
                                    <col style="min-width:150px;">
                                    <col style="width:130px; min-width:130px; max-width:130px;">
                                    <col style="width:130px; min-width:130px; max-width:130px;">
                                    <col style="width:105px; min-width:105px; max-width:105px;">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>เขต</th>
                                        <th>จังหวัด</th>
                                        <th>จำนวนที่บันทึก</th>
                                        <th>ครบ 21 ตัวแปร</th>
                                        <th>ร้อยละ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($provinceData as $row)
                                        <tr>
                                            <td>{{ $row->region }}</td>
                                            <td class="hospital-name">{{ $row->province }}</td>
                                            <td class="text-end">{{ number_format($row->records) }}</td>
                                            <td class="text-end">{{ number_format($row->complete) }}</td>
                                            <td class="text-end quality high">{{ number_format(($row->complete / max($row->records, 1)) * 100, 2) }}%</td>
                                        </tr>
                                    @endforeach
                                    <tr class="fw-bold">
                                        <td></td>
                                        <td>รวมทั้งหมด</td>
                                        <td class="text-end">{{ number_format($recordTotal) }}</td>
                                        <td class="text-end">{{ number_format($completeTotal) }}</td>
                                        <td class="text-end quality high">{{ number_format($quality, 2) }}%</td>
                                    </tr>
                                </tbody>
                                <tfoot class="fw-bold">
                                    <tr>
                                        <th colspan="2">รวม / เฉลี่ย</th>
                                        <th id="province-record-sum" class="text-end">{{ number_format($recordTotal) }}</th>
                                        <th id="province-complete-sum" class="text-end">{{ number_format($completeTotal) }}</th>
                                        <th id="province-percent-average" class="text-end">0.00%</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </section>
                </div>
            </div>

            <section class="is-panel">
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
                    <div>
                        <h2>รายชื่อโรงพยาบาล</h2>
                        <small class="text-muted">เรียงตามคุณภาพข้อมูลจากมากไปน้อย</small>
                    </div>
                    <button class="btn btn-outline-primary btn-sm" type="button">
                        <i class="fa-solid fa-download me-1"></i>ส่งออก Excel
                    </button>
                </div>
                <div class="is-datatable">
                    <table id="hospital-table" class="table table-hover mb-0" data-toggle="data-table" data-page-length="50" data-auto-width="false">
                        <colgroup>
                            <col style="width:75px; min-width:75px; max-width:75px;">
                            <col style="width:150px; min-width:150px; max-width:150px;">
                            <col style="width:70px; min-width:70px; max-width:70px;">
                            <col style="min-width:280px;">
                            <col style="width:130px; min-width:130px; max-width:130px;">
                            <col style="width:130px; min-width:130px; max-width:130px;">
                            <col style="width:190px; min-width:190px; max-width:190px;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>เขต</th>
                                <th>จังหวัด</th>
                                <th>ระดับ</th>
                                <th>โรงพยาบาล</th>
                                <th>จำนวนบันทึก</th>
                                <th>ครบ 21 ตัวแปร</th>
                                <th>คุณภาพข้อมูล</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($hospitalRows->sortByDesc(fn($row) => $row->records ? $row->complete_records / $row->records : 0) as $row)
                                @php($percent = $row->records ? ($row->complete_records / $row->records) * 100 : 0)
                                <tr class="align-top">
                                    <td>{{ $row->region }}</td>
                                    <td>{{ $row->province }}</td>
                                    <td>
                                        <span class="level-tag">{{ $row->splevel }}</span>
                                    </td>
                                    <td class="hospital-name">{{ $row->hospital }}</td>
                                    <td class="text-end">{{ number_format($row->records) }}</td>
                                    <td class="text-end">{{ number_format($row->complete_records) }}</td>
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
                        <tfoot class="fw-bold">
                            <tr>
                                <th colspan="4">รวม / เฉลี่ย</th>
                                <th id="hospital-record-sum" class="text-end">{{ number_format($recordTotal) }}</th>
                                <th id="hospital-complete-sum" class="text-end">{{ number_format($completeTotal) }}</th>
                                <th id="hospital-percent-average" class="text-end">0.00%</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>
            <div id="last-updated" class="small text-muted text-end mt-3 d-none">
                อัปเดตข้อมูลล่าสุดเมื่อ <span></span>
            </div>
        </div>
    </main>
@endsection
