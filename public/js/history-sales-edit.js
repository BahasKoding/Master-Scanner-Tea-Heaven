/**
 * History Sales Edit/Delete Implementation
 * ======================================
 * This file contains the functionality for editing and deleting history sales records.
 */

// Reference to DataTable instance
let historyTable;

// Initialize edit functionality
function initializeHistoryEdit(tableInstance) {
    historyTable = tableInstance;
}

function editHistorySale(id) {
    $.ajax({
        url: `/history-sales/${id}/edit`,
        type: 'GET',
        success: function (response) {
            if (response.status === 'success') {
                const data = response.data;
                $('#edit_history_sale_id').val(data.id);
                $('#edit_no_resi').val(data.no_resi);

                $('#edit-sku-container').empty();
                data.no_sku.forEach((sku, index) => {
                    const qty = data.qty[index];
                    addEditSkuField(sku, qty);
                });

                $('#editHistorySaleModal').modal('show');
            }
        },
        error: function (xhr) {
            Swal.fire('Error!', 'Failed to load history sale data.', 'error');
        }
    });
}

function deleteHistorySale(id) {
    Swal.fire({
        title: 'Hapus Data?',
        text: "Data akan diarsipkan dan bisa dipulihkan kembali",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Arsipkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/history-sales/${id}`,
                type: 'DELETE',
                success: function (response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Terarsipkan!',
                            text: 'Data berhasil diarsipkan dan dapat dipulihkan kembali',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        if (historyTable) {
                            historyTable.ajax.reload(null, false);
                        }
                    }
                },
                error: function (xhr) {
                    Swal.fire('Gagal!', 'Terjadi kesalahan saat mengarsipkan data. Silakan coba lagi.', 'error');
                }
            });
        }
    });
}

function restoreHistorySale(id) {
    Swal.fire({
        title: 'Pulihkan Data?',
        text: "Data akan dikembalikan ke daftar aktif",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Pulihkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/history-sales/${id}/restore`,
                type: 'POST',
                success: function (response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Data berhasil dipulihkan',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        if (historyTable) {
                            historyTable.ajax.reload(null, false);
                        }
                    }
                },
                error: function (xhr) {
                    Swal.fire('Gagal!', 'Terjadi kesalahan saat memulihkan data. Silakan coba lagi.', 'error');
                }
            });
        }
    });
}

function forceDeleteHistorySale(id) {
    Swal.fire({
        title: 'Hapus Permanen?',
        text: "Data akan dihapus secara permanen dan tidak dapat dipulihkan kembali!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus Permanen!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/history-sales/${id}/force`,
                type: 'DELETE',
                success: function (response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Terhapus!',
                            text: 'Data telah dihapus secara permanen',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        if (historyTable) {
                            historyTable.ajax.reload(null, false);
                        }
                    }
                },
                error: function (xhr) {
                    Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus data secara permanen. Silakan coba lagi.', 'error');
                }
            });
        }
    });
}

function addEditSkuField(sku = '', qty = 1) {
    const skuInput = `
        <div class="sku-input-container">
            <input type="text" class="form-control sku-input" name="no_sku[]" value="${sku}" required>
            <input type="number" class="form-control qty-input" name="qty[]" value="${qty}" required min="1">
            <button type="button" class="btn btn-outline-danger remove-edit-sku-btn">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    `;
    $('#edit-sku-container').append(skuInput);
}

// Document ready handlers for edit functionality
$(document).ready(function () {
    // Add SKU button in edit modal
    $('#add-edit-sku-btn').on('click', function () {
        addEditSkuField();
    });

    // Remove SKU button in edit modal
    $(document).on('click', '.remove-edit-sku-btn', function () {
        if ($('#edit-sku-container .sku-input-container').length > 1) {
            $(this).closest('.sku-input-container').remove();
        } else {
            Swal.fire('Warning!', 'At least one SKU is required.', 'warning');
        }
    });

    // Edit form submission
    $('#editHistorySaleForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#edit_history_sale_id').val();

        $.ajax({
            url: `/history-sales/${id}`,
            type: 'PUT',
            data: $(this).serialize(),
            success: function (response) {
                if (response.status === 'success') {
                    $('#editHistorySaleModal').modal('hide');
                    if (historyTable) {
                        historyTable.ajax.reload(null, false);
                    }

                    // Show success message with warning if applicable
                    if (response.warning) {
                        Swal.fire('Berhasil dengan Peringatan!',
                            response.message + '\n\n' + response.warning, 'warning');
                    } else {
                        Swal.fire('Success!', response.message, 'success');
                    }
                }
            },
            error: function (xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message ||
                    'Failed to update history sale.', 'error');
            }
        });
    });
}); 