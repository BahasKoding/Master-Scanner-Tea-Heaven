@extends('layouts.main')

@section('title', 'Purchase Stiker')
@section('breadcrumb-item', 'Purchase Stiker')

@section('css')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- [Page specific CSS] start -->
    <!-- data tables css -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datatables/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datatables/buttons.bootstrap5.min.css') }}">
    <!-- Choices css -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/choices.min.css') }}">
    <!-- [Page specific CSS] end -->
    <style>
        .form-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        /* Choices.js custom styling */
        .choices__inner {
            min-height: 38px;
            padding: 4px 8px;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }

        .choices__list--dropdown .choices__item {
            padding: 6px 10px;
        }

        .is-open .choices__inner {
            border-radius: 0.375rem 0.375rem 0 0;
        }

        .choices[data-type*="select-one"] .choices__inner {
            padding-bottom: 4px;
        }

        .choices__list--single .choices__item {
            display: flex;
            align-items: center;
        }

        /* Product select container */
        .product-select-container {
            width: 100%;
        }

        /* Responsive styling */
        .filter-section {
            padding: 15px;
            margin-bottom: 15px;
        }

        .filter-label {
            font-size: 12px;
            margin-bottom: 4px;
        }

        /* Table responsiveness */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Action button styling */
        .action-buttons .btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }

        /* DataTables buttons responsiveness */
        .dt-buttons {
            margin-bottom: 10px;
        }

        .dt-button {
            margin-right: 5px;
            margin-bottom: 5px;
        }

        /* Modal responsiveness */
        @media (max-width: 768px) {
            .modal-dialog {
                max-width: 95%;
                margin: 10px auto;
            }

            .filter-section {
                padding: 10px;
            }

            .table {
                font-size: 14px;
            }

            .dt-buttons {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
            }

            .dt-button {
                flex: 1;
                white-space: nowrap;
                min-width: auto !important;
                padding: 0.25rem 0.5rem !important;
                font-size: 0.875rem !important;
            }

            .paginate_button {
                padding: 0.3rem 0.5rem !important;
            }
        }

        @media (max-width: 576px) {
            .action-buttons .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .card-header .btn-group {
                margin-top: 10px;
                width: 100%;
            }

            .paginate_button {
                padding: 0.2rem 0.3rem !important;
                font-size: 0.8rem;
            }
        }
    </style>
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- Purchase Sticker Table start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="mb-2 mb-sm-0">{{ $item ?? 'Purchase Stiker' }}</h5>
                        <div class="d-flex flex-wrap">
                            <button type="button" class="btn btn-primary mb-2 mb-sm-0" onclick="showCreateModal()">
                                <i class="fas fa-plus"></i> Tambah Purchase Stiker
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Show error message if any -->
                    @if (isset($error_message))
                        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                            <strong>Error!</strong> {{ $error_message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="mb-3 filter-section p-3 border rounded bg-light">
                        <button id="filter-toggle-btn" class="btn btn-sm btn-outline-secondary mb-2 w-100 d-md-none"
                            type="button" data-bs-toggle="collapse" data-bs-target="#filterControls">
                            <i class="fas fa-filter"></i> Toggle Filters <i class="fas fa-chevron-down"></i>
                        </button>
                        <div id="filterControls" class="collapse show">
                            <div class="row">
                                <div class="col-md-4 col-sm-12 mb-2">
                                    <label for="filter_product" class="form-label filter-label">Filter Produk</label>
                                    <select class="form-select form-select-sm" id="filter_product">
                                        <option value="">-- Semua Produk --</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name_product }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 col-sm-12 mb-2">
                                    <label for="filter_ukuran" class="form-label filter-label">Filter Ukuran</label>
                                    <input type="text" class="form-control form-control-sm" id="filter_ukuran"
                                        placeholder="Masukkan ukuran stiker">
                                </div>
                                <div class="col-md-4 col-sm-12 d-flex align-items-end">
                                    <button type="button" class="btn btn-secondary btn-sm me-2 mb-2"
                                        onclick="applyFilters()">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm mb-2"
                                        onclick="clearFilters()">
                                        <i class="fas fa-times"></i> Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dt-responsive table-responsive">
                        <table id="purchase-sticker-table" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Produk</th>
                                    <th>SKU</th>
                                    <th>Ukuran Stiker</th>
                                    <th title="Informasi jumlah stiker yang bisa didapat per lembar A3">Jumlah Stiker / A3
                                        (PCS)</th>
                                    <th title="Jumlah stiker yang direncanakan untuk order pelanggan">Jumlah Order</th>
                                    <th title="Jumlah stiker yang sudah diterima dari supplier">Stok Masuk</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Purchase Sticker Table end -->
    </div>
    <!-- [ Main Content ] end -->

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="purchaseStickerModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="purchaseStickerModalLabel">Tambah Purchase Stiker</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="purchaseStickerForm">
                    @csrf
                    <input type="hidden" id="purchase_sticker_id" name="purchase_sticker_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_id" class="form-label">Produk <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="product_id" name="product_id" required>
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">
                                                {{ $product->name_product }} ({{ $product->sku }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="error-product_id"></div>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>{{ count($products) }}</strong> produk tersedia
                                        (Hanya produk yang sudah memiliki data sticker)
                                    </small>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ukuran_stiker" class="form-label">Ukuran Stiker <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="ukuran_stiker" name="ukuran_stiker"
                                        placeholder="Akan terisi otomatis saat produk dipilih" readonly required>
                                    <div class="invalid-feedback" id="error-ukuran_stiker"></div>
                                    <small class="text-muted">
                                        <strong>Tambah Baru:</strong> Terisi otomatis dari data sticker produk<br>
                                        <strong>Edit:</strong> Menggunakan data existing (tidak diubah otomatis)
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jumlah_stiker" class="form-label">Jumlah Stiker / A3 <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="jumlah_stiker" name="jumlah_stiker"
                                        min="1" required>
                                    <div class="invalid-feedback" id="error-jumlah_stiker"></div>
                                    <small class="text-muted">
                                        <strong>Auto-fill:</strong> Terisi otomatis dari data sticker, tetapi bisa diedit
                                        manual
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jumlah_order" class="form-label">Jumlah Order <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="jumlah_order" name="jumlah_order"
                                        min="0" required>
                                    <div class="invalid-feedback" id="error-jumlah_order"></div>
                                    <small class="text-muted">Jumlah stiker yang akan digunakan untuk order</small>
                                </div>
                            </div>
                        </div>

                        <!-- Info box for sticker per A3 -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-light border" id="sticker-info" style="display: none;">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle text-info me-2"></i>
                                        <div>
                                            <strong>Info Sticker:</strong>
                                            <span id="sticker-details">Pilih produk untuk melihat detail sticker</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h6>Detail Penerimaan Stiker</h6>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="stok_masuk" class="form-label">Stok Masuk</label>
                                    <input type="number" class="form-control" id="stok_masuk" name="stok_masuk"
                                        min="0" value="0">
                                    <div class="invalid-feedback" id="error-stok_masuk"></div>
                                    <small class="text-muted">Jumlah stiker yang sudah diterima/masuk ke gudang</small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <strong>Penjelasan Field:</strong>
                            <ul class="mb-2">
                                <li><strong>Ukuran Stiker:</strong> Auto-fill dari data sticker (tidak dapat diedit)</li>
                                <li><strong>Jumlah Stiker / A3:</strong> Auto-fill dari data sticker, tetapi dapat diedit
                                    manual jika diperlukan</li>
                                <li><strong>Jumlah Order:</strong> Jumlah stiker yang direncanakan untuk order pelanggan
                                    (DATA DINAMIS)</li>
                                <li><strong>Stok Masuk:</strong> Jumlah stiker yang sudah diterima dari supplier (DATA
                                    DINAMIS)</li>
                            </ul>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted"><strong>Auto-Fill:</strong> Ukuran Stiker (readonly), Jumlah
                                        Stiker/A3 (editable)</small>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted"><strong>Manual Input:</strong> Jumlah Order, Stok
                                        Masuk</small>
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="row">
                                <div class="col-md-12">
                                    <small class="text-primary">
                                        <i class="fas fa-database"></i>
                                        <strong>Data Produk:</strong> {{ count($products) }} produk dengan data sticker
                                        @php
                                            $labelCounts = $products->groupBy('label')->map->count();
                                            $packagingCounts = $products->groupBy('packaging')->map->count();
                                        @endphp
                                        <br>
                                        <strong>Per Label:</strong>
                                        @foreach ($labelCounts as $label => $count)
                                            Label {{ $label }}: {{ $count }}
                                            produk{{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                        <br>
                                        <strong>Per Packaging:</strong>
                                        @foreach ($packagingCounts as $packaging => $count)
                                            {{ $packaging ?: 'Kosong' }}: {{ $count }}
                                            produk{{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                        <br>
                                        <small class="text-info">
                                            <i class="fas fa-lightbulb"></i>
                                            <strong>Catatan:</strong> Purchase Sticker hanya dapat dibuat untuk produk yang
                                            sudah memiliki data di menu Sticker.
                                        </small>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Purchase Stiker</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <!-- Detail content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- SweetAlert2 -->
    <script src="{{ URL::asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>

    <!-- Core JS files -->
    <script src="{{ URL::asset('build/js/plugins/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/bootstrap.min.js') }}"></script>

    <!-- DataTables Core -->
    <script src="{{ URL::asset('build/js/plugins/dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/dataTables.bootstrap5.min.js') }}"></script>

    <!-- DataTables Buttons and Extensions -->
    <script src="{{ URL::asset('build/js/plugins/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/pdfmake.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/vfs_fonts.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/buttons.html5.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/buttons.print.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/buttons.colVis.min.js') }}"></script>

    <!-- Choices JS -->
    <script src="{{ URL::asset('build/js/plugins/choices.min.js') }}"></script>

    <script type="text/javascript">
        // Global debug function - harus di luar document ready
        function debugLog(...args) {
            // Debug logging disabled for production
            // console.log('[DEBUG Purchase Sticker]', new Date().toISOString(), ...args);
        }

        // Global helper functions
        function clearValidationErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
        }

        function showValidationErrors(errors) {
            $.each(errors, function(field, messages) {
                const input = $(`[name="${field}"]`);
                input.addClass('is-invalid');
                $(`#error-${field}`).text(messages[0]);
            });
        }

        // Global variables for Choices instances
        let productChoicesInstance = null;

        // Global function to reset form
        function resetForm() {
            $('#purchaseStickerForm')[0].reset();
            $('#purchase_sticker_id').val('');
            clearValidationErrors();

            // Reset Choices.js if initialized
            if (productChoicesInstance) {
                try {
                    productChoicesInstance.setChoiceByValue('');
                    debugLog('Product Choices.js reset successfully');
                } catch (e) {
                    debugLog('Error resetting product choices:', e);
                    // Try to reinitialize if reset fails
                    forceReinitializeChoices();
                }
            }

            // Reset sticker info
            $('#sticker-info').hide();
            $('#ukuran_stiker').val('');
        }

        // Function to force reinitialize Choices.js if there are issues
        function forceReinitializeChoices() {
            debugLog('Force reinitializing Choices.js...');

            // Clean up existing instance
            if (productChoicesInstance) {
                try {
                    productChoicesInstance.destroy();
                    productChoicesInstance = null;
                } catch (e) {
                    debugLog('Error destroying choices during force reinit:', e);
                }
            }

            // Reinitialize after a short delay
            setTimeout(() => {
                initProductChoices();
                debugLog('Choices.js force reinitialized');
            }, 50);
        }

        // Function to initialize product choices
        function initProductChoices() {
            const productSelect = document.querySelector('#product_id');
            if (productSelect) {
                // Destroy existing instance if it exists
                if (productChoicesInstance) {
                    try {
                        productChoicesInstance.destroy();
                    } catch (e) {
                        debugLog('Error destroying product choices:', e);
                    }
                }

                productChoicesInstance = new Choices(productSelect, {
                    searchEnabled: true,
                    searchPlaceholderValue: "Cari produk...",
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: "-- Pilih Produk --",
                    removeItemButton: true,
                    allowHTML: false,
                    classNames: {
                        containerOuter: 'choices form-select product-select-container',
                    }
                });

                debugLog('Product Choices.js initialized');
            }
        }

        // Function to clean up choices instances
        function cleanupChoicesInstances() {
            if (productChoicesInstance) {
                try {
                    productChoicesInstance.destroy();
                    productChoicesInstance = null;
                    debugLog('Product Choices.js destroyed');
                } catch (e) {
                    debugLog('Error destroying product choices:', e);
                }
            }
        }

        // Function to handle product selection (compatible with both native select and Choices.js)
        function handleProductSelection(productId, preserveExistingData = false) {
            debugLog('Handling product selection:', productId, 'preserveExistingData:', preserveExistingData);

            if (productId) {
                // Show loading state only if we're not preserving existing data
                if (!preserveExistingData) {
                    $('#ukuran_stiker').val('Memuat...');
                }
                $('#sticker-info').hide();

                debugLog('Making AJAX request to get sticker data for product:', productId);

                $.ajax({
                    url: `/purchase-sticker/get-sticker-data/${productId}`,
                    type: 'GET',
                    timeout: 10000, // 10 second timeout
                    beforeSend: function() {
                        debugLog('AJAX request started for product:', productId);
                    },
                    success: function(response) {
                        debugLog('Sticker data response received:', response);

                        if (response.success) {
                            // Only fill in the sticker data if we're not preserving existing data
                            if (!preserveExistingData) {
                                $('#ukuran_stiker').val(response.data.ukuran_stiker);
                                $('#jumlah_stiker').val(response.data.jumlah_per_a3);
                            }

                            // Always show sticker info for reference
                            $('#sticker-details').html(
                                `Ukuran: <strong>${response.data.ukuran_stiker}</strong> | ` +
                                `Jumlah per A3: <strong>${response.data.jumlah_per_a3}</strong> sticker`
                            );
                            $('#sticker-info').fadeIn();

                            debugLog('Sticker data loaded successfully:', response.data);

                            if (preserveExistingData) {
                                debugLog('Existing data preserved, not overwriting ukuran_stiker field');
                            }
                        } else {
                            if (!preserveExistingData) {
                                $('#ukuran_stiker').val('');
                            }
                            $('#sticker-info').hide();

                            debugLog('Sticker data not found or error:', response.message);

                            // Show more informative error message
                            Swal.fire({
                                title: 'Perhatian',
                                html: `<div class="text-start">
                                    <p><strong>Pesan:</strong> ${response.message}</p>
                                    <p><strong>Produk ID:</strong> ${productId}</p>
                                    <hr>
                                    <small class="text-muted">
                                        <strong>Kemungkinan penyebab:</strong><br>
                                        • Data sticker untuk produk ini belum dibuat<br>
                                        • Silakan buat data sticker terlebih dahulu di menu Sticker<br>
                                        • Pastikan produk memiliki ukuran dan jumlah sticker yang valid
                                    </small>
                                </div>`,
                                icon: 'warning',
                                confirmButtonText: 'Mengerti',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        if (!preserveExistingData) {
                            $('#ukuran_stiker').val('');
                        }
                        $('#sticker-info').hide();

                        debugLog('AJAX Error details:', {
                            xhr: xhr,
                            status: status,
                            error: error,
                            responseText: xhr.responseText,
                            statusCode: xhr.status
                        });

                        let errorMessage = 'Gagal mengambil data sticker. Silakan coba lagi.';

                        if (xhr.status === 404) {
                            errorMessage = 'Endpoint tidak ditemukan. Silakan hubungi administrator.';
                        } else if (xhr.status === 500) {
                            errorMessage =
                                'Terjadi kesalahan server. Silakan coba lagi atau hubungi administrator.';
                        } else if (status === 'timeout') {
                            errorMessage = 'Permintaan timeout. Silakan coba lagi.';
                        }

                        Swal.fire({
                            title: 'Error',
                            html: `<div class="text-start">
                                <p><strong>Pesan:</strong> ${errorMessage}</p>
                                <p><strong>Status:</strong> ${xhr.status} ${status}</p>
                                <p><strong>Produk ID:</strong> ${productId}</p>
                                <hr>
                                <small class="text-muted">
                                    Jika masalah berlanjut, silakan hubungi administrator dengan informasi di atas.
                                </small>
                            </div>`,
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                });
            } else {
                // Clear fields when no product selected
                $('#ukuran_stiker').val('');
                $('#sticker-info').hide();
                debugLog('No product selected, clearing fields');
            }
        }

        $(document).ready(function() {
            // Log that script is starting
            debugLog('Purchase Sticker script starting...');

            // Test if bootstrap modal is available
            if (typeof window.bootstrap === 'undefined' && typeof $.fn.modal === 'undefined') {
                console.error('Bootstrap modal not available');
                return;
            }

            // Test if SweetAlert is available
            if (typeof Swal === 'undefined') {
                console.error('SweetAlert not available');
                return;
            }

            debugLog('All dependencies available, proceeding with initialization...');

            // Initialize choices when modal is shown
            $('#purchaseStickerModal').on('shown.bs.modal', function() {
                debugLog('Modal shown - initializing Choices.js');
                setTimeout(() => {
                    initProductChoices();

                    // Add event listener for Choices.js change event
                    if (productChoicesInstance) {
                        const productElement = document.querySelector('#product_id');
                        if (productElement) {
                            // Remove existing event listeners to avoid duplicates
                            productElement.removeEventListener('choice', handleChoiceEvent);

                            // Add new event listener
                            productElement.addEventListener('choice', handleChoiceEvent);

                            debugLog('Choices.js event listener added for edit modal');
                        }
                    }
                }, 100);
            });

            // Define the choice event handler function
            function handleChoiceEvent(event) {
                debugLog('Product choice changed via Choices.js:', event.detail.choice.value);
                handleProductSelection(event.detail.choice.value);
            }

            // Clean up when modal is hidden
            $('#purchaseStickerModal').on('hidden.bs.modal', function() {
                debugLog('Modal hidden - cleaning up Choices.js');
                cleanupChoicesInstances();

                // Reset form
                $('#purchaseStickerForm')[0].reset();
                $('#purchase_sticker_id').val('');
                clearValidationErrors();

                // Reset sticker info
                $('#sticker-info').hide();
                $('#ukuran_stiker').val('');

                // Remove any remaining backdrop
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');
            });

            // Total Order Final dapat diedit bebas tanpa auto-calculation

            // Show session flash messages with SweetAlert
            @if (session('success'))
                Swal.fire({
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            @endif

            // Responsive adjustments for mobile
            function adjustForMobile() {
                if (window.innerWidth < 768) {
                    $('.dt-responsive table').addClass('table-sm');
                    if (!$('#filterControls').hasClass('show')) {
                        $('#filterControls').collapse('hide');
                    }
                } else {
                    $('.dt-responsive table').removeClass('table-sm');
                    $('#filterControls').addClass('show');
                }
            }

            adjustForMobile();
            $(window).resize(function() {
                adjustForMobile();
            });

            // Initialize DataTable
            try {
                var table = $('#purchase-sticker-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('purchase-sticker.index') }}",
                        data: function(d) {
                            d.product_id = $('#filter_product').val();
                            d.ukuran_stiker = $('#filter_ukuran').val();
                            return d;
                        },
                        error: function(xhr, error, code) {
                            console.error('DataTables Ajax Error:', xhr.responseText);
                            Swal.fire({
                                title: 'Error',
                                text: 'Gagal memuat data. Silakan refresh halaman.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'product_name',
                            name: 'product_name'
                        },
                        {
                            data: 'product_sku',
                            name: 'product_sku',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'ukuran_stiker',
                            name: 'ukuran_stiker'
                        },
                        {
                            data: 'jumlah_stiker',
                            name: 'jumlah_stiker'
                        },
                        {
                            data: 'jumlah_order',
                            name: 'jumlah_order'
                        },
                        {
                            data: 'stok_masuk',
                            name: 'stok_masuk'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                return `
                                    <div class="action-buttons">
                                        <button type="button" class="btn btn-sm btn-info" onclick="showDetail(${data})" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-warning" onclick="editPurchaseSticker(${data})" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deletePurchaseSticker(${data})" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                `;
                            }
                        }
                    ],
                    order: [
                        [0, 'desc']
                    ],
                    pageLength: 25,
                    responsive: true,
                    dom: 'Bfrtip',
                    buttons: [{
                            extend: 'copy',
                            text: '<i class="fas fa-copy"></i> Salin',
                            className: 'btn btn-secondary'
                        },
                        {
                            extend: 'excel',
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            className: 'btn btn-success'
                        },
                        {
                            extend: 'print',
                            text: '<i class="fas fa-print"></i> Cetak',
                            className: 'btn btn-info'
                        }
                    ],
                    language: {
                        processing: '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>',
                        emptyTable: 'Tidak ada data purchase stiker',
                        zeroRecords: 'Tidak ditemukan purchase stiker yang sesuai',
                        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ purchase stiker',
                        infoEmpty: 'Menampilkan 0 sampai 0 dari 0 purchase stiker',
                        infoFiltered: '(difilter dari _MAX_ total purchase stiker)',
                        search: 'Cari:',
                        lengthMenu: "Tampilkan _MENU_ data per halaman",
                        loadingRecords: "Memuat data...",
                        zeroRecords: "Tidak ada data yang ditemukan",
                        paginate: {
                            first: "Pertama",
                            previous: "Sebelumnya",
                            next: "Selanjutnya",
                            last: "Terakhir"
                        }
                    }
                });

                window.purchaseStickerTable = table;

                // Apply filters
                $('#filter_product, #filter_ukuran').on('change keyup', function() {
                    table.draw();
                });

                // Handle product selection change with Choices.js compatibility
                $(document).on('change', '#product_id', function() {
                    const selectedValue = $(this).val();
                    debugLog('Product selection changed via jQuery event:', selectedValue);
                    debugLog('Event triggered by element:', this);

                    // Only handle if it's a valid selection
                    if (selectedValue && selectedValue !== '') {
                        handleProductSelection(selectedValue);
                    } else {
                        debugLog('Empty product selection, clearing fields');
                        $('#ukuran_stiker').val('');
                        $('#sticker-info').hide();
                    }
                });

                // Also keep the original change handler for backup
                $('#product_id').on('change', function() {
                    var productId = $(this).val();
                    debugLog('Product selection changed via direct handler:', productId);

                    if (productId) {
                        // Show loading state
                        $('#ukuran_stiker').val('Memuat...');
                        $('#sticker-info').hide();

                        $.ajax({
                            url: `/purchase-sticker/get-sticker-data/${productId}`,
                            type: 'GET',
                            success: function(response) {
                                if (response.success) {
                                    // Fill in the sticker data
                                    $('#ukuran_stiker').val(response.data.ukuran_stiker);
                                    $('#jumlah_stiker').val(response.data.jumlah_per_a3);

                                    // Show sticker info
                                    $('#sticker-details').html(
                                        `Ukuran: <strong>${response.data.ukuran_stiker}</strong> | ` +
                                        `Jumlah per A3: <strong>${response.data.jumlah_per_a3}</strong> sticker`
                                    );
                                    $('#sticker-info').fadeIn();
                                } else {
                                    $('#ukuran_stiker').val('');
                                    $('#sticker-info').hide();

                                    Swal.fire({
                                        title: 'Perhatian',
                                        text: response.message ||
                                            'Data sticker untuk produk ini tidak ditemukan',
                                        icon: 'warning',
                                        timer: 3000,
                                        showConfirmButton: false,
                                        toast: true,
                                        position: 'top-end'
                                    });
                                }
                            },
                            error: function(xhr) {
                                $('#ukuran_stiker').val('');
                                $('#sticker-info').hide();

                                console.error('Error getting sticker data:', xhr);
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Gagal mengambil data sticker. Silakan coba lagi.',
                                    icon: 'error',
                                    timer: 3000,
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end'
                                });
                            }
                        });
                    } else {
                        // Clear fields when no product selected
                        $('#ukuran_stiker').val('');
                        $('#sticker-info').hide();
                    }
                });

                // Form submission with Choices.js compatibility
                $('#purchaseStickerForm').on('submit', function(e) {
                    e.preventDefault();

                    // Ensure we get the correct value from Choices.js
                    if (productChoicesInstance) {
                        const selectedProduct = productChoicesInstance.getValue(true);
                        if (selectedProduct) {
                            // Manually set the value to the hidden select
                            $('#product_id').val(selectedProduct);
                            debugLog('Product ID fixed from Choices.js:', selectedProduct);
                        }
                    }

                    var formData = new FormData(this);
                    var purchaseStickerId = $('#purchase_sticker_id').val();
                    var url = purchaseStickerId ? `/purchase-sticker/${purchaseStickerId}` :
                        "{{ route('purchase-sticker.store') }}";

                    if (purchaseStickerId) {
                        formData.append('_method', 'PUT');
                    }

                    // Debug form data
                    debugLog('Form submission data:', {
                        product_id: formData.get('product_id'),
                        ukuran_stiker: formData.get('ukuran_stiker'),
                        jumlah_stiker: formData.get('jumlah_stiker'),
                        jumlah_order: formData.get('jumlah_order'),
                        stok_masuk: formData.get('stok_masuk')
                    });

                    Swal.fire({
                        title: purchaseStickerId ? 'Memperbarui...' : 'Menyimpan...',
                        text: 'Sedang memproses data purchase stiker',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            $('#saveBtn').prop('disabled', true).html(
                                '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...'
                            );
                            clearValidationErrors();
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#purchaseStickerModal').modal('hide');
                                if (window.purchaseStickerTable) {
                                    window.purchaseStickerTable.ajax.reload(null, false);
                                }

                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end'
                                });

                                resetForm();
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                showValidationErrors(errors);

                                Swal.fire({
                                    title: 'Mohon Periksa Input Anda',
                                    text: 'Ada beberapa kesalahan pada form yang Anda isi.',
                                    icon: 'warning',
                                    confirmButtonText: 'Saya Mengerti',
                                    confirmButtonColor: '#3085d6'
                                });
                            } else {
                                Swal.fire({
                                    title: 'Oops...',
                                    text: xhr.responseJSON?.message ||
                                        'Terjadi kesalahan pada permintaan.',
                                    icon: 'error',
                                    confirmButtonText: 'Coba Lagi',
                                    confirmButtonColor: '#3085d6'
                                });
                            }
                        },
                        complete: function() {
                            $('#saveBtn').prop('disabled', false).html('Simpan');
                        }
                    });
                });

            } catch (error) {
                console.error('Error initializing DataTable:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Gagal menginisialisasi tabel data.',
                    icon: 'error'
                });
            }
        });

        // Global functions
        window.showCreateModal = function() {
            try {
                debugLog('showCreateModal called');

                // Check if modal element exists
                const modalElement = document.getElementById('purchaseStickerModal');
                if (!modalElement) {
                    console.error('Modal element #purchaseStickerModal not found');
                    Swal.fire({
                        title: 'Error',
                        text: 'Modal tidak ditemukan. Silakan refresh halaman.',
                        icon: 'error'
                    });
                    return;
                }

                debugLog('Modal element found, resetting form...');
                resetForm();

                debugLog('Setting modal title...');
                $('#purchaseStickerModalLabel').text('Tambah Purchase Stiker');

                debugLog('Showing modal...');
                $('#purchaseStickerModal').modal('show');

                debugLog('showCreateModal completed successfully');
            } catch (error) {
                console.error('Error in showCreateModal:', error);
                debugLog('Error in showCreateModal:', error);

                Swal.fire({
                    title: 'Error',
                    text: 'Terjadi kesalahan saat membuka form. Silakan refresh halaman.',
                    icon: 'error'
                });
            }
        };

        window.editPurchaseSticker = function(id) {
            try {
                debugLog('editPurchaseSticker called with id:', id);

                // Check if modal element exists
                const modalElement = document.getElementById('purchaseStickerModal');
                if (!modalElement) {
                    console.error('Modal element #purchaseStickerModal not found');
                    Swal.fire({
                        title: 'Error',
                        text: 'Modal tidak ditemukan. Silakan refresh halaman.',
                        icon: 'error'
                    });
                    return;
                }

                if (!id) {
                    console.error('No ID provided to editPurchaseSticker');
                    Swal.fire({
                        title: 'Error',
                        text: 'ID data tidak valid.',
                        icon: 'error'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Memuat...',
                    text: 'Mengambil data purchase stiker',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                debugLog('Making AJAX request to get edit data...');

                $.get(`/purchase-sticker/${id}/edit`, function(response) {
                    Swal.close();

                    if (response.success) {
                        var data = response.data;
                        debugLog('Edit data received:', data);

                        $('#purchase_sticker_id').val(data.id);
                        $('#purchaseStickerModalLabel').text('Edit Purchase Stiker');

                        debugLog('Showing edit modal...');
                        // Show the modal first
                        $('#purchaseStickerModal').modal('show');

                        // Wait for modal to be fully shown and Choices.js to be initialized
                        $('#purchaseStickerModal').one('shown.bs.modal', function() {
                            debugLog('Edit modal shown, setting form data...');

                            setTimeout(() => {
                                // Set product value first
                                if (productChoicesInstance) {
                                    try {
                                        // Check if the product exists in choices
                                        const productExists = productChoicesInstance.getValue(
                                            true);
                                        debugLog('Current Choices.js value:', productExists);

                                        // Clear current selection first
                                        productChoicesInstance.removeActiveItems();

                                        // Set new value
                                        productChoicesInstance.setChoiceByValue(data.product_id
                                            .toString());

                                        // Verify the value was set
                                        const newValue = productChoicesInstance.getValue(true);
                                        debugLog('Product set via Choices.js - New value:',
                                            newValue);

                                        // Also update the underlying select element
                                        $('#product_id').val(data.product_id);

                                        // Trigger change event manually to ensure handlers are called
                                        $('#product_id').trigger('change');

                                    } catch (e) {
                                        debugLog('Error setting product via Choices.js:', e);
                                        // Fallback to regular select
                                        $('#product_id').val(data.product_id).trigger('change');
                                        debugLog('Product set via regular select fallback:',
                                            data
                                            .product_id);
                                    }
                                } else {
                                    debugLog(
                                        'Choices.js not initialized, using regular select');
                                    $('#product_id').val(data.product_id).trigger('change');
                                }

                                // Set other form values after a small delay to ensure product is set first
                                setTimeout(() => {
                                    $('#ukuran_stiker').val(data.ukuran_stiker);
                                    $('#jumlah_stiker').val(data.jumlah_stiker);
                                    $('#jumlah_order').val(data.jumlah_order);
                                    $('#stok_masuk').val(data.stok_masuk);

                                    debugLog('All form fields set:', {
                                        product_id: data.product_id,
                                        ukuran_stiker: data.ukuran_stiker,
                                        jumlah_stiker: data.jumlah_stiker,
                                        jumlah_order: data.jumlah_order,
                                        stok_masuk: data.stok_masuk
                                    });

                                    // Force trigger product selection to load sticker data for reference only
                                    // Use preserveExistingData=true to avoid overwriting the existing ukuran_stiker
                                    if (data.product_id) {
                                        handleProductSelection(data.product_id, true);
                                    }
                                }, 100);

                            }, 300); // Increased delay to ensure Choices.js is fully ready
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.message || 'Gagal mengambil data purchase stiker',
                            icon: 'error'
                        });
                    }
                }).fail(function(xhr) {
                    Swal.close();
                    debugLog('Edit request failed:', xhr);
                    Swal.fire({
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Gagal memuat data purchase stiker',
                        icon: 'error'
                    });
                });
            } catch (error) {
                console.error('Error in editPurchaseSticker:', error);
                debugLog('Error in editPurchaseSticker:', error);

                Swal.fire({
                    title: 'Error',
                    text: 'Terjadi kesalahan saat membuka form edit. Silakan refresh halaman.',
                    icon: 'error'
                });
            }
        };

        window.deletePurchaseSticker = function(id) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: "Apakah Anda yakin ingin menghapus purchase stiker ini? Tindakan ini tidak dapat dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Sedang menghapus data purchase stiker',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `/purchase-sticker/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                if (window.purchaseStickerTable) {
                                    window.purchaseStickerTable.ajax.reload(null, false);
                                }

                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Gagal Menghapus',
                                text: xhr.responseJSON?.message ||
                                    'Tidak dapat menghapus purchase stiker saat ini.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    });
                }
            });
        };

        window.showDetail = function(id) {
            Swal.fire({
                title: 'Memuat...',
                text: 'Mengambil detail purchase stiker',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            $.get(`/purchase-sticker/${id}`, function(response) {
                Swal.close();
                $('#detailContent').html(response);
                $('#detailModal').modal('show');
            }).fail(function(xhr) {
                Swal.fire({
                    title: 'Error',
                    text: 'Gagal memuat detail purchase stiker',
                    icon: 'error'
                });
            });
        };

        window.applyFilters = function() {
            if (window.purchaseStickerTable) {
                window.purchaseStickerTable.ajax.reload();
            }
        };

        window.clearFilters = function() {
            $('#filter_product').val('');
            $('#filter_ukuran').val('');
            if (window.purchaseStickerTable) {
                window.purchaseStickerTable.ajax.reload();
            }
        };

        // Test functions availability at the end
        debugLog('Testing function availability...');
        debugLog('showCreateModal available:', typeof window.showCreateModal);
        debugLog('editPurchaseSticker available:', typeof window.editPurchaseSticker);
        debugLog('deletePurchaseSticker available:', typeof window.deletePurchaseSticker);

        // Log that initialization is complete
        debugLog('Purchase Sticker initialization completed successfully');

        // Test modal element availability
        debugLog('Modal element #purchaseStickerModal exists:', !!document.getElementById('purchaseStickerModal'));
        debugLog('Button element with onclick="showCreateModal()" exists:', !!document.querySelector(
            '[onclick*="showCreateModal"]'));
    </script>
@endsection
