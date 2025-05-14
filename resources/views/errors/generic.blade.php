@extends('layouts.main')

@section('title', isset($exception) ? $exception->getStatusCode() . ' Error' : 'Error')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="error-block mt-5">
                    <h1 class="error-title mb-4">{{ isset($exception) ? $exception->getStatusCode() : 'Error' }}</h1>
                    <h3 class="mb-3">
                        {{ isset($exception) && $exception->getMessage() ? $exception->getMessage() : 'Terjadi Kesalahan' }}
                    </h3>
                    <p class="mb-4 text-muted">Maaf, terjadi kesalahan saat memproses permintaan Anda.
                    </p>

                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                            <i class="ti ti-home me-2"></i>Kembali ke Dashboard
                        </a>
                        <button onclick="window.history.back()" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-2"></i>Kembali
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .error-title {
            font-size: 7rem;
            font-weight: 700;
            color: #607D8B;
            text-shadow: 2px 2px 8px rgba(96, 125, 139, 0.2);
        }

        .error-block {
            padding: 3rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
    </style>
@endsection
