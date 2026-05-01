@extends('layouts/layoutMaster')

@section('title', 'Tagihan - Apps')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
])
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/plugins/monthSelect/style.css">
<style>
/* ========================================= */
/* SHADCN UI STYLE - MODERN CLEAN 2025 */
/* ========================================= */
:root {
  --card-shadow: 0 2px 8px rgba(0,0,0,0.08);
  --card-hover-shadow: 0 4px 16px rgba(0,0,0,0.12);
  --border-radius: 12px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  --primary-color: #18181b;
}

.card {
  border: none;
  border-radius: var(--border-radius);
  box-shadow: var(--card-shadow);
  transition: var(--transition);
  overflow: hidden;
  background: #ffffff;
}

.card:hover {
  box-shadow: var(--card-hover-shadow);
}

.card-header-custom {
  color: #18181b;
  border-radius: 12px 12px 0 0 !important;
  padding: 1.5rem;
  border-bottom: 2px solid #e4e4e7;
  background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
}

/* ========================================= */
/* SHADCN UI STYLE BUTTONS - ALL BLACK */
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

/* Primary Button - Black */
.btn.btn-primary,
.btn-primary {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn.btn-primary:hover,
.btn-primary:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

.btn.btn-primary:focus,
.btn-primary:focus {
  outline: none !important;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b !important;
}

/* Success Button - Black */
.btn.btn-success,
.btn-success {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn.btn-success:hover,
.btn-success:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

/* Warning Button - Black */
.btn.btn-warning,
.btn-warning {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn.btn-warning:hover,
.btn-warning:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

/* Secondary Button - Black */
.btn.btn-secondary,
.btn-secondary {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn.btn-secondary:hover,
.btn-secondary:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

/* Outline Buttons */
.btn.btn-outline-primary,
.btn.btn-outline-secondary,
.btn.btn-outline-danger,
.btn-outline-primary,
.btn-outline-secondary,
.btn-outline-danger {
  background: transparent !important;
  background-color: transparent !important;
  border: 1px solid #e4e4e7 !important;
  color: #18181b !important;
}

.btn.btn-outline-primary:hover,
.btn.btn-outline-secondary:hover,
.btn.btn-outline-danger:hover,
.btn-outline-primary:hover,
.btn-outline-secondary:hover,
.btn-outline-danger:hover {
  background: #f4f4f5 !important;
  background-color: #f4f4f5 !important;
  border-color: #a1a1aa !important;
  color: #18181b !important;
}

.btn-sm {
  padding: 0.375rem 0.75rem !important;
  font-size: 0.8125rem !important;
}

/* ========================================= */
/* SHADCN UI STYLE BADGES */
/* ========================================= */
.badge {
  padding: 0.25rem 0.625rem;
  border-radius: 9999px;
  font-weight: 500;
  font-size: 0.75rem;
  letter-spacing: 0;
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
}

/* Status Lunas - Black */
.badge.bg-success {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
  border-radius: 9999px !important;
}

/* Status Belum Bayar - Red */
.badge.bg-warning,
.badge.bg-danger {
  background: #dc2626 !important;
  color: #fafafa !important;
  border: none !important;
  border-radius: 9999px !important;
}

/* Neutralize accent labels - shadcn style */
.bg-label-primary,
.bg-label-success,
.bg-label-warning,
.bg-label-dark {
  background: #f4f4f5 !important;
  color: #18181b !important;
  border: 1px solid #e4e4e7 !important;
}

/* Badge Paket - Black background */
.bg-label-info {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
  border-radius: 9999px !important;
}

/* Clean Table Design */
.table-modern {
  border-radius: 8px;
  overflow: hidden;
}

.table-modern thead th {
  background: #f8fafc;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
  color: #18181b;
  border: none;
  padding: 1rem;
}

.table-modern tbody tr {
  transition: all 0.2s;
  border-bottom: 1px solid #e4e4e7;
  cursor: pointer;
}

.table-modern tbody tr:hover {
  background-color: #f4f4f5 !important;
  transform: scale(1.001);
}

.table-modern tbody td {
  padding: 1rem;
  vertical-align: middle;
  font-size: 0.875rem;
  color: #18181b;
}

.btn-icon-detail {
  width: 32px;
  height: 32px;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  background: transparent !important;
  color: #18181b !important;
  border: 1px solid #e4e4e7 !important;
  transition: all 0.15s;
}

.btn-icon-detail:hover {
  background: #f4f4f5 !important;
  border-color: #a1a1aa !important;
}

/* Form Controls */
.form-select,
.form-control {
  border-radius: 8px;
  border: 1px solid #e4e4e7;
  padding: 0.625rem 1rem;
  transition: var(--transition);
  font-size: 0.875rem;
}

.form-select:focus,
.form-control:focus {
  border-color: #18181b !important;
  box-shadow: none !important;
  outline: none !important;
}

.form-control[readonly] {
  background-color: #f4f4f5;
}

/* Flatpickr Calendar */
.flatpickr-calendar {
  border: 1px solid #e4e4e7;
  border-radius: 14px;
  box-shadow: 0 18px 38px rgba(15, 23, 42, 0.16);
  padding: 10px 12px 12px;
  width: 336px;
  max-width: calc(100vw - 24px);
  font-family: inherit;
  background: #ffffff;
  z-index: 1065;
}

.flatpickr-calendar.arrowTop:before,
.flatpickr-calendar.arrowTop:after {
  display: none;
}

.flatpickr-months {
  align-items: center;
  margin-bottom: 6px;
}

.flatpickr-month {
  height: 50px;
}

.flatpickr-current-month {
  padding-top: 10px;
  font-size: 1.05rem;
  color: #0f172a;
}

.flatpickr-current-month .flatpickr-monthDropdown-months,
.flatpickr-current-month input.cur-year {
  font-weight: 700;
  color: #0f172a;
}

.flatpickr-weekdays {
  margin-bottom: 2px;
}

.flatpickr-rContainer,
.flatpickr-weekdays,
.flatpickr-days {
  width: 100% !important;
  min-width: 100% !important;
  max-width: 100% !important;
}

span.flatpickr-weekday {
  color: #374151;
  font-weight: 600;
  font-size: 0.94rem;
}

.flatpickr-days,
.dayContainer {
  width: 100%;
  min-width: 100%;
  max-width: 100%;
}

.flatpickr-day {
  width: calc(100% / 7);
  min-width: calc(100% / 7);
  max-width: calc(100% / 7);
  aspect-ratio: 1 / 1;
  height: auto;
  line-height: 1;
  margin: 0;
  border-radius: 999px;
  color: #374151;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.flatpickr-day.selected,
.flatpickr-day.startRange,
.flatpickr-day.endRange {
  background: #111827;
  border-color: #111827;
  color: #ffffff;
}

.flatpickr-day.today {
  border-color: #9ca3af;
}

.flatpickr-day.prevMonthDay,
.flatpickr-day.nextMonthDay {
  color: #9ca3af;
}

@media (max-width: 576px) {
  .flatpickr-calendar {
    width: min(340px, calc(100vw - 16px));
    padding: 8px 10px 10px;
  }

  .flatpickr-day {
    aspect-ratio: 1 / 1;
  }
}

/* ========================================= */
/* SHADCN UI STYLE MODAL */
/* ========================================= */
.modal-content {
  border-radius: 12px;
  border: 1px solid #e4e4e7;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
  background: #ffffff;
  overflow: hidden;
}

.modal-header {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #e4e4e7;
  background: #18181b !important;
}

.modal-header.bg-primary,
.modal-header.bg-warning {
  background: #18181b !important;
  border-bottom: none;
}

.modal-header.bg-primary .modal-title,
.modal-header.bg-warning .modal-title {
  color: #fafafa;
}

.modal-title {
  font-weight: 600;
  font-size: 1.125rem;
  color: #fafafa;
}

.modal-body {
  padding: 1.5rem;
  padding-top: 2rem;
  max-height: 65vh;
  overflow-y: auto;
}

.modal-footer {
  padding: 1rem 1.5rem;
  padding-top: 1rem;
  padding-bottom: 1rem;
  border-top: 1px solid #e4e4e7;
  background: #fafafa;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 0.75rem;
}

/* Modal backdrop with blur effect */
.modal-backdrop.show {
  opacity: 1;
  background-color: rgba(24, 24, 27, 0.4);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}

/* Detail Section in Modal */
.detail-section {
  background: #ffffff;
  border: 1px solid #e4e4e7;
  border-radius: 8px;
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
  padding: 0.75rem 0;
  border-bottom: 1px solid #f0f0f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
}

.detail-item:last-child {
  border-bottom: none;
}

.detail-label {
  color: #5a5f7d;
  font-weight: 600;
  font-size: 0.875rem;
  flex-shrink: 0;
  min-width: 140px;
}

.detail-value {
  color: #2c3e50;
  font-size: 0.875rem;
  text-align: right;
  word-break: break-word;
  flex: 1;
}

/* Loading Overlay */
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.btn-close-white,
.modal-header .btn-close {
  filter: brightness(0) invert(1) !important;
  opacity: 0.8 !important;
}

.btn-close-white:hover,
.modal-header .btn-close:hover {
  opacity: 1 !important;
}

/* Image Preview */
.table img {
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  transition: var(--transition);
  cursor: pointer;
}

.table img:hover {
  transform: scale(1.5);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  z-index: 999;
}

/* Responsive */
@media (max-width: 768px) {
  .modal-body {
    padding: 1.5rem;
  }

  .card-header-custom {
    padding: 1.25rem;
  }

  .detail-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }

  .detail-label {
    min-width: auto;
  }

  .detail-value {
    text-align: left;
  }
}

/* Scrollbar */
.modal-body::-webkit-scrollbar {
  width: 6px;
}

.modal-body::-webkit-scrollbar-track {
  background: #e5e7eb;
  border-radius: 10px;
}

.modal-body::-webkit-scrollbar-thumb {
  background: #18181b;
  border-radius: 10px;
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

.pagination-modern {
  display: flex;
  align-items: center;
  gap: 0.45rem;
}

.pagination-pages {
  display: flex;
  align-items: center;
  gap: 0.45rem;
}

.page-dot-btn {
  min-width: 44px;
  height: 44px;
  border: none;
  border-radius: 999px;
  background: #f3f4f6;
  color: #111827;
  font-weight: 600;
  font-size: 1.1rem;
  line-height: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  transition: all 0.2s ease;
}

.page-dot-btn:hover:not(.disabled):not(.active) {
  background: #e5e7eb;
  color: #111827;
}

.page-dot-btn.active {
  background: #0f111a;
  color: #ffffff;
  box-shadow: 0 6px 14px rgba(15, 17, 26, 0.2);
}

.page-dot-btn.disabled {
  opacity: 0.55;
  pointer-events: none;
}

.page-dot-btn.nav-btn {
  font-size: 1.3rem;
}

.page-ellipsis {
  min-width: 44px;
  height: 44px;
  border-radius: 999px;
  background: #f3f4f6;
  color: #6b7280;
  font-weight: 700;
  font-size: 1rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

@media (max-width: 576px) {
  .pagination-wrapper {
    flex-direction: column;
    gap: 1rem;
    align-items: center;
    text-align: center;
  }

  .pagination-modern {
    transform: scale(0.82);
    transform-origin: center;
  }
}

/* Hide DataTables Default Elements */
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate,
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
  display: none !important;
}

/* Hide Bootstrap Pagination Info Text (Showing X to Y of Z results) */
.pagination-wrapper > div:last-child > nav > div:first-child,
.pagination-wrapper nav > div.hidden,
.pagination-wrapper nav > div:first-child:not(:last-child),
.pagination-wrapper small.text-muted,
.pagination-wrapper .text-sm,
[role="navigation"] > div:first-child:not([aria-label]) {
  display: none !important;
}

/* More specific: hide "Showing X of Y results" text in pagination wrapper */
.pagination-wrapper > div > nav > div.flex.justify-between > div:first-child,
.pagination-wrapper > div > nav > div > p,
.pagination-wrapper > div > nav > div > span.relative,
nav[role="navigation"] > div:first-child,
nav[role="navigation"] > div > p,
nav[role="navigation"] .hidden,
nav[role="navigation"] > div.flex-1,
.pagination-wrapper p.text-sm,
.pagination-wrapper .leading-5,
p:has(span.font-medium),
/* Extra aggressive selectors */
.pagination-wrapper nav > div.flex,
.pagination-wrapper nav > div.sm\:flex-1,
.pagination-wrapper nav > div.justify-between,
.pagination-wrapper nav > div:not(.flex):not(:has(.pagination)):not(:has(.page-item)),
nav.d-flex > div:first-child:not(:has(.pagination)),
nav.d-flex > div.d-none,
.pagination-wrapper > div:last-child > nav > div.d-none,
.pagination-wrapper > div:last-child > nav > div.d-sm-flex > div:first-child {
  display: none !important;
}
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
])
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/plugins/monthSelect/index.js"></script>
@endsection

@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function () {
    // ========================================
    // BULAN FILTER (MONTH SELECT)
    // ========================================
    flatpickr('#filterBulanPicker', {
        plugins: [new monthSelectPlugin({
            shorthand: true,
            dateFormat: "Y-m",
            altFormat: "F Y",
            theme: "light"
        })],
        locale: "id",
        disableMobile: true,
        defaultDate: "{{ request('filter_bulan') }}"
    });

    // ========================================
    // HELPER FUNCTIONS
    // ========================================
    function showLoading() {
        $('.loading-overlay').css('display', 'flex');
    }

    function hideLoading() {
        $('.loading-overlay').fadeOut(300);
    }

    // ========================================
    // CUSTOM MODAL DETAIL IMPLEMENTATION
    // ========================================

    /**
     * Build modal content HTML dari data tagihan
     */
    function buildModalContent(data) {
        // Build bukti pembayaran section
        let buktiSection = '<span class="text-muted">Belum ada bukti</span>';
        if (data.bukti && data.bukti !== '') {
            buktiSection = `
                <button type="button" class="btn btn-sm btn-outline-primary btn-view-bukti" data-bukti="${data.bukti}">
                    <i class="ri-image-line me-1"></i>Lihat Bukti
                </button>
            `;
        }

        // Build kwitansi section
        let kwitansiSection = '<span class="text-muted">Belum ada kwitansi</span>';
        if (data.kwitansi && data.kwitansi !== '') {
            kwitansiSection = `
                <a href="${data.kwitansi}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="ri-file-pdf-line me-1"></i>Download PDF
                </a>
            `;
        }

        return `
            <div class="detail-section">
                <h6><i class="ri-user-3-line me-2"></i>Informasi Pelanggan</h6>
                <div class="detail-item">
                    <span class="detail-label">No. ID</span>
                    <span class="detail-value"><strong>${data.nomorId}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Nama Lengkap</span>
                    <span class="detail-value">${data.nama}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">No. WhatsApp</span>
                    <span class="detail-value">
                        <a href="https://wa.me/${data.whatsapp}" target="_blank" class="text-success text-decoration-none">
                            <i class="ri-whatsapp-line me-1"></i><strong>${data.whatsapp}</strong>
                        </a>
                    </span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-map-pin-line me-2"></i>Alamat Lengkap</h6>
                <div class="detail-item">
                    <span class="detail-label">Alamat</span>
                    <span class="detail-value">${data.alamat}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Kecamatan</span>
                    <span class="detail-value">${data.kecamatan}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Kabupaten</span>
                    <span class="detail-value">${data.kabupaten}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Provinsi</span>
                    <span class="detail-value">${data.provinsi}</span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-box-3-line me-2"></i>Informasi Paket</h6>
                <div class="detail-item">
                    <span class="detail-label">Nama Paket</span>
                    <span class="detail-value"><span class="badge bg-label-info">${data.paket}</span></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Harga Paket</span>
                    <span class="detail-value"><strong class="text-primary">${data.harga}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Kecepatan</span>
                    <span class="detail-value"><span class="badge bg-label-success">${data.kecepatan}</span></span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-calendar-check-line me-2"></i>Informasi Tagihan</h6>
                <div class="detail-item">
                    <span class="detail-label">Status Pembayaran</span>
                    <span class="detail-value">
                        <span class="badge ${data.status === 'lunas' ? 'bg-success' : 'bg-warning'}">
                            ${data.status.toUpperCase()}
                        </span>
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Tanggal Mulai</span>
                    <span class="detail-value">${data.tanggalMulai}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Jatuh Tempo</span>
                    <span class="detail-value"><strong class="text-danger">${data.jatuhTempo}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Bukti Pembayaran</span>
                    <span class="detail-value">${buktiSection}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Kwitansi</span>
                    <span class="detail-value">${kwitansiSection}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Catatan</span>
                    <span class="detail-value"><em>${data.catatan}</em></span>
                </div>
            </div>
        `;
    }

    /**
     * Build modal footer buttons
     */
    function buildModalFooter(data) {
      const deleteButton = `
        <button type="button" class="btn btn-outline-danger btn-delete-modal" 
          data-tagihan-id="${data.id}" 
          data-nama="${data.nama}">
          <i class="ri-delete-bin-line me-1"></i>Hapus Tagihan Lunas
        </button>
      `;

      return `
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i>Tutup
        </button>
        ${deleteButton}
      `;
    }

    /**
     * Event handler untuk button detail di tabel
     */
    $(document).on('click', '.btn-icon-detail', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const tr = $(this).closest('tr');

        // Extract data dari tr attributes
        const tagihanData = {
            id: tr.data('tagihan-id'),
            status: tr.data('status'),
            nomorId: tr.data('nomor-id'),
            nama: tr.data('nama'),
            whatsapp: tr.data('whatsapp'),
            alamat: tr.data('alamat'),
            kecamatan: tr.data('kecamatan'),
            kabupaten: tr.data('kabupaten'),
            provinsi: tr.data('provinsi'),
            paket: tr.data('paket'),
            harga: tr.data('harga'),
            kecepatan: tr.data('kecepatan'),
            tanggalMulai: tr.data('tanggal-mulai'),
            jatuhTempo: tr.data('jatuh-tempo'),
            bukti: tr.data('bukti'),
            kwitansi: tr.data('kwitansi'),
            catatan: tr.data('catatan') || '-'
        };

        // Build content dan footer modal
        const modalContent = buildModalContent(tagihanData);
        const modalFooter = buildModalFooter(tagihanData);

        // Populate modal custom
        $('#detailModal .modal-body').html(modalContent);
        $('#detailModal .modal-footer').html(modalFooter);

        // Simpan data untuk digunakan handler lain
        $('#detailModal').data('tagihan-data', tagihanData);

        // Show modal menggunakan Bootstrap 5 API
        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
        detailModal.show();
    });

    // ========================================
    // BUKTI PEMBAYARAN MODAL HANDLER
    // ========================================
    $(document).on('click', '.btn-view-bukti', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const buktiUrl = $(this).data('bukti');

        // Set image source
        $('#buktiImage').attr('src', buktiUrl);
        $('#buktiDownloadLink').attr('href', buktiUrl);

        // Hide detail modal first
        const detailModalEl = document.getElementById('detailModal');
        const detailModal = bootstrap.Modal.getInstance(detailModalEl);
        if (detailModal) {
            detailModal.hide();
        }

        // Show bukti modal after detail modal is hidden
        setTimeout(() => {
            const buktiModal = new bootstrap.Modal(document.getElementById('buktiModal'));
            buktiModal.show();
        }, 300);
    });

    // When bukti modal is closed, reopen detail modal
    $('#buktiModal').on('hidden.bs.modal', function () {
        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
        detailModal.show();
    });

    // ========================================
    // MODAL FOOTER BUTTON HANDLERS
    // ========================================

    // Hapus handler edit button (tidak ada tombol edit di lunas)

    // Handler tombol hapus jika ingin pakai SweetAlert (opsional, default pakai form submit)
    // $(document).on('click', '.btn-delete-from-detail', function(e) {
    //     e.preventDefault();
    //     const tagihanId = $(this).data('tagihan-id');
    //     const nama = $(this).data('nama');
    //     Swal.fire({
    //         title: 'Konfirmasi Penghapusan',
    //         html: `Yakin ingin menghapus tagihan <strong>${nama}</strong>?<br><small class="text-danger">Data tidak dapat dikembalikan!</small>`,
    //         icon: 'warning',
    //         showCancelButton: true,
    //         confirmButtonText: '<i class="ri-delete-bin-line me-1"></i>Ya, Hapus!',
    //         cancelButtonText: '<i class="ri-close-line me-1"></i>Batal',
    //         confirmButtonColor: '#ff3e1d',
    //         cancelButtonColor: '#8898aa',
    //         customClass: {
    //             confirmButton: 'btn btn-danger me-2',
    //             cancelButton: 'btn btn-secondary'
    //         },
    //         buttonsStyling: false
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             showLoading();
    //             const form = $('<form>', {
    //                 'method': 'POST',
    //                 'action': `/dashboard/admin/tagihan/tagihan-lunas/${tagihanId}`
    //             });
    //             form.append($('<input>', {
    //                 'type': 'hidden',
    //                 'name': '_token',
    //                 'value': $('meta[name="csrf-token"]').attr('content')
    //             }));
    //             form.append($('<input>', {
    //                 'type': 'hidden',
    //                 'name': '_method',
    //                 'value': 'DELETE'
    //             }));
    //             $('body').append(form);
    //             form.submit();
    //         }
    //     });
    // });

    /**
     * Konfirmasi lunas button handler
     */
    $(document).on('click', '.btn-konfirmasi-from-detail', function(e) {
        e.preventDefault();
        const tagihanId = $(this).data('tagihan-id');
        const nama = $(this).data('nama');

        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            html: `Apakah tagihan <strong>${nama}</strong> sudah lunas?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="ri-check-circle-line me-1"></i>Ya, Sudah Lunas!',
            cancelButtonText: '<i class="ri-close-line me-1"></i>Batal',
            confirmButtonColor: '#71dd37',
            cancelButtonColor: '#8898aa',
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();

                $.ajax({
                    url: `/dashboard/admin/tagihan/${tagihanId}/bayar`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function(response) {
                        hideLoading();
                        if(response.success) {
                            $('#detailModal').modal('hide');

                            Swal.fire({
                                icon: 'success',
                                title: 'Pembayaran Berhasil!',
                                html: `
                                    <p class="mb-3">Tagihan <strong>${nama}</strong> telah ditandai lunas.</p>
                                    ${response.pdfUrl ? `
                                        <a href="${response.pdfUrl}" target="_blank" class="btn btn-primary">
                                            <i class="ri-printer-line me-1"></i>Cetak Kwitansi
                                        </a>
                                    ` : ''}
                                `,
                                showConfirmButton: true,
                                confirmButtonText: 'OK',
                                allowOutsideClick: false,
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message || 'Terjadi kesalahan.',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn btn-danger'
                                },
                                buttonsStyling: false
                            });
                        }
                    },
                    error: function(xhr) {
                        hideLoading();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Server!',
                            text: 'Terjadi kesalahan pada server.',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-danger'
                            },
                            buttonsStyling: false
                        });
                    }
                });
            }
        });
    });

    // ========================================
    // DELETE TAGIHAN LUNAS HANDLER
    // ========================================
    $(document).on('click', '.btn-delete-tagihan', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const tagihanId = $(this).data('tagihan-id');
        const nama = $(this).data('nama');
        
        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `Yakin ingin menghapus tagihan <strong>${nama}</strong>?<br><small class="text-danger">Data tagihan dan kwitansi akan dihapus permanen!</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="ri-delete-bin-line me-1"></i>Ya, Hapus!',
            cancelButtonText: '<i class="ri-close-line me-1"></i>Batal',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#71717a',
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false,
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                
                // Submit delete form
                const form = $('<form>', {
                    'method': 'POST',
                    'action': `/dashboard/admin/tagihan/tagihan-lunas/${tagihanId}`
                });
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': $('meta[name="csrf-token"]').attr('content')
                }));
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_method',
                    'value': 'DELETE'
                }));
                
                $('body').append(form);
                form.submit();
            }
        });
    });

    // Handler untuk tombol hapus di modal (SweetAlert)
    $(document).on('click', '.btn-delete-modal', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const tagihanId = $(this).data('tagihan-id');
        const nama = $(this).data('nama');
        
        // Tutup modal detail dulu
        const detailModalEl = document.getElementById('detailModal');
        const detailModal = bootstrap.Modal.getInstance(detailModalEl);
        if (detailModal) {
            detailModal.hide();
        }
        
        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `Yakin ingin menghapus tagihan <strong>${nama}</strong>?<br><small class="text-danger">Data tagihan dan kwitansi akan dihapus permanen!</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="ri-delete-bin-line me-1"></i>Ya, Hapus!',
            cancelButtonText: '<i class="ri-close-line me-1"></i>Tidak',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#71717a',
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false,
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                
                const form = $('<form>', {
                    'method': 'POST',
                    'action': `/dashboard/admin/tagihan/tagihan-lunas/${tagihanId}`
                });
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': $('meta[name="csrf-token"]').attr('content')
                }));
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_method',
                    'value': 'DELETE'
                }));
                
                $('body').append(form);
                form.submit();
            }
        });
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
  <!-- Main Table Card -->
  <div class="card border-0 shadow-sm">
 <div class="card-header-custom">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="ri-bill-line me-2"></i>Daftar Tagihan Lunas
            </h4>
            <p class="mb-0 opacity-75 small">Kelola seluruh tagihan pelanggan yang sudah lunas</p>
        </div>

        <!-- Button Actions -->
        <div class="d-flex flex-wrap align-items-center gap-2">
          <!-- Total Customer Lunas Badge -->
          @if(($tagihans->total() ?? 0) > 0)
          <span class="badge" style="padding: 10px 20px; font-size: 0.9rem; background: rgba(24, 24, 27, 0.1); color: #18181b; border: 1px solid rgba(24, 24, 27, 0.2);">
            <i class="ri-group-line me-1"></i>
            {{ number_format($tagihans->total()) }} Tagihan Lunas
          </span>
          @endif

          <!-- Button Export Excel -->
          <a href="{{ route('tagihan.bayar.export', ['filter_bulan' => request('filter_bulan')]) }}"
             class="btn btn-success"
             title="Export ke Excel">
              <i class="ri-file-excel-2-line me-1"></i>Export Excel
          </a>
        </div>
    </div>
</div>




    <div class="card-body p-0">
      <!-- Filter Section (Moved Here) -->
      <div class="px-4 py-3 border-bottom">
        <form method="GET" action="{{ route('tagihan.lunas') }}">
          <div class="d-flex align-items-center flex-wrap gap-3">
              <!-- Search -->
              <div style="min-width: 280px;">
                  <input
                    type="search"
                    name="search"
                    class="form-control"
                    style="border-color: #e4e4e7;"
                    placeholder="Cari nama, nomer_id, WhatsApp, paket..."
                    value="{{ request('search') }}">
              </div>

              <div style="min-width: 220px;">
                  <input
                    type="text"
                    id="filterBulanPicker"
                    name="filter_bulan"
                    class="form-control"
                    style="border-color: #e4e4e7;"
                    placeholder="Pilih bulan pembayaran"
                    value="{{ request('filter_bulan') }}"
                    autocomplete="off"
                    readonly>
              </div>

              <!-- Action Buttons -->
              <div class="d-flex gap-2">
                  <button class="btn btn-primary" type="submit" style="height: 38px;">
                      <i class="ri-search-line me-1"></i>Cari
                  </button>
                  
                  @if(request('search') || request('filter_bulan'))
              <a class="btn btn-outline-secondary" href="{{ route('tagihan.lunas') }}" style="height: 38px;">
                <i class="ri-refresh-line me-1"></i>Reset
              </a>
            @endif      
              </div>
          </div>
        </form>
      </div>

      <div class="table-responsive p-3">
        <table class="table table-modern table-hover align-middle mb-0">
          <thead>
            <tr>
              <th>No</th>
              <th>Detail</th>
              <th>No. ID</th>
              <th>Nama</th>
              <th>WhatsApp</th>
              <th>Type Pembayaran</th>
              <th>Status</th>
              <th>Paket</th>
              <th>Harga</th>
              <th>Kecepatan</th>
              <th>Kwitansi</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($tagihans as $item)
            <tr
              data-tagihan-id="{{ $item->id }}"
              data-status="{{ strtolower($item->status_pembayaran ?? '') }}"
              data-nomor-id="{{ $item->pelanggan->nomer_id ?? '-' }}"
              data-nama="{{ $item->pelanggan->nama_lengkap ?? '-' }}"
              data-whatsapp="{{ $item->pelanggan->no_whatsapp ?? '-' }}"
              data-alamat="{{ collect([$item->pelanggan->alamat_jalan ?? '', ($item->pelanggan->rt || $item->pelanggan->rw) ? 'RT '.($item->pelanggan->rt ?? '').' / RW '.($item->pelanggan->rw ?? '') : null, $item->pelanggan->desa ? 'Desa '.$item->pelanggan->desa : null])->filter()->implode(', ') }}"
              data-kecamatan="{{ $item->pelanggan->kecamatan ?? '-' }}"
              data-kabupaten="{{ $item->pelanggan->kabupaten ?? '-' }}"
              data-provinsi="{{ $item->pelanggan->provinsi ?? '-' }}"
              data-paket="{{ $item->paket->nama_paket ?? '-' }}"
              data-harga="Rp {{ number_format($item->paket->harga ?? 0, 0, ',', '.') }}"
              data-kecepatan="{{ $item->paket->kecepatan ?? '-' }} Mbps"
              data-tanggal-mulai="{{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') : '-' }}"
              data-jatuh-tempo="{{ $item->tanggal_berakhir ? \Carbon\Carbon::parse($item->tanggal_berakhir)->format('d M Y') : '-' }}"
              data-bukti="{{ !empty($item->bukti_pembayaran) ? asset('storage/' . $item->bukti_pembayaran) : '' }}"
              data-kwitansi="{{ !empty($item->kwitansi) ? asset('storage/'. $item->kwitansi) : '' }}"
              data-catatan="{{ $item->catatan ?? '-' }}"
            >
              <td class="text-muted fw-semibold" style="width: 60px;">{{ ($tagihans->firstItem() ?? 1) + $loop->index }}</td>
              <td>
                <button class="btn btn-sm btn-icon btn-outline-primary btn-icon-detail" title="Lihat Detail">
                  <i class="ri-eye-line"></i>
                </button>
              </td>
              <td><span class="badge bg-label-dark">{{ $item->pelanggan->nomer_id ?? '-' }}</span></td>
              <td><strong>{{ $item->pelanggan->nama_lengkap ?? '-' }}</strong></td>
              <td>{{ $item->pelanggan->no_whatsapp ?? '-' }}</td>
              <td>{{ $item->rekening->nama_bank ?? '-' }}</td>
              <td>
                @php
                  $status = strtolower($item->status_pembayaran ?? '');
                  $badgeClass = match($status) {
                    'lunas' => 'badge bg-success',
                    'belum bayar' => 'badge bg-warning',
                    default => 'badge bg-secondary',
                  };
                @endphp
                <span class="{{ $badgeClass }}">{{ ucfirst($status ?: '-') }}</span>
              </td>
              <td><span class="badge bg-label-info">{{ $item->paket->nama_paket ?? '-' }}</span></td>
              <td><strong>Rp {{ number_format($item->paket->harga ?? 0, 0, ',', '.') }}</strong></td>
              <td>{{ $item->paket->kecepatan ?? '-' }} Mbps</td>
              <td>
                @if(!empty($item->kwitansi))
                  <a href="{{ asset('storage/' . $item->kwitansi) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Download Kwitansi">
                    <i class="ri-file-pdf-line"></i>
                  </a>
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
              <td>
                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-tagihan" 
                  data-tagihan-id="{{ $item->id }}" 
                  data-nama="{{ $item->pelanggan->nama_lengkap ?? '-' }}" 
                  title="Hapus Tagihan">
                  <i class="ri-delete-bin-line"></i>
                </button>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      @if($tagihans->hasPages())
      <div class="pagination-wrapper">
        <div class="pagination-info">
          Menampilkan <strong>{{ $tagihans->firstItem() ?? 0 }}</strong> - <strong>{{ $tagihans->lastItem() ?? 0 }}</strong>
          dari <strong>{{ $tagihans->total() }}</strong> tagihan
        </div>
        <div class="pagination-modern">
          @php
            $current = $tagihans->currentPage();
            $last = $tagihans->lastPage();
            $visiblePages = [];

            if ($last <= 10) {
                $visiblePages = range(1, $last);
            } elseif ($current <= 5) {
                $visiblePages = [1,2,3,4,5,6,7,8,'ellipsis',$last-1,$last];
            } elseif ($current >= $last - 4) {
                $visiblePages = [1,2,'ellipsis',$last-7,$last-6,$last-5,$last-4,$last-3,$last-2,$last-1,$last];
            } else {
                $visiblePages = [1,2,'ellipsis',$current-1,$current,$current+1,'ellipsis',$last-1,$last];
            }

            $visiblePages = array_values(array_filter($visiblePages, function($item) use ($last) {
                return $item === 'ellipsis' || (is_int($item) && $item >= 1 && $item <= $last);
            }));
          @endphp

          <a href="{{ $tagihans->onFirstPage() ? '#' : $tagihans->appends(request()->query())->previousPageUrl() }}"
             class="page-dot-btn nav-btn {{ $tagihans->onFirstPage() ? 'disabled' : '' }}"
             aria-label="Halaman sebelumnya">
            <i class="ri-arrow-left-s-line"></i>
          </a>

          <div class="pagination-pages">
            @php $prevWasEllipsis = false; @endphp
            @foreach($visiblePages as $page)
              @if($page === 'ellipsis')
                @if(!$prevWasEllipsis)
                  <span class="page-ellipsis">...</span>
                @endif
                @php $prevWasEllipsis = true; @endphp
              @else
                <a href="{{ $tagihans->appends(request()->query())->url($page) }}"
                   class="page-dot-btn {{ $page === $current ? 'active' : '' }}"
                   aria-label="Halaman {{ $page }}">
                  {{ $page }}
                </a>
                @php $prevWasEllipsis = false; @endphp
              @endif
            @endforeach
          </div>

          <a href="{{ $tagihans->hasMorePages() ? $tagihans->appends(request()->query())->nextPageUrl() : '#' }}"
             class="page-dot-btn nav-btn {{ !$tagihans->hasMorePages() ? 'disabled' : '' }}"
             aria-label="Halaman selanjutnya">
            <i class="ri-arrow-right-s-line"></i>
          </a>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>

<!-- MODAL DETAIL CUSTOM - 100% MILIK ANDA -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header py-4">
        <h5 class="modal-title text-white" id="detailModalLabel">
          <i class="ri-information-line me-2"></i>Detail Tagihan Pelanggan
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Content akan di-populate oleh JavaScript -->
        <div class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      </div>
      <div class="modal-footer py-4">
        <!-- Footer buttons akan di-populate oleh JavaScript -->
      </div>
    </div>
  </div>
</div>

<!-- MODAL BUKTI PEMBAYARAN -->
<div class="modal fade" id="buktiModal" tabindex="-1" aria-labelledby="buktiModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header py-4">
        <h5 class="modal-title text-white" id="buktiModalLabel">
          <i class="ri-image-line me-2"></i>Bukti Pembayaran
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center p-4">
        <img id="buktiImage" src="" alt="Bukti Pembayaran" class="img-fluid rounded" style="max-height: 70vh; object-fit: contain;">
      </div>
      <div class="modal-footer py-4">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="ri-arrow-left-line me-1"></i>Kembali
        </button>
        <a id="buktiDownloadLink" href="" target="_blank" class="btn btn-primary">
          <i class="ri-download-line me-1"></i>Download
        </a>
      </div>
    </div>
  </div>
</div>


@endsection
