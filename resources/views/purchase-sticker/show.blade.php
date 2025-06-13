<!-- Purchase Sticker Detail Content -->
<div class="row">
    <!-- Product Information -->
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header bg-light border-bottom">
                <h6 class="mb-0 text-dark"><i class="fas fa-box me-2"></i> Informasi Produk</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td width="40%" class="text-muted"><strong>Nama Produk:</strong></td>
                        <td class="fw-medium">
                            {{ $purchaseSticker->product ? $purchaseSticker->product->name_product : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>SKU:</strong></td>
                        <td class="fw-medium">{{ $purchaseSticker->product ? $purchaseSticker->product->sku : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Label:</strong></td>
                        <td>
                            @if ($purchaseSticker->product)
                                <span class="badge bg-secondary">
                                    {{ $purchaseSticker->product->getLabelNameAttribute() }}
                                </span>
                            @else
                                <span class="badge bg-light text-dark">-</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Sticker Information -->
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header bg-light border-bottom">
                <h6 class="mb-0 text-dark"><i class="fas fa-tag me-2"></i> Informasi Stiker</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td width="40%" class="text-muted"><strong>Ukuran Stiker:</strong></td>
                        <td class="fw-medium">{{ $purchaseSticker->ukuran_stiker ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Jumlah Stiker / A3:</strong></td>
                        <td class="fw-medium">{{ number_format($purchaseSticker->jumlah_stiker) }} <small
                                class="text-muted">pcs/A3</small>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Tanggal Order:</strong></td>
                        <td class="fw-medium">
                            {{ $purchaseSticker->created_at ? $purchaseSticker->created_at->format('d/m/Y H:i') : '-' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Order and Stock Information -->
<div class="row">
    <div class="col-md-12 mb-3">
        <div class="card">
            <div class="card-header bg-light border-bottom">
                <h6 class="mb-0 text-dark"><i class="fas fa-shopping-cart me-2"></i> Informasi Order & Stok</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <div class="text-center p-3 border rounded">
                            <div class="h4 mb-1 text-dark">
                                {{ number_format($purchaseSticker->jumlah_order) }}</div>
                            <small class="text-muted fw-medium">Jumlah Order</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="text-center p-3 border rounded">
                            <div class="h4 mb-1 text-dark">
                                {{ number_format($purchaseSticker->stok_masuk) }}</div>
                            <small class="text-muted fw-medium">Stok Masuk</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="text-center p-3 border rounded">
                            <div class="h4 mb-1 text-dark">
                                {{ number_format($purchaseSticker->jumlah_order - $purchaseSticker->stok_masuk) }}</div>
                            <small class="text-muted fw-medium">Sisa Order</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="text-center p-3 border rounded">
                            @if ($purchaseSticker->stok_masuk >= $purchaseSticker->jumlah_order)
                                <span class="badge bg-success">SELESAI</span>
                            @elseif($purchaseSticker->stok_masuk > 0)
                                <span class="badge bg-warning">PROSES</span>
                            @else
                                <span class="badge bg-danger">BELUM</span>
                            @endif
                            <div class="mt-2">
                                <small class="text-muted fw-medium">Status</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Information -->
<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header bg-light border-bottom">
                <h6 class="mb-0 text-dark"><i class="fas fa-info-circle me-2"></i> Detail Order</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted"><strong>Jumlah Order:</strong></td>
                        <td class="fw-medium">{{ number_format($purchaseSticker->jumlah_order) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Stok Masuk:</strong></td>
                        <td class="fw-medium">{{ number_format($purchaseSticker->stok_masuk) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Sisa:</strong></td>
                        <td class="fw-medium">
                            {{ number_format($purchaseSticker->jumlah_order - $purchaseSticker->stok_masuk) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header bg-light border-bottom">
                <h6 class="mb-0 text-dark"><i class="fas fa-calendar me-2"></i> Informasi Tambahan</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" width="40%"><strong>Dibuat:</strong></td>
                        <td class="fw-medium">
                            {{ $purchaseSticker->created_at ? $purchaseSticker->created_at->format('d/m/Y H:i') : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Diupdate:</strong></td>
                        <td class="fw-medium">
                            {{ $purchaseSticker->updated_at ? $purchaseSticker->updated_at->format('d/m/Y H:i') : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Ukuran:</strong></td>
                        <td class="fw-medium">{{ $purchaseSticker->ukuran_stiker ?: '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-warning btn-sm"
                onclick="editPurchaseSticker({{ $purchaseSticker->id }}); $('#detailModal').modal('hide');">
                <i class="fas fa-edit me-1"></i> Edit Data
            </button>
            <button type="button" class="btn btn-danger btn-sm"
                onclick="deletePurchaseSticker({{ $purchaseSticker->id }}); $('#detailModal').modal('hide');">
                <i class="fas fa-trash me-1"></i> Hapus
            </button>
        </div>
    </div>
</div>

<style>
    /* Simple and clean styling */
    .card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 1rem;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 0.75rem 1rem;
        border-radius: 8px 8px 0 0;
    }

    .card-body {
        padding: 1rem;
    }

    .table td {
        padding: 0.5rem 0;
        border: none;
        vertical-align: middle;
    }

    .text-muted {
        color: #6c757d !important;
    }

    .fw-medium {
        font-weight: 500 !important;
    }

    .fw-bold {
        font-weight: 600 !important;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .btn {
        border-radius: 6px;
        font-weight: 500;
        padding: 0.375rem 0.75rem;
    }

    .border {
        border: 1px solid #dee2e6 !important;
    }

    .rounded {
        border-radius: 6px !important;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .card-body {
            padding: 0.75rem;
        }

        .card-header {
            padding: 0.5rem 0.75rem;
        }

        .d-flex.gap-2 {
            flex-direction: column;
            gap: 0.5rem !important;
        }

        .btn {
            width: 100%;
        }

        .h4 {
            font-size: 1.25rem !important;
        }
    }

    @media (max-width: 576px) {
        .table {
            font-size: 0.875rem;
        }

        .card-header h6 {
            font-size: 0.9rem;
        }
    }

    /* Print styles */
    @media print {
        .btn {
            display: none !important;
        }

        .card {
            box-shadow: none;
            border: 1px solid #000 !important;
        }
    }
</style>
