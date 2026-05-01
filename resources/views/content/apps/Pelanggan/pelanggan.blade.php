@extends('layouts/layoutMaster')

@section('title', 'Data Pelanggan')

@php
use Illuminate\Support\Str;
@endphp

{{-- VENDOR STYLE --}}
@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

{{-- PAGE STYLE --}}
@section('page-style')
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

.btn-primary i, .btn.btn-primary i {
  color: #ffffff !important;
}

.btn-primary:hover,
.btn.btn-primary:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

.btn-add {
  padding: 10px 24px !important;
  border-radius: 8px !important;
  font-weight: 600 !important;
  transition: all 0.3s ease !important;
}
.btn-primary.btn-add i {
  color: #ffffff !important;
}

.btn-add:hover {
  transform: translateY(-2px) !important;
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
  background: #dc2626 !important;
  background-color: #dc2626 !important;
  color: #fafafa !important;
  border: 1px solid #dc2626 !important;
}

.btn-danger:hover,
.btn.btn-danger:hover {
  background: #b91c1c !important;
  background-color: #b91c1c !important;
  border-color: #b91c1c !important;
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
  color: #dc2626 !important;
}

.btn-outline-danger:hover,
.btn.btn-outline-danger:hover {
  background: #dc2626 !important;
  background-color: #dc2626 !important;
  border-color: #dc2626 !important;
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

/* ========== SEARCH SECTION ========== */
.search-section {
  background: var(--gray-bg);
  padding: 1.5rem;
  border-bottom: 1px solid var(--gray-border);
}

.search-input-group {
  max-width: 900px;
  margin: 0 auto;
}

.search-input-group .input-group {
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  border-radius: 10px;
  overflow: hidden;
}

.search-input-group .input-group-text {
  background: white;
  border: 1px solid #e4e4e7;
  border-right: 0;
  padding: 0.75rem 1rem;
}

.search-input-group .input-group-text i {
  color: #18181b !important;
}

.search-input-group .form-control {
  border: 1px solid #e4e4e7;
  border-left: 0;
  border-right: 0;
  padding: 0.75rem 1rem;
  font-size: 0.95rem;
}

.search-input-group .form-control:focus {
  border-color: #18181b;
  box-shadow: none;
}

.search-input-group .btn {
  border: 1px solid #18181b;
  padding: 0.75rem 1.5rem !important;
  font-weight: 600;
  white-space: nowrap;
}

.btn-clear-search {
  background: white !important;
  border: 1px solid #e4e4e7 !important;
  border-left: 0 !important;
  border-right: 0 !important;
  color: #71717a !important;
  padding: 0.75rem 1rem !important;
}

.btn-clear-search:hover {
  background: #f4f4f5 !important;
  color: #dc2626 !important;
}

.search-info-box {
  max-width: 900px;
  margin: 1rem auto 0;
  padding: 0.75rem 1rem;
  background: white;
  border-radius: 8px;
  border: 1px solid #e4e4e7;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.search-keyword {
  background: #f4f4f5;
  color: #18181b;
  padding: 2px 10px;
  border-radius: 4px;
  font-weight: 600;
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

.badge.bg-secondary,
.bg-secondary:not(.btn) {
  background: #71717a !important;
  color: #fafafa !important;
}

.bg-label-dark {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
}

/* ========== PAGINATION SECTION ========== */
.pagination-section {
  background: var(--gray-bg);
  padding: 1.5rem;
  border-top: 1px solid var(--gray-border);
  border-radius: 0 0 12px 12px;
}

.pagination-wrapper {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}

.pagination-info {
  color: #71717a;
  font-size: 0.9rem;
}

.pagination-info i {
  color: #18181b;
  margin-right: 0.5rem;
}

.pagination {
  margin: 0;
}

.pagination .page-link {
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
  font-weight: 600 !important;
  transition: all 0.3s ease !important;
}

.pagination .page-link:hover {
  background: #fff !important;
  border-color: #e4e4e7 !important;
  color: #18181b !important;
}

.pagination .page-item.active .page-link {
  background: #18181b !important;
  border-color: #18181b !important;
  color: #fafafa !important;
}

.pagination .page-item.disabled .page-link {
  background: #f4f4f5 !important;
  border-color: #e4e4e7 !important;
  color: #a1a1aa !important;
  cursor: not-allowed;
}

/* ========== EMPTY STATE ========== */
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
}

.empty-state-icon {
  font-size: 4rem;
  color: #e4e4e7;
  margin-bottom: 1rem;
}

.empty-state h5 {
  color: #18181b;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.empty-state p {
  color: #71717a;
  margin-bottom: 1.5rem;
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
  background-color: rgba(0, 0, 0, 0.5) !important;
}

.modal-backdrop.show {
  opacity: 1 !important;
}

.modal-content {
  border-radius: 16px;
  border: none;
  box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.modal-header {
  background: #18181b !important;
  border-radius: 16px 16px 0 0;
  color: white;
  padding: 1.75rem 2rem;
  border: none;
}

.modal-title {
  margin: 0.5rem 0;
}

.modal-title {
  font-weight: 600;
  font-size: 1.125rem;
  color: #fafafa !important;
}

.modal-header .btn-close {
  filter: invert(1);
  opacity: 1;
}

.modal-body {
  padding: 1.5rem;
  max-height: 70vh;
  overflow-y: auto;
}

.modal-footer {
  padding: 2rem 2rem;
  border-top: 1px solid #e4e4e7;
  background: #fafafa;
  border-radius: 0 0 16px 16px;
}

.customer-avatar {
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
  min-width: 180px;
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

.customer-header-info {
  text-align: center;
  padding: 1.5rem;
  background: #fafafa;
  border-radius: 10px;
  margin-bottom: 1.5rem;
  border: 1px solid #e4e4e7;
}

.customer-name {
  font-size: 1.5rem;
  font-weight: 700;
  color: #18181b;
  margin-bottom: 0.5rem;
}

.customer-id {
  display: inline-block;
  padding: 0.5rem 1.5rem;
  background: #18181b !important;
  color: white;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.875rem;
  box-shadow: 0 2px 8px rgba(24, 24, 27, 0.3);
}

.ktp-preview {
  max-width: 100%;
  border-radius: 8px;
  border: 1px solid #e4e4e7;
  margin-top: 0.5rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.progress-flow {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  margin-top: 0.25rem;
  flex-wrap: wrap;
}

.progress-dot {
  width: 24px;
  height: 24px;
  border-radius: 999px;
  border: 2px solid #d4d4d8;
  color: #a1a1aa;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.72rem;
  font-weight: 700;
  background: #fff;
  flex-shrink: 0;
}

.progress-dot.done {
  border-color: #16a34a;
  color: #16a34a;
}

.progress-dot.current {
  border-color: #16a34a;
  background: #16a34a;
  color: #fff;
}

.progress-line {
  width: 22px;
  height: 2px;
  background: #d4d4d8;
  border-radius: 2px;
}

.progress-line.done {
  background: #16a34a;
}

.progress-caption {
  margin-top: 0.6rem;
  font-size: 0.8rem;
  color: #71717a;
}

/* ========== TEXT COLORS ========== */
.text-primary {
  color: #18181b !important;
}

.text-success {
  color: #22c55e !important;
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

/* ========== WHATSAPP LINK ========== */
.text-success.text-decoration-none {
  color: #18181b !important;
}

.text-success.text-decoration-none:hover {
  color: #27272a !important;
}

/* ========== RESPONSIVE ========== */
@media (max-width: 768px) {
  .card-header-custom,
  .search-section,
  .pagination-section {
    padding: 1rem 1.25rem;
  }

  .pagination-wrapper {
    flex-direction: column;
    text-align: center;
  }

  .btn-add {
    width: 100%;
  }

  .search-input-group .input-group {
    flex-wrap: wrap;
  }

  .search-input-group .btn {
    flex: 1 1 100%;
    border-radius: 0 0 8px 8px !important;
    border: 1px solid #18181b !important;
    margin-top: -1px;
  }

  .detail-label {
    min-width: 120px;
    font-size: 0.8rem;
  }

  .detail-value {
    font-size: 0.8rem;
  }

  .modal-body {
    padding: 1rem;
    max-height: 75vh;
  }

  .modal-dialog {
    margin: 0.5rem;
  }

  .detail-item {
    flex-direction: column;
    gap: 0.35rem;
    align-items: flex-start;
  }

  .detail-label {
    min-width: 0;
  }

  .progress-dot {
    width: 28px;
    height: 28px;
    font-size: 0.78rem;
  }

  .progress-line {
    width: 18px;
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

  .empty-state {
    padding: 3rem 1rem;
  }

  .customer-avatar {
    width: 72px;
    height: 72px;
    font-size: 1.75rem;
  }

  .customer-name {
    font-size: 1.1rem;
  }

  .customer-id {
    font-size: 0.78rem;
    padding: 0.4rem 0.9rem;
  }

  .detail-section {
    padding: 0.9rem;
    margin-bottom: 0.9rem;
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

/* Hide duplicate pagination summary from Laravel Links */
.pagination-wrapper nav .text-muted {
    display: none !important;
}

/* Ensure DataTables info is hidden */
.dataTables_info {
    display: none !important;
}

/* Custom Hover for Outline Button - White Icon, Black BG, No Shadow */
.btn-outline-primary:hover {
    background-color: #18181b !important;
    color: #ffffff !important;
    border-color: #18181b !important;
    box-shadow: none !important;
}
.btn-outline-primary:hover i {
    color: #ffffff !important;
}

/* ========== STAT CARDS ========== */
.stat-cards {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid var(--gray-border);
  background: var(--gray-bg);
}

.stat-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.25rem;
  background: #fff;
  border: 1px solid var(--gray-border);
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none !important;
  color: inherit !important;
}

.stat-card:hover {
  border-color: #18181b;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  transform: translateY(-1px);
}

.stat-card.active {
  background: #18181b;
  border-color: #18181b;
  color: #fff !important;
}

.stat-card.active .stat-icon {
  background: rgba(255,255,255,0.15);
  color: #fff;
}

.stat-card.active .stat-label {
  color: #a1a1aa;
}

.stat-card.active .stat-value {
  color: #fff;
}

.stat-icon {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  flex-shrink: 0;
}

.stat-icon.total {
  background: #f4f4f5;
  color: #18181b;
}

.stat-icon.approve {
  background: #dcfce7;
  color: #16a34a;
}

.stat-icon.pending {
  background: #fef3c7;
  color: #d97706;
}

.stat-label {
  font-size: 0.75rem;
  color: #71717a;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.025em;
}

.stat-value {
  font-size: 1.375rem;
  font-weight: 700;
  color: #18181b;
  line-height: 1.2;
}

@media (max-width: 768px) {
  .stat-cards {
    grid-template-columns: repeat(3, 1fr);
    gap: 0.5rem;
    padding: 0.75rem 1rem;
  }
  .stat-card {
    flex-direction: column;
    text-align: center;
    gap: 0.5rem;
    padding: 0.75rem 0.5rem;
  }
  .stat-icon {
    width: 36px;
    height: 36px;
    font-size: 1rem;
  }
  .stat-value {
    font-size: 1.125rem;
  }
  .stat-label {
    font-size: 0.625rem;
  }
}
</style>
@endsection

{{-- VENDOR SCRIPT --}}
@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

{{-- PAGE SCRIPT --}}
@section('page-script')
@if (env('ENABLE_ONESIGNAL', false) && config('services.onesignal.app_id'))
<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
<script>
    window.OneSignal = window.OneSignal || [];
    OneSignal.push(function() {
        OneSignal.init({
            appId: "{{ config('services.onesignal.app_id') }}",
            safari_web_id: "{{ env('ONESIGNAL_SAFARI_WEB_ID', '') }}",
            allowLocalhostAsSecureOrigin: true,
        });

        OneSignal.on('subscriptionChange', function (isSubscribed) {
            if (isSubscribed) {
                OneSignal.getUserId(function(player_id) {
                    fetch('/pelanggan/save-player-id', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ player_id })
                    });
                });
            }
        });
    });
</script>
@endif

<script>
document.addEventListener("DOMContentLoaded", function() {
    function showLoading() {
        $('.loading-overlay').css('display', 'flex');
    }

    function hideLoading() {
        $('.loading-overlay').fadeOut(300);
    }

    // ? HANYA INISIALISASI DATATABLES JIKA ADA DATA
    @if($pelanggan->count() > 0)
        const dtUserTable = $('.datatables-users').DataTable({
            paging: false,
            searching: false,
            ordering: true,
            info: false,
            responsive: false,
            dom: 'rt',
            columnDefs: [
              { orderable: false, targets: [0, 1, -1] }
            ],
            language: {
                emptyTable: "Tidak ada data pelanggan tersedia",
                zeroRecords: "Tidak ditemukan data yang sesuai"
            }
        });
    @endif

    // Clear search button
    $('#clearSearch').on('click', function(e) {
        e.preventDefault();
        showLoading();
        window.location.href = "{{ route('pelanggan') }}";
    });

    // Show loading saat submit form
    $('#searchForm').on('submit', function() {
        showLoading();
    });

    // EVENT DETAIL MODAL
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const tr = $(this).closest('tr');

        const nomerId = tr.data('nomer-id') || '-';
        const namaLengkap = tr.data('nama') || '-';
        const noWhatsapp = tr.data('whatsapp') || '-';
        const alamatJalan = tr.data('alamat') || '-';
        const rt = tr.data('rt') || '-';
        const rw = tr.data('rw') || '-';
        const kecamatan = tr.data('kecamatan') || '-';
        const kabupaten = tr.data('kabupaten') || '-';
        const tanggalMulai = tr.data('tanggal-mulai') || '-';
        const fotoKtp = tr.data('foto-ktp') || '';
        const status = tr.data('status') || '-';
        const marketingName = tr.data('marketing-name') || 'Sistem';
        const marketingEmail = tr.data('marketing-email') || '-';
        const createdAt = tr.data('created-at') || '-';
        const progressNote = tr.attr('data-progress-note') || '-';
        const rawProgres = tr.data('progres') || '';
        const initial = namaLengkap ? namaLengkap.charAt(0).toUpperCase() : '?';
        const statusLower = status.toLowerCase();
        const progres = rawProgres || (statusLower === 'approve' ? 'Registrasi' : 'Belum Diproses');

        let statusBadge = '';
        if (statusLower === 'approve') {
            statusBadge = '<span class="badge bg-success">Approve</span>';
        } else if (statusLower === 'pending' || statusLower === 'proses') {
            statusBadge = '<span class="badge bg-warning">Progress</span>';
        } else if (statusLower === 'reject') {
            statusBadge = '<span class="badge bg-danger">Reject</span>';
        } else {
            statusBadge = '<span class="badge bg-secondary">' + status + '</span>';
        }

        const progressStages = [
            { value: 'Belum Diproses', label: 'Belum Diproses' },
            { value: 'Tarik Kabel', label: 'Tarik Kabel' },
            { value: 'Aktivasi', label: 'Aktivasi' },
            { value: 'Registrasi', label: 'Register' }
        ];
        const currentStageIndex = progressStages.findIndex(stage => stage.value === progres);
        const isApproved = statusLower === 'approve';
        const currentStageLabel = (progressStages.find(stage => stage.value === progres)?.label) || 'Belum Diproses';
        const progressFlowHtml = `
            <div class="progress-flow">
                ${progressStages.map((stage, i) => {
                    const done = isApproved || (currentStageIndex !== -1 && i < currentStageIndex);
                    const current = !isApproved && currentStageIndex !== -1 && i === currentStageIndex;
                    const dotClass = done ? 'done' : (current ? 'current' : '');
                    const dotValue = done ? '<i class="ri-check-line"></i>' : (i + 1);
                    const lineClass = done ? 'done' : '';
                    const line = i < progressStages.length - 1 ? `<div class="progress-line ${lineClass}"></div>` : '';
                    return `<div class="progress-dot ${dotClass}" title="${stage.label}">${dotValue}</div>${line}`;
                }).join('')}
            </div>
            <div class="progress-caption">Alur: Belum Diproses -> Tarik Kabel -> Aktivasi -> Register<br>Tahap Saat Ini: <strong>${isApproved ? 'Register' : currentStageLabel}</strong></div>
        `;

        const html = `
            <div class="customer-header-info">
                <div class="customer-avatar mx-auto">
                    ${initial}
                </div>
                <div class="customer-name">${namaLengkap}</div>
                <div class="customer-id">
                    <i class="ri-barcode-line me-2"></i>ID: ${nomerId}
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-user-3-line"></i>Informasi Pribadi</h6>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-id-card-line"></i>No. ID
                    </span>
                    <span class="detail-value"><strong>${nomerId}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-user-line"></i>Nama Lengkap
                    </span>
                    <span class="detail-value">${namaLengkap}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-whatsapp-line"></i>No. WhatsApp
                    </span>
                    <span class="detail-value">
                        <a href="https://wa.me/${noWhatsapp}" target="_blank" class="text-success text-decoration-none">
                            <strong>${noWhatsapp}</strong> <i class="ri-external-link-line"></i>
                        </a>
                    </span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-map-pin-line"></i>Alamat Lengkap</h6>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-road-map-line"></i>Jalan
                    </span>
                    <span class="detail-value">${alamatJalan}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-community-line"></i>RT / RW
                    </span>
                    <span class="detail-value">${rt} / ${rw}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-building-line"></i>Kecamatan
                    </span>
                    <span class="detail-value">${kecamatan}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-map-2-line"></i>Kabupaten
                    </span>
                    <span class="detail-value">${kabupaten}</span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-calendar-check-line"></i>Informasi Langganan</h6>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-calendar-line"></i>Tanggal Mulai
                    </span>
                    <span class="detail-value">${tanggalMulai}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-shield-check-line"></i>Status
                    </span>
                    <span class="detail-value">${statusBadge}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-git-merge-line"></i>Alur Progres
                    </span>
                    <span class="detail-value">${progressFlowHtml}</span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-file-text-line"></i>Catatan & Deskripsi</h6>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-sticky-note-line"></i>Catatan Progres
                    </span>
                    <span class="detail-value" style="white-space: pre-wrap;">${progressNote}</span>
                </div>
                
            </div>

            <div class="detail-section">
                <h6><i class="ri-user-settings-line"></i>Ditambahkan Oleh</h6>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-user-star-line"></i>Di tambahkan olehs
                    </span>
                    <span class="detail-value">
                        <strong>${marketingName}</strong>
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-mail-line"></i>Email
                    </span>
                    <span class="detail-value">${marketingEmail}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-time-line"></i>Tanggal Input
                    </span>
                    <span class="detail-value">${createdAt}</span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-image-line"></i>Foto KTP</h6>
                <div class="text-center">
                    ${fotoKtp ? '<img src="' + fotoKtp + '" class="ktp-preview" alt="Foto KTP">' : '<p class="text-muted">Tidak ada foto KTP</p>'}
                </div>
            </div>
        `;

        $('#detailModal .modal-body').html(html);
        $('#detailModal').modal('show');
    });

    // EVENT DELETE
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Konfirmasi Penghapusan',
            text: 'Yakin ingin menghapus data pelanggan ini? Data tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            showDenyButton: false,
            showCloseButton: false,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#f5365c',
            cancelButtonColor: '#8898aa',
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
                        text: 'Data pelanggan berhasil dihapus.',
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

{{-- CONTENT --}}
@section('content')
<div class="loading-overlay">
    <div class="spinner-border spinner-border-custom text-light" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<div class="card">
    {{-- HEADER --}}
    <div class="card-header-custom">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="mb-1 fw-bold">
                    <i class="ri-user-star-line me-2 text-primary"></i>Data Pelanggan
                </h4>
                <p class="mb-0 text-muted small">Kelola dan monitor data pelanggan</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="/pelanggan/export" class="btn btn-outline-primary btn-add">
                    <i class="ri-file-excel-2-line me-2"></i>Export Excel
                </a>
                <a href="{{ route('add-pelanggan') }}" class="btn btn-primary btn-add">
                    <i class="ri-user-add-line me-2"></i>Tambah Pelanggan
                </a>
            </div>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="stat-cards">
        <a href="{{ route('pelanggan') }}" class="stat-card {{ !$statusFilter ? 'active' : '' }}">
            <div class="stat-icon total">
                <i class="ri-group-line"></i>
            </div>
            <div>
                <div class="stat-label">Total</div>
                <div class="stat-value">{{ $countTotal }}</div>
            </div>
        </a>
        <a href="{{ route('pelanggan', ['status' => 'approve']) }}" class="stat-card {{ $statusFilter === 'approve' ? 'active' : '' }}">
            <div class="stat-icon approve">
                <i class="ri-checkbox-circle-line"></i>
            </div>
            <div>
                <div class="stat-label">Approve</div>
                <div class="stat-value">{{ $countApprove }}</div>
            </div>
        </a>
        <a href="{{ route('pelanggan', ['status' => 'proses']) }}" class="stat-card {{ $statusFilter === 'proses' ? 'active' : '' }}">
            <div class="stat-icon pending">
                <i class="ri-time-line"></i>
            </div>
            <div>
                <div class="stat-label">Progress</div>
                <div class="stat-value">{{ $countPending }}</div>
            </div>
        </a>
    </div>

    {{-- SEARCH SECTION --}}
    <div class="search-section">
        <form action="{{ route('pelanggan') }}" method="GET" id="searchForm">
            @if($statusFilter)
            <input type="hidden" name="status" value="{{ $statusFilter }}">
            @endif
            <div class="search-input-group">
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="ri-search-line text-primary"></i>
                    </span>
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Cari berdasarkan ID, Nama, No. WA, Alamat, RT/RW, Kecamatan, Kabupaten, atau Status..."
                        value="{{ request('search') }}"
                        id="searchInput"
                        autocomplete="off"
                    >
                    @if(request('search'))
                    <button type="button" class="btn btn-clear-search" id="clearSearch" title="Hapus Pencarian">
                        <i class="ri-close-line"></i>
                    </button>
                    @endif
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-search-2-line me-1"></i>Cari
                    </button>
                </div>
            </div>

            @if(request('search'))
            <div class="search-info-box">
                <div>
                    <i class="ri-filter-3-line text-primary me-2"></i>
                    <small class="text-muted">
                        Hasil pencarian: <span class="search-keyword">"{{ request('search') }}"</span>
                    </small>
                </div>
                <a href="{{ route('pelanggan') }}" class="btn btn-sm btn-outline-primary">
                    <i class="ri-refresh-line me-1"></i>Reset
                </a>
            </div>
            @endif
        </form>
    </div>

    {{-- TABLE SECTION --}}
    <div class="card-body p-0">
        <div class="table-responsive p-3">
            @if($pelanggan->count() > 0)
                <table class="datatables-users table table-modern table-hover">
                    <thead>
                        <tr>
                        <th><i class="ri-hashtag me-1"></i>No</th>
                            <th><i class="ri-eye-line me-1"></i>Detail</th>
                            <th><i class="ri-barcode-line me-1"></i>No. ID</th>
                            <th><i class="ri-user-3-line me-1"></i>Nama Lengkap</th>
                            <th><i class="ri-whatsapp-line me-1"></i>No. WhatsApp</th>
                            <th><i class="ri-map-pin-line me-1"></i>Alamat</th>
                            <th><i class="ri-calendar-line me-1"></i>Tanggal</th>
                            <th><i class="ri-shield-check-line me-1"></i>Status</th>
                            <th class="text-center"><i class="ri-settings-3-line me-1"></i>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pelanggan as $p)
                        <tr
                            data-nomer-id="{{ $p->nomer_id }}"
                            data-nama="{{ $p->nama_lengkap }}"
                            data-whatsapp="{{ $p->no_whatsapp }}"
                            data-alamat="{{ $p->alamat_jalan }}"
                            data-rt="{{ $p->rt }}"
                            data-rw="{{ $p->rw }}"
                            data-kecamatan="{{ $p->kecamatan }}"
                            data-kabupaten="{{ $p->kabupaten }}"
                            data-tanggal-mulai="{{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') }}"
                            data-foto-ktp="{{ $p->foto_ktp ? asset('storage/' . $p->foto_ktp) : '' }}"
                            data-status="{{ ucfirst($p->status ?? '-') }}"
                            data-progres="{{ $p->progres ?? (strtolower($p->status ?? '') === 'approve' ? 'Registrasi' : \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES) }}"
                            data-marketing-name="{{ $p->user->name ?? 'Admin' }}"
                            data-marketing-email="{{ $p->user->email ?? '-' }}"
                            data-created-at="{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y H:i') }}"
                            data-progress-note="{{ $p->progress_note }}"
                        >
                            <td class="text-muted fw-semibold">{{ ($pelanggan->firstItem() ?? 1) + $loop->index }}</td>
                            <td>
                                <button class="btn btn-sm btn-icon btn-outline-primary btn-detail" title="Lihat Detail">
                                    <i class="ri-eye-line"></i>
                                </button>
                            </td>

                            <td>
                                <span class="badge bg-label-dark">{{ $p->nomer_id }}</span>
                            </td>

                            <td>
                                <span class="fw-semibold">{{ $p->nama_lengkap }}</span>
                            </td>

                            <td>
                                <a href="https://wa.me/{{ $p->no_whatsapp }}" target="_blank" class="text-success text-decoration-none">
                                    <i class="ri-whatsapp-line me-1"></i>{{ $p->no_whatsapp }}
                                </a>
                            </td>

                            <td>
                                {{ Str::limit($p->alamat_jalan, 30) }}<br>
                                <small class="text-muted">RT {{ $p->rt }}/RW {{ $p->rw }}, {{ $p->kecamatan }}</small>
                            </td>

                            <td>{{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') }}</td>

                            <td>
                                @php
                                  $statusClass = match(strtolower($p->status ?? '')) {
                                      'reject' => 'badge bg-danger',
                                      'pending', 'proses' => 'badge bg-warning',
                                      'approve' => 'badge bg-success',
                                      default => 'badge bg-secondary',
                                  };
                                @endphp
                                <span class="{{ $statusClass }}">{{ in_array(strtolower($p->status ?? ''), ['pending', 'proses']) ? 'Progress' : ucfirst($p->status ?? '-') }}</span>
                            </td>

                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('pelanggan.edit', $p->id) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Edit">
                                        <i class="ri-edit-2-line"></i>
                                    </a>

                                    <form action="{{ route('pelanggan.delete', $p->id) }}" method="POST" class="d-inline">
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
            @else
                {{-- EMPTY STATE --}}
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="ri-inbox-line"></i>
                    </div>
                    @if(request('search'))
                        <h5>Tidak ada hasil untuk "{{ request('search') }}"</h5>
                        <p>Coba gunakan kata kunci lain atau ubah filter pencarian</p>
                        <a href="{{ route('pelanggan') }}" class="btn btn-outline-primary">
                            <i class="ri-refresh-line me-2"></i>Reset Pencarian
                        </a>
                    @else
                        <h5>Belum ada data pelanggan</h5>
                        <p>Mulai tambahkan pelanggan baru untuk mengelola data</p>
                        <a href="{{ route('add-pelanggan') }}" class="btn btn-primary">
                            <i class="ri-user-add-line me-2"></i>Tambah Pelanggan Pertama
                        </a>
                    @endif
                </div>
            @endif
        </div>

        {{-- PAGINATION --}}
        @if($pelanggan->hasPages())
        <div class="pagination-section mt-4">
            <div class="pagination-wrapper p-3 d-flex justify-content-between align-items-center  rounded-3 ">
                <div class="pagination-info text-muted small fw-medium">
                  Menampilkan <strong>{{ $pelanggan->firstItem() ?? 0 }}</strong> - <strong>{{ $pelanggan->lastItem() ?? 0 }}</strong> dari <strong>{{ $pelanggan->total() }}</strong> pelanggan
                </div>
                <div>
                    {{ $pelanggan->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
              <div class="modal-header bg-primary py-4">
        <h5 class="modal-title text-white fw-bold">
          <i class="ri-information-line me-2"></i>Detail Pelanggan
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

            <div class="modal-body">
                <!-- Content will be inserted via JavaScript -->
            </div>

        </div>
    </div>
</div>
@endsection
