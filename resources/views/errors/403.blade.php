@extends('layouts.main')

@section('title', 'Access Denied')
@section('breadcrumb-item', '403 Error')

@section('css')
    <style>
        .error-container {
            text-align: center;
            padding: 40px 20px;
        }

        .error-icon {
            font-size: 80px;
            color: #f44336;
            margin-bottom: 20px;
        }

        .error-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }

        .error-message {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
        }

        .error-actions {
            margin-top: 30px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="error-container">
                        <div class="error-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h1 class="error-title">403 - Akses Ditolak</h1>
                        <div class="error-message">
                            <p>Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.</p>
                            <p>Silakan hubungi administrator sistem jika Anda memerlukan akses ke halaman ini.</p>
                        </div>
                        <div class="error-actions">
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                <i class="fas fa-home mr-2"></i> Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
