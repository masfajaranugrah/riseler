@extends('layouts/layoutMaster')

@section('title', 'Data Karyawan')

@php
use Illuminate\Support\Str;
@endphp

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
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
  --gray-bg: #fafafa;
  --gray-border: #e4e4e7;
}

* {
  box-sizing: border-box;
}

body {
  background: #f5f5f9;
}

/* ========== CARD ========== */
.card {
  border: none;
  border-radius: var(--border-radius);
  box-shadow: var(--card-shadow);
  background: white;
  transition: var(--transition);
  overflow: hidden;
}

.card:hover {
  box-shadow: var(--card-hover-shadow);
}

/* ========== HEADER SECTION ========== */
.card-header-custom {
  background: #ffffff !important;
  border-bottom: 1px solid var(--gray-border);
  padding: 1.5rem;
  border-radius: var(--border-radius) var(--border-radius) 0 0;
}

.card-header-custom h4 {
  color: #18181b !important;
  font-size: 1.5rem;
}

.card-header-custom p {
  color: #71717a !important;
}

.card-header-custom i {
  color: #18181b !important;
}

/* ========== BUTTONS - ALL BLACK ========== */
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
}

.btn-primary i,
.btn.btn-primary i {
  color: #ffffff !important;
}

.btn-add {
  padding: 10px 24px !important;
  border-radius: 8px !important;
  font-weight: 600 !important;
  box-shadow: 0 4px 12px rgba(24, 24, 27, 0.25) !important;
  transition: all 0.3s ease !important;
}

.btn-add:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 6px 16px rgba(24, 24, 27, 0.35) !important;
}

.btn-add i {
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

.btn-danger,
.btn.btn-danger {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn-danger:hover,
.btn.btn-danger:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

/* Outline Buttons */
.btn-outline-primary,
.btn.btn-outline-primary {
  background: transparent !important;
  background-color: transparent !important;
  border: 1px solid #e4e4e7 !important;
  color: #18181b !important;
}

.btn-outline-primary:hover,
.btn.btn-outline-primary:hover {
  background: #18181b !important;
  background-color: #18181b !important;
  border-color: #18181b !important;
  color: #fafafa !important;
}

.btn-outline-danger,
.btn.btn-outline-danger {
  background: transparent !important;
  background-color: transparent !important;
  border: 1px solid #e4e4e7 !important;
  color: #18181b !important;
}

.btn-outline-danger:hover,
.btn.btn-outline-danger:hover {
  background: #18181b !important;
  background-color: #18181b !important;
  border-color: #18181b !important;
  color: #fafafa !important;
}

.btn-icon {
  width: 32px;
  height: 32px;
  padding: 0 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
}

/* ========== TABLE STYLES ========== */
.table-modern {
  margin-bottom: 0;
  border-radius: 8px;
  overflow: hidden;
  border-collapse: separate;
  border-spacing: 0;
}

.table-modern thead th {
  background: #f8fafc;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
  color: #18181b;
  padding: 1rem;
  border: none;
  white-space: nowrap;
}

.table-modern tbody tr {
  transition: var(--transition);
  border-bottom: 1px solid #e4e4e7;
}

.table-modern tbody tr:hover {
  background-color: #f4f4f5 !important;
}

.table-modern tbody td {
  padding: 1rem;
  vertical-align: middle;
  border-bottom: 1px solid #e4e4e7;
  color: #18181b;
}

.table-modern thead th:first-child,
.table-modern tbody td:first-child {
  text-align: center;
  width: 60px;
}

/* ========== BADGES - SHADCN STYLE ========== */
.badge {
  border-radius: 9999px !important;
  font-weight: 500 !important;
  letter-spacing: 0 !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 0.25rem !important;
  padding: 0.35rem 0.75rem !important;
}

.bg-label-dark {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
}

.bg-label-info {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
}

/* ========================================= */
/* PAGINATION STYLES */
/* ========================================= */
.pagination-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-top: 1px solid #f0f0f0;
  background: #fafafa;
  border-radius: 0 0 var(--border-radius) var(--border-radius);
}

.pagination-info {
  color: #71717a;
  font-size: 0.875rem;
  font-weight: 500;
}

/* Hide default Laravel pagination summary */
.pagination-wrapper nav .text-muted {
  display: none !important;
}

.pagination {
  margin: 0;
  gap: 0.5rem;
  justify-content: flex-end;
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
  margin: 0 4px;
  transition: all 0.3s ease;
}

.pagination .page-item .page-link:hover {
  background-color: #f4f4f5;
  border-color: #18181b;
  color: #18181b;
}

.pagination .page-item.active .page-link {
  background-color: #18181b !important;
  border-color: #18181b !important;
  color: #fafafa !important;
  box-shadow: none;
}

.pagination .page-item.disabled .page-link {
  background-color: #f4f4f5;
  border-color: #e4e4e7;
  color: #a1a1aa;
  cursor: not-allowed;
}

/* DataTables pagination styles */
.dataTables_wrapper .dataTables_info {
  float: left !important;
  padding-top: 1.25rem;
  padding-bottom: 1rem;
  color: #71717a;
  font-size: 0.875rem;
}

.dataTables_wrapper .dataTables_paginate {
  float: right !important;
  text-align: right !important;
  padding-top: 1rem;
  padding-bottom: 1rem;
}

.dataTables_wrapper .dataTables_paginate .pagination {
  justify-content: flex-end !important;
  margin: 0 !important;
}

.dataTables_wrapper .dataTables_paginate .page-item .page-link {
  border-radius: 50% !important;
  width: 40px !important;
  height: 40px !important;
  padding: 0 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  margin: 0 4px !important;
  border: 1px solid #e4e4e7 !important;
  color: #18181b !important;
  background: #fff !important;
  background-color: #fff !important;
  font-weight: 600 !important;
  transition: all 0.3s ease !important;
}

.dataTables_wrapper .dataTables_paginate .page-item .page-link:hover {
  background: #f4f4f5 !important;
  background-color: #f4f4f5 !important;
  border-color: #18181b !important;
  color: #18181b !important;
}

.dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
  background: #18181b !important;
  background-color: #18181b !important;
  border-color: #18181b !important;
  color: #fafafa !important;
}

.dataTables_wrapper .dataTables_paginate .page-item.disabled .page-link {
  background: #f4f4f5 !important;
  background-color: #f4f4f5 !important;
  border-color: #e4e4e7 !important;
  color: #a1a1aa !important;
  cursor: not-allowed !important;
}

.dataTables_wrapper::after {
  content: '';
  display: table;
  clear: both;
}

/* ========== LOADING OVERLAY ========== */
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(24, 24, 27, 0.5);
  backdrop-filter: blur(4px);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.spinner-border-custom {
  width: 3rem;
  height: 3rem;
  border-width: 0.3rem;
}

/* ========== MODAL STYLING ========== */
.modal-backdrop {
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  background-color: rgba(24, 24, 27, 0.4) !important;
}

.modal-backdrop.show {
  opacity: 1 !important;
}

.modal-content {
  border-radius: 12px;
  border: 1px solid #e4e4e7;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
  background: #ffffff;
  overflow: hidden;
}

.modal-header {
  background: #18181b !important;
  padding: 1.5rem;
  border-bottom: 1px solid #e4e4e7;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.modal-title {
  font-weight: 600;
  font-size: 1.125rem;
  color: #fafafa !important;
  margin: 0;
}

.modal-header .btn-close {
  padding: 0.5rem;
  margin: -0.5rem -0.5rem -0.5rem auto;
  filter: invert(1);
  opacity: 0.8;
  transition: opacity 0.15s ease;
}

.modal-header .btn-close:hover {
  opacity: 1;
}

.modal-body {
  padding: 1.5rem;
  padding-top: 2rem;
  max-height: 70vh;
  overflow-y: auto;
}

.modal-footer {
  padding: 1.25rem 1.5rem;
  border-top: 1px solid #e4e4e7;
  background: #fafafa;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  min-height: 4.5rem;
}

.employee-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: #18181b !important;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  font-size: 2.5rem;
  margin-bottom: 1rem;
  box-shadow: 0 4px 16px rgba(24, 24, 27, 0.4);
  border: 4px solid white;
}

.detail-section {
  background: white;
  border: 1px solid #e4e4e7;
  border-radius: 10px;
  padding: 1.25rem;
  margin-bottom: 1.25rem;
  transition: all 0.2s ease;
}

.detail-section:hover {
  border-color: #18181b;
  box-shadow: 0 2px 8px rgba(24, 24, 27, 0.1);
}

.detail-section h6 {
  color: #18181b !important;
  font-weight: 700;
  margin-bottom: 1rem;
  font-size: 0.85rem;
  text-transform: uppercase;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid #18181b;
  display: flex;
  align-items: center;
}

.detail-section h6 i {
  margin-right: 0.5rem;
  font-size: 1.1rem;
  color: #18181b !important;
}

.detail-item {
  display: flex;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f0f0f0;
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
  color: #18181b !important;
  font-size: 1rem;
}

.detail-value {
  color: #18181b;
  font-size: 0.875rem;
  flex: 1;
  word-break: break-word;
}

.employee-header-info {
  text-align: center;
  padding: 1.5rem;
  background: #fafafa;
  border-radius: 10px;
  margin-bottom: 1.5rem;
  border: 1px solid #e4e4e7;
}

.employee-name {
  font-size: 1.5rem;
  font-weight: 700;
  color: #18181b;
  margin-bottom: 0.5rem;
}

.employee-position {
  display: inline-block;
  padding: 0.5rem 1.5rem;
  background: #18181b !important;
  color: white;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.875rem;
  box-shadow: 0 2px 8px rgba(24, 24, 27, 0.3);
}

/* ========== TEXT COLORS ========== */
.text-primary {
  color: #18181b !important;
}

.text-muted {
  color: #71717a !important;
}

/* ========== RESPONSIVE ========== */
@media (max-width: 768px) {
  .card-header-custom {
    padding: 1rem 1.25rem;
  }

  .btn-add {
    width: 100%;
  }

  .detail-label {
    min-width: 120px;
    font-size: 0.8rem;
  }

  .detail-value {
    font-size: 0.8rem;
  }
}

@media (max-width: 576px) {
  .table-modern {
    font-size: 0.85rem;
  }

  .table-modern thead th,
  .table-modern tbody td {
    padding: 0.75rem 0.5rem;
  }
}

/* ========== ANIMATIONS ========== */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.card {
  animation: fadeIn 0.3s ease-out;
}
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Helper function untuk loading overlay
    function showLoading() {
        $('.loading-overlay').css('display', 'flex');
    }

    function hideLoading() {
        $('.loading-overlay').fadeOut(300);
    }

    // Inisialisasi DataTable
    const dtUserTable = $('.datatables-users').DataTable({
        paging: false,
        searching: false, // Disable DataTables search
        ordering: true,
        info: false,
        responsive: false,
        dom: 'rt',
        columnDefs: [
            { orderable: false, targets: [0, 1, -1] }
        ],
        language: {
            zeroRecords: "Tidak ada data yang sesuai"
        }
    });

    // Event Detail Karyawan - via icon mata
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const btn = $(this);
        const tr = btn.closest('tr');

        // Get data from tr attributes - paksa ke String agar .charAt() tidak error
        const nik          = String(tr.data('nik')          ?? '-') || '-';
        const fullName     = String(tr.data('nama')         ?? '-') || '-';
        const address      = String(tr.data('alamat')       ?? '-') || '-';
        const birthPlace   = String(tr.data('tempat-lahir') ?? '-') || '-';
        const birthDate    = String(tr.data('tanggal-lahir')?? '-') || '-';
        const phone        = String(tr.data('hp')           ?? '-') || '-';
        const joinDate     = String(tr.data('tanggal-masuk')?? '-') || '-';
        const position     = String(tr.data('jabatan')      ?? '-') || '-';
        const bank         = String(tr.data('bank')         ?? '-') || '-';
        const accountNumber= String(tr.data('no-rekening')  ?? '-') || '-';
        const accountName  = String(tr.data('atas-nama')    ?? '-') || '-';
        const hasFoto      = tr.data('has-foto') == 1;
        const hasFotoKtp   = tr.data('has-foto-ktp') == 1;
        const employeeId   = tr.data('id');
        const initial      = fullName.charAt(0).toUpperCase() || '?';

        const photoUrl = hasFoto ? `/dashboard/admin/employees/image/${employeeId}/foto` : '';
        const ktpUrl = hasFotoKtp ? `/dashboard/admin/employees/image/${employeeId}/foto_ktp` : '';

        const html = `
            <div class="employee-header-info">
                <div class="employee-avatar mx-auto overflow-hidden" style="width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #e4e4e7; font-size: 2rem; margin-bottom: 1rem;">
                    ${hasFoto ? `<img src="${photoUrl}" alt="Foto" style="width:100%; height:100%; object-fit:cover;">` : initial}
                </div>
                <div class="employee-name">${fullName}</div>
                <div class="employee-position">
                    <i class="ri-briefcase-line me-2"></i>${position}
                </div>
            </div>

            <div class="row">
                <div class="col-md-7">
                    <div class="detail-section">
                        <h6><i class="ri-user-3-line"></i>Informasi Pribadi</h6>
                        <div class="detail-item">
                            <span class="detail-label">
                                <i class="ri-id-card-line"></i>NIK
                            </span>
                            <span class="detail-value"><strong>${nik}</strong></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">
                                <i class="ri-user-line"></i>Nama Lengkap
                            </span>
                            <span class="detail-value">${fullName}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">
                                <i class="ri-map-pin-2-line"></i>Tempat Lahir
                            </span>
                            <span class="detail-value">${birthPlace}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">
                                <i class="ri-calendar-line"></i>Tanggal Lahir
                            </span>
                            <span class="detail-value">${birthDate}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="detail-section">
                        <h6><i class="ri-file-list-3-line"></i>Dokumen (Enkripsi)</h6>
                        <div class="mb-3 text-center">
                            <p class="small text-muted mb-1">Foto Karyawan</p>
                            ${hasFoto ? 
                                `<img src="${photoUrl}" class="img-fluid rounded border" style="max-height: 120px; cursor: pointer;" onclick="window.open('${photoUrl}', '_blank')">` : 
                                `<div class="bg-light rounded border py-3 small text-muted">Tidak ada foto</div>`}
                        </div>
                        <div class="text-center">
                            <p class="small text-muted mb-1">Foto KTP</p>
                            ${hasFotoKtp ? 
                                `<img src="${ktpUrl}" class="img-fluid rounded border" style="max-height: 120px; cursor: pointer;" onclick="window.open('${ktpUrl}', '_blank')">` : 
                                `<div class="bg-light rounded border py-3 small text-muted">Tidak ada KTP</div>`}
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-phone-line"></i>Kontak & Alamat</h6>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-smartphone-line"></i>Nomor HP
                    </span>
                    <span class="detail-value">
                        <a href="tel:${phone}" class="text-primary text-decoration-none">
                            <strong>${phone}</strong>
                        </a>
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-map-pin-line"></i>Alamat Lengkap
                    </span>
                    <span class="detail-value">${address}</span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-briefcase-4-line"></i>Informasi Kepegawaian</h6>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-calendar-check-line"></i>Tanggal Masuk
                    </span>
                    <span class="detail-value">${joinDate}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-award-line"></i>Jabatan
                    </span>
                    <span class="detail-value">
                        <span class="badge bg-label-info">${position}</span>
                    </span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-bank-card-2-line"></i>Informasi Rekening Bank</h6>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-bank-line"></i>Nama Bank
                    </span>
                    <span class="detail-value"><strong>${bank}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-bank-card-line"></i>Nomor Rekening
                    </span>
                    <span class="detail-value"><code style="background: #f4f4f5; padding: 4px 8px; border-radius: 4px; font-size: 0.875rem; color: #18181b;">${accountNumber}</code></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-user-3-line"></i>Atas Nama
                    </span>
                    <span class="detail-value">${accountName}</span>
                </div>
            </div>
        `;

        $('#detailModal .modal-body').html(html);

        // Gunakan Bootstrap 5 native modal API
        const detailModalEl = document.getElementById('detailModal');
        let detailModal = bootstrap.Modal.getInstance(detailModalEl);
        if (!detailModal) {
            detailModal = new bootstrap.Modal(detailModalEl, {
                backdrop: true,
                keyboard: true,
                focus: true
            });
        }
        detailModal.show();
    });

    // Event DELETE dengan konfirmasi modern - HANYA 2 BUTTON
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Konfirmasi Penghapusan',
            text: 'Yakin ingin menghapus data karyawan ini? Data tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            showDenyButton: false,
            showCloseButton: false,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#18181b',
            cancelButtonColor: '#71717a',
            reverseButtons: false,
            allowOutsideClick: false,
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                const btn = $(form).find('.btn-delete');
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menghapus...');
                showLoading();

                setTimeout(() => {
                    hideLoading();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data karyawan berhasil dihapus.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        form.submit();
                    });
                }, 1000);
            }
        });
    });
});
</script>
@endsection

@section('content')
<!-- Loading Overlay -->
<div class="loading-overlay">
    <div class="spinner-border spinner-border-custom text-light" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<!-- Karyawan List Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header-custom">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h4 class="mb-1 fw-bold">
                    <i class="ri-team-line me-2"></i>Data Karyawan
                </h4>
                <p class="mb-0 opacity-75 small">Kelola dan monitor data karyawan perusahaan</p>
            </div>
            <div class="d-flex action-buttons mt-3 mt-md-0">
                <a href="{{ route('karyawan.create') }}" class="btn btn-primary btn-add">
                    <i class="ri-user-add-line"></i>
                    Tambah Karyawan Baru
                </a>
            </div>
        </div>

        <!-- Search Form -->
        <div class="mt-4">
            <form action="{{ route('karyawan.index') }}" method="GET" class="d-flex gap-2">
                <div class="input-group" style="max-width: 400px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="ri-search-line text-muted"></i>
                    </span>
                    <input 
                        type="text" 
                        name="search" 
                        class="form-control border-start-0 ps-0" 
                        placeholder="Cari NIK, Nama, HP, atau Jabatan..." 
                        value="{{ request('search') }}"
                        autocomplete="off"
                    >
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-search-line me-1"></i> Cari
                </button>
                @if(request('search'))
                <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary">
                    <i class="ri-close-line me-1"></i> Reset
                </a>
                @endif
            </form>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="card-datatable table-responsive p-3">
            <table class="datatables-users table table-modern table-hover">
                <thead>
                    <tr>
                        <th><i class="ri-hashtag me-1"></i>No</th>
                        <th><i class="ri-eye-line me-1"></i>Detail</th>
                        <th><i class="ri-id-card-line me-1"></i>NIK</th>
                        <th><i class="ri-user-3-line me-1"></i>Nama Lengkap</th>
                        <th><i class="ri-map-pin-line me-1"></i>Alamat</th>
                        <th><i class="ri-calendar-line me-1"></i>Tempat & Tanggal Lahir</th>
                        <th><i class="ri-phone-line me-1"></i>No. HP</th>
                        <th><i class="ri-calendar-check-line me-1"></i>Tanggal Masuk</th>
                        <th><i class="ri-briefcase-line me-1"></i>Jabatan</th>
                        <th class="text-center"><i class="ri-settings-3-line me-1"></i>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                    <tr
                        data-nik="{{ $employee->nik }}"
                        data-nama="{{ $employee->full_name }}"
                        data-alamat="{{ $employee->full_address }}"
                        data-tempat-lahir="{{ $employee->place_of_birth }}"
                        data-tanggal-lahir="{{ \Carbon\Carbon::parse($employee->date_of_birth)->format('d M Y') }}"
                        data-hp="{{ $employee->no_hp }}"
                        data-tanggal-masuk="{{ \Carbon\Carbon::parse($employee->tanggal_masuk)->format('d M Y') }}"
                        data-jabatan="{{ $employee->jabatan }}"
                        data-bank="{{ $employee->bank }}"
                        data-no-rekening="{{ $employee->no_rekening }}"
                        data-atas-nama="{{ $employee->atas_nama }}"
                        data-id="{{ $employee->id }}"
                        data-has-foto="{{ $employee->foto ? 1 : 0 }}"
                        data-has-foto-ktp="{{ $employee->foto_ktp ? 1 : 0 }}"
                    >
                        <td class="text-muted fw-semibold">{{ $loop->iteration }}</td>
                        <td>
                            <button class="btn btn-sm btn-icon btn-outline-primary btn-detail" title="Lihat Detail">
                                <i class="ri-eye-line"></i>
                            </button>
                        </td>

                        <td>
                            <span class="badge bg-label-dark">{{ $employee->nik }}</span>
                        </td>

                        <td>
                            <span class="fw-semibold">{{ $employee->full_name }}</span>
                        </td>

                        <td>{{ Str::limit($employee->full_address, 30) }}</td>

                        <td>
                            {{ $employee->place_of_birth }},
                            <br>
                            {{ \Carbon\Carbon::parse($employee->date_of_birth)->format('d M Y') }}
                        </td>

                        <td>{{ $employee->no_hp }}</td>

                        <td>{{ \Carbon\Carbon::parse($employee->tanggal_masuk)->format('d M Y') }}</td>

                        <td>
                            <span class="badge bg-label-info">{{ $employee->jabatan }}</span>
                        </td>

                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('employees.edit', $employee->id) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   title="Edit">
                                    <i class="ri-edit-2-line"></i>
                                </a>

                                <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete" title="Hapus">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Menampilkan <strong>{{ $employees->firstItem() ?? 0 }}</strong> - <strong>{{ $employees->lastItem() ?? 0 }}</strong>
                dari <strong>{{ $employees->total() }}</strong> karyawan
            </div>
            <div>
                {{ $employees->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header pb-4">
                <h5 class="modal-title">
                    <i class="ri-information-line me-2"></i>Detail Karyawan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <!-- Custom content will be inserted via JavaScript -->
            </div>

         
        </div>
    </div>
</div>
@endsection
