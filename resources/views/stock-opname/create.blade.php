@extends('layouts.main')

@section('title', 'Buat Stock Opname Baru')
@section('breadcrumb-item', 'Buat Stock Opname Baru')

@section('css')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- [Page specific CSS] start -->
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
    </style>
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- Create Stock Opname start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Buat Stock Opname Baru</h5>
                        <div>
                            <a href="{{ route('stock-opname.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('stock-opname.store') }}" method="POST" id="createStockOpnameForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Jenis Stock Opname <span class="text-danger">*</span></label>
                                    <select class="form-control" name="type" id="create-type" required>
                                        <option value="">Pilih Jenis Opname</option>
                                        <option value="bahan_baku" {{ old('type') == 'bahan_baku' ? 'selected' : '' }}>Bahan Baku</option>
                                        <option value="finished_goods" {{ old('type') == 'finished_goods' ? 'selected' : '' }}>Finished Goods</option>
                                        <option value="sticker" {{ old('type') == 'sticker' ? 'selected' : '' }}>Sticker</option>
                                    </select>
                                    @error('type')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Pilih jenis inventory yang akan dilakukan stock opname</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Opname <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="tanggal_opname" id="create-tanggal" 
                                           value="{{ old('tanggal_opname', date('Y-m-d')) }}" required>
                                    @error('tanggal_opname')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Tanggal pelaksanaan stock opname</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Catatan</label>
                                    <textarea class="form-control" name="notes" id="create-notes" rows="3" 
                                              placeholder="Catatan tambahan untuk stock opname ini...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Info Box -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Informasi:</h6>
                            <ul class="mb-0 small">
                                <li>Sistem akan otomatis mengambil data stok dari sistem</li>
                                <li>Anda akan diarahkan ke halaman input stok fisik</li>
                                <li>Selisih akan dihitung otomatis: Stok Fisik - Stok Sistem</li>
                            </ul>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('stock-opname.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Buat Stock Opname
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Create Stock Opname end -->
    </div>
    <!-- [ Main Content ] end -->
@endsection

@section('scripts')
    <!-- Core JS files -->
    <script src="{{ URL::asset('build/js/plugins/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/bootstrap.min.js') }}"></script>

    <!-- Choices JS -->
    <script src="{{ URL::asset('build/js/plugins/choices.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="{{ URL::asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize Choices.js for create form
            var createTypeChoices = new Choices('#create-type', {
                searchEnabled: true,
                searchPlaceholderValue: "Cari jenis opname",
                itemSelectText: '',
                placeholder: true,
                placeholderValue: "Pilih jenis opname",
                allowHTML: false
            });

            // Auto-focus on type selection
            $('#create-type').focus();
            
            // Show different info based on selected type
            $('#create-type').on('change', function() {
                const type = $(this).value;
                let info = '';
                
                switch(type) {
                    case 'bahan_baku':
                        info = 'Akan mengambil data dari Inventory Bahan Baku (live_stok_gudang)';
                        break;
                    case 'finished_goods':
                        info = 'Akan mengambil data dari Finished Goods Stock (stok_finished_goods)';
                        break;
                    case 'sticker':
                        info = 'Akan mengambil data dari Sticker Stock (stok_stiker)';
                        break;
                    default:
                        info = 'Pilih jenis opname untuk melihat informasi detail';
                }
                
                // Update info text
                if (info) {
                    if ($('#type-info').length === 0) {
                        $('#create-type').closest('.mb-3').append('<small id="type-info" class="form-text text-info mt-1"></small>');
                    }
                    $('#type-info').text(info);
                } else {
                    $('#type-info').remove();
                }
            });

            // Handle form submission
            $('#createStockOpnameForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var submitButton = form.find('button[type="submit"]');

                // Disable submit button to prevent double submission
                submitButton.prop('disabled', true);

                var formData = new FormData(this);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.success) {
                            // Show success message and redirect
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Stock Opname berhasil dibuat. Anda akan diarahkan ke halaman input stok fisik.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                // Redirect to show page
                                window.location.href = data.redirect_url || "{{ route('stock-opname.index') }}";
                            });
                        }
                    },
                    error: function(xhr) {
                        // Re-enable submit button on error
                        submitButton.prop('disabled', false);

                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            const errorMessages = Object.values(errors).flat();

                            Swal.fire({
                                title: 'Mohon Periksa Input Anda',
                                html: errorMessages.join('<br>'),
                                icon: 'warning',
                                confirmButtonText: 'Saya Mengerti',
                                confirmButtonColor: '#3085d6'
                            });
                        } else {
                            // Other errors
                            Swal.fire({
                                title: 'Oops...',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan pada permintaan.',
                                icon: 'error',
                                confirmButtonText: 'Coba Lagi',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection
