@extends('layouts.main')

@section('title', 'Stock Opname - Detail')
@section('breadcrumb-item', 'Stock Opname - Detail')

@section('css')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .status-badge {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
        }
        
        .variance-positive {
            color: #28a745;
            font-weight: bold;
        }
        
        .variance-negative {
            color: #dc3545;
            font-weight: bold;
        }
        
        .variance-zero {
            color: #6c757d;
            font-weight: bold;
        }
        
        .form-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
    @php
    // Helper function to format numbers with dot thousands separator and comma decimal separator (Indonesian format)
    function formatNumber($value) {
        if ($value == floor($value)) {
            return number_format($value, 0, ',', '.');
        } else {
            return rtrim(rtrim(number_format($value, 2, ',', '.'), '0'), ',');
        }
    }
    @endphp
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- Stock Opname Detail start -->
        <div class="col-sm-12">
            <!-- Header Info -->
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Stock Opname - 
                            @php
                                $typeNames = [
                                    'bahan_baku' => 'Bahan Baku',
                                    'finished_goods' => 'Finished Goods', 
                                    'sticker' => 'Sticker'
                                ];
                            @endphp
                            {{ $typeNames[$stockOpname->type] ?? $stockOpname->type }}
                        </h5>
                        <div>
                            <a href="{{ route('stock-opname.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Tanggal Opname:</strong><br>
                            {{ $stockOpname->tanggal_opname->format('d/m/Y') }}
                        </div>
                        <div class="col-md-3">
                            <strong>Status:</strong><br>
                            @php
                                $badgeClass = [
                                    'draft' => 'secondary',
                                    'in_progress' => 'warning',
                                    'completed' => 'success'
                                ][$stockOpname->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">{{ $stockOpname->status_name }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Total Items:</strong><br>
                            {{ $stockOpname->total_items }} items
                        </div>
                        <div class="col-md-3">
                            <strong>Dibuat Oleh:</strong><br>
                            {{ $stockOpname->creator->name ?? '-' }}
                        </div>
                    </div>
                    @if($stockOpname->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <strong>Catatan:</strong><br>
                            {{ $stockOpname->notes }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Items Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Daftar Items untuk Opname</h4>
                    @if($stockOpname->status !== 'completed')
                    <div>
                        <button type="button" id="save-all-btn" class="btn btn-success me-2">
                            <i class="fas fa-save me-1"></i>
                            Simpan Semua
                        </button>
                        <button type="button" id="finalize-btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#finalizeModal">
                            <i class="fas fa-check me-1"></i>
                            Selesaikan Opname
                        </button>
                    </div>
                    @else
                    <div>
                        <button type="button" id="export-excel-btn" class="btn btn-success me-2">
                            <i class="fas fa-file-excel me-1"></i>
                            Export Excel
                        </button>
                        <button type="button" id="export-pdf-btn" class="btn btn-danger">
                            <i class="fas fa-file-pdf me-1"></i>
                            Export PDF
                        </button>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    @if($stockOpname->status !== 'completed')
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Petunjuk:</strong> Input stok fisik hasil perhitungan manual. Selisih akan dihitung otomatis.
                    </div>
                    @endif

                    <!-- Filter Section -->
                    <div class="mb-3 p-3 border rounded bg-light filter-section">
                        <button id="filter-toggle-btn" class="btn btn-sm btn-outline-secondary mb-2 w-100 d-md-none"
                            type="button" data-bs-toggle="collapse" data-bs-target="#filterControls">
                            <i class="fas fa-filter"></i> Toggle Filters <i class="fas fa-chevron-down"></i>
                        </button>
                        <div id="filterControls" class="collapse show">
                            <div class="row">
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                                    <label for="search-items" class="form-label small">Cari Item</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" id="search-items" class="form-control form-control-sm" placeholder="Cari nama item atau SKU...">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                                    <label for="filter-status" class="form-label small">Status</label>
                                    <select class="form-control form-control-sm" id="filter-status">
                                        <option value="">Semua Status</option>
                                        <option value="belum_input">Belum Input</option>
                                        <option value="sesuai">Sesuai</option>
                                        <option value="surplus">Surplus</option>
                                        <option value="kurang">Kurang</option>
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                                    <label for="items-per-page" class="form-label small">Items per Page</label>
                                    <select class="form-control form-control-sm" id="items-per-page">
                                        <option value="10">10</option>
                                        <option value="25" selected>25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="-1">Semua</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <button id="clear-filters" class="btn btn-sm btn-secondary me-2">
                                        <i class="fas fa-times"></i> Clear Filters
                                    </button>
                                    <span class="text-muted" id="items-count">Total: {{ count($stockOpname->items) }} items</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State Message -->
                    <div id="empty-state-message" class="alert alert-warning text-center" style="display: none;">
                        <i class="fas fa-search me-2"></i>
                        <strong>Tidak ada data yang sesuai dengan filter.</strong>
                        <br>
                        <small class="text-muted">
                            Coba ubah kriteria pencarian atau 
                            <a href="javascript:void(0)" id="refresh-page-link" class="alert-link">
                                <i class="fas fa-refresh me-1"></i>refresh halaman ini
                            </a> jika data mungkin sudah berubah.
                        </small>
                    </div>

                    <form id="opname-form">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="items-table">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        @if(in_array($stockOpname->type, ['bahan_baku', 'finished_goods']))
                                        <th width="15%">SKU</th>
                                        <th width="15%">Nama Item</th>
                                        @else
                                        <th width="20%">Nama Item</th>
                                        @endif
                                        <th width="12%">Stok Sistem</th>
                                        <th width="12%">Stok Fisik</th>
                                        <th width="12%">Selisih</th>
                                        <th width="8%">Satuan</th>
                                        <th width="12%">Status</th>
                                        @if($stockOpname->status !== 'completed')
                                        @if(in_array($stockOpname->type, ['bahan_baku', 'finished_goods']))
                                        <th width="14%">Action</th>
                                        @else
                                        <th width="19%">Action</th>
                                        @endif
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stockOpname->items as $index => $item)
                                    <tr data-item-id="{{ $item->id }}">
                                        <td>{{ $index + 1 }}</td>
                                        @if(in_array($stockOpname->type, ['bahan_baku', 'finished_goods']))
                                        <td>
                                            <span class="text-muted">{{ $item->master_item_sku }}</span>
                                        </td>
                                        @endif
                                        <td>
                                            <strong>{{ $item->master_item_name }}</strong>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold">{{ formatNumber($item->stok_sistem) }}</span>
                                        </td>
                                        <td>
                                            @if($stockOpname->status !== 'completed')
                                            <input type="number" 
                                                   name="items[{{ $item->id }}][stok_fisik]" 
                                                   class="form-control stok-fisik-input" 
                                                   value="{{ $item->stok_fisik }}" 
                                                   step="1" 
                                                   min="0"
                                                   oninput="this.value = Math.floor(this.value)"
                                                   data-stok-sistem="{{ $item->stok_sistem }}"
                                                   data-item-id="{{ $item->id }}">
                                            <input type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}">
                                            @else
                                            <span class="fw-bold">{{ formatNumber($item->stok_fisik ?? 0) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <span class="selisih-display fw-bold" 
                                                  data-item-id="{{ $item->id }}">
                                                @if($item->stok_fisik !== null)
                                                    @php
                                                        $selisih = ($item->stok_fisik ?? 0) - $item->stok_sistem;
                                                    @endphp
                                                    <span class="fw-bold {{ $selisih > 0 ? 'text-success' : ($selisih < 0 ? 'text-danger' : 'text-muted') }}">
                                                        {{ $selisih > 0 ? '+' : '' }}{{ formatNumber($selisih) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </span>
                                        </td>
                                        <td class="text-center">{{ $item->satuan }}</td>
                                        <td class="text-center">
                                            <span class="status-badge" data-item-id="{{ $item->id }}">
                                                @if($item->stok_fisik !== null)
                                                    @php
                                                        $selisih = $item->stok_fisik - $item->stok_sistem;
                                                        if ($selisih > 0) {
                                                            echo '<span class="badge bg-success">Surplus</span>';
                                                        } elseif ($selisih < 0) {
                                                            echo '<span class="badge bg-danger">Kurang</span>';
                                                        } else {
                                                            echo '<span class="badge bg-secondary">Sesuai</span>';
                                                        }
                                                    @endphp
                                                @else
                                                    <span class="badge bg-warning">Belum Input</span>
                                                @endif
                                            </span>
                                        </td>
                                        @if($stockOpname->status !== 'completed')
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-success update-item-btn" 
                                                    data-item-id="{{ $item->id }}" 
                                                    data-item-name="{{ $item->master_item_name }}"
                                                    title="Update item ini">
                                                <i class="fas fa-save me-1"></i>Update
                                            </button>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                    
                    <!-- Pagination Container -->
                    <div id="pagination-container" class="mt-3"></div>
                </div>
            </div>
        </div>
        <!-- Stock Opname Detail end -->
    </div>
    <!-- [ Main Content ] end -->

    <!-- Finalize Modal -->
    <div class="modal fade" id="finalizeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Selesaikan Stock Opname</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menyelesaikan stock opname ini?</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="update_stock" name="update_stock">
                        <label class="form-check-label" for="update_stock">
                            <strong>Update stok sistem sesuai hasil opname</strong>
                        </label>
                        <small class="form-text text-muted d-block">
                            Jika dicentang, stok di sistem akan diupdate sesuai dengan stok fisik yang diinput.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="confirm-finalize">Ya, Selesaikan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Core JS files -->
    <script src="{{ URL::asset('build/js/plugins/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/bootstrap.min.js') }}"></script>

    <!-- DataTables Core -->
    <script src="{{ URL::asset('build/js/plugins/dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/dataTables.bootstrap5.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="{{ URL::asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>

    <!-- XLSX library for Excel export -->
    <script src="{{ URL::asset('build/js/plugins/xlsx.full.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Number formatting function (Indonesian format: 1.000,50)
            function formatNumber(value) {
                if (value == Math.floor(value)) {
                    return new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(value);
                } else {
                    return new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(value).replace(/,00$/, '');
                }
            }
            
            // Pagination and filtering variables
            let currentPage = 1;
            let itemsPerPage = 25;
            let filteredItems = [];
            let allItems = [];
            
            // Initialize items data
            function initializeItemsData() {
                allItems = [];
                $('#items-table tbody tr').each(function() {
                    const row = $(this);
                    const itemName = row.find('td:first').text().trim();
                    const sku = row.find('td:nth-child(2)').text().trim();
                    const stokSistem = parseFloat(row.find('td:nth-child(3)').text().replace(/[^\d.-]/g, '')) || 0;
                    const stokFisikInput = row.find('input[name*="[stok_fisik]"]');
                    const stokFisik = stokFisikInput.length ? parseFloat(stokFisikInput.val()) || null : parseFloat(row.find('td:nth-child(4)').text().replace(/[^\d.-]/g, '')) || null;
                    
                    // Calculate status
                    let status = 'belum_input';
                    if (stokFisik !== null) {
                        const selisih = stokFisik - stokSistem;
                        if (selisih === 0) {
                            status = 'sesuai';
                        } else if (selisih > 0) {
                            status = 'surplus';
                        } else {
                            status = 'kurang';
                        }
                    }
                    
                    allItems.push({
                        element: row,
                        name: itemName,
                        sku: sku,
                        status: status,
                        stokSistem: stokSistem,
                        stokFisik: stokFisik
                    });
                });
                filteredItems = [...allItems];
            }
            
            // Apply filters
            function applyFilters() {
                const searchTerm = $('#search-items').val().toLowerCase();
                const statusFilter = $('#filter-status').val();
                
                filteredItems = allItems.filter(item => {
                    // Search filter
                    const matchesSearch = searchTerm === '' || 
                        item.name.toLowerCase().includes(searchTerm) || 
                        item.sku.toLowerCase().includes(searchTerm);
                    
                    // Status filter
                    const matchesStatus = statusFilter === '' || item.status === statusFilter;
                    
                    return matchesSearch && matchesStatus;
                });
                
                currentPage = 1;
                updateDisplay();
            }
            
            // Update display with pagination
            function updateDisplay() {
                itemsPerPage = parseInt($('#items-per-page').val());
                
                // Hide all rows first
                $('#items-table tbody tr').hide();
                
                // Check if we have filtered results
                if (filteredItems.length === 0) {
                    // Show empty state message
                    $('#empty-state-message').show();
                    $('#items-table').hide();
                    $('#pagination-container').hide();
                    $('#items-count').text(`Showing: 0 of {{ count($stockOpname->items) }} items`);
                    return;
                } else {
                    // Hide empty state message and show table
                    $('#empty-state-message').hide();
                    $('#items-table').show();
                }
                
                if (itemsPerPage === -1) {
                    // Show all filtered items
                    filteredItems.forEach(item => {
                        item.element.show();
                    });
                    $('#pagination-container').hide();
                } else {
                    // Calculate pagination
                    const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
                    const startIndex = (currentPage - 1) * itemsPerPage;
                    const endIndex = startIndex + itemsPerPage;
                    
                    // Show items for current page
                    filteredItems.slice(startIndex, endIndex).forEach(item => {
                        item.element.show();
                    });
                    
                    // Update pagination controls
                    updatePaginationControls(totalPages);
                    $('#pagination-container').show();
                }
                
                // Update count
                $('#items-count').text(`Showing: ${filteredItems.length} of {{ count($stockOpname->items) }} items`);
            }
            
            // Update pagination controls
            function updatePaginationControls(totalPages) {
                let paginationHtml = '';
                
                if (totalPages > 1) {
                    paginationHtml += `<nav><ul class="pagination pagination-sm justify-content-center">`;
                    
                    // Previous button
                    paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">`;
                    paginationHtml += `<a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a></li>`;
                    
                    // Page numbers
                    const startPage = Math.max(1, currentPage - 2);
                    const endPage = Math.min(totalPages, currentPage + 2);
                    
                    if (startPage > 1) {
                        paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
                        if (startPage > 2) {
                            paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                        }
                    }
                    
                    for (let i = startPage; i <= endPage; i++) {
                        paginationHtml += `<li class="page-item ${i === currentPage ? 'active' : ''}">`;
                        paginationHtml += `<a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                    }
                    
                    if (endPage < totalPages) {
                        if (endPage < totalPages - 1) {
                            paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                        }
                        paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
                    }
                    
                    // Next button
                    paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">`;
                    paginationHtml += `<a class="page-link" href="#" data-page="${currentPage + 1}">Next</a></li>`;
                    
                    paginationHtml += `</ul></nav>`;
                }
                
                $('#pagination-container').html(paginationHtml);
            }
            
            // Event handlers
            $('#search-items').on('input', debounce(applyFilters, 300));
            $('#filter-status').on('change', applyFilters);
            $('#items-per-page').on('change', updateDisplay);
            
            // Clear filters
            $('#clear-filters').on('click', function() {
                $('#search-items').val('');
                $('#filter-status').val('');
                $('#items-per-page').val('25');
                applyFilters();
            });
            
            // Refresh page link handler
            $('#refresh-page-link').on('click', function() {
                location.reload();
            });
            
            // Pagination click handler
            $(document).on('click', '#pagination-container .page-link', function(e) {
                e.preventDefault();
                const page = parseInt($(this).data('page'));
                if (page && page !== currentPage) {
                    currentPage = page;
                    updateDisplay();
                    // Scroll to top of table
                    $('html, body').animate({
                        scrollTop: $('#items-table').offset().top - 100
                    }, 300);
                }
            });
            
            // Debounce function
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }
            
            /**
             * Calculate correct variance handling negative system stock
             * 
             * Business Logic:
             * - If system stock is negative, it means we have over-allocated/over-used stock
             * - Physical count should be compared against the shortage baseline
             * - Any physical stock found when system is negative is a recovery/gain
             * 
             * @param {number} physicalStock
             * @param {number} systemStock
             * @return {number}
             */
            function calculateCorrectVariance(physicalStock, systemStock) {
                // Convert to numbers to ensure proper calculation
                physicalStock = parseFloat(physicalStock) || 0;
                systemStock = parseFloat(systemStock) || 0;
                
                if (systemStock < 0) {
                    // When system stock is negative:
                    // - If physical = 0: We're still short by the absolute system stock amount
                    // - If physical > 0: We recovered some stock, but still short by (abs(system) - physical)
                    // - Real shortage = absolute system stock amount
                    
                    const realShortage = Math.abs(systemStock);
                    
                    if (physicalStock >= realShortage) {
                        // Physical stock covers the shortage and more = surplus
                        return physicalStock - realShortage;
                    } else {
                        // Still short = negative variance
                        return physicalStock - realShortage;
                    }
                } else {
                    // Normal calculation for positive or zero system stock
                    return physicalStock - systemStock;
                }
            }
            
            // Initialize on page load
            initializeItemsData();
            updateDisplay();
            
            // Auto calculate selisih when stok fisik is changed
            $('.stok-fisik-input').on('input', function() {
                const itemId = $(this).data('item-id');
                const stokSistem = parseFloat($(this).data('stok-sistem'));
                const stokFisik = parseFloat($(this).val()) || 0;
                const selisih = calculateCorrectVariance(stokFisik, stokSistem);
                
                // Update selisih display
                const selisihDisplay = $(`.selisih-display[data-item-id="${itemId}"]`);
                let colorClass = 'text-secondary';
                let prefix = '';
                
                if (selisih > 0) {
                    colorClass = 'text-success';
                    prefix = '+';
                } else if (selisih < 0) {
                    colorClass = 'text-danger';
                }
                
                // Format number with Indonesian format
                const formattedSelisih = formatNumber(Math.abs(selisih));
                selisihDisplay.html(`<span class="${colorClass}">${prefix}${formattedSelisih}</span>`);
                
                // Update status badge
                const statusBadge = $(`.status-badge[data-item-id="${itemId}"]`);
                let badgeHtml = '';
                
                if (selisih > 0) {
                    badgeHtml = '<span class="badge bg-success">Surplus</span>';
                } else if (selisih < 0) {
                    badgeHtml = '<span class="badge bg-danger">Kurang</span>';
                } else {
                    badgeHtml = '<span class="badge bg-secondary">Sesuai</span>';
                }
                
                statusBadge.html(badgeHtml);
            });
            
            // Individual item update
            $('.update-item-btn').click(function() {
                const itemId = $(this).data('item-id');
                const itemName = $(this).data('item-name');
                const stokFisikInput = $(`.stok-fisik-input[data-item-id="${itemId}"]`);
                const stokFisik = stokFisikInput.val();
                
                if (!stokFisik || stokFisik === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan!',
                        text: 'Silakan input stok fisik terlebih dahulu'
                    });
                    stokFisikInput.focus();
                    return;
                }
                
                // Show loading state
                const btn = $(this);
                const originalHtml = btn.html();
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Updating...');
                
                $.ajax({
                    url: `/stock-opname/{{ $stockOpname->id }}/items/${itemId}`,
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        stok_fisik: stokFisik
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: `${itemName} berhasil diupdate`,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            
                            // Update UI with response data
                            const selisihDisplay = $(`.selisih-display[data-item-id="${itemId}"]`);
                            const statusBadge = $(`.status-badge[data-item-id="${itemId}"]`);
                            
                            // Update selisih
                            let colorClass = 'text-secondary';
                            let prefix = '';
                            if (response.data.selisih > 0) {
                                colorClass = 'text-success';
                                prefix = '+';
                            } else if (response.data.selisih < 0) {
                                colorClass = 'text-danger';
                            }
                            // Format selisih with Indonesian number format
                            const selisihValue = parseFloat(response.data.selisih);
                            const formattedSelisih = formatNumber(Math.abs(selisihValue));
                            selisihDisplay.html(`<span class="${colorClass}">${prefix}${formattedSelisih}</span>`);
                            
                            // Update status badge
                            let badgeHtml = '';
                            if (response.data.status === 'surplus') {
                                badgeHtml = '<span class="badge bg-success">Surplus</span>';
                            } else if (response.data.status === 'kurang') {
                                badgeHtml = '<span class="badge bg-danger">Kurang</span>';
                            } else {
                                badgeHtml = '<span class="badge bg-secondary">Sesuai</span>';
                            }
                            statusBadge.html(badgeHtml);
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal mengupdate item: ' + (xhr.responseJSON?.message || 'Unknown error')
                        });
                    },
                    complete: function() {
                        // Restore button state
                        btn.prop('disabled', false).html(originalHtml);
                    }
                });
            });
            
            // Save all data
            $('#save-all-btn').click(function() {
                const formData = $('#opname-form').serialize();
                
                $.ajax({
                    url: '{{ route('stock-opname.update', $stockOpname->id) }}',
                    method: 'PUT',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal menyimpan data: ' + xhr.responseJSON.message
                        });
                    }
                });
            });
            
            // Finalize opname
            $('#confirm-finalize').click(function() {
                const updateStock = $('#update_stock').is(':checked');
                
                const form = $('<form>', {
                    method: 'POST',
                    action: '{{ route('stock-opname.process', $stockOpname->id) }}'
                });
                
                form.append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: '{{ csrf_token() }}'
                }));
                
                if (updateStock) {
                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'update_stock',
                        value: '1'
                    }));
                }
                
                $('body').append(form);
                form.submit();
            });
            
            @if($stockOpname->status === 'completed')
            // Export Excel functionality
            $('#export-excel-btn').click(function() {
                const btn = $(this);
                const originalHtml = btn.html();
                
                // Show loading state
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Exporting...');
                
                $.ajax({
                    url: '{{ route('stock-opname.export', $stockOpname->id) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            // Generate filename
                            const fileName = 'Stock_Opname_' + response.opname_info.type.replace(/\s+/g, '_') + '_' + 
                                response.opname_info.date.replace(/\//g, '') + '_' + getCurrentDate();
                            
                            // Export to Excel
                            exportToExcel(response.data, fileName);
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: `${response.count} items berhasil diekspor ke Excel`,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message || 'Gagal mengekspor data'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan: ' + (xhr.responseJSON?.message || 'Tidak dapat menghubungi server')
                        });
                    },
                    complete: function() {
                        // Restore button state
                        btn.prop('disabled', false).html(originalHtml);
                    }
                });
            });
            
            // Export PDF functionality (using browser print)
            $('#export-pdf-btn').click(function() {
                // Create a new window with printable content
                const printWindow = window.open('', '_blank');
                const printContent = generatePrintContent();
                
                printWindow.document.write(printContent);
                printWindow.document.close();
                
                // Wait for content to load then print
                printWindow.onload = function() {
                    printWindow.print();
                    printWindow.close();
                };
            });
            
            // Function to export data to Excel
            function exportToExcel(data, fileName) {
                // Create a new workbook
                const wb = XLSX.utils.book_new();
                
                // Convert the data to a worksheet
                const ws = XLSX.utils.json_to_sheet(data);
                
                // Set column widths for better readability
                const colWidths = [
                    { wch: 5 },   // No
                    { wch: 15 },  // SKU
                    { wch: 30 },  // Nama Item
                    { wch: 12 },  // Stok Sistem
                    { wch: 12 },  // Stok Fisik
                    { wch: 12 },  // Selisih
                    { wch: 10 },  // Satuan
                    { wch: 12 },  // Status
                    { wch: 25 }   // Catatan
                ];
                
                ws['!cols'] = colWidths;
                
                // Add the worksheet to the workbook
                XLSX.utils.book_append_sheet(wb, ws, 'Stock Opname');
                
                // Generate Excel file and trigger download
                XLSX.writeFile(wb, fileName + '.xlsx');
            }
            
            // Function to generate printable content for PDF
            function generatePrintContent() {
                const typeNames = {
                    'bahan_baku': 'Bahan Baku',
                    'finished_goods': 'Finished Goods',
                    'sticker': 'Sticker'
                };
                
                let printHtml = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Stock Opname Report</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .header { text-align: center; margin-bottom: 30px; }
                        .info { margin-bottom: 20px; }
                        .info div { margin-bottom: 5px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        th { background-color: #f2f2f2; font-weight: bold; }
                        .text-center { text-align: center; }
                        .text-right { text-align: right; }
                        .status-surplus { color: #28a745; font-weight: bold; }
                        .status-kurang { color: #dc3545; font-weight: bold; }
                        .status-sesuai { color: #6c757d; font-weight: bold; }
                        @media print {
                            body { margin: 0; }
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>LAPORAN STOCK OPNAME</h1>
                        <h2>${typeNames['{{ $stockOpname->type }}'] || '{{ $stockOpname->type }}'}</h2>
                    </div>
                    
                    <div class="info">
                        <div><strong>Tanggal Opname:</strong> {{ $stockOpname->tanggal_opname->format('d/m/Y') }}</div>
                        <div><strong>Status:</strong> {{ $stockOpname->status_name }}</div>
                        <div><strong>Total Items:</strong> {{ $stockOpname->total_items }} items</div>
                        <div><strong>Dibuat Oleh:</strong> {{ $stockOpname->creator->name ?? '-' }}</div>
                        @if($stockOpname->notes)
                        <div><strong>Catatan:</strong> {{ $stockOpname->notes }}</div>
                        @endif
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                @if(in_array($stockOpname->type, ['bahan_baku', 'finished_goods']))
                                <th width="15%">SKU</th>
                                <th width="20%">Nama Item</th>
                                @else
                                <th width="35%">Nama Item</th>
                                @endif
                                <th width="12%">Stok Sistem</th>
                                <th width="12%">Stok Fisik</th>
                                <th width="12%">Selisih</th>
                                <th width="8%">Satuan</th>
                                <th width="12%">Status</th>
                                <th width="20%">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>`;
                
                // Add table rows
                @foreach($stockOpname->items as $index => $item)
                    @php
                        $selisih = ($item->stok_fisik ?? 0) - $item->stok_sistem;
                        $statusClass = '';
                        $statusText = 'Belum Input';
                        if ($item->stok_fisik !== null) {
                            if ($selisih > 0) {
                                $statusClass = 'status-surplus';
                                $statusText = 'Surplus';
                            } elseif ($selisih < 0) {
                                $statusClass = 'status-kurang';
                                $statusText = 'Kurang';
                            } else {
                                $statusClass = 'status-sesuai';
                                $statusText = 'Sesuai';
                            }
                        }
                    @endphp
                    
                    printHtml += `
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            @if(in_array($stockOpname->type, ['bahan_baku', 'finished_goods']))
                            <td>{{ $item->master_item_sku ?? '-' }}</td>
                            @endif
                            <td>{{ $item->master_item_name }}</td>
                            <td class="text-right">{{ formatNumber($item->stok_sistem) }}</td>
                            <td class="text-right">{{ $item->stok_fisik !== null ? formatNumber($item->stok_fisik) : '-' }}</td>
                            <td class="text-right {{ $statusClass }}">
                                @if($item->stok_fisik !== null)
                                    {{ $selisih > 0 ? '+' : '' }}{{ formatNumber($selisih) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">{{ $item->satuan }}</td>
                            <td class="text-center {{ $statusClass }}">{{ $statusText }}</td>
                            <td>{{ $item->notes ?? '' }}</td>
                        </tr>`;
                @endforeach
                
                printHtml += `
                        </tbody>
                    </table>
                    
                    <div style="margin-top: 30px; text-align: right;">
                        <p>Dicetak pada: ${new Date().toLocaleDateString('id-ID', { 
                            day: 'numeric', 
                            month: 'long', 
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}</p>
                    </div>
                </body>
                </html>`;
                
                return printHtml;
            }
            
            // Helper function to get current date for filename
            function getCurrentDate() {
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                return `${year}${month}${day}`;
            }
            @endif
        });
    </script>
@endsection
