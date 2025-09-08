@extends('layouts.app')
@section('page_title', 'เกิดข้อผิดพลาด')
@section('content')
    @php
        $alerts = [
            'primary' => 'สำเร็จ',
            'success' => 'สำเร็จ',
            'warning' => 'คำเตือน',
            'info' => 'รายละเอียด',
            'danger' => 'เกิดข้อผิดพลาด',
        ];

        $hasAlert = false;
        foreach ($alerts as $type => $title) {
            if (Session::has($type)) {
                $hasAlert = true;
                break;
            }
        }
    @endphp

    @if (!$hasAlert)
        {{-- ถ้าไม่มี message ให้ redirect ไปหน้าอื่น --}}
        <script>
            window.location.href = "https://connect.moph.go.th/pher-plus/";
        </script>
    @else
        <div class="content-inner pb-0 container-fluid" id="page_layout">
            <div class="row">
                <div class="col-lg-12">
                    @foreach ($alerts as $type => $title)
                        @if (Session::has($type))
                            <div class="row m-0 align-items-center">
                                <div class="justify-content-center text-center">
                                    <div class="alert alert-left alert-{{ $type }} alert-dismissible fade show mb-0" role="alert">
                                        @if (in_array($type, ['warning', 'danger']))
                                            <i class="fa-solid fa-triangle-exclamation mb-3" style="font-size: 3.5rem;"></i>
                                        @else
                                            <i class="fa-solid fa-circle-check mb-3 display-4"></i>
                                        @endif
                                        <p class="fs-3 fw-bolder mb-2">{{ $title }}</p>
                                        <p class="fs-5">
                                            <sup><i class="fa-solid fa-quote-left"></i></sup>
                                            {{ Session::get($type) }}
                                            <sup><i class="fa-solid fa-quote-right"></i></sup>
                                        </p>
                                        <a class="btn btn-primary" href="https://connect.moph.go.th/pher-plus/">
                                            <i class="fa-solid fa-arrow-left me-1"></i>
                                            <span class="item-name">กลับสู่ Pher Plus</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            localStorage.clear();
        });
    </script>
@endsection
