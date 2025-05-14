@extends('layouts.main')

@section('title', 'Page Expired')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="error-block mt-5">
                    <h1 class="error-title mb-4">419</h1>
                    <h3 class="mb-3">Halaman Kedaluwarsa</h3>
                    <p class="mb-4 text-muted">Maaf, sesi Anda telah berakhir. Silakan muat ulang halaman dan coba lagi.
                    </p>

                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                            <i class="ti ti-home me-2"></i>Kembali ke Dashboard
                        </a>
                        <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                            <i class="ti ti-refresh me-2"></i>Muat Ulang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .error-title {
            font-size: 7rem;
            font-weight: 700;
            color: #F57C00;
            text-shadow: 2px 2px 8px rgba(245, 124, 0, 0.2);
        }

        .error-block {
            padding: 3rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
    </style>
@endsection
