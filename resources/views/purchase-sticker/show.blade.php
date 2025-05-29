<!-- Purchase Sticker Detail Content -->
<div class="row">
    <!-- Product Information -->
    <div class="col-md-6 mb-3">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-box"></i> Informasi Produk</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="40%"><strong>Nama Produk:</strong></td>
                        <td>{{ $purchaseSticker->product ? $purchaseSticker->product->nama_produk : '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>SKU:</strong></td>
                        <td>{{ $purchaseSticker->product ? $purchaseSticker->product->sku_produk : '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Label:</strong></td>
                        <td>
                            @if ($purchaseSticker->product)
                                @switch($purchaseSticker->product->label)
                                    @case(1)
                                        <span class="badge bg-success">Tea Bag</span>
                                    @break

                                    @case(2)
                                        <span class="badge bg-info">Drip Bag</span>
                                    @break

                                    @case(5)
                                        <span class="badge bg-warning">Box Tea</span>
                                    @break

                                    @default
                                        <span class="badge bg-secondary">Lainnya</span>
                                @endswitch
                            @else
                                <span class="badge bg-secondary">-</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Sticker Information -->
    <div class="col-md-6 mb-3">
        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="fas fa-tag"></i> Informasi Stiker</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="40%"><strong>Ukuran Stiker:</strong></td>
                        <td>{{ $purchaseSticker->ukuran_stiker ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jumlah Stiker:</strong></td>
                        <td>{{ number_format($purchaseSticker->jumlah_stiker) }} pcs</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Order:</strong></td>
                        <td>{{ $purchaseSticker->created_at ? $purchaseSticker->created_at->format('d/m/Y H:i') : '-' }}
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
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="fas fa-shopping-cart"></i> Informasi Order & Stok</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="text-center p-3 bg-light rounded">
                            <div class="h4 text-primary mb-1">{{ number_format($purchaseSticker->jumlah_order) }}</div>
                            <small class="text-muted">Jumlah Order</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="text-center p-3 bg-light rounded">
                            <div class="h4 text-success mb-1">{{ number_format($purchaseSticker->stok_masuk) }}</div>
                            <small class="text-muted">Stok Masuk</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="text-center p-3 bg-light rounded">
                            <div class="h4 text-info mb-1">{{ number_format($purchaseSticker->total_order) }}</div>
                            <small class="text-muted">Total Order</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="text-center p-3 bg-light rounded">
                            <div class="h4 text-warning mb-1">
                                {{ number_format($purchaseSticker->total_order - $purchaseSticker->stok_masuk) }}</div>
                            <small class="text-muted">Sisa Order</small>
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
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-chart-line"></i> Progress Penerimaan</h6>
            </div>
            <div class="card-body">
                @php
                    $stokMasukPercentage = $purchaseSticker->stok_masuk_percentage;
                    $progressColor =
                        $stokMasukPercentage >= 100 ? 'success' : ($stokMasukPercentage >= 80 ? 'warning' : 'danger');
                @endphp

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Progress Stok Masuk</span>
                        <span class="fw-bold">{{ $stokMasukPercentage }}%</span>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-{{ $progressColor }}" role="progressbar"
                            style="width: {{ min($stokMasukPercentage, 100) }}%"
                            aria-valuenow="{{ $stokMasukPercentage }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>

                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <div class="fw-bold text-success">{{ number_format($purchaseSticker->stok_masuk) }}</div>
                            <small class="text-muted">Sudah Masuk</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="fw-bold text-warning">{{ number_format($purchaseSticker->total_order) }}</div>
                        <small class="text-muted">Total Target</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card border-secondary">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="fas fa-percentage"></i> Analisa Order</h6>
            </div>
            <div class="card-body">
                @php
                    $orderPercentage = $purchaseSticker->order_percentage;
                    $orderColor = $orderPercentage >= 80 ? 'success' : ($orderPercentage >= 50 ? 'warning' : 'danger');
                @endphp

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Persentase Order dari Stiker</span>
                        <span class="fw-bold">{{ $orderPercentage }}%</span>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-{{ $orderColor }}" role="progressbar"
                            style="width: {{ min($orderPercentage, 100) }}%" aria-valuenow="{{ $orderPercentage }}"
                            aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>

                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <div class="fw-bold text-primary">{{ number_format($purchaseSticker->jumlah_order) }}</div>
                            <small class="text-muted">Di-order</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="fw-bold text-info">{{ number_format($purchaseSticker->jumlah_stiker) }}</div>
                        <small class="text-muted">Tersedia</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Summary -->
<div class="row">
    <div class="col-md-12">
        <div class="card border-dark">
            <div class="card-header bg-dark text-white">
                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Status & Ringkasan</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Status Penerimaan:</h6>
                        @if ($purchaseSticker->stok_masuk >= $purchaseSticker->total_order)
                            <span class="badge bg-success fs-6"><i class="fas fa-check-circle"></i> Lengkap</span>
                            <p class="text-success mt-2 mb-0">
                                <i class="fas fa-thumbs-up"></i> Semua stiker sudah diterima sesuai target.
                            </p>
                        @elseif($purchaseSticker->stok_masuk > 0)
                            <span class="badge bg-warning fs-6"><i class="fas fa-clock"></i> Parsial</span>
                            <p class="text-warning mt-2 mb-0">
                                <i class="fas fa-exclamation-triangle"></i>
                                Masih kurang
                                {{ number_format($purchaseSticker->total_order - $purchaseSticker->stok_masuk) }}
                                stiker lagi.
                            </p>
                        @else
                            <span class="badge bg-danger fs-6"><i class="fas fa-times-circle"></i> Belum Masuk</span>
                            <p class="text-danger mt-2 mb-0">
                                <i class="fas fa-exclamation-circle"></i> Belum ada stiker yang diterima.
                            </p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-secondary">Informasi Tambahan:</h6>
                        <ul class="list-unstyled mb-0">
                            <li><small class="text-muted"><i class="fas fa-calendar"></i> Dibuat:
                                    {{ $purchaseSticker->created_at ? $purchaseSticker->created_at->format('d/m/Y H:i') : '-' }}</small>
                            </li>
                            <li><small class="text-muted"><i class="fas fa-edit"></i> Diupdate:
                                    {{ $purchaseSticker->updated_at ? $purchaseSticker->updated_at->format('d/m/Y H:i') : '-' }}</small>
                            </li>
                            @if ($purchaseSticker->jumlah_stiker > $purchaseSticker->jumlah_order)
                                <li><small class="text-info"><i class="fas fa-info"></i> Stiker tersisa:
                                        {{ number_format($purchaseSticker->jumlah_stiker - $purchaseSticker->jumlah_order) }}
                                        pcs</small></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-3">
    <div class="col-md-12">
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-warning btn-sm"
                onclick="editPurchaseSticker({{ $purchaseSticker->id }}); $('#detailModal').modal('hide');">
                <i class="fas fa-edit"></i> Edit
            </button>
            <button type="button" class="btn btn-danger btn-sm"
                onclick="deletePurchaseSticker({{ $purchaseSticker->id }}); $('#detailModal').modal('hide');">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </div>
    </div>
</div>

<style>
    .progress {
        background-color: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-bar {
        transition: width 0.6s ease;
        border-radius: 10px;
    }

    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }

    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }

    @media (max-width: 768px) {
        .row .col-md-6 {
            margin-bottom: 1rem;
        }

        .d-flex.gap-2 {
            flex-direction: column;
        }

        .d-flex.gap-2 .btn {
            margin-bottom: 0.5rem;
        }
    }
</style>
