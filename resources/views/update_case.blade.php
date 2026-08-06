@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 style="font-weight: 500">แก้ไข Case</h1>
        <form id="case-form" method="post" action="{{ route('submit_new_case') }}" class="mt-4 w-100">
            @csrf
            <div>
                <div class="border rounded-3 bg-white p-3 p-md-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                        <div>
                            <div class="fw-semibold">รายละเอียด Case</div>
                            <small class="text-muted">กำหนดเงื่อนไขและตัวแปรที่ใช้ตรวจสอบ</small>
                        </div>
                        <span class="badge bg-light text-dark border">Case {{ $case->number }}</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="name" class="form-label fw-semibold">ชื่อ Case</label>
                            <input id="name" type="text" class="form-control" name="name" value="{{ $case->name }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="error_type" class="form-label fw-semibold">ประเภทความผิดพลาด</label>
                            <select id="error_type" class="custom-select form-control select2" name="error_type" required>
                                <option @if ($case->errorType == 1) selected @endif value="1">ความถูกต้อง (Accuracy)</option>
                                <option @if ($case->errorType == 2) selected @endif value="2">ความสมบูรณ์ (Completeness)</option>
                                <option @if ($case->errorType == 3) selected @endif value="3">ความเที่ยงตรง (Consistency)</option>
                                <option @if ($case->errorType == 4) selected @endif value="4">ความตรงตามกาล (Timeliness)</option>
                                <option @if ($case->errorType == 5) selected @endif value="5">ความเป็นเอกลักษณ์ (Uniqueness)</option>
                                <option @if ($case->errorType == 6) selected @endif value="6">ความแม่นยำ (Orderliness)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">คำอธิบายเงื่อนไข</label>
                            <textarea id="description" class="form-control" name="description" rows="3">{{ $case->description }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="border rounded-3 bg-light p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="fw-semibold">ตัวแปรที่ใช้ตรวจสอบ</div>
                            <small class="text-muted">เลือกฟิลด์ที่ระบบใช้ตรวจสอบสำหรับ Case นี้</small>
                        </div>
                        <button id="reset-check-fields" type="button" class="btn btn-sm btn-outline-secondary">
                            <i class="fa-solid fa-arrow-rotate-right me-1"></i>
                            คืนค่าฟิลด์เดิม
                        </button>
                    </div>
                    <div id="field-change-legend" class="d-flex flex-wrap gap-2 mb-2">
                        <span class="small text-muted align-self-center me-1">สถานะการเลือกฟิลด์:</span>
                        <span class="field-change-key field-change-key--current"><span></span>เลือกอยู่เดิม</span>
                        <span class="field-change-key field-change-key--new"><span></span>เลือกเพิ่ม</span>
                        <span class="field-change-key field-change-key--removed"><span></span>ยกเลิกการเลือก</span>
                    </div>
                    <div id="selected-fields-section" class="border rounded bg-white p-3 mb-3 d-none">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-semibold text-dark">ฟิลด์ที่เลือกแล้ว</span>
                            <span class="small text-muted"><span id="selected-fields-count"></span> รายการ</span>
                        </div>
                        <div id="selected-fields" class="row gx-2"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center small text-muted mb-2">
                        <span class="fw-semibold">เลือกฟิลด์เพิ่มเติม</span>
                        <span>ค้นหาแล้วติ๊กเพื่อเพิ่ม</span>
                    </div>
                    <div class="input-group mb-2">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="search" id="field-search" class="form-control" placeholder="ค้นหาชื่อฟิลด์ เช่น age, injp">
                    </div>
                    <div class="field-picker border rounded p-2 bg-white" style="max-height: 280px; overflow-y: auto;">
                        <div id="available-fields">
                            @foreach ($fields as $field)
                                <div class="col-md-3 col-sm-4 col-6 mb-2 check-field">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="check_fields[]" value="{{ $field }}" id="field-{{ $field }}" @if(in_array($field, $selectedFields, true)) checked @endif>
                                        <label class="form-check-label" for="field-{{ $field }}">{{ $field }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div id="no-fields-found" class="text-center text-muted py-3 d-none">ไม่พบฟิลด์ที่ค้นหา</div>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fa-solid fa-floppy-disk me-1"></i>
                        บันทึกการแก้ไข
                    </button>
                </div>
            </div>
            <input name="id" type="hidden" value="{{ $case->id }}">
        </form>

        @if (Session::has('duplicated case'))
            <div class="alert alert-warning m-2" role="alert">
                <span><strong>Case นี้ถูกสร้างแล้ว</strong></span>
            </div>
        @endif

        @error('number')
            <div class="alert alert-danger m-2" role="alert" style="width: 50%">
                <span><strong>Number ต้องเป็น ตัวเลขเท่านั้น</strong></span>
            </div>
        @enderror
    </div>
@endsection

@section('script')
    <style>
        #selected-fields .check-field {
            flex: 0 0 auto;
            width: auto;
            margin-bottom: .35rem !important;
        }

        #selected-fields .form-check {
            display: flex;
            align-items: center;
            gap: .35rem;
            margin: 0;
            padding: .2rem .6rem;
            border: 1px solid #b8d9c4;
            border-radius: .375rem;
            background: #edf8f0;
        }

        #selected-fields .form-check-input {
            margin: 0;
        }

        #selected-fields .check-field.is-new-field .form-check {
            border-color: #9ec5fe;
            background: #edf5ff;
        }

        #available-fields {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: .25rem .5rem;
        }

        #available-fields .check-field {
            flex: none;
            width: auto;
            margin: 0 !important;
        }

        #available-fields .form-check {
            display: flex;
            align-items: center;
            gap: .5rem;
            min-height: 38px;
            margin: 0;
            padding: .35rem .55rem;
            border: 1px solid transparent;
            border-radius: .375rem;
            cursor: pointer;
        }

        #available-fields .form-check-input {
            flex: 0 0 auto;
            margin: 0;
        }

        .check-field .form-check-label {
            cursor: pointer;
        }

        #available-fields .form-check:hover {
            border-color: #c7dfce;
            background: #f1f8f3;
        }

        #available-fields .check-field.is-removed-field .form-check {
            border-color: #f5b5bc;
            background: #fff0f1;
        }

        .field-change-key {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .25rem .55rem;
            border: 1px solid;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 600;
        }

        .field-change-key span {
            width: 7px;
            height: 7px;
            border-radius: 50%;
        }

        .field-change-key--new {
            color: #0d6efd;
            border-color: #b9d4ff;
            background: #edf5ff;
        }

        .field-change-key--new span {
            background: #0d6efd;
        }

        .field-change-key--current {
            color: #198754;
            border-color: #b8d9c4;
            background: #edf8f0;
        }

        .field-change-key--current span {
            background: #198754;
        }

        .field-change-key--removed {
            color: #b4232d;
            border-color: #fac8cc;
            background: #fff0f1;
        }

        .field-change-key--removed span {
            background: #dc3545;
        }
    </style>
    <script>
        const initialCheckFields = new Set($('.check-field input:checked').map(function() {
            return this.value;
        }).get());

        function arrangeCheckFields() {
            $('.check-field').each(function() {
                const input = $(this).find('input');
                const isChecked = input.is(':checked');
                const isNew = isChecked && !initialCheckFields.has(input.val());
                const isRemoved = !isChecked && initialCheckFields.has(input.val());
                $(this).toggleClass('is-new-field', isNew).toggleClass('is-removed-field', isRemoved);
                const target = isChecked ? '#selected-fields' : '#available-fields';
                $(target).append(this);
            });
            const count = $('#selected-fields .check-field').length;
            $('#selected-fields-count').text(count);
            $('#selected-fields-section').toggleClass('d-none', count === 0);
            $('#reset-check-fields').toggleClass('d-none', $('.is-new-field, .is-removed-field').length === 0);
        }

        arrangeCheckFields();

        $(document).on('change', '.check-field input', arrangeCheckFields);

        $(document).on('click', '.check-field .form-check', function(event) {
            if ($(event.target).is('input, label')) return;
            $(this).find('input').trigger('click');
        });

        $('#case-form').on('submit', function(event) {
            event.preventDefault();
            const form = this;

            AppSwal.confirmSave({
                title: 'บันทึกการแก้ไข?',
                text: 'ต้องการบันทึกข้อมูล Case ที่แก้ไขใช่หรือไม่?',
                confirmButtonText: 'บันทึก'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });

        $('#reset-check-fields').on('click', function() {
            AppSwal.confirmAction({
                title: 'คืนค่าฟิลด์เดิม?',
                text: 'การเลือกฟิลด์ปัจจุบันจะกลับไปเป็นค่าเดิมตอนเปิดหน้า',
                confirmButtonText: 'คืนค่าเดิม'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $('.check-field input').each(function() {
                    $(this).prop('checked', initialCheckFields.has(this.value));
                });
                $('#field-search').val('').trigger('input');
                arrangeCheckFields();
            });
        });

        $('#field-search').on('input', function() {
            const keyword = this.value.toLowerCase();
            $('#available-fields .check-field').each(function() {
                $(this).toggle($(this).text().toLowerCase().includes(keyword));
            });
            $('#no-fields-found').toggleClass('d-none', $('#available-fields .check-field:visible').length > 0);
        });
    </script>
@endsection
