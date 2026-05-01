@extends('layouts/layoutMaster')

@section('title', 'Proses Verifikasi Tagihan')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
])
<style>
/* ========================================= */
/* MODERN CLEAN STYLES - BLACK & WHITE THEME */
/* ========================================= */

/* SELECT2 OVERRIDES - BLACK & WHITE */
.select2-container--default .select2-results__option--highlighted[aria-selected] {
  background-color: #18181b !important; /* Black background for selected item */
  color: #ffffff !important;
}
.select2-container--default .select2-selection--single {
  border-color: #e2e8f0 !important;
}
.select2-container--default .select2-selection--single:focus,
.select2-container--open .select2-selection--single {
  border-color: #18181b !important; /* Black border on focus */
}
.select2-dropdown {
  border-color: #e2e8f0 !important;
}

/* FLATPICKR OVERRIDES - BLACK & WHITE */
.flatpickr-day.selected, 
.flatpickr-day.startRange, 
.flatpickr-day.endRange, 
.flatpickr-day.selected.inRange, 
.flatpickr-day.startRange.inRange, 
.flatpickr-day.endRange.inRange, 
.flatpickr-day.selected:focus, 
.flatpickr-day.startRange:focus, 
.flatpickr-day.endRange:focus, 
.flatpickr-day.selected:hover, 
.flatpickr-day.startRange:hover, 
.flatpickr-day.endRange:hover, 
.flatpickr-day.selected.prevMonthDay, 
.flatpickr-day.startRange.prevMonthDay, 
.flatpickr-day.endRange.prevMonthDay, 
.flatpickr-day.selected.nextMonthDay, 
.flatpickr-day.startRange.nextMonthDay, 
.flatpickr-day.endRange.nextMonthDay {
  background: #18181b !important;
  border-color: #18181b !important;
  color: #fff !important;
}

/* INPUT FOCUS OVERRIDES */
.form-control:focus, .form-select:focus {
  border-color: #18181b !important;
  box-shadow: 0 0 0 0.25rem rgba(24, 24, 27, 0.1) !important;
}

:root {
  --card-shadow: 0 2px 8px rgba(0,0,0,0.08);
  --card-hover-shadow: 0 4px 16px rgba(0,0,0,0.12);
  --border-radius: 12px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  --primary-color: #111827;
  --success-color: #28c76f;
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
  transform: translateY(-2px);
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
  background: linear-gradient(180deg, #111827 0%, #0b1220 100%);
}

.card-border-shadow-success::before {
  background: linear-gradient(180deg, #d1d5db 0%, #9ca3af 100%);
}

.card-border-shadow-warning::before {
  background: linear-gradient(180deg, #e5e7eb 0%, #d1d5db 100%);
}

.card-border-shadow-info::before {
  background: linear-gradient(180deg, #cbd5e1 0%, #94a3b8 100%);
}

/* Stats Card */
.stats-card {
  border-radius: var(--border-radius);
  padding: 1.5rem;
  background: #ffffff;
  color: #0f172a;
  border: 1px solid #e5e7eb;
  transition: var(--transition);
}

.stats-card p,
.stats-card h2,
.stats-card .text-muted {
  color: #0f172a !important;
}

.stats-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
}

.stats-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  background: #f3f4f6;
  color: #111827;
}

/* Avatar */
.avatar-initial {
  border-radius: 12px;
  transition: var(--transition);
}

.card:hover .avatar-initial {
  transform: scale(1.05);
}

/* ========================================= */
/* SHADCN UI STYLE BUTTONS - ALL BLACK */
/* Override Bootstrap default button colors */
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
.btn-primary:focus,
.btn.btn-primary:focus-visible,
.btn-primary:focus-visible {
  outline: none !important;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b !important;
  background: #18181b !important;
  color: #fafafa !important;
}

.btn.btn-primary:active,
.btn-primary:active {
  background: #09090b !important;
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

.btn.btn-warning:focus,
.btn-warning:focus,
.btn.btn-warning:focus-visible,
.btn-warning:focus-visible {
  outline: none !important;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b !important;
  background: #18181b !important;
  color: #fafafa !important;
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

.btn.btn-success:focus,
.btn-success:focus,
.btn.btn-success:focus-visible,
.btn-success:focus-visible {
  outline: none !important;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b !important;
  background: #18181b !important;
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

.btn.btn-secondary:focus,
.btn-secondary:focus,
.btn.btn-secondary:focus-visible,
.btn-secondary:focus-visible {
  outline: none !important;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b !important;
  background: #18181b !important;
  color: #fafafa !important;
}

/* Danger Button - Black */
.btn.btn-danger,
.btn-danger {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn.btn-danger:hover,
.btn-danger:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

.btn.btn-danger:focus,
.btn-danger:focus,
.btn.btn-danger:focus-visible,
.btn-danger:focus-visible {
  outline: none !important;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b !important;
  background: #18181b !important;
  color: #fafafa !important;
}

/* Outline Buttons - Light background, black text */
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

.btn.btn-outline-primary:focus,
.btn.btn-outline-secondary:focus,
.btn.btn-outline-danger:focus,
.btn-outline-primary:focus-visible,
.btn-outline-secondary:focus-visible,
.btn-outline-danger:focus-visible {
  outline: none !important;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b !important;
}

/* Small Button */
.btn.btn-sm,
.btn-sm {
  padding: 0.375rem 0.75rem !important;
  font-size: 0.8125rem !important;
}

/* Icon Button */
.btn-icon {
  width: 2rem;
  height: 2rem;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

/* ========================================= */
/* SHADCN UI STYLE BADGES & TEXT */
/* ========================================= */

/* Badges */
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

/* Neutralize accent labels and badges - shadcn style */
.bg-label-primary,
.bg-label-success,
.bg-label-warning,
.bg-label-dark {
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

.stats-icon.bg-label-primary,
.stats-icon.bg-label-success,
.stats-icon.bg-label-warning,
.stats-icon.bg-label-info {
  background: #f4f4f5 !important;
  color: #18181b !important;
}

/* Status Lunas - Black */
.badge.bg-success {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
  border-radius: 9999px !important;
}

/* Status Proses Verifikasi - Warning Yellow */
.badge.bg-warning {
  background: #f59e0b !important;
  color: #ffffff !important;
  border: none !important;
  border-radius: 9999px !important;
}

/* Status Belum Bayar - Red with white text, rounded */
.badge.bg-danger {
  background: #dc2626 !important;
  color: #fafafa !important;
  border: none !important;
  border-radius: 9999px !important;
}

/* Solid badges default */
.bg-info,
.bg-primary,
.bg-dark {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
}

/* All text colors - Black (shadcn style) */
.text-success,
.text-info,
.text-warning,
.text-primary,
.text-danger,
.text-muted {
  color: #71717a !important;
}
 

/* Card Header */
.card-header {
  background: transparent;
  padding: 1.5rem;
  border-bottom: 1px solid #f0f0f0;
}

.card-header-custom {
  background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
  border-radius: var(--border-radius) var(--border-radius) 0 0;
  padding: 1.5rem;
  border-bottom: 1px solid #f0f0f0;
}

/* Search Form */
.search-wrapper {
  background: #f8f9fa;
  padding: 1.25rem;
  border-radius: 10px;
  margin-bottom: 1rem;
}

/* Input Groups */
.input-group-text {
  border-radius: 8px 0 0 8px;
  background: #f8f9fa;
  border: 1px solid #e0e0e0;
  color: #5a5f7d;
  font-weight: 500;
}

.input-group .form-control {
  border-left: none;
  border-color: #e0e0e0;
}

.input-group .form-control:focus {
  border-color: var(--primary-color);
  box-shadow: none;
}

.input-group:focus-within .input-group-text {
  border-color: var(--primary-color);
}

.input-group:focus-within .form-control {
  border-color: var(--primary-color);
}

/* Form Controls */
.form-select, .form-control {
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  padding: 0.625rem 1rem;
  transition: var(--transition);
}

.form-select:focus, .form-control:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(17, 24, 39, 0.12);
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
  color: #0f172a;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  white-space: nowrap;
}

.table tbody tr {
  transition: var(--transition);
}

.table tbody tr:not(.empty-state-row):hover {
  background: #f1f5f9;
  transform: scale(1.001);
}

.table tbody td {
  padding: 1rem;
  border-bottom: 1px solid #e5e7eb;
  vertical-align: middle;
}

.table thead th:first-child,
.table tbody td:first-child {
  text-align: center;
}

/* Empty State */
.empty-state-row td {
  background: #fafbfc !important;
  border: none !important;
}

.empty-state-content {
  padding: 3rem 1rem;
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
  padding: 1.25rem 1.5rem 1.75rem 1.5rem;
  border-bottom: 1px solid #e4e4e7;
  background: #fafafa;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.modal-header .modal-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #18181b;
  margin: 0;
}

.modal-header.bg-light {
  background: #18181b !important;
  border-bottom: none;
}

.modal-header.bg-light .modal-title {
  color: #fafafa;
}

.modal-header.bg-warning {
  background: #18181b !important;
  border-bottom: none;
}

.modal-header.bg-warning .modal-title {
  color: #fafafa;
}

.modal-header .btn-close {
  padding: 0.5rem;
  margin: -0.5rem -0.5rem -0.5rem auto;
  opacity: 0.5;
  transition: opacity 0.15s ease;
}

.modal-header .btn-close:hover {
  opacity: 1;
}

.modal-body {
  padding: 1.5rem;
  padding-top: 2rem;
  max-height: 65vh;
  overflow-y: auto;
}

.modal-footer {
  padding: 1rem 1.5rem;
  margin-top: 0.5rem;
  border-top: 1px solid #e4e4e7;
  background: #fafafa;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 0.75rem;
}

.btn-close-white {
  filter: brightness(0) invert(1);
}

/* Modal Dialog Centered with proper spacing */
.modal-dialog-centered {
  min-height: calc(100% - 3.5rem);
  margin: 1.75rem auto;
}

/* Modal backdrop with blur effect */
.modal-backdrop.show {
  opacity: 1;
  background-color: rgba(24, 24, 27, 0.4);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
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

/* ========================================= */
/* DETAIL MODAL STYLES */
/* ========================================= */
.customer-header-info {
  text-align: center;
  padding: 1.5rem;
  background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
  border-radius: 12px;
  margin-bottom: 1.5rem;
  border: 1px solid #e8e8e8;
}

.customer-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: linear-gradient(135deg, #111827 0%, #0b1220 100%);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  font-size: 2.5rem;
  margin-bottom: 1rem;
  box-shadow: 0 4px 16px rgba(17, 24, 39, 0.4);
  border: 4px solid white;
}

.customer-name {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2c3e50;
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
  border-radius: 8px;
  padding: 1.25rem;
  margin-bottom: 1.25rem;
  transition: all 0.2s;
}

.detail-section:first-child,
.modal-body > .detail-section:first-of-type {
  margin-top: 0.5rem;
}

.detail-section:hover {
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  border-color: #111827;
}

.detail-section h6 {
  color: #111827;
  font-weight: 700;
  margin-bottom: 1.25rem;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  display: flex;
  align-items: center;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid #111827;
}

.detail-section h6 i {
  margin-right: 0.5rem;
  font-size: 1.1rem;
}

.detail-item {
  display: flex;
  padding: 0.875rem 0;
  border-bottom: 1px solid #f0f0f0;
  align-items: flex-start;
}

.detail-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.detail-label {
  color: #5a5f7d;
  font-weight: 600;
  min-width: 150px;
  font-size: 0.875rem;
  display: flex;
  align-items: center;
}

.detail-label i {
  margin-right: 0.5rem;
  color: #a8afc7;
  font-size: 1rem;
}

.detail-value {
  color: #2c3e50;
  font-size: 0.875rem;
  flex: 1;
  word-break: break-word;
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
p:has(span.font-medium) {
  display: none !important;
}

/* Animations */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.card {
  animation: fadeIn 0.3s ease-out;
}

/* Image Hover */
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

@media (max-width: 768px) {
  .modal-body {
    padding: 1.5rem;
  }
  .card-header {
    padding: 1.25rem;
  }
  .detail-label {
    min-width: 120px;
  }
}
</style>
@endsection

@section('vendor-script')
@vite([
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

    // ========================================
    // DETAIL MODAL - MODERN UI
    // ========================================
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const tr = $(this).closest('tr');
        const $tr = $(tr);
        
        // Get visible data from table
        const nomorId = $tr.find('td').eq(1).text().trim() || '-';
        const namaLengkap = $tr.find('td').eq(2).text().trim() || '-';
        const noWhatsapp = $tr.find('td').eq(3).text().trim() || '-';
        const statusPembayaran = $tr.find('td').eq(4).text().trim() || '-';
        
        // Get hidden data from data attributes
        const alamatLengkap = $tr.data('alamat') || '-';
        const kecamatan = $tr.data('kecamatan') || '-';
        const kabupaten = $tr.data('kabupaten') || '-';
        const provinsi = $tr.data('provinsi') || '-';
        const paket = $tr.data('paket') || '-';
        const harga = $tr.data('harga') || '-';
        const kecepatan = $tr.data('kecepatan') || '-';
        const tanggalMulai = $tr.data('tanggal-mulai') || '-';
        const jatuhTempo = $tr.data('jatuh-tempo') || '-';
        const catatan = $tr.data('catatan') || '-';
        const buktiPembayaran = $tr.data('bukti') || '';
        
        // Get tagihan ID and status for button
        const tagihanId = $tr.data('tagihan-id');
        const nama = namaLengkap;
        const status = $tr.find('.btn-konfirmasi').length > 0 ? 'belum_bayar' : 'lunas';
        
        // Build bukti section
        let buktiSection = '<span class="text-muted">-</span>';
        if (buktiPembayaran) {
            buktiSection = `<button type="button" class="btn btn-sm btn-outline-primary btn-lihat-bukti" data-bukti="${buktiPembayaran}">
                <i class="ri-image-line me-1"></i>Lihat Bukti
            </button>`;
        }
        
        const initial = namaLengkap ? namaLengkap.charAt(0).toUpperCase() : '?';
        const statusLower = statusPembayaran.toLowerCase();
        const statusClass = statusLower.includes('lunas') ? 'bg-success' : statusLower.includes('proses') ? 'bg-warning' : 'bg-secondary';
        
        const html = `
            <div class="customer-header-info">
                <div class="customer-avatar mx-auto">${initial}</div>
                <div class="customer-name">${namaLengkap}</div>
                <div class="customer-status ${statusClass}">
                    <i class="ri-checkbox-circle-line me-2"></i>${statusPembayaran}
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-user-3-line"></i>Informasi Pelanggan</h6>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-id-card-line"></i>No. ID</span>
                    <span class="detail-value"><strong>${nomorId}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-user-line"></i>Nama Lengkap</span>
                    <span class="detail-value">${namaLengkap}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-whatsapp-line"></i>No. WhatsApp</span>
                    <span class="detail-value">
                        <a href="https://wa.me/${noWhatsapp}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; background: #18181b; color: #fafafa; padding: 6px 12px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-decoration: none;">
                            <i class="ri-whatsapp-line"></i>${noWhatsapp}
                        </a>
                    </span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-map-pin-line"></i>Alamat Lengkap</h6>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-map-2-line"></i>Alamat</span>
                    <span class="detail-value">${alamatLengkap}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-building-line"></i>Kecamatan</span>
                    <span class="detail-value">${kecamatan}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-community-line"></i>Kabupaten</span>
                    <span class="detail-value">${kabupaten}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-map-pin-2-line"></i>Provinsi</span>
                    <span class="detail-value">${provinsi}</span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-box-3-line"></i>Informasi Paket</h6>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-box-line"></i>Nama Paket</span>
                    <span class="detail-value"><span class="badge bg-label-info">${paket}</span></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-money-dollar-circle-line"></i>Harga</span>
                    <span class="detail-value"><strong>${harga}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-speed-line"></i>Kecepatan</span>
                    <span class="detail-value">${kecepatan}</span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-calendar-check-line"></i>Informasi Tagihan</h6>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-calendar-line"></i>Tanggal Mulai</span>
                    <span class="detail-value">${tanggalMulai}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-calendar-event-line"></i>Jatuh Tempo</span>
                    <span class="detail-value"><strong class="text-danger">${jatuhTempo}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-file-list-line"></i>Bukti Pembayaran</span>
                    <span class="detail-value">${buktiSection}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-file-text-line"></i>Catatan</span>
                    <span class="detail-value">${catatan}</span>
                </div>
            </div>
        `;
        
        $('#detailModal .modal-body').html(html);
        $('#detailModal').data('tagihan-id', tagihanId);
        $('#detailModal').data('tagihan-nama', nama);
        $('#detailModal').data('tagihan-status', status);
        
        const btnKonfirmasi = $('#btnKonfirmasiDetail');
        if (status === 'lunas') {
            btnKonfirmasi.prop('disabled', true).removeClass('btn-success').addClass('btn-secondary').html('<i class="ri-check-circle-line me-1"></i> Sudah Lunas');
        } else {
            btnKonfirmasi.prop('disabled', false).removeClass('btn-secondary').addClass('btn-success').html('<i class="ri-check-circle-line me-1"></i> Konfirmasi Lunas');
        }
        
        $('#detailModal').modal('show');
    });

    // ========================================
    // KONFIRMASI PEMBAYARAN DARI MODAL DETAIL
    // ========================================
    $(document).on('click', '#btnKonfirmasiDetail', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const tagihanId = $('#detailModal').data('tagihan-id');
        const nama = $('#detailModal').data('tagihan-nama');
        
        if (!tagihanId) {
            Swal.fire('Error!', 'Data tagihan tidak ditemukan.', 'error');
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            html: `<p class="mb-0">Apakah <strong>${nama}</strong> sudah melakukan pembayaran?</p>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2dce89',
            cancelButtonColor: '#8898aa',
            confirmButtonText: '<i class="ri-check-line me-1"></i>Ya, Lunas',
            cancelButtonText: 'Batal',
            reverseButtons: true,
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
                                title: 'Pembayaran Dikonfirmasi!',
                                html: `
                                    <p>Tagihan <strong>${nama}</strong> telah ditandai lunas.</p>
                                    <a href="${response.pdfUrl}" target="_blank" class="btn btn-primary mt-3">
                                        <i class="ri-printer-line me-1"></i> Cetak Kwitansi
                                    </a>
                                `,
                                icon: 'success',
                                showConfirmButton: false,
                                allowOutsideClick: true,
                                didClose: () => location.reload()
                            });
                        } else {
                            Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                        }
                    },
                    error: function() {
                        hideLoading();
                        Swal.fire('Gagal!', 'Terjadi kesalahan server.', 'error');
                    }
                });
            }
        });
    });

    // ========================================
    // TOLAK PEMBAYARAN
    // ========================================
    $(document).on('click', '#btnTolakPembayaran', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const tagihanId = $('#detailModal').data('tagihan-id');
        const nama = $('#detailModal').data('tagihan-nama');
        
        if (!tagihanId) {
            Swal.fire('Error!', 'Data tagihan tidak ditemukan.', 'error');
            return;
        }

        Swal.fire({
            title: 'Tolak Pembayaran',
            html: `<p class="mb-0">Apakah Anda yakin ingin menolak pembayaran dari <strong>${nama}</strong>?</p><p class="text-muted small">Status akan dikembalikan ke "Belum Bayar" dan bukti pembayaran akan dihapus.</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#8898aa',
            confirmButtonText: '<i class="ri-close-line me-1"></i>Ya, Tolak',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                
                $.ajax({
                    url: `/dashboard/admin/tagihan/${tagihanId}/kembalikan-belum-bayar`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function(response) {
                        hideLoading();
                        if(response.success) {
                            $('#detailModal').modal('hide');
                            Swal.fire({
                                title: 'Pembayaran Ditolak!',
                                html: `<p>Tagihan <strong>${nama}</strong> telah dikembalikan ke status "Belum Bayar".</p>`,
                                icon: 'success',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false,
                                didClose: () => location.reload()
                            });
                        } else {
                            Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                        }
                    },
                    error: function(xhr) {
                        hideLoading();
                        const response = xhr.responseJSON;
                        Swal.fire('Gagal!', response?.message || 'Terjadi kesalahan server.', 'error');
                    }
                });
            }
        });
    });

    // ========================================
    // EDIT PAKET MODAL
    // ========================================
    $(document).on('click', '#btnEditPaket', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const tagihanId = $('#detailModal').data('tagihan-id');
        const nama = $('#detailModal').data('tagihan-nama');
        
        if (!tagihanId) {
            Swal.fire('Error!', 'Data tagihan tidak ditemukan.', 'error');
            return;
        }
        
        // Get current row data for dates
        const $tr = $(`tr[data-tagihan-id="${tagihanId}"]`);
        const tanggalMulaiRaw = $tr.data('tanggal-mulai-raw') || '';
        const tanggalBerakhirRaw = $tr.data('tanggal-berakhir-raw') || '';
        const currentPaketId = $tr.data('paket-id') || '';
        
        // Set the current tagihan ID to edit modal
        $('#editPaketModal').data('tagihan-id', tagihanId);
        $('#editPaketModal').data('tagihan-nama', nama);
        $('#editNamaTagihan').text(nama);
        
        // Set date values
        $('#editTanggalMulai').val(tanggalMulaiRaw);
        $('#editTanggalBerakhir').val(tanggalBerakhirRaw);
        
        // Set current paket if available
        if (currentPaketId) {
            $('#selectPaketEdit').val(currentPaketId).trigger('change');
        } else {
            $('#selectPaketEdit').val('').trigger('change');
        }
        
        // Hide detail modal and show edit modal
        $('#detailModal').modal('hide');
        
        setTimeout(function() {
            $('#editPaketModal').modal('show');
            
            // Initialize Select2 after modal is shown
            if (!$('#selectPaketEdit').hasClass('select2-hidden-accessible')) {
                $('#selectPaketEdit').select2({
                    dropdownParent: $('#editPaketModal'),
                    placeholder: '-- Cari dan Pilih Paket --',
                    allowClear: true,
                    width: '100%'
                });
            }
            
            // Initialize Flatpickr for date inputs
            if (!$('#editTanggalMulai').hasClass('flatpickr-input')) {
                flatpickr('#editTanggalMulai', {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd M Y',
                    allowInput: true,
                    defaultDate: tanggalMulaiRaw || null
                });
            } else {
                $('#editTanggalMulai')[0]._flatpickr.setDate(tanggalMulaiRaw || null);
            }
            
            if (!$('#editTanggalBerakhir').hasClass('flatpickr-input')) {
                flatpickr('#editTanggalBerakhir', {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd M Y',
                    allowInput: true,
                    defaultDate: tanggalBerakhirRaw || null
                });
            } else {
                $('#editTanggalBerakhir')[0]._flatpickr.setDate(tanggalBerakhirRaw || null);
            }
        }, 300);
    });

    // ========================================
    // KEMBALI DARI EDIT MODAL KE DETAIL MODAL
    // ========================================
    $(document).on('click', '#btnBackToDetailFromEdit', function(e) {
        e.preventDefault();
        
        $('#editPaketModal').modal('hide');
        
        setTimeout(function() {
            $('#detailModal').modal('show');
        }, 300);
    });

    // ========================================
    // SIMPAN PERUBAHAN PAKET
    // ========================================
    $(document).on('click', '#btnSimpanPaket', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const tagihanId = $('#editPaketModal').data('tagihan-id');
        const nama = $('#editPaketModal').data('tagihan-nama');
        const paketId = $('#selectPaketEdit').val();
        const tanggalMulai = $('#editTanggalMulai').val();
        const tanggalBerakhir = $('#editTanggalBerakhir').val();
        
        if (!tagihanId) {
            Swal.fire('Error!', 'Data tagihan tidak ditemukan.', 'error');
            return;
        }
        
        if (!paketId) {
            Swal.fire('Peringatan!', 'Silakan pilih paket terlebih dahulu.', 'warning');
            return;
        }
        
        if (!tanggalMulai) {
            Swal.fire('Peringatan!', 'Silakan isi tanggal mulai.', 'warning');
            return;
        }
        
        if (!tanggalBerakhir) {
            Swal.fire('Peringatan!', 'Silakan isi tanggal berakhir.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Perubahan',
            html: `<p class="mb-0">Apakah Anda yakin ingin menyimpan perubahan tagihan untuk <strong>${nama}</strong>?</p>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#18181b',
            cancelButtonColor: '#8898aa',
            confirmButtonText: '<i class="ri-check-line me-1"></i>Ya, Simpan',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-primary me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                
                $.ajax({
                    url: `/dashboard/admin/tagihan/${tagihanId}/update-paket`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        paket_id: paketId,
                        tanggal_mulai: tanggalMulai,
                        tanggal_berakhir: tanggalBerakhir
                    },
                    success: function(response) {
                        hideLoading();
                        if(response.success) {
                            $('#editPaketModal').modal('hide');
                            Swal.fire({
                                title: 'Berhasil!',
                                html: `<p>Paket untuk <strong>${nama}</strong> telah diperbarui.</p>`,
                                icon: 'success',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false,
                                didClose: () => location.reload()
                            });
                        } else {
                            Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                        }
                    },
                    error: function(xhr) {
                        hideLoading();
                        const response = xhr.responseJSON;
                        Swal.fire('Gagal!', response?.message || 'Terjadi kesalahan server.', 'error');
                    }
                });
            }
        });
    });

    // ========================================
    // UPDATE HARGA WHEN PAKET CHANGES
    // ========================================
    $('#selectPaketEdit').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const harga = selectedOption.data('harga') || 0;
        const kecepatan = selectedOption.data('kecepatan') || 0;
        
        $('#previewHarga').text('Rp ' + new Intl.NumberFormat('id-ID').format(harga));
        $('#previewKecepatan').text(kecepatan + ' Mbps');
    });

    // ========================================
    // KONFIRMASI PEMBAYARAN DARI TABEL
    // ========================================
    $(document).on('click', '.btn-konfirmasi', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const tagihanId = $(this).data('id');
        const nama = $(this).data('nama');

        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            html: `<p class="mb-0">Apakah <strong>${nama}</strong> sudah melakukan pembayaran?</p>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2dce89',
            cancelButtonColor: '#8898aa',
            confirmButtonText: '<i class="ri-check-line me-1"></i>Ya, Lunas',
            cancelButtonText: 'Batal',
            reverseButtons: true,
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
                            Swal.fire({
                                title: 'Pembayaran Dikonfirmasi!',
                                html: `
                                    <p>Tagihan <strong>${nama}</strong> telah ditandai lunas.</p>
                                    <a href="${response.pdfUrl}" target="_blank" class="btn btn-primary mt-3">
                                        <i class="ri-printer-line me-1"></i> Cetak Kwitansi
                                    </a>
                                `,
                                icon: 'success',
                                showConfirmButton: false,
                                allowOutsideClick: true,
                                didClose: () => location.reload()
                            });
                        } else {
                            Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                        }
                    },
                    error: function() {
                        hideLoading();
                        Swal.fire('Gagal!', 'Terjadi kesalahan server.', 'error');
                    }
                });
            }
        });
    });

    // ========================================
    // LIHAT BUKTI PEMBAYARAN - MODAL IMAGE
    // ========================================
    $(document).on('click', '.btn-lihat-bukti', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const buktiUrl = $(this).data('bukti');
        
        if (buktiUrl) {
            // Set image source
            $('#buktiImage').attr('src', buktiUrl);
            
            // Hide detail modal and show bukti modal
            $('#detailModal').modal('hide');
            
            // Wait for detail modal to hide before showing bukti modal
            setTimeout(function() {
                $('#buktiModal').modal('show');
            }, 300);
        }
    });

    // ========================================
    // KEMBALI DARI MODAL BUKTI KE MODAL DETAIL
    // ========================================
    $(document).on('click', '#btnBackToDetail', function(e) {
        e.preventDefault();
        
        // Hide bukti modal and show detail modal
        $('#buktiModal').modal('hide');
        
        // Wait for bukti modal to hide before showing detail modal
        setTimeout(function() {
            $('#detailModal').modal('show');
        }, 300);
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
  <!-- DAFTAR TAGIHAN PROSES VERIFIKASI -->
  <!-- ========================================= -->
  <div class="card">
    <div class="card-header">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
          <h5 class="mb-1 fw-bold">
            <i class="ri-file-list-3-line me-2 text-warning"></i>
            Tagihan Proses Verifikasi
          </h5>
          <small class="text-muted">Kelola tagihan yang sedang dalam proses verifikasi pembayaran</small>
        </div>
        
        @if($tagihans->total() > 0)
        <div>
          <span class="badge bg-label-warning" style="padding: 10px 20px; font-size: 0.9rem;">
            <i class="ri-database-2-line me-1"></i>
            {{ $tagihans->total() }} Tagihan
          </span>
        </div>
        @endif
      </div>

      <!-- ========================================= -->
      <!-- FORM SEARCH -->
      <!-- ========================================= -->
      <div class="search-wrapper mt-3">
        <form action="{{ url()->current() }}" method="GET">
          <div class="row g-3 align-items-center">
            <div class="col-md-10">
              <div class="input-group">
                <span class="input-group-text">
                  <i class="ri-search-line"></i>
                </span>
                <input type="text" 
                  class="form-control" 
                  name="search" 
                  placeholder="Cari berdasarkan Nama, No. ID, WhatsApp, Paket, Alamat, Kecamatan, Kabupaten..." 
                  value="{{ request('search') }}"
                  autocomplete="off">
              </div>
            </div>
            <div class="col-md-2">
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                  <i class="ri-search-line me-1"></i>Cari
                </button>
                @if(request('search'))
                <a href="{{ url()->current() }}" class="btn btn-outline-secondary" title="Reset">
                  <i class="ri-refresh-line"></i>
                </a>
                @endif
              </div>
            </div>
          </div>
        </form>
      </div>
      <!-- END FORM SEARCH -->

    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th><i class="ri-eye-line me-1"></i>Detail</th>
              <th><i class="ri-barcode-line me-1"></i>No. ID</th>
              <th><i class="ri-user-3-line me-1"></i>Nama</th>
              <th><i class="ri-whatsapp-line me-1"></i>WhatsApp</th>
              <th><i class="ri-shield-check-line me-1"></i>Status</th>
              <th><i class="ri-box-3-line me-1"></i>Paket</th>
              <th><i class="ri-money-dollar-circle-line me-1"></i>Harga</th>
              <th><i class="ri-settings-3-line me-1"></i>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($tagihans as $item)
            @php
              $status = strtolower($item->status_pembayaran ?? '');
              $badgeClass = match($status) {
                'lunas' => 'badge bg-success',
                'proses_verifikasi' => 'badge bg-warning',
                default => 'badge bg-secondary',
              };

              $alamatParts = [];
              if($item->pelanggan->alamat_jalan ?? '') $alamatParts[] = $item->pelanggan->alamat_jalan;
              if(($item->pelanggan->rt ?? '') || ($item->pelanggan->rw ?? '')) {
                $alamatParts[] = 'RT '.($item->pelanggan->rt ?? '-').' / RW '.($item->pelanggan->rw ?? '-');
              }
              if($item->pelanggan->desa ?? '') $alamatParts[] = 'Desa '.$item->pelanggan->desa;
              if($item->pelanggan->kecamatan ?? '') $alamatParts[] = 'Kecamatan '.$item->pelanggan->kecamatan;
              if($item->pelanggan->kabupaten ?? '') $alamatParts[] = 'Kabupaten '.$item->pelanggan->kabupaten;
              if($item->pelanggan->provinsi ?? '') $alamatParts[] = $item->pelanggan->provinsi;
              $alamatLengkap = implode(', ', $alamatParts);
            @endphp

            <tr 
              data-tagihan-id="{{ $item->id }}"
              data-alamat="{{ $alamatLengkap }}"
              data-kecamatan="{{ $item->pelanggan->kecamatan ?? '-' }}"
              data-kabupaten="{{ $item->pelanggan->kabupaten ?? '-' }}"
              data-provinsi="{{ $item->pelanggan->provinsi ?? '-' }}"
              data-paket="{{ $item->paket->nama_paket ?? '-' }}"
              data-paket-id="{{ $item->paket->id ?? '' }}"
              data-harga="Rp {{ number_format($item->paket->harga ?? 0, 0, ',', '.') }}"
              data-kecepatan="{{ $item->paket->kecepatan ?? '-' }} Mbps"
              data-tanggal-mulai="{{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') : '-' }}"
              data-tanggal-mulai-raw="{{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('Y-m-d') : '' }}"
              data-jatuh-tempo="{{ $item->tanggal_berakhir ? \Carbon\Carbon::parse($item->tanggal_berakhir)->format('d M Y') : '-' }}"
              data-tanggal-berakhir-raw="{{ $item->tanggal_berakhir ? \Carbon\Carbon::parse($item->tanggal_berakhir)->format('Y-m-d') : '' }}"
              data-catatan="{{ $item->catatan ?? '-' }}"
              data-bukti="{{ !empty($item->bukti_pembayaran) ? asset('storage/' . $item->bukti_pembayaran) : '' }}"
            >
              <td>
                <button class="btn btn-sm btn-icon btn-outline-primary btn-detail" title="Lihat Detail">
                  <i class="ri-eye-line"></i>
                </button>
              </td>
              <td><span class="badge bg-label-dark">{{ $item->pelanggan->nomer_id ?? '-' }}</span></td>
              <td><strong>{{ $item->pelanggan->nama_lengkap ?? '-' }}</strong></td>
              <td>
                <a href="https://wa.me/{{ $item->pelanggan->no_whatsapp ?? '' }}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; background: #18181b; color: #fafafa; padding: 6px 12px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-decoration: none; white-space: nowrap;">
                  <i class="ri-whatsapp-line"></i>{{ $item->pelanggan->no_whatsapp ?? '-' }}
                </a>
              </td>
              <td>
                <span class="{{ $badgeClass }}">
                  <i class="ri-time-line me-1"></i>{{ ucfirst(str_replace('_', ' ', $status) ?: 'Belum Bayar') }}
                </span>
              </td>
              <td>
                <span class="badge bg-label-info">
                  <i class="ri-box-line me-1"></i>{{ $item->paket->nama_paket ?? '-' }}
                </span>
              </td>
              <td><strong>Rp {{ number_format($item->paket->harga ?? 0, 0, ',', '.') }}</strong></td>
              <td>
                <div class="d-flex gap-2 flex-wrap">
                  @if($status === 'lunas')
                    <button class="btn btn-sm btn-secondary" disabled>
                      <i class="ri-check-circle-line me-1"></i> Lunas
                    </button>
                  @else
                    <button class="btn btn-sm btn-success btn-konfirmasi" 
                      data-id="{{ $item->id }}" 
                      data-nama="{{ $item->pelanggan->nama_lengkap ?? '-' }}">
                      <i class="ri-check-circle-line me-1"></i> Konfirmasi Lunas
                    </button>
                  @endif
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="text-center py-5">
                <div class="mb-3">
                  <i class="ri-inbox-line" style="font-size: 4rem; color: #ddd;"></i>
                </div>
                @if(request('search'))
                <h5 class="text-muted mb-2">Tidak Ada Hasil</h5>
                <p class="text-muted">Tidak ditemukan tagihan dengan kata kunci "<strong>{{ request('search') }}</strong>"</p>
                <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-primary mt-2">
                  <i class="ri-refresh-line me-1"></i>Reset Pencarian
                </a>
                @else
                <h5 class="text-muted mb-2">Tidak Ada Tagihan Dalam Proses Verifikasi</h5>
                <p class="text-muted">Saat ini tidak ada tagihan yang sedang dalam proses verifikasi.</p>
                @endif
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- ========================================= -->
      <!-- PAGINATION LARAVEL -->
      <!-- ========================================= -->
      @if($tagihans->total() > 0)
      <div class="pagination-wrapper">
        <div class="pagination-info">
            Menampilkan <strong>{{ $tagihans->firstItem() ?? 0 }}</strong> - <strong>{{ $tagihans->lastItem() ?? 0 }}</strong> dari <strong>{{ $tagihans->total() }}</strong> tagihan
            @if(request('search'))
              <span class="badge bg-label-primary ms-2">
                <i class="ri-search-line me-1"></i>Hasil pencarian: "{{ request('search') }}"
              </span>
            @endif
        </div>
        <div>
          {{ $tagihans->onEachSide(2)->links('vendor.pagination.custom-always') }}
        </div>
      </div>
      @elseif(request('search'))
      <div class="px-4 py-3 border-top bg-light">
        <div class="text-muted small">
          <span class="badge bg-label-primary">
            <i class="ri-search-line me-1"></i>Hasil pencarian: "{{ request('search') }}"
          </span>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>

<!-- ========================================= -->
<!-- MODAL: DETAIL - MODERN UI -->
<!-- ========================================= -->
<div class="modal fade" id="detailModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title text-white mb-4">
          <i class="ri-information-line me-2"></i>Detail Pelanggan & Tagihan
        </h5>
        <button type="button" class="btn-close btn-close-white mb-2" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <!-- Content will be inserted via JavaScript -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-primary mt-4" id="btnEditPaket">
          <i class="ri-edit-line me-1"></i> Edit Paket
        </button>
        <button type="button" class="btn btn-danger mt-4" id="btnTolakPembayaran">
          <i class="ri-close-circle-line me-1"></i> Tolak Pembayaran
        </button>
        <button type="button" class="btn btn-success mt-4" id="btnKonfirmasiDetail">
          <i class="ri-check-circle-line me-1"></i> Konfirmasi Lunas
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ========================================= -->
<!-- MODAL: BUKTI PEMBAYARAN -->
<!-- ========================================= -->
<div class="modal fade" id="buktiModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title text-white mb-4">
          <i class="ri-image-line me-2"></i>Bukti Pembayaran
        </h5>
        <button type="button" class="btn-close btn-close-white mb-2" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center p-4">
        <!-- Zoom Controls -->
        <div class="zoom-controls mb-3">
          <button type="button" class="btn btn-sm btn-outline-dark me-1" id="btnZoomOut" title="Zoom Out">
            <i class="ri-zoom-out-line"></i>
          </button>
          <span id="zoomLevel" class="mx-2 fw-bold">100%</span>
          <button type="button" class="btn btn-sm btn-outline-dark ms-1" id="btnZoomIn" title="Zoom In">
            <i class="ri-zoom-in-line"></i>
          </button>
          <button type="button" class="btn btn-sm btn-outline-dark ms-3" id="btnZoomReset" title="Reset Zoom">
            <i class="ri-refresh-line"></i>
          </button>
        </div>
        
        <!-- Image Container with Zoom -->
        <div id="imageContainer" style="overflow: hidden; position: relative; max-height: 55vh; border-radius: 8px; background: #f4f4f5; cursor: grab;">
          <img id="buktiImage" src="" alt="Bukti Pembayaran" 
               style="transition: transform 0.2s ease; transform-origin: center center; max-width: 100%; max-height: 55vh; box-shadow: 0 4px 20px rgba(0,0,0,0.15); border-radius: 8px;">
        </div>
        
        <p class="text-muted small mt-2 mb-0">
          <i class="ri-information-line me-1"></i>Gunakan tombol zoom atau scroll mouse untuk memperbesar/memperkecil. Klik dan drag untuk menggeser gambar.
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary mt-2" id="btnBackToDetail">
          <i class="ri-arrow-left-line me-1"></i>Kembali
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ========================================= -->
<!-- MODAL: EDIT PAKET -->
<!-- ========================================= -->
<div class="modal fade" id="editPaketModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #18181b;">
        <h5 class="modal-title text-white mb-4">
          <i class="ri-edit-line me-2"></i>Edit Tagihan
        </h5>
        <button type="button" class="btn-close btn-close-white mb-2" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-4">
          <p class="text-muted mb-2">Mengedit tagihan untuk:</p>
          <h5 class="fw-bold" id="editNamaTagihan">-</h5>
        </div>
        
        <div class="row">
          <div class="col-12 mb-4">
            <label class="form-label fw-semibold"><i class="ri-box-3-line me-1"></i>Pilih Paket</label>
            <select class="form-select select2-paket" id="selectPaketEdit" style="width: 100%;">
              <option value="">-- Cari dan Pilih Paket --</option>
              @foreach($paket as $p)
                <option value="{{ $p->id }}" data-harga="{{ $p->harga }}" data-kecepatan="{{ $p->kecepatan }}" data-masa="{{ $p->masa_pembayaran ?? 30 }}">
                  {{ $p->nama_paket }} - Rp {{ number_format($p->harga, 0, ',', '.') }} ({{ $p->kecepatan }} Mbps)
                </option>
              @endforeach
            </select>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6 mb-4">
            <label class="form-label fw-semibold"><i class="ri-calendar-line me-1"></i>Tanggal Mulai</label>
            <input type="text" class="form-control flatpickr-date" id="editTanggalMulai" placeholder="Pilih tanggal mulai">
          </div>
          <div class="col-md-6 mb-4">
            <label class="form-label fw-semibold"><i class="ri-calendar-event-line me-1"></i>Tanggal Berakhir</label>
            <input type="text" class="form-control flatpickr-date" id="editTanggalBerakhir" placeholder="Pilih tanggal berakhir">
          </div>
        </div>
        
        <div class="detail-section">
          <h6><i class="ri-box-3-line"></i>Preview Paket</h6>
          <div class="detail-item">
            <span class="detail-label"><i class="ri-money-dollar-circle-line"></i>Harga</span>
            <span class="detail-value"><strong id="previewHarga">-</strong></span>
          </div>
          <div class="detail-item">
            <span class="detail-label"><i class="ri-speed-line"></i>Kecepatan</span>
            <span class="detail-value" id="previewKecepatan">-</span>
          </div>
        </div>
        
        <div class="alert alert-warning mt-3" role="alert">
          <i class="ri-information-line me-2"></i>
          <small>Mengubah paket akan memperbarui nominal tagihan sesuai harga paket baru.</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary mt-2" id="btnBackToDetailFromEdit">
          <i class="ri-arrow-left-line me-1"></i>Kembali
        </button>
        <button type="button" class="btn btn-primary mt-2" id="btnSimpanPaket">
          <i class="ri-save-line me-1"></i>Simpan Perubahan
        </button>
      </div>
    </div>
  </div>
</div>

<script>
// ========================================
// IMAGE ZOOM FUNCTIONALITY
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    const buktiImage = document.getElementById('buktiImage');
    const imageContainer = document.getElementById('imageContainer');
    const zoomLevelSpan = document.getElementById('zoomLevel');
    const btnZoomIn = document.getElementById('btnZoomIn');
    const btnZoomOut = document.getElementById('btnZoomOut');
    const btnZoomReset = document.getElementById('btnZoomReset');
    
    let currentZoom = 1;
    let isDragging = false;
    let startX, startY, translateX = 0, translateY = 0;
    
    // Update zoom display
    function updateZoom() {
        buktiImage.style.transform = `scale(${currentZoom}) translate(${translateX}px, ${translateY}px)`;
        zoomLevelSpan.textContent = Math.round(currentZoom * 100) + '%';
        
        // Change cursor based on zoom level
        if (currentZoom > 1) {
            imageContainer.style.cursor = isDragging ? 'grabbing' : 'grab';
        } else {
            imageContainer.style.cursor = 'default';
            translateX = 0;
            translateY = 0;
        }
    }
    
    // Zoom In
    btnZoomIn.addEventListener('click', function() {
        if (currentZoom < 4) {
            currentZoom += 0.25;
            updateZoom();
        }
    });
    
    // Zoom Out
    btnZoomOut.addEventListener('click', function() {
        if (currentZoom > 0.5) {
            currentZoom -= 0.25;
            if (currentZoom <= 1) {
                translateX = 0;
                translateY = 0;
            }
            updateZoom();
        }
    });
    
    // Reset Zoom
    btnZoomReset.addEventListener('click', function() {
        currentZoom = 1;
        translateX = 0;
        translateY = 0;
        updateZoom();
    });
    
    // Mouse Wheel Zoom
    imageContainer.addEventListener('wheel', function(e) {
        e.preventDefault();
        if (e.deltaY < 0) {
            // Scroll up - Zoom in
            if (currentZoom < 4) {
                currentZoom += 0.15;
                updateZoom();
            }
        } else {
            // Scroll down - Zoom out
            if (currentZoom > 0.5) {
                currentZoom -= 0.15;
                if (currentZoom <= 1) {
                    translateX = 0;
                    translateY = 0;
                }
                updateZoom();
            }
        }
    });
    
    // Dragging for panning
    imageContainer.addEventListener('mousedown', function(e) {
        if (currentZoom > 1) {
            isDragging = true;
            startX = e.clientX - translateX;
            startY = e.clientY - translateY;
            imageContainer.style.cursor = 'grabbing';
        }
    });
    
    document.addEventListener('mousemove', function(e) {
        if (isDragging && currentZoom > 1) {
            translateX = e.clientX - startX;
            translateY = e.clientY - startY;
            buktiImage.style.transform = `scale(${currentZoom}) translate(${translateX / currentZoom}px, ${translateY / currentZoom}px)`;
        }
    });
    
    document.addEventListener('mouseup', function() {
        isDragging = false;
        if (currentZoom > 1) {
            imageContainer.style.cursor = 'grab';
        }
    });
    
    // Reset zoom when modal is closed
    document.getElementById('buktiModal').addEventListener('hidden.bs.modal', function() {
        currentZoom = 1;
        translateX = 0;
        translateY = 0;
        updateZoom();
    });
    
    // Double click to toggle zoom
    buktiImage.addEventListener('dblclick', function() {
        if (currentZoom === 1) {
            currentZoom = 2;
        } else {
            currentZoom = 1;
            translateX = 0;
            translateY = 0;
        }
        updateZoom();
    });
});
</script>

@endsection
