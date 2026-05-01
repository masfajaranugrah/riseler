@extends('layouts/layoutMaster')

@section('title', 'Daftar Tagihan')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
])
<style>
/* ========================================= */
/* SHADCN UI STYLE - BLACK & WHITE */
/* ========================================= */
:root {
  --card-shadow: 0 2px 8px rgba(0,0,0,0.08);
  --card-hover-shadow: 0 4px 16px rgba(0,0,0,0.12);
  --border-radius: 12px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  --primary-color: #18181b;
  --success-color: #18181b;
}

/* Card Design */
.card {
  border: none;
  border-radius: var(--border-radius);
  box-shadow: var(--card-shadow);
  transition: var(--transition);
  overflow: hidden;
}

.card:hover {
  box-shadow: var(--card-hover-shadow);
}

/* Dashboard Cards with Border Accent */
.card-border-shadow-primary::before,
.card-border-shadow-success::before,
.card-border-shadow-warning::before,
.card-border-shadow-info::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
}

.card-border-shadow-primary::before {
  background: #18181b;
}

.card-border-shadow-success::before {
  background: #18181b;
}

.card-border-shadow-warning::before {
  background: #f59e0b;
}

.card-border-shadow-info::before {
  background: #18181b;
}

/* Stats Card */
.stats-card {
  border-radius: var(--border-radius);
  padding: 1.5rem;
  background: #fff;
  border: 1px solid #e4e4e7;
  transition: var(--transition);
}

.stats-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

.stats-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
}

/* Avatar */
.avatar-initial {
  border-radius: 12px;
  transition: var(--transition);
  background: #18181b !important;
  color: #fafafa !important;
}

.card:hover .avatar-initial {
  transform: scale(1.05);
}

/* Neutralize accent labels - shadcn style (gray background) */
.bg-label-primary,
.bg-label-success,
.bg-label-warning,
.bg-label-dark,
.bg-label-secondary {
  background: #f4f4f5 !important;
  color: #18181b !important;
  border: 1px solid #e4e4e7 !important;
}

/* Badge Paket - Black background, white text */
.bg-label-info {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
  border-radius: 9999px !important;
}

/* Stats icon - gray background */
.stats-icon.bg-label-primary,
.stats-icon.bg-label-success,
.stats-icon.bg-label-warning,
.stats-icon.bg-label-info {
  background: #f4f4f5 !important;
  color: #18181b !important;
}

.bg-label-danger {
  background: #dc2626 !important;
  color: #fafafa !important;
}

.bg-label-warning {
  background: #f59e0b !important;
  color: #fafafa !important;
}

/* ========================================= */
/* BUTTONS - ALL BLACK */
/* ========================================= */
.btn {
  border-radius: 6px !important;
  padding: 0.5rem 1rem !important;
  font-weight: 500 !important;
  font-size: 0.875rem !important;
  transition: all 0.15s ease !important;
  cursor: pointer !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 0.5rem !important;
}

.btn-primary,
.btn.btn-primary {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
  box-shadow: none !important;
}

.btn-primary:hover,
.btn.btn-primary:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
  transform: none !important;
}

.btn-success,
.btn.btn-success {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
  box-shadow: none !important;
}

.btn-success:hover,
.btn.btn-success:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

.btn-warning,
.btn.btn-warning {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
  box-shadow: none !important;
}

.btn-warning:hover,
.btn.btn-warning:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

.btn-secondary,
.btn.btn-secondary {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn-secondary:hover,
.btn.btn-secondary:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

.btn-sm {
  padding: 0.375rem 0.75rem !important;
  font-size: 0.8125rem !important;
}

/* Outline Buttons */
.btn-outline-primary,
.btn-outline-secondary,
.btn-outline-success,
.btn.btn-outline-primary,
.btn.btn-outline-secondary,
.btn.btn-outline-success {
  background: transparent !important;
  background-color: transparent !important;
  border: 1px solid #e4e4e7 !important;
  color: #18181b !important;
}

.btn-outline-primary:hover,
.btn-outline-secondary:hover,
.btn-outline-success:hover,
.btn.btn-outline-primary:hover,
.btn.btn-outline-secondary:hover,
.btn.btn-outline-success:hover {
  background: #f4f4f5 !important;
  background-color: #f4f4f5 !important;
  border-color: #18181b !important;
  color: #18181b !important;
}

/* ========================================= */
/* BADGES - SHADCN STYLE */
/* ========================================= */
.badge {
  border-radius: 9999px !important;
  font-weight: 500 !important;
  letter-spacing: 0 !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 0.25rem !important;
}

.badge.bg-success,
.bg-success {
  background: #22c55e !important;
  color: #fafafa !important;
}

.badge.bg-warning,
.bg-warning {
  background: #f59e0b !important;
  color: #fafafa !important;
}

.badge.bg-danger,
.bg-danger {
  background: #dc2626 !important;
  color: #fafafa !important;
}

.badge.bg-primary,
.bg-primary:not(.btn):not(.modal-header) {
  background: #18181b !important;
  color: #fafafa !important;
}

.badge.bg-info,
.bg-info:not(.btn) {
  background: #18181b !important;
  color: #fafafa !important;
}

/* Form Controls */
.form-select, .form-control {
  border-radius: 8px;
  border: 1px solid #e4e4e7;
  padding: 0.625rem 1rem;
  transition: var(--transition);
}

.form-select:focus, .form-control:focus {
  border-color: #18181b;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b;
}

/* Modal - Black Header */
.modal-content {
  border-radius: 16px;
  border: none;
  box-shadow: 0 8px 32px rgba(0,0,0,0.15);
}

.modal-header {
  border-radius: 16px 16px 0 0;
  padding: 1.5rem;
  border-bottom: none;
  background: #18181b !important;
  color: #fafafa !important;
}

.modal-header.bg-primary,
.modal-header.bg-warning,
.modal-header.bg-success,
.modal-header.bg-info {
  background: #18181b !important;
  color: #fafafa !important;
}

.modal-header .modal-title,
.modal-header h5,
.modal-header .btn-close {
  color: #fafafa !important;
}

.modal-header .btn-close {
  filter: brightness(0) invert(1) !important;
}

.modal-body {
  padding: 2rem;
  max-height: 70vh;
  overflow-y: auto;
}

.modal-footer {
  padding: 1.5rem;
  border-top: 1px solid #e4e4e7;
  background: #fafafa;
}

.btn-close-white {
  filter: brightness(0) invert(1);
}

/* Modal Backdrop */
.modal-backdrop {
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}

.modal-backdrop.show {
  opacity: 0.4;
  background-color: #18181b;
}

/* Table */
.table {
  border-collapse: separate;
  border-spacing: 0;
}

.table thead th {
  background: #f8fafc;
  border: none;
  padding: 1rem;
  font-weight: 600;
  color: #18181b;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  white-space: nowrap;
}

.table tbody tr {
  transition: var(--transition);
}

.table tbody tr:not(.empty-state-row):hover {
  background: #f4f4f5;
}

.table tbody td {
  padding: 1rem;
  border-bottom: 1px solid #e4e4e7;
  vertical-align: middle;
  color: #18181b;
}

/* Empty State */
.empty-state-row td {
  background: #fafbfc !important;
  border: none !important;
}

.empty-state-content {
  padding: 3rem 1rem;
}

/* Loading Overlay */
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(24, 24, 27, 0.5);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  backdrop-filter: blur(4px);
}

/* ========================================= */
/* DETAIL MODAL STYLES */
/* ========================================= */
.customer-header-info {
  text-align: center;
  padding: 1.5rem;
  background: #f4f4f5;
  border-radius: 12px;
  margin-bottom: 1.5rem;
  border: 1px solid #e4e4e7;
}

.customer-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: #18181b !important;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #fafafa !important;
  font-weight: 700;
  font-size: 2.5rem;
  margin-bottom: 1rem;
  box-shadow: 0 4px 16px rgba(24, 24, 27, 0.3);
  border: 4px solid white;
}

.customer-name {
  font-size: 1.5rem;
  font-weight: 700;
  color: #18181b;
  margin-bottom: 0.5rem;
}

.customer-status {
  display: inline-block;
  padding: 0.5rem 1.5rem;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.875rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.detail-section {
  background: #ffffff;
  border: 1px solid #e4e4e7;
  border-radius: 12px;
  padding: 1.25rem;
  margin-bottom: 1.25rem;
  transition: all 0.2s;
}

.detail-section:hover {
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  border-color: #18181b;
}

.detail-section h6 {
  color: #18181b;
  font-weight: 700;
  margin-bottom: 1.25rem;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  display: flex;
  align-items: center;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid #18181b;
}

.detail-section h6 i {
  margin-right: 0.5rem;
  font-size: 1.1rem;
}

.detail-item {
  display: flex;
  padding: 0.875rem 0;
  border-bottom: 1px solid #e4e4e7;
  align-items: flex-start;
}

.detail-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.detail-label {
  color: #71717a;
  font-weight: 600;
  min-width: 150px;
  font-size: 0.875rem;
  display: flex;
  align-items: center;
}

.detail-label i {
  margin-right: 0.5rem;
  color: #a1a1aa;
  font-size: 1rem;
}

.detail-value {
  color: #18181b;
  font-size: 0.875rem;
  flex: 1;
  word-break: break-word;
}

/* Card Header */
.card-header {
  background: transparent;
  padding: 1.5rem;
  border-bottom: 1px solid #e4e4e7;
}

.card-header-custom {
  background: #ffffff !important;
  color: #18181b !important;
  border-radius: var(--border-radius) var(--border-radius) 0 0;
  padding: 1.5rem;
  border-bottom: 1px solid #e4e4e7;
}

.card-header-custom h4,
.card-header-custom h5,
.card-header-custom p,
.card-header-custom i,
.card-header-custom small {
  color: #18181b !important;
}

.card-header-custom .opacity-75 {
  color: #71717a !important;
}

/* Input Groups */
.input-group-text {
  border-radius: 8px 0 0 8px;
  background: #f4f4f5;
  border: 1px solid #e4e4e7;
  color: #18181b;
  font-weight: 500;
}

/* ========================================= */
/* PAGINATION STYLES */
/* ========================================= */
.pagination-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-top: 1px solid #e4e4e7;
  background: #fafafa;
  border-radius: 0 0 var(--border-radius) var(--border-radius);
}

.pagination {
  margin: 0;
  gap: 0.5rem;
}

.pagination .page-item .page-link {
  border-radius: 50% !important;
  width: 40px;
  height: 40px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #e4e4e7;
  color: #18181b;
  font-weight: 600;
  background-color: #fff;
  margin: 0 2px;
  transition: all 0.3s ease;
}

.pagination .page-item .page-link:hover {
  background-color: #18181b;
  border-color: #18181b;
  color: #fafafa;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(24, 24, 27, 0.2);
}

.pagination .page-item.active .page-link {
  background-color: #18181b !important;
  border-color: #18181b !important;
  color: #fafafa !important;
  box-shadow: 0 4px 12px rgba(24, 24, 27, 0.4);
}

.pagination .page-item.disabled .page-link {
  background-color: #f4f4f5;
  border-color: #e4e4e7;
  color: #a1a1aa;
  cursor: not-allowed;
}

.pagination-info {
  color: #71717a;
  font-size: 0.875rem;
  font-weight: 500;
}

/* Hide DataTables default controls */
.dataTables_info,
.dataTables_paginate,
.dataTables_length,
.dataTables_filter {
  display: none !important;
}

/* Hide default Laravel pagination results text */
.pagination-wrapper .pagination + div,
.pagination-wrapper nav + div,
.pagination-wrapper div:has(> nav) > p,
.pagination-wrapper > div > nav ~ *:not(.pagination),
.pagination-wrapper > div:last-child p {
  display: none !important;
}

/* Alternative: Hide any 'Showing X to Y of Z results' text */
.pagination-wrapper div:last-child > p,
.pagination-wrapper > div > .text-sm,
nav[role="navigation"] > div:first-child,
nav[role="navigation"] > div > p {
  display: none !important;
}

/* Text colors */
.text-primary {
  color: #18181b !important;
}

.text-success {
  color: #18181b !important;
}

.text-info {
  color: #18181b !important;
}

.text-danger {
  color: #dc2626 !important;
}

.text-warning {
  color: #f59e0b !important;
}

.text-muted {
  color: #71717a !important;
}

/* Animations */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.card {
  animation: fadeIn 0.3s ease-out;
}

/* Responsive */
@media (max-width: 768px) {
  .modal-body {
    padding: 1.5rem;
  }
  .card-body {
    padding: 1.25rem;
  }
  .pagination-wrapper {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
}
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
])
@endsection

@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function () {
    // ========================================
    // HELPER FUNCTIONS
    // ========================================
    function showLoading() {
        $('.loading-overlay').css('display', 'flex');
    }

    function hideLoading() {
        $('.loading-overlay').fadeOut(300);
    }

    const formatDate = d => d.toISOString().split('T')[0];

    // ========================================
    // FLATPICKR INITIALIZATION
    // ========================================
    $(document).on('shown.bs.modal', '[id^="modalEditTagihan-"]', function () {
        flatpickr($(this).find('.flatpickr-edit-start'), {
            dateFormat: "Y-m-d",
            allowInput: true
        });
        flatpickr($(this).find('.flatpickr-edit-end'), {
            dateFormat: "Y-m-d",
            allowInput: true
        });
    });

    flatpickr("#tanggal_mulai", {
        dateFormat: "Y-m-d",
        defaultDate: new Date(),
        allowInput: true
    });

    flatpickr("#tanggal_berakhir", {
        dateFormat: "Y-m-d",
        allowInput: false
    });

    // ========================================
    // SELECT2 PELANGGAN - AJAX MODE
    // ========================================
    $('#pelangganSelect').select2({
        placeholder: '-- Ketik untuk mencari pelanggan --',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#modalTambahTagihan'),
        minimumInputLength: 1,
        ajax: {
            url: '{{ route("pelanggan.search") }}',
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return {
                    q: params.term,
                    page: params.page || 1
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: {
                        more: data.pagination.more
                    }
                };
            },
            cache: true
        },
        language: {
            inputTooShort: function() {
                return 'Ketik minimal 1 karakter untuk mencari...';
            },
            searching: function() {
                return 'Mencari...';
            },
            noResults: function() {
                return 'Tidak ditemukan';
            }
        }
    });

    const tglMulai = document.getElementById('tanggal_mulai');
    if (tglMulai) {
        tglMulai.value = formatDate(new Date());
    }

    function fillFields(selected) {
        if (!selected || !selected.id) {
            $('#nama_lengkap, #alamat_jalan, #rt, #rw, #desa, #kecamatan, #kabupaten, #provinsi, #kode_pos, #no_whatsapp, #nomer_id, #paket, #harga, #masa_pembayaran, #kecepatan, #pelanggan_id, #paket_id, #tanggal_berakhir').val('');
            return;
        }

        // Data dari AJAX response Select2
        $('#nama_lengkap').val(selected.nama || '');
        $('#alamat_jalan').val(selected.alamat_jalan || '');
        $('#rt').val(selected.rt || '');
        $('#rw').val(selected.rw || '');
        $('#desa').val(selected.desa || '');
        $('#kecamatan').val(selected.kecamatan || '');
        $('#kabupaten').val(selected.kabupaten || '');
        $('#provinsi').val(selected.provinsi || '');
        $('#kode_pos').val(selected.kode_pos || '');
        $('#no_whatsapp').val(selected.nowhatsapp || '');
        $('#nomer_id').val(selected.nomorid || '');
        $('#paket').val(selected.paket || '');
        $('#harga').val(selected.harga || '');
        $('#masa_pembayaran').val(selected.masa || '');
        $('#kecepatan').val(selected.kecepatan || '');
        $('#pelanggan_id').val(selected.id);
        $('#paket_id').val(selected.paket_id || '');

        // Hitung tanggal berakhir
        const startDateVal = $('#tanggal_mulai').val();
        if (startDateVal && selected.masa) {
            const startDate = new Date(startDateVal);
            const endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + parseInt(selected.masa));
            $('#tanggal_berakhir').val(formatDate(endDate));
        }
    }

    // Handler saat pelanggan dipilih dari dropdown
    $('#pelangganSelect').on('select2:select', function(e) {
        fillFields(e.params.data);
    });

    // Handler saat pelanggan di-clear
    $('#pelangganSelect').on('select2:clear', function() {
        fillFields(null);
    });

    if (tglMulai) {
        tglMulai.addEventListener('change', function () {
            // Recalculate tanggal_berakhir jika ada pelanggan yang dipilih
            const selectedData = $('#pelangganSelect').select2('data');
            if (selectedData && selectedData.length > 0 && selectedData[0].id) {
                const data = selectedData[0];
                if (data.masa) {
                    const startDate = new Date($('#tanggal_mulai').val());
                    const endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + parseInt(data.masa));
                    $('#tanggal_berakhir').val(formatDate(endDate));
                }
            }
        });
    }

    // Modal shown - focus ke search pelanggan
    $('#modalTambahTagihan').on('shown.bs.modal', function () {
        $('#pelangganSelect').select2('open');
    });

    // ========================================
    // DATATABLES - TANPA SEARCH & PAGINATION
    // ========================================
    const $table = $('.datatables-users');
    if ($table.length) {
        const hasData = $table.find('tbody tr').not('.empty-state-row').length > 0;

        if (hasData) {
            try {
                $table.DataTable({
                    paging: false,
                    lengthChange: false,
                    searching: false,
                    ordering: true,
                    info: false,
                    scrollX: true,
                    autoWidth: false,
                    dom: 'rt',
                    columnDefs: [
                        { orderable: false, targets: [0, -1] },
                        { width: '80px', targets: 0 },
                        { width: '100px', targets: 1 }
                    ],
                    language: {
                        emptyTable: "Tidak ada data tersedia",
                        zeroRecords: "Tidak ada data yang sesuai"
                    }
                });
            } catch (error) {
                console.warn('DataTables initialization error:', error);
            }
        }
    }

    // ========================================
    // AUTO SUBMIT ON FILTER CHANGE
    // ========================================
    $('#statusFilter').on('change', function() {
        $('#filterForm').submit();
    });

    // ========================================
    // LOADING OVERLAY ON FORM SUBMIT
    // ========================================
    $('#filterForm').on('submit', function() {
        showLoading();
    });

    // ========================================
    // SWEETALERT DELETE
    // ========================================
    $(document).on('submit', '.delete-form', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const form = this;

        Swal.fire({
            title: 'Konfirmasi Penghapusan',
            html: '<p class="mb-0">Yakin ingin menghapus tagihan ini?<br><strong class="text-danger">Data tidak dapat dikembalikan!</strong></p>',
            icon: 'warning',
            showCancelButton: true,
            showConfirmButton: true,
            showDenyButton: false,
            confirmButtonColor: '#f5365c',
            cancelButtonColor: '#8898aa',
            confirmButtonText: '<i class="ri-delete-bin-line me-1"></i>Hapus',
            cancelButtonText: 'Batal',
            allowOutsideClick: false,
            allowEscapeKey: false,
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                setTimeout(() => form.submit(), 500);
            }
        });
    });

    // ========================================
    // KONFIRMASI PEMBAYARAN
    // ========================================
    $(document).on('click', '.btn-konfirmasi', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const id = $(this).data('id');
        const nama = $(this).data('nama');

        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            html: `<p class="mb-0">Apakah <strong>${nama}</strong> sudah membayar?</p>`,
            icon: 'question',
            showCancelButton: true,
            showConfirmButton: true,
            confirmButtonColor: '#2dce89',
            cancelButtonColor: '#8898aa',
            confirmButtonText: '<i class="ri-check-line me-1"></i>Ya, Lunas',
            cancelButtonText: 'Batal',
            buttonsStyling: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();

                $.post(`/dashboard/admin/tagihan/${id}/bayar`, {
                    _token: $('meta[name="csrf-token"]').attr('content')
                })
                .done(resp => {
                    hideLoading();
                    if (resp.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Pembayaran berhasil dikonfirmasi',
                            timer: 1500,
                            showConfirmButton: false,
                            allowOutsideClick: false
                        }).then(() => location.reload());
                    }
                })
                .fail(() => {
                    hideLoading();
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan server',
                        confirmButtonText: 'OK'
                    });
                });
            }
        });
    });

    // ========================================
    // MASS TAGIHAN
    // ========================================
    $('#modalMassTagihan').on('shown.bs.modal', function () {
        flatpickr(".flatpickr-select-start-all", {
            dateFormat: "Y-m-d",
            defaultDate: new Date(),
            minDate: "today",
            allowInput: true
        });
        flatpickr(".flatpickr-select-start-end", {
            dateFormat: "Y-m-d",
            defaultDate: new Date().fp_incr(7),
            minDate: "today",
            allowInput: true
        });
    });

    // ========================================
    // ? BUTTON DETAIL - SHOW MODAL
    // ========================================
    $(document).on('click', '.btn-detail', function() {
        const $row = $(this).closest('tr');

        // Ambil data dari table cells
        const nomorId = $row.find('.badge.bg-label-dark').text().trim();
        const namaLengkap = $row.find('td:nth-child(3) strong').text().trim();
        const noWhatsapp = $row.find('code').text().trim().replace(/\D/g, '');
        const noWhatsappDisplay = $row.find('code').text().trim();
        const status = $row.find('td:nth-child(5) .badge').text().trim();
        const paket = $row.find('td:nth-child(6) .badge').text().trim();
        const harga = $row.find('td:nth-child(7) strong').text().trim();

        // Data dari attribute
        const alamat = $row.data('alamat') || '-';
        const kecamatan = $row.data('kecamatan') || '-';
        const kabupaten = $row.data('kabupaten') || '-';
        const provinsi = $row.data('provinsi') || '-';
        const kecepatan = $row.data('kecepatan') || '-';
        const tanggalMulai = $row.data('tanggal-mulai') || '-';
        const jatuhTempo = $row.data('jatuh-tempo') || '-';
        const catatan = $row.data('catatan') || '-';
        const buktiUrl = $row.data('bukti') || '';

        // Badge status color
        const statusClass = status.toLowerCase().includes('lunas') ? 'bg-success' : 'bg-danger';
        const statusIcon = status.toLowerCase().includes('lunas') ? 'checkbox-circle' : 'close-circle';

        // Build modal content
        const modalContent = `
            <div class="customer-header-info">
                <div class="customer-avatar">
                    ${namaLengkap.charAt(0).toUpperCase()}
                </div>
                <h5 class="customer-name">${namaLengkap}</h5>
                <span class="badge ${statusClass} customer-status">
                    <i class="ri-${statusIcon}-line me-1"></i>
                    ${status}
                </span>
            </div>

            <!-- Informasi Dasar -->
            <div class="detail-section">
                <h6><i class="ri-user-3-line"></i>Informasi Dasar</h6>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-barcode-line"></i>
                        Nomor ID
                    </div>
                    <div class="detail-value"><strong>${nomorId}</strong></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-user-line"></i>
                        Nama Lengkap
                    </div>
                    <div class="detail-value">${namaLengkap}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-whatsapp-line"></i>
                        WhatsApp
                    </div>
                    <div class="detail-value">
                        <a href="https://wa.me/${noWhatsapp}" target="_blank" class="text-success text-decoration-none">
                            <i class="ri-whatsapp-line me-1"></i>${noWhatsappDisplay}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Alamat -->
            <div class="detail-section">
                <h6><i class="ri-map-pin-line"></i>Alamat Lengkap</h6>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-map-2-line"></i>
                        Alamat
                    </div>
                    <div class="detail-value">${alamat}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-building-line"></i>
                        Kecamatan
                    </div>
                    <div class="detail-value">${kecamatan}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-map-pin-range-line"></i>
                        Kabupaten
                    </div>
                    <div class="detail-value">${kabupaten}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-global-line"></i>
                        Provinsi
                    </div>
                    <div class="detail-value">${provinsi}</div>
                </div>
            </div>

            <!-- Paket Internet -->
            <div class="detail-section">
                <h6><i class="ri-wifi-line"></i>Paket Internet</h6>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-box-3-line"></i>
                        Nama Paket
                    </div>
                    <div class="detail-value">${paket}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-speed-line"></i>
                        Kecepatan
                    </div>
                    <div class="detail-value"><strong>${kecepatan}</strong></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-money-dollar-circle-line"></i>
                        Harga
                    </div>
                    <div class="detail-value"><strong class="text-primary">${harga}</strong></div>
                </div>
            </div>

            <!-- Tagihan -->
            <div class="detail-section">
                <h6><i class="ri-calendar-check-line"></i>Detail Tagihan</h6>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-calendar-line"></i>
                        Tanggal Mulai
                    </div>
                    <div class="detail-value">${tanggalMulai}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-calendar-close-line"></i>
                        Jatuh Tempo
                    </div>
                    <div class="detail-value"><strong class="text-danger">${jatuhTempo}</strong></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-file-text-line"></i>
                        Catatan
                    </div>
                    <div class="detail-value">${catatan}</div>
                </div>

            </div>
        `;

        // Populate modal dan tampilkan
        $('#detailModal .modal-body').html(modalContent);
        $('#detailModal').modal('show');
    });
});
</script>
@endsection

@section('content')
<!-- Loading Overlay -->
<div class="loading-overlay">
    <div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<div class="container-fluid px-4 py-4">

  <!-- ========================================= -->
  <!-- FILTER & SEARCH -->
  <!-- ========================================= -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('tagihan.outstanding') }}" id="filterForm">
        <div class="row g-3 align-items-end">
          <div class="col-md-9">
            <label class="form-label small fw-semibold mb-2">
              <i class="ri-search-line me-1"></i>Pencarian
            </label>
            <input
              type="text"
              name="search"
              class="form-control"
              placeholder="Cari nama, No. ID, WhatsApp..."
              value="{{ request('search') }}">
          </div>

          <div class="col-md-3">
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary flex-grow-1">
                <i class="ri-search-line me-1"></i>Cari
              </button>
              @if(request()->hasAny(['search']))
                <a href="{{ route('tagihan.index') }}" class="btn btn-secondary">
                  <i class="ri-refresh-line me-1"></i>Reset
                </a>
              @endif
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- ========================================= -->
  <!-- DAFTAR TAGIHAN -->
  <!-- ========================================= -->
  <div class="card border-0 shadow-sm">
    <div class="card-header-custom">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
          <h4 class="mb-1 fw-bold">
            <i class="ri-file-list-3-line me-2"></i>Daftar Tagihan Outstanding
          </h4>
          <p class="mb-0 opacity-75 small">Kelola seluruh tagihan pelanggan secara efisien.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
          @if($tagihans->total() > 0)
            <span class="badge" style="padding: 10px 20px; font-size: 14px; background: rgba(24, 24, 27, 0.1); color: #18181b; border: 1px solid rgba(24, 24, 27, 0.2);">
              <i class="ri-database-2-line me-1"></i>
              {{ $tagihans->total() }} Tagihan
            </span>
          @endif

          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahTagihan">
            <span style="color: #fafafa; font-weight: bold;">+</span> Tambah Outstanding
          </button>
        </div>
      </div>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive p-3">
        <table class="datatables-users table table-hover nowrap" style="width: 100%;">
          <thead>
            <tr>
              <th><i class="ri-eye-line me-1"></i>Detail</th>
              <th><i class="ri-barcode-line me-1"></i>No. ID</th>
              <th><i class="ri-user-3-line me-1"></i>Nama</th>
              <th><i class="ri-whatsapp-line me-1"></i>No. WA</th>
              <th><i class="ri-shield-check-line me-1"></i>Status</th>
              <th><i class="ri-box-3-line me-1"></i>Paket</th>
              <th><i class="ri-money-dollar-circle-line me-1"></i>Harga</th>
              <th><i class="ri-settings-3-line me-1"></i>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($tagihans as $item)
            @php
              $status = strtolower($item['status_pembayaran'] ?? '');
              $badgeClass = match($status) {
                'lunas' => 'badge bg-success',
                'proses verifikasi' => 'badge bg-warning',
                'belum bayar' => 'badge bg-danger',
                default => 'badge bg-secondary',
              };

              $alamatParts = [];
              if($item['alamat_jalan']) $alamatParts[] = $item['alamat_jalan'];
              if($item['rt'] || $item['rw']) $alamatParts[] = 'RT '.$item['rt'].' / RW '.$item['rw'];
              if($item['desa']) $alamatParts[] = 'Desa '.$item['desa'];
              if($item['kecamatan']) $alamatParts[] = 'Kecamatan '.$item['kecamatan'];
              if($item['kabupaten']) $alamatParts[] = 'Kabupaten '.$item['kabupaten'];
              if($item['provinsi']) $alamatParts[] = $item['provinsi'];
              $alamatLengkap = implode(', ', $alamatParts);

              $buktiUrl = !empty($item['bukti_pembayaran']) ? asset('storage/kwitansi/' . $item['bukti_pembayaran']) : '';
            @endphp
            <tr
              data-alamat="{{ $alamatLengkap }}"
              data-kecamatan="{{ $item['kecamatan'] ?? '-' }}"
              data-kabupaten="{{ $item['kabupaten'] ?? '-' }}"
              data-provinsi="{{ $item['provinsi'] ?? '-' }}"
              data-kecepatan="{{ $item['paket']['kecepatan'] ?? '-' }} Mbps"
              data-tanggal-mulai="{{ $item['tanggal_mulai'] ? \Carbon\Carbon::parse($item['tanggal_mulai'])->format('d M Y') : '-' }}"
              data-jatuh-tempo="{{ $item['tanggal_berakhir'] ? \Carbon\Carbon::parse($item['tanggal_berakhir'])->format('d M Y') : '-' }}"
              data-catatan="{{ $item['catatan'] ?? '-' }}"
              data-bukti="{{ $buktiUrl }}"
            >
              <td>
                <button class="btn btn-sm btn-icon btn-outline-primary btn-detail" title="Lihat Detail">
                  <i class="ri-eye-line"></i>
                </button>
              </td>
              <td><span class="badge bg-label-dark">{{ $item['nomer_id'] }}</span></td>
              <td><strong>{{ $item['nama_lengkap'] }}</strong></td>
              <td>
                <a href="https://wa.me/{{ $item['no_whatsapp'] }}" target="_blank" class="text-decoration-none">
                  <code style="background: #18181b; padding: 6px 12px; border-radius: 6px; font-size: 0.875rem; font-weight: 600; color: #fafafa;">
                    <i class="ri-whatsapp-line me-1"></i>{{ $item['no_whatsapp'] }}
                  </code>
                </a>
              </td>
              <td>
                <span class="{{ $badgeClass }}">
                  <i class="ri-{{ $status == 'lunas' ? 'checkbox-circle' : 'close-circle' }}-line me-1"></i>
                  {{ ucfirst($status ?: '-') }}
                </span>
              </td>
              <td>
                <span class="badge bg-label-info">
                  <i class="ri-box-line me-1"></i>{{ $item['paket']['nama_paket'] ?? '-' }}
                </span>
              </td>
              <td><strong>Rp {{ number_format($item['paket']['harga'] ?? 0, 0, ',', '.') }}</strong></td>
              <td>
                <div class="d-flex gap-2">
                  <button type="button"
                    class="btn btn-sm btn-outline-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEditTagihan-{{ $item['id'] }}"
                    title="Edit">
                    <i class="ri-edit-2-line"></i>
                  </button>

                  <form action="{{ route('tagihan.destroy', $item['id']) }}" method="POST" class="delete-form d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                      <i class="ri-delete-bin-line"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
            @empty
            <tr class="empty-state-row">
              <td colspan="8" class="text-center">
                <div class="empty-state-content">
                  <div class="mb-3">
                    <i class="ri-inbox-line" style="font-size: 4rem; color: #ddd;"></i>
                  </div>

                  @if(request()->hasAny(['search']))
                    <h5 class="text-muted mb-2">
                      <i class="ri-search-eye-line me-2"></i>Data Tidak Ditemukan
                    </h5>
                    <p class="text-muted mb-3">
                      Tidak ada data yang sesuai dengan pencarian Anda.
                    </p>

                    <div class="mb-3">
                      @if(request('search'))
                        <span class="badge bg-label-primary me-2" style="padding: 8px 16px;">
                          <i class="ri-search-line me-1"></i>
                          Pencarian: "{{ request('search') }}"
                        </span>
                      @endif
                    </div>

                    <a href="{{ route('tagihan.index') }}" class="btn btn-primary mt-2">
                      <i class="ri-refresh-line me-1"></i>Reset & Tampilkan Semua Data
                    </a>
                  @else
                    <h5 class="text-muted mb-2">
                      <i class="ri-file-list-line me-2"></i>Belum Ada Data Tagihan
                    </h5>
                    <p class="text-muted">
                      Saat ini belum ada data tagihan yang terdaftar dalam sistem.
                    </p>
                  @endif
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    @if($tagihans->count() > 0)
      <div class="pagination-wrapper">
        <div class="pagination-info">
          Menampilkan <strong>{{ $tagihans->firstItem() ?? 0 }}</strong> - <strong>{{ $tagihans->lastItem() ?? 0 }}</strong>
          dari <strong>{{ $tagihans->total() }}</strong> tagihan
        </div>
        <div>
          @if($tagihans->hasPages())
            {{-- Jika ada lebih dari 1 halaman, tampilkan pagination Laravel --}}
            {{ $tagihans->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}
          @else
            {{-- Jika hanya 1 halaman, tampilkan tombol halaman 1 saja --}}
            <nav>
              <ul class="pagination mb-0">
                <li class="page-item disabled">
                  <span class="page-link">&lsaquo;</span>
                </li>
                <li class="page-item active">
                  <span class="page-link">1</span>
                </li>
                <li class="page-item disabled">
                  <span class="page-link">&rsaquo;</span>
                </li>
              </ul>
            </nav>
          @endif
        </div>
      </div>
    @endif
  </div>
</div>

{{-- MODAL DETAIL --}}
<div class="modal fade" id="detailModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary py-4">
        <h5 class="modal-title text-white fw-bold">
          <i class="ri-information-line me-2"></i>Detail Pelanggan
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1); opacity: 1;"></button>
      </div>
      <div class="modal-body">
        <!-- Content will be inserted via JavaScript -->
      </div>

    </div>
  </div>
</div>


<!-- ========================================= -->
<!-- MODAL: TAMBAH TAGIHAN -->
<!-- ========================================= -->
<div class="modal fade" id="modalTambahTagihan" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form action="{{ route('tagihan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="modal-header bg-primary">
          <h5 class="modal-title text-white fw-bold">
            <i class="ri-add-circle-line me-2"></i>Tambah Tagihan Baru
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <!-- Pilih Pelanggan -->
            <div class="col-12">
              <label class="form-label fw-semibold">Pilih Pelanggan <span class="text-danger">*</span></label>
              <select id="pelangganSelect" name="pelanggan_id" class="form-select select2" required>
                <option value=""></option>
                <!-- Options akan diload via AJAX Select2 -->
              </select>
            </div>

            <input type="hidden" name="paket_id" id="paket_id">

            <!-- Info Pelanggan -->
            <div class="col-12 mt-4">
              <h6 class="text-primary fw-bold mb-3">
                <i class="ri-user-3-line me-2"></i>Informasi Pelanggan
              </h6>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Nama Lengkap</label>
              <input type="text" id="nama_lengkap" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Nomor ID</label>
              <input type="text" id="nomer_id" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Nomor WhatsApp</label>
              <input type="text" id="no_whatsapp" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Kode Pos</label>
              <input type="text" id="kode_pos" class="form-control bg-light" readonly>
            </div>

            <!-- Alamat -->
            <div class="col-12 mt-4">
              <h6 class="text-primary fw-bold mb-3">
                <i class="ri-map-pin-line me-2"></i>Alamat Lengkap
              </h6>
            </div>

            <div class="col-12">
              <label class="form-label small text-muted">Alamat Jalan</label>
              <input type="text" id="alamat_jalan" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-3">
              <label class="form-label small text-muted">RT</label>
              <input type="text" id="rt" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-3">
              <label class="form-label small text-muted">RW</label>
              <input type="text" id="rw" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Desa/Kelurahan</label>
              <input type="text" id="desa" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-4">
              <label class="form-label small text-muted">Kecamatan</label>
              <input type="text" id="kecamatan" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-4">
              <label class="form-label small text-muted">Kabupaten/Kota</label>
              <input type="text" id="kabupaten" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-4">
              <label class="form-label small text-muted">Provinsi</label>
              <input type="text" id="provinsi" class="form-control bg-light" readonly>
            </div>

            <!-- Paket -->
            <div class="col-12 mt-4">
              <h6 class="text-primary fw-bold mb-3">
                <i class="ri-box-3-line me-2"></i>Informasi Paket
              </h6>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Nama Paket</label>
              <input type="text" id="paket" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Harga Paket</label>
              <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" id="harga" name="harga" class="form-control bg-light" readonly>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Kecepatan</label>
              <div class="input-group">
                <input type="text" id="kecepatan" class="form-control bg-light" readonly>
                <span class="input-group-text">Mbps</span>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Masa Pembayaran</label>
              <div class="input-group">
                <input type="text" id="masa_pembayaran" class="form-control bg-light" readonly>
                <span class="input-group-text">Hari</span>
              </div>
            </div>

            <!-- Tagihan -->
            <div class="col-12 mt-4">
              <h6 class="text-primary fw-bold mb-3">
                <i class="ri-calendar-check-line me-2"></i>Detail Tagihan
              </h6>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
              <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
              <input type="date" id="tanggal_berakhir" name="tanggal_berakhir" class="form-control bg-light" readonly required>
            </div>

            <div class="col-12">
              <label class="form-label">Catatan (Opsional)</label>
              <textarea class="form-control" id="catatan" name="catatan" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
            </div>

            <div class="col-12">
              <label class="form-label">Upload Bukti Pembayaran (Opsional)</label>
              <input type="file" name="bukti_pembayaran" class="form-control" accept="image/*,.pdf">
              <small class="text-muted">Format: JPG, PNG, PDF | Max: 2MB</small>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="ri-close-line me-1"></i>Batal
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="ri-save-line me-1"></i>Simpan Tagihan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ========================================= -->
<!-- MODAL: EDIT TAGIHAN (FOREACH) -->
<!-- ========================================= -->
@foreach($tagihans as $tagihan)
<div class="modal fade" id="modalEditTagihan-{{ $tagihan['id'] }}" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('tagihan.update', $tagihan['id']) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="modal-header bg-warning py-4">
          <h5 class="modal-title text-white fw-bold">
            <i class="ri-edit-2-line me-2"></i>Edit Tagihan
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1); opacity: 1;"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Nama Pelanggan</label>
              <input type="text" class="form-control bg-light" value="{{ $tagihan['nama_lengkap'] ?? '-' }}" readonly>
            </div>

            <input type="hidden" name="pelanggan_id" value="{{ $tagihan['pelanggan_id'] ?? '' }}">
            <input type="hidden" name="paket_id" value="{{ $tagihan['paket']['id'] ?? '' }}">

            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Mulai</label>
              <input type="text" name="tanggal_mulai" class="form-control flatpickr-edit-start" value="{{ $tagihan['tanggal_mulai'] }}" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Jatuh Tempo</label>
              <input type="text" name="tanggal_berakhir" class="form-control flatpickr-edit-end" value="{{ $tagihan['tanggal_berakhir'] }}" required>
            </div>

            <div class="col-12">
              <label class="form-label">Catatan</label>
              <textarea class="form-control" name="catatan" rows="2">{{ $tagihan['catatan'] ?? '' }}</textarea>
            </div>

            <div class="col-12">
              <label class="form-label">Bukti Pembayaran</label>
              <input type="file" name="bukti_pembayaran" class="form-control" accept="image/*,.pdf">
              <small class="text-muted">Format: JPG, PNG, PDF | Max: 2MB</small>
            </div>
          </div>
        </div>

        <div class="modal-footer py-4">
           <button type="submit" class="btn btn-warning">
            <i class="ri-save-line me-1"></i>Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

<!-- ========================================= -->
<!-- MODAL: MASS TAGIHAN -->
<!-- ========================================= -->
<div class="modal fade" id="modalMassTagihan" tabindex="-1">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('tagihan.massStore') }}" method="POST">
        @csrf

        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title fw-bold">
            <i class="ri-group-line me-2"></i>Buat Tagihan Massal
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="alert alert-info d-flex align-items-center">
            <i class="ri-information-line me-2" style="font-size: 1.5rem;"></i>
            <div>
              <strong>{{ count($pelanggan) }} pelanggan</strong> akan dibuatkan tagihan secara otomatis
            </div>
          </div>

          <div class="border rounded p-3 mb-3" style="max-height: 200px; overflow-y: auto; background: #f8f9fa;">
            @foreach($pelanggan as $p)
            <div class="py-2 border-bottom">
              <span class="badge bg-dark me-2">{{ $p->nomer_id }}</span>
              <strong>{{ $p->nama_lengkap }}</strong>
            </div>
            @endforeach
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Tanggal Mulai</label>
            <input type="text" name="tanggal_mulai" class="form-control flatpickr-select-start-all" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Tanggal Jatuh Tempo</label>
            <input type="text" name="tanggal_berakhir" class="form-control flatpickr-select-start-end" required>
          </div>

          <div class="alert alert-warning small mb-0">
            <i class="ri-error-warning-line me-1"></i>
            Semua pelanggan di atas akan otomatis dibuatkan tagihan baru
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning">
            <i class="ri-check-circle-line me-1"></i>Buat Semua Tagihan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

