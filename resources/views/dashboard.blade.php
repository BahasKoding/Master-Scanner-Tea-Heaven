@extends('layouts.main')

@section('title', 'Dashboard')
@section('breadcrumb-item', 'Dashboard')
@section('breadcrumb-item-active', 'Overview')

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0" style="background-color: rgba(34, 139, 34, 0.1);">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex flex-column flex-md-row align-items-center align-items-md-center">
                        <div class="avatar avatar-lg mb-3 mb-md-0" style="background-color: #228B22;">
                            <i class="ti ti-user text-white f-22"></i>
                        </div>
                        <div class="ms-md-3 text-center text-md-start">
                            <h3 class="mb-1 fs-5 fs-md-4 fs-lg-3">Selamat Datang, {{ Auth::user()->name }}!</h3>
                            <p class="mb-0 text-muted">
                                Anda login sebagai <span class="badge"
                                    style="background-color: rgba(34, 139, 34, 0.2); color: #228B22;">{{ Auth::user()->getRoleNames()->first() }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feature Cards Row -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3 ps-2">Fitur Utama</h5>
        </div>

        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="card tea-card hover-shadow-lg h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="tea-icon-wrapper">
                            <i class="ti ti-history"></i>
                        </div>
                        <h5 class="ms-3 mb-0 fs-6 fs-md-5">Riwayat Penjualan</h5>
                    </div>
                    <p class="text-muted mb-3 small">Pantau dan kelola seluruh transaksi penjualan</p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-3 px-md-4">
                    <a href="{{ route('history-sales.index') }}" class="btn btn-sm btn-outline-primary w-100">
                        <i class="ti ti-arrow-right me-1"></i>Lihat Riwayat
                    </a>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="card tea-card hover-shadow-lg h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="tea-icon-wrapper">
                            <i class="ti ti-building-store"></i>
                        </div>
                        <h5 class="ms-3 mb-0 fs-6 fs-md-5">Supplier</h5>
                    </div>
                    <p class="text-muted mb-3 small">Kelola data supplier untuk kebutuhan usaha Anda</p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-3 px-md-4">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-outline-primary w-100">
                        <i class="ti ti-arrow-right me-1"></i>Kelola Supplier
                    </a>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="card tea-card hover-shadow-lg h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="tea-icon-wrapper">
                            <i class="ti ti-category"></i>
                        </div>
                        <h5 class="ms-3 mb-0 fs-6 fs-md-5">Kategori</h5>
                    </div>
                    <p class="text-muted mb-3 small">Atur kategori supplier dan produk untuk Tea Heaven</p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-3 px-md-4">
                    <a href="{{ route('category-suppliers.index') }}" class="btn btn-sm btn-outline-primary w-100">
                        <i class="ti ti-arrow-right me-1"></i>Kelola Kategori
                    </a>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="card tea-card hover-shadow-lg h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="tea-icon-wrapper">
                            <i class="ti ti-report-analytics"></i>
                        </div>
                        <h5 class="ms-3 mb-0 fs-6 fs-md-5">Laporan</h5>
                    </div>
                    <p class="text-muted mb-3 small">Akses dan unduh laporan penjualan untuk analisis</p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-3 px-md-4">
                    <a href="{{ route('history-sales.report') }}" class="btn btn-sm btn-outline-primary w-100">
                        <i class="ti ti-arrow-right me-1"></i>Lihat Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Tools Section (conditionally displayed based on role) -->
    @if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Super Admin'))
        <div class="row">
            <div class="col-12">
                <h5 class="mb-3 ps-2">Admin Tools</h5>
                <div class="card">
                    <div class="card-body p-3 p-md-4">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-4 mb-2 mb-lg-0">
                                <a href="{{ route('users.index') }}"
                                    class="card shadow-none border tea-card-link text-decoration-none h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="tea-icon-wrapper-sm">
                                                <i class="ti ti-users"></i>
                                            </div>
                                            <div class="ms-3">
                                                <h6 class="mb-1">Pengguna</h6>
                                                <p class="text-muted mb-0 small">Kelola akun pengguna</p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-4 mb-2 mb-lg-0">
                                <a href="{{ route('roles.index') }}"
                                    class="card shadow-none border tea-card-link text-decoration-none h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="tea-icon-wrapper-sm">
                                                <i class="ti ti-shield-lock"></i>
                                            </div>
                                            <div class="ms-3">
                                                <h6 class="mb-1">Peran & Hak Akses</h6>
                                                <p class="text-muted mb-0 small">Atur peran dan izin</p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-4 mb-2 mb-lg-0">
                                <a href="{{ route('activity') }}"
                                    class="card shadow-none border tea-card-link text-decoration-none h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="tea-icon-wrapper-sm">
                                                <i class="ti ti-activity"></i>
                                            </div>
                                            <div class="ms-3">
                                                <h6 class="mb-1">Aktivitas</h6>
                                                <p class="text-muted mb-0 small">Log aktivitas pengguna</p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!-- [ Main Content ] end -->

    <style>
        .hover-shadow-lg {
            transition: all 0.3s ease;
        }

        .hover-shadow-lg:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .avatar {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .tea-card {
            border: 1px solid rgba(34, 139, 34, 0.1);
            transition: all 0.3s ease;
        }

        .tea-card:hover {
            border-color: #228B22;
        }

        .tea-card-link {
            transition: all 0.3s ease;
        }

        .tea-card-link:hover {
            border-color: #228B22 !important;
            background-color: rgba(34, 139, 34, 0.05);
        }

        .btn-outline-primary {
            color: #228B22;
            border-color: #228B22;
        }

        .btn-outline-primary:hover {
            background-color: #228B22;
            border-color: #228B22;
            color: white;
        }

        .tea-icon-wrapper {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: rgba(34, 139, 34, 0.1);
        }

        .tea-icon-wrapper i {
            color: #228B22;
            font-size: 20px;
        }

        .tea-icon-wrapper-sm {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: rgba(34, 139, 34, 0.1);
        }

        .tea-icon-wrapper-sm i {
            color: #228B22;
            font-size: 18px;
        }

        /* Extra small devices (phones, up to 575.98px) */
        @media (max-width: 575.98px) {
            .avatar.avatar-lg {
                width: 45px;
                height: 45px;
                margin: 0 auto;
            }

            h3.fs-5 {
                font-size: 1.1rem !important;
            }

            .card-body {
                padding: 0.875rem;
            }

            .tea-icon-wrapper {
                width: 38px;
                height: 38px;
            }

            .tea-icon-wrapper i {
                font-size: 18px;
            }
        }

        /* Small devices (landscape phones, 576px and up) */
        @media (min-width: 576px) and (max-width: 767.98px) {
            .avatar.avatar-lg {
                width: 48px;
                height: 48px;
            }

            h3.fs-5 {
                font-size: 1.15rem !important;
            }
        }

        /* Medium devices (tablets, 768px and up) */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .col-sm-6 {
                margin-bottom: 1.5rem;
            }

            h3.fs-md-4 {
                font-size: 1.35rem !important;
            }
        }

        /* Large devices (desktops, 992px and up) */
        @media (min-width: 992px) {
            h3.fs-lg-3 {
                font-size: 1.5rem !important;
            }
        }
    </style>
@endsection
