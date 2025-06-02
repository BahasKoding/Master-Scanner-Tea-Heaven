<!-- Purchase Sticker Detail Content -->
<div class="row">
    <!-- Product Information -->
    <div class="col-md-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-header" style="background: linear-gradient(135deg, #6c7ae0 0%, #7b88d1 100%); color: white;">
                <h6 class="mb-0"><i class="fas fa-box me-2"></i> Informasi Produk</h6>
            </div>
            <div class="card-body bg-light">
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
                                @switch($purchaseSticker->product->label)
                                    @case(1)
                                        <span class="badge"
                                            style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;">
                                            <i class="fas fa-leaf me-1"></i>Tea Bag
                                        </span>
                                    @break

                                    @case(2)
                                        <span class="badge"
                                            style="background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb;">
                                            <i class="fas fa-coffee me-1"></i>Drip Bag
                                        </span>
                                    @break

                                    @case(5)
                                        <span class="badge"
                                            style="background-color: #fff3cd; color: #856404; border: 1px solid #ffeaa7;">
                                            <i class="fas fa-box me-1"></i>Box Tea
                                        </span>
                                    @break

                                    @default
                                        <span class="badge"
                                            style="background-color: #f8f9fa; color: #6c757d; border: 1px solid #dee2e6;">
                                            <i class="fas fa-tag me-1"></i>Lainnya
                                        </span>
                                @endswitch
                            @else
                                <span class="badge" style="background-color: #f8f9fa; color: #6c757d;">-</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Sticker Information -->
    <div class="col-md-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-header"
                style="background: linear-gradient(135deg, #51cf66 0%, #69db7c 100%); color: white;">
                <h6 class="mb-0"><i class="fas fa-tag me-2"></i> Informasi Stiker</h6>
            </div>
            <div class="card-body bg-light">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td width="40%" class="text-muted"><strong>Ukuran Stiker:</strong></td>
                        <td class="fw-medium">{{ $purchaseSticker->ukuran_stiker ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Jumlah Stiker / A3:</strong></td>
                        <td class="fw-medium text-primary">{{ number_format($purchaseSticker->jumlah_stiker) }} <small
                                class="text-muted">pcs/A3</small>
                            <span class="badge bg-light text-dark border ms-2">REFERENSI</span>
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
        <div class="card border-0 shadow-sm">
            <div class="card-header"
                style="background: linear-gradient(135deg, #ffd43b 0%, #ffec8c 100%); color: #495057;">
                <h6 class="mb-0"><i class="fas fa-shopping-cart me-2"></i> Informasi Order & Stok</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <div class="text-center p-3 rounded-3"
                            style="background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%); border: 1px solid #e1bee7;">
                            <div class="h4 mb-1" style="color: #1976d2;">
                                {{ number_format($purchaseSticker->jumlah_order) }}</div>
                            <small class="text-muted fw-medium">Jumlah Order<br><span
                                    class="badge bg-primary">DINAMIS</span></small>
                            <div class="mt-1">
                                <i class="fas fa-clipboard-list" style="color: #1976d2; opacity: 0.7;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="text-center p-3 rounded-3"
                            style="background: linear-gradient(135deg, #e8f5e8 0%, #f1f8e9 100%); border: 1px solid #c8e6c9;">
                            <div class="h4 mb-1" style="color: #388e3c;">
                                {{ number_format($purchaseSticker->stok_masuk) }}</div>
                            <small class="text-muted fw-medium">Stok Masuk<br><span
                                    class="badge bg-success">DINAMIS</span></small>
                            <div class="mt-1">
                                <i class="fas fa-box-open" style="color: #388e3c; opacity: 0.7;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="text-center p-3 rounded-3"
                            style="background: linear-gradient(135deg, #fff8e1 0%, #fffde7 100%); border: 1px solid #fff176;">
                            <div class="h4 mb-1" style="color: #f57c00;">
                                {{ number_format($purchaseSticker->total_order) }}</div>
                            <small class="text-muted fw-medium">Total Order Final<br><span
                                    class="badge bg-warning">DINAMIS</span></small>
                            <div class="mt-1">
                                <i class="fas fa-bullseye" style="color: #f57c00; opacity: 0.7;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="text-center p-3 rounded-3"
                            style="background: linear-gradient(135deg, #fce4ec 0%, #f3e5f5 100%); border: 1px solid #f8bbd9;">
                            <div class="h4 mb-1" style="color: #c2185b;">
                                {{ number_format($purchaseSticker->total_order - $purchaseSticker->stok_masuk) }}</div>
                            <small class="text-muted fw-medium">Sisa Order<br><span
                                    class="badge bg-info">KALKULASI</span></small>
                            <div class="mt-1">
                                <i class="fas fa-hourglass-half" style="color: #c2185b; opacity: 0.7;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Progress and Analysis -->
<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-header"
                style="background: linear-gradient(135deg, #26c6da 0%, #4dd0e1 100%); color: white;">
                <h6 class="mb-0"><i class="fas fa-box-open me-2"></i> Status Penerimaan</h6>
            </div>
            <div class="card-body" style="background-color: #f8fffe;">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end border-2" style="border-color: #e0e0e0 !important;">
                            <div class="fw-bold" style="color: #4caf50; font-size: 1.5rem;">
                                {{ number_format($purchaseSticker->stok_masuk) }}</div>
                            <small class="text-muted fw-medium">Sudah Diterima</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="fw-bold" style="color: #f57c00; font-size: 1.5rem;">
                            {{ number_format($purchaseSticker->total_order) }}</div>
                        <small class="text-muted fw-medium">Target</small>
                    </div>
                </div>

                @if ($purchaseSticker->stok_masuk >= $purchaseSticker->total_order)
                    <div class="text-center mt-3">
                        <span class="badge bg-success fs-6 px-3 py-2">
                            <i class="fas fa-check-circle me-1"></i> SELESAI
                        </span>
                    </div>
                @elseif($purchaseSticker->stok_masuk > 0)
                    <div class="text-center mt-3">
                        <span class="badge bg-warning fs-6 px-3 py-2">
                            <i class="fas fa-clock me-1"></i> PROSES
                        </span>
                        <div class="mt-2">
                            <small class="text-muted">Kurang:
                                <strong>{{ number_format($purchaseSticker->total_order - $purchaseSticker->stok_masuk) }}</strong></small>
                        </div>
                    </div>
                @else
                    <div class="text-center mt-3">
                        <span class="badge bg-danger fs-6 px-3 py-2">
                            <i class="fas fa-exclamation-circle me-1"></i> BELUM
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-header"
                style="background: linear-gradient(135deg, #ab47bc 0%, #ba68c8 100%); color: white;">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i> Detail Order</h6>
            </div>
            <div class="card-body" style="background-color: #fafafa;">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted"><strong>Rencana Order:</strong></td>
                        <td class="fw-bold text-primary">{{ number_format($purchaseSticker->jumlah_order) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Order Final:</strong></td>
                        <td class="fw-bold text-warning">{{ number_format($purchaseSticker->total_order) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Sudah Masuk:</strong></td>
                        <td class="fw-bold text-success">{{ number_format($purchaseSticker->stok_masuk) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Sisa:</strong></td>
                        <td class="fw-bold text-danger">
                            {{ number_format($purchaseSticker->total_order - $purchaseSticker->stok_masuk) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Status Summary -->
<div class="row">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header"
                style="background: linear-gradient(135deg, #37474f 0%, #546e7a 100%); color: white;">
                <h6 class="mb-0"><i class="fas fa-calendar me-2"></i> Informasi Tambahan</h6>
            </div>
            <div class="card-body" style="background-color: #fafafa;">
                <div class="row">
                    <div class="col-md-6">
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
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted" width="40%"><strong>Referensi:</strong></td>
                                <td class="fw-medium">{{ number_format($purchaseSticker->jumlah_stiker) }} stiker/A3
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
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-sm shadow-sm"
                style="background: linear-gradient(135deg, #ffc107 0%, #ffca28 100%); color: #495057; border: none; padding: 8px 16px;"
                onclick="editPurchaseSticker({{ $purchaseSticker->id }}); $('#detailModal').modal('hide');">
                <i class="fas fa-edit me-1"></i> Edit Data
            </button>
            <button type="button" class="btn btn-sm shadow-sm"
                style="background: linear-gradient(135deg, #f44336 0%, #ef5350 100%); color: white; border: none; padding: 8px 16px;"
                onclick="deletePurchaseSticker({{ $purchaseSticker->id }}); $('#detailModal').modal('hide');">
                <i class="fas fa-trash me-1"></i> Hapus
            </button>
        </div>
    </div>
</div>

<style>
    /* Card Styling - More comfortable and modern */
    .card {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: none !important;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 1rem;
    }

    .card-header {
        border-bottom: none;
        font-weight: 600;
        border-radius: 12px 12px 0 0 !important;
        padding: 1rem 1.25rem;
    }

    .card-body {
        padding: 1.25rem;
        border-radius: 0 0 12px 12px;
    }

    /* Progress Bar Improvements */
    .progress {
        background-color: #f1f3f4;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .progress-bar {
        transition: width 0.8s ease-in-out;
        border-radius: 12px;
        position: relative;
        font-size: 0.875rem;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    /* Alert Improvements */
    .alert {
        border-radius: 8px;
        border: none;
        padding: 12px 16px;
        margin-bottom: 0;
        font-size: 0.9rem;
    }

    .alert strong {
        font-weight: 600;
    }

    /* Badge Improvements */
    .badge {
        font-size: 0.8rem;
        font-weight: 500;
        padding: 6px 10px;
        border-radius: 6px;
        letter-spacing: 0.5px;
    }

    /* Table Improvements */
    .table td {
        padding: 0.75rem 0;
        border: none;
        vertical-align: middle;
    }

    .table .text-muted {
        font-size: 0.875rem;
        font-weight: 500;
    }

    .fw-medium {
        font-weight: 500 !important;
    }

    /* Statistics Cards */
    .stats-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 12px;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Button Improvements */
    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
        border: none !important;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Color Palette - Soft and comfortable */
    .text-soft-primary {
        color: #1976d2 !important;
    }

    .text-soft-success {
        color: #388e3c !important;
    }

    .text-soft-warning {
        color: #f57c00 !important;
    }

    .text-soft-danger {
        color: #c62828 !important;
    }

    .text-soft-info {
        color: #0288d1 !important;
    }

    .text-soft-secondary {
        color: #424242 !important;
    }

    /* Background gradients that are easy on the eyes */
    .bg-gradient-blue {
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
        border: 1px solid #e1bee7;
    }

    .bg-gradient-green {
        background: linear-gradient(135deg, #e8f5e8 0%, #f1f8e9 100%);
        border: 1px solid #c8e6c9;
    }

    .bg-gradient-orange {
        background: linear-gradient(135deg, #fff8e1 0%, #fffde7 100%);
        border: 1px solid #fff176;
    }

    .bg-gradient-pink {
        background: linear-gradient(135deg, #fce4ec 0%, #f3e5f5 100%);
        border: 1px solid #f8bbd9;
    }

    /* Icon styling */
    .fas {
        opacity: 0.9;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .card-body {
            padding: 1rem;
        }

        .card-header {
            padding: 0.75rem 1rem;
        }

        .row .col-md-6,
        .row .col-md-3 {
            margin-bottom: 1rem;
        }

        .d-flex.gap-2 {
            flex-direction: column;
            gap: 0.5rem !important;
        }

        .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }

        .h4 {
            font-size: 1.5rem !important;
        }

        .progress {
            height: 20px !important;
        }

        .progress-bar span {
            font-size: 0.75rem;
        }
    }

    @media (max-width: 576px) {
        .stats-card {
            margin-bottom: 1rem;
        }

        .alert {
            font-size: 0.85rem;
            padding: 10px 12px;
        }

        .table {
            font-size: 0.875rem;
        }

        .card-header h6 {
            font-size: 0.95rem;
        }
    }

    /* Dark mode friendly shadows */
    @media (prefers-color-scheme: dark) {
        .card {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .progress {
            background-color: #333;
        }
    }

    /* Print styles */
    @media print {

        .btn,
        .shadow-sm {
            display: none !important;
        }

        .card {
            box-shadow: none;
            border: 1px solid #ddd !important;
        }

        .progress-bar {
            background: #333 !important;
            color: white !important;
        }
    }

    /* Accessibility improvements */
    .btn:focus,
    .progress:focus {
        outline: 2px solid #1976d2;
        outline-offset: 2px;
    }

    /* Smooth animations */
    * {
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
    }

    /* Enhanced readability */
    .small,
    small {
        font-size: 0.875rem;
        line-height: 1.4;
    }

    .fw-bold {
        font-weight: 600 !important;
    }

    /* Better spacing */
    .mb-2 {
        margin-bottom: 0.75rem !important;
    }

    .mt-1 {
        margin-top: 0.5rem !important;
    }
</style>
