<div class="container-fluid p-0">
    <!-- Basic Purchase Information Section -->
    <div class="row g-3">
        <div class="col-lg-6 col-md-12">
            <div class="card h-100">
                <div class="card-header bg-light border-bottom">
                    <h6 class="mb-0 text-dark"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Purchase</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="45%" class="fw-bold text-muted">Bahan Baku:</td>
                                <td class="text-break">
                                    {{ $purchase->bahanBaku ? $purchase->bahanBaku->full_name : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Satuan:</td>
                                <td>{{ $purchase->bahanBaku ? $purchase->bahanBaku->satuan : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Qty Pembelian:</td>
                                <td><span
                                        class="badge bg-primary bg-opacity-75">{{ number_format($purchase->qty_pembelian) }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12">
            <div class="card h-100">
                <div class="card-header bg-light border-bottom">
                    <h6 class="mb-0 text-dark"><i class="fas fa-calendar-check me-2 text-secondary"></i>Informasi
                        Penerimaan</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="45%" class="fw-bold text-muted">Tanggal Kedatangan:</td>
                                <td>
                                    @if ($purchase->tanggal_kedatangan_barang)
                                        <span
                                            class="badge bg-secondary bg-opacity-75">{{ $purchase->tanggal_kedatangan_barang->format('d/m/Y') }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Penerima Barang:</td>
                                <td>{{ $purchase->checker_penerima_barang ?: '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Penerimaan Barang Section -->
    <div class="row g-3 mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light border-bottom">
                    <h6 class="mb-0 text-dark"><i class="fas fa-clipboard-list me-2 text-success"></i>Detail Penerimaan
                        Barang</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-sm-12 mb-3">
                            <label class="form-label fw-bold text-muted">Qty Barang Masuk</label>
                            <div>
                                <span
                                    class="badge bg-primary bg-opacity-75 fs-6">{{ number_format($purchase->qty_barang_masuk) }}</span>
                                <span
                                    class="text-muted ms-2">{{ $purchase->bahanBaku ? $purchase->bahanBaku->satuan : '' }}</span>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12 mb-3">
                            <label class="form-label fw-bold text-muted">Barang Defect</label>
                            <div>
                                <span
                                    class="badge bg-warning bg-opacity-75 fs-6 text-dark">{{ number_format($purchase->barang_defect_tanpa_retur) }}</span>
                                <span
                                    class="text-muted ms-2">{{ $purchase->bahanBaku ? $purchase->bahanBaku->satuan : '' }}</span>
                                @if ($purchase->qty_barang_masuk > 0)
                                    <br><small class="text-muted">({{ $purchase->defect_percentage }}%)</small>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12 mb-3">
                            <label class="form-label fw-bold text-muted">Barang Retur</label>
                            <div>
                                <span
                                    class="badge bg-danger bg-opacity-75 fs-6">{{ number_format($purchase->barang_diretur_ke_supplier) }}</span>
                                <span
                                    class="text-muted ms-2">{{ $purchase->bahanBaku ? $purchase->bahanBaku->satuan : '' }}</span>
                                @if ($purchase->qty_pembelian > 0)
                                    <br><small class="text-muted">({{ $purchase->retur_percentage }}%)</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-light border border-success">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calculator text-success me-2"></i>
                            <strong class="text-dark">Total Stok Masuk:</strong>
                            <span
                                class="badge bg-success bg-opacity-75 fs-6 ms-2">{{ number_format($purchase->total_stok_masuk) }}</span>
                            <span
                                class="text-muted ms-1">{{ $purchase->bahanBaku ? $purchase->bahanBaku->satuan : '' }}</span>
                        </div>
                        <small class="text-muted d-block mt-1">Formula: Qty Masuk - Defect + Retur</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formula Calculation Section -->
    @if ($purchase->bahanBaku)
        <div class="row g-3 mt-2">
            <div class="col-12">
                <div class="card border-info border-opacity-50">
                    <div class="card-header bg-light border-bottom">
                        <h6 class="mb-0 text-dark"><i class="fas fa-calculator me-2 text-info"></i>Formula Perhitungan
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-light border border-info border-opacity-50 mb-0">
                            <div class="row align-items-center">
                                <div class="col-lg-8 col-md-12 mb-2 mb-lg-0">
                                    <strong class="text-dark">Total Stok Masuk = Qty Barang Masuk - Barang Defect +
                                        Barang Retur</strong>
                                </div>
                                <div class="col-lg-4 col-md-12">
                                    <div class="text-center p-3 bg-white rounded border border-opacity-50">
                                        <div class="d-flex justify-content-center align-items-center flex-wrap gap-2">
                                            <span
                                                class="badge bg-primary bg-opacity-75">{{ number_format($purchase->qty_barang_masuk) }}</span>
                                            <span class="text-muted">-</span>
                                            <span
                                                class="badge bg-warning bg-opacity-75 text-dark">{{ number_format($purchase->barang_defect_tanpa_retur) }}</span>
                                            <span class="text-muted">+</span>
                                            <span
                                                class="badge bg-danger bg-opacity-75">{{ number_format($purchase->barang_diretur_ke_supplier) }}</span>
                                            <span class="text-muted">=</span>
                                            <strong
                                                class="badge bg-success bg-opacity-75 fs-6">{{ number_format($purchase->total_stok_masuk) }}
                                                {{ $purchase->bahanBaku->satuan }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- System Information Section -->
    <div class="row g-3 mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light border-bottom">
                    <h6 class="mb-0 text-dark"><i class="fas fa-server me-2 text-secondary"></i>Informasi Sistem</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-sm-12 mb-2">
                            <small class="text-muted">Dibuat:</small><br>
                            <span class="fw-bold text-dark">{{ $purchase->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        <div class="col-md-6 col-sm-12 mb-2">
                            <small class="text-muted">Diperbarui:</small><br>
                            <span class="fw-bold text-dark">{{ $purchase->updated_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Responsive improvements for the detail view */
    @media (max-width: 576px) {
        .table td {
            padding: 0.5rem 0.25rem;
            font-size: 0.875rem;
        }

        .badge {
            font-size: 0.75rem;
        }

        .card-header h6 {
            font-size: 0.9rem;
        }

        .text-break {
            word-break: break-word;
            hyphens: auto;
        }
    }

    @media (max-width: 768px) {
        .table td:first-child {
            width: 40%;
        }

        .d-flex.flex-wrap .badge {
            margin: 2px;
        }
    }

    @media (min-width: 992px) {
        .card {
            height: 100%;
        }
    }

    /* Enhanced badge styling */
    .badge {
        font-weight: 500;
        padding: 0.5em 0.75em;
    }

    .badge.fs-6 {
        font-size: 1rem !important;
        padding: 0.6em 1em;
    }

    /* Improved spacing */
    .g-3>* {
        padding-right: 0.75rem;
        padding-left: 0.75rem;
    }

    .g-3 {
        margin-right: -0.75rem;
        margin-left: -0.75rem;
    }

    /* Table improvements */
    .table-borderless td {
        border: none;
        vertical-align: middle;
    }

    .table-sm td {
        padding: 0.5rem;
    }

    /* Card improvements */
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }

    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }

    /* Alert improvements */
    .alert-light {
        background-color: #f8f9fa;
    }
</style>
