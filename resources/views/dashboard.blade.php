@extends('layouts.main')

@section('title', 'Dashboard')
@section('breadcrumb-item', 'Dashboard')
@section('breadcrumb-item-active', 'Overview')

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Selamat Datang</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-success" role="alert">
                        <h4 class="alert-heading">Halo!</h4>
                        <p>Selamat datang di aplikasi Master Tea Heaven.</p>
                        <hr>
                        <p class="mb-0">Gunakan menu di sebelah kiri untuk navigasi ke fitur yang tersedia.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple Cards Row -->
    <div class="row">
        <div class="col-md-6 col-xl-3">
            <div class="card bg-light-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-primary">
                            <i class="ti ti-users text-white f-20"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 text-muted">Pengguna</h6>
                            <h5 class="mb-0">Manajemen Pengguna</h5>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-primary">Lihat Pengguna</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card bg-light-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-success">
                            <i class="ti ti-building-store text-white f-20"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 text-muted">Supplier</h6>
                            <h5 class="mb-0">Daftar Supplier</h5>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-success">Kelola Supplier</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card bg-light-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-warning">
                            <i class="ti ti-category text-white f-20"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 text-muted">Kategori</h6>
                            <h5 class="mb-0">Kategori Supplier</h5>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('category-suppliers.index') }}" class="btn btn-sm btn-warning">Kelola Kategori</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card bg-light-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-info">
                            <i class="ti ti-category-2 text-white f-20"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 text-muted">Kategori</h6>
                            <h5 class="mb-0">Kategori Produk</h5>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('category-products.index') }}" class="btn btn-sm btn-info">Kelola Kategori</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Akses Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card shadow-none border">
                                <div class="card-body p-3 text-center">
                                    <i class="ti ti-history text-primary f-36 mb-3"></i>
                                    <h5>Riwayat Penjualan</h5>
                                    <p class="text-muted mb-3">Kelola riwayat penjualan dan laporan</p>
                                    <a href="{{ route('history-sales.index') }}" class="btn btn-outline-primary">Buka</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow-none border">
                                <div class="card-body p-3 text-center">
                                    <i class="ti ti-shield-lock text-success f-36 mb-3"></i>
                                    <h5>Manajemen Hak Akses</h5>
                                    <p class="text-muted mb-3">Kelola peran dan izin pengguna</p>
                                    <a href="{{ route('roles.index') }}" class="btn btn-outline-success">Buka</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow-none border">
                                <div class="card-body p-3 text-center">
                                    <i class="ti ti-report-analytics text-warning f-36 mb-3"></i>
                                    <h5>Laporan</h5>
                                    <p class="text-muted mb-3">Lihat dan ekspor laporan penjualan</p>
                                    <a href="{{ route('history-sales.report') }}" class="btn btn-outline-warning">Buka</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
@endsection
