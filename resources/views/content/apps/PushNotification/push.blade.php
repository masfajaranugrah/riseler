@extends('layouts/layoutMaster')

@section('title', 'Push Notification')

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

/* Card Header */
.card-header-custom {
  background: #ffffff !important;
  color: #18181b !important;
  border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
  padding: 1.5rem;
  border-bottom: 1px solid #e4e4e7;
}

.card-header-custom h4,
.card-header-custom h5,
.card-header-custom p,
.card-header-custom i {
  color: #18181b !important;
}

.card-header-custom .opacity-75 {
  color: #71717a !important;
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
}

.btn-primary i,
.btn.btn-primary i {
  color: #ffffff !important;
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

.btn-info,
.btn.btn-info {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
  box-shadow: none !important;
}

.btn-info:hover,
.btn.btn-info:hover {
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

.btn-broadcast {
  padding: 10px 24px !important;
  border-radius: 8px !important;
  font-weight: 600 !important;
  transition: all 0.3s !important;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.btn-broadcast:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 6px 20px rgba(0,0,0,0.2) !important;
}

.btn-broadcast i {
  margin-right: 8px;
  color: #ffffff !important;
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

.badge-status {
  font-weight: 600;
  padding: 6px 12px;
  border-radius: 9999px;
  font-size: 0.75rem;
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
.bg-primary:not(.btn) {
  background: #18181b !important;
  color: #fafafa !important;
}

.badge.bg-info,
.bg-info:not(.btn) {
  background: #18181b !important;
  color: #fafafa !important;
}

.badge.bg-secondary,
.bg-secondary:not(.btn) {
  background: #71717a !important;
  color: #fafafa !important;
}

/* Badge Labels */
.bg-label-info {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
  border-radius: 9999px !important;
}

.bg-label-primary,
.bg-label-success,
.bg-label-warning,
.bg-label-dark,
.bg-label-secondary {
  background: #f4f4f5 !important;
  color: #18181b !important;
  border: 1px solid #e4e4e7 !important;
}

/* ========================================= */
/* TABLE STYLES */
/* ========================================= */
.table-modern {
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
  border: none;
  padding: 1rem;
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
  border-bottom: 1px solid #e4e4e7;
  vertical-align: middle;
  color: #18181b;
}

/* ========================================= */
/* FORM CONTROLS */
/* ========================================= */
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

/* ========================================= */
/* LOADING OVERLAY */
/* ========================================= */
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

.spinner-border-custom {
  width: 3rem;
  height: 3rem;
  border-width: 0.3rem;
}

/* ========================================= */
/* ICON WRAPPER */
/* ========================================= */
.icon-wrapper {
  width: 48px;
  height: 48px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  font-size: 24px;
  background: #f4f4f5;
  color: #18181b;
}

/* ========================================= */
/* ACTION BUTTONS */
/* ========================================= */
.action-buttons {
  gap: 12px;
}

/* ========================================= */
/* DATATABLES CUSTOM */
/* ========================================= */
.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
  border: 1px solid #e4e4e7 !important;
  border-radius: 8px !important;
  padding: 0.5rem 1rem !important;
}

.dataTables_wrapper .dataTables_length select:focus,
.dataTables_wrapper .dataTables_filter input:focus {
  border-color: #18181b !important;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b !important;
  outline: none !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button,
.dataTables_wrapper .dataTables_paginate .paginate_button:link,
.dataTables_wrapper .dataTables_paginate .paginate_button:visited {
  border-radius: 50% !important;
  width: 40px !important;
  height: 40px !important;
  padding: 0 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  border: 1px solid #e4e4e7 !important;
  color: #18181b !important;
  background: #fff !important;
  background-color: #fff !important;
  margin: 0 4px !important;
  font-weight: 600 !important;
  transition: all 0.3s ease !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover,
.dataTables_wrapper .dataTables_paginate .paginate_button:focus,
.dataTables_wrapper .dataTables_paginate .paginate_button:active {
  background: #fff !important;
  background-color: #fff !important;
  border-color: #e4e4e7 !important;
  color: #18181b !important;
  transform: none !important;
  box-shadow: none !important;
  outline: none !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:link,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:visited,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:focus {
  background: #18181b !important;
  background-color: #18181b !important;
  border-color: #18181b !important;
  color: #fafafa !important;
  box-shadow: 0 4px 12px rgba(24, 24, 27, 0.4) !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled:link,
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled:visited {
  background: #f4f4f5 !important;
  background-color: #f4f4f5 !important;
  border-color: #e4e4e7 !important;
  color: #a1a1aa !important;
  cursor: not-allowed !important;
  transform: none !important;
  box-shadow: none !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover,
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled:focus,
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled:active {
  background: #f4f4f5 !important;
  background-color: #f4f4f5 !important;
  border-color: #e4e4e7 !important;
  color: #a1a1aa !important;
  transform: none !important;
  box-shadow: none !important;
}

/* Override any Bootstrap/DataTables default link colors */
.page-link,
.paginate_button a,
.dataTables_paginate a {
  color: #18181b !important;
}

.page-item.active .page-link,
.page-link:hover {
  background-color: #18181b !important;
  border-color: #18181b !important;
  color: #fafafa !important;
}

.dataTables_info {
  color: #71717a;
  font-size: 0.875rem;
  font-weight: 500;
}

.dataTables_length label,
.dataTables_filter label {
  color: #18181b;
  font-weight: 500;
}

/* Pagination Wrapper */
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
nav[role="navigation"] > div > p,
.pagination-wrapper nav[role="navigation"] > div.hidden,
.pagination-wrapper nav[role="navigation"] > div:not(:has(.pagination)),
.pagination-wrapper > div:last-child > nav > div:first-child,
nav[role="navigation"] .hidden,
nav[role="navigation"] > div.flex-1,
.pagination-wrapper p.text-sm,
.pagination-wrapper .leading-5,
.pagination-wrapper span.relative,
p:has(span.font-medium) {
  display: none !important;
}

.dataTables_wrapper .dataTables_paginate {
  padding: 1rem 0;
}

.dataTables_wrapper {
  padding: 0;
}

/* Fix layout - Length & Search sejajar */
.dataTables_wrapper .row:first-child {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #e4e4e7;
  background: #fafafa;
  display: flex !important;
  flex-wrap: wrap !important;
  align-items: center !important;
  justify-content: space-between !important;
}

.dataTables_wrapper .row:first-child > div {
  flex: 0 0 auto !important;
  width: auto !important;
  max-width: none !important;
  padding: 0.25rem 0.5rem !important;
}

.dataTables_wrapper .dataTables_length {
  display: flex !important;
  align-items: center !important;
  gap: 0.75rem !important;
}

.dataTables_wrapper .dataTables_length label {
  display: flex !important;
  align-items: center !important;
  gap: 0.75rem !important;
  margin-bottom: 0 !important;
  white-space: nowrap !important;
}

.dataTables_wrapper .dataTables_length select {
  margin: 0 0.5rem !important;
  width: 71px !important;
  display: inline-block !important;
}

.dataTables_wrapper .dataTables_filter {
  display: flex !important;
  align-items: center !important;
  gap: 0.5rem !important;
}

.dataTables_wrapper .dataTables_filter label {
  display: flex !important;
  align-items: center !important;
  gap: 0.5rem !important;
  margin-bottom: 0 !important;
  white-space: nowrap !important;
}

.dataTables_wrapper .dataTables_filter input {
  min-width: 200px !important;
}

.dataTables_wrapper .row:last-child {
  padding: 1rem 1.5rem;
  border-top: 1px solid #e4e4e7;
  background: #fafafa;
  display: flex !important;
  flex-wrap: wrap !important;
  align-items: center !important;
  justify-content: space-between !important;
}

.dataTables_wrapper .row:last-child > div {
  flex: 0 0 auto !important;
  width: auto !important;
  max-width: none !important;
  padding: 0.25rem 0.5rem !important;
}

/* Responsive - stack on mobile */
@media (max-width: 768px) {
  .dataTables_wrapper .row:first-child,
  .dataTables_wrapper .row:last-child {
    flex-direction: column !important;
    gap: 1rem !important;
    align-items: stretch !important;
  }

  .dataTables_wrapper .row:first-child > div,
  .dataTables_wrapper .row:last-child > div {
    width: 100% !important;
  }

  .dataTables_wrapper .dataTables_filter input {
    width: 100% !important;
  }
}

/* ========================================= */
/* TEXT COLORS */
/* ========================================= */
.text-primary {
  color: #18181b !important;
}

.text-success {
  color: #22c55e !important;
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

/* ========================================= */
/* ANIMATIONS */
/* ========================================= */
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

@section('content')
<!-- Loading Overlay -->
<div class="loading-overlay">
    <div class="spinner-border spinner-border-custom text-light" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<!-- Main Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-header-custom">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h4 class="mb-1 fw-bold">
                    <i class="ri-bill-line me-2"></i>Daftar Tagihan Belum Bayar
                </h4>
                <p class="mb-0 opacity-75 small">
                    Kelola dan kirim notifikasi tagihan ke pelanggan
                    <span class="badge bg-primary ms-2">{{ $totalTagihan ?? $tagihans->total() }} Total Tagihan</span>
                </p>
            </div>
            <div class="d-flex action-buttons mt-3 mt-md-0 gap-2">
                <button id="send-broadcast-push" class="btn btn-success btn-broadcast">
                    <i class="ri-notification-3-fill"></i>
                    Kirim Notifikasi ke Semua ({{ $totalTagihan ?? $tagihans->total() }})
                </button>
            </div>
        </div>
        
        <!-- Search Form -->
        <div class="mt-3">
            <form action="{{ route('push.notification.index') }}" method="GET" class="d-flex gap-2">
                <div class="input-group" style="max-width: 400px;">
                    <span class="input-group-text bg-white"><i class="ri-search-line"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, ID, atau WhatsApp..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-search-line me-1"></i> Cari
                </button>
                @if(request('search'))
                <a href="{{ route('push.notification.index') }}" class="btn btn-outline-secondary">
                    <i class="ri-close-line me-1"></i> Reset
                </a>
                @endif
            </form>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive p-3">
            <table class="table table-modern table-hover">
                <thead>
                    <tr>
                        <th><i class="ri-hashtag me-1"></i>No</th>
                        <th><i class="ri-user-3-line me-1"></i>Nama Pelanggan</th>
                        <th><i class="ri-shopping-bag-line me-1"></i>Paket</th>
                        <th><i class="ri-checkbox-circle-line me-1"></i>Status</th>
                        <th><i class="ri-calendar-line me-1"></i>Tanggal Mulai</th>
                        <th><i class="ri-calendar-check-line me-1"></i>Tanggal Berakhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tagihans as $index => $tagihan)
                    <tr id="row-{{ $tagihan['id'] }}">
                        <td class="fw-bold">{{ $tagihans->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="fw-semibold">{{ $tagihan['nama_lengkap'] }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-label-info">{{ $tagihan['paket']['nama_paket'] ?? '-' }}</span>
                        </td>
                        <td>
                            @php
                                $statusClass = match($tagihan['status_pembayaran']) {
                                    'Lunas' => 'bg-success',
                                    'Belum Bayar' => 'bg-warning',
                                    'Jatuh Tempo' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge badge-status {{ $statusClass }}">
                                {{ $tagihan['status_pembayaran'] }}
                            </span>
                        </td>
                        <td>{{ $tagihan['tanggal_mulai'] ?? '-' }}</td>
                        <td>{{ $tagihan['tanggal_berakhir'] ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="ri-inbox-line fs-1 text-muted mb-3 d-block"></i>
                            <p class="text-muted mb-0">Tidak ada tagihan yang belum dibayar</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Laravel Pagination -->
        @if($tagihans->hasPages())
        <div class="pagination-wrapper">
          <div class="pagination-info">
            Menampilkan <strong>{{ $tagihans->firstItem() ?? 0 }}</strong> - <strong>{{ $tagihans->lastItem() ?? 0 }}</strong>
            dari <strong>{{ $tagihans->total() }}</strong> tagihan
          </div>
          <div>
            {{ $tagihans->appends(request()->query())->onEachSide(2)->links('pagination::bootstrap-5') }}
          </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Fungsi helper untuk menampilkan loading
    function showLoading() {
        $('.loading-overlay').css('display', 'flex');
    }

    function hideLoading() {
        $('.loading-overlay').css('display', 'none');
    }

    // ========================================
    // TOMBOL BROADCAST PUSH NOTIFICATION
    // Fetch ALL IDs from backend (not just current page)
    // ========================================
    $('#send-broadcast-push').on('click', async function() {
        const btn = $(this);
        const originalText = btn.html();
        
        // Show loading state
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memuat...');

        try {
            // Fetch ALL tagihan IDs from backend
            const response = await fetch("{{ route('push.notification.all.ids') }}", {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            btn.prop('disabled', false).html(originalText);
            
            if (!data.success || !data.ids || data.ids.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Ada Data',
                    text: 'Tidak ada tagihan yang bisa dikirim.',
                    showCancelButton: false,
                    showDenyButton: false,
                    showCloseButton: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-warning'
                    },
                    buttonsStyling: false
                });
                return;
            }
            
            const allTagihanIds = data.ids;

        // MODAL KONFIRMASI - 2 BUTTON: YA & BATAL
        Swal.fire({
            title: 'Apakah Anda yakin?',
            html: `
                <p>Kirim notifikasi tagihan ke <strong>${allTagihanIds.length}</strong> pelanggan?</p>
                <p class="text-muted small mt-2">
                    <i class="ri-information-line"></i> Notifikasi akan dikirim ke semua pelanggan yang belum bayar
                </p>
            `,
            icon: 'question',
            showCancelButton: true,
            showDenyButton: false,
            showCloseButton: false,
            confirmButtonText: '<i class="ri-checkbox-circle-line me-2"></i>Ya, Kirim!',
            cancelButtonText: '<i class="ri-close-line me-2"></i>Batal',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            reverseButtons: false,
            allowOutsideClick: false,
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...');

                fetch("{{ route('tagihan.push') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ tagihan_ids: allTagihanIds })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    btn.prop('disabled', false).html(originalText);

                    console.log('Response data:', data);

                    if (data.queued) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Dikirim ke antrian',
                            html: `<small>Notifikasi untuk <strong>${data.total || allTagihanIds.length}</strong> tagihan sedang dikirim di background. Anda bisa lanjut bekerja.</small>`,
                            showConfirmButton: false,
                            timer: 4000,
                            timerProgressBar: true,
                        });
                    } else if (data.success && data.sent > 0) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Terkirim!',
                            html: `
                                <div class="text-center">
                                    <p class="mb-2"><strong class="text-success fs-4">${data.sent}</strong> notifikasi berhasil dikirim</p>
                                    ${data.ignored > 0 ? `<p class="text-muted small mb-0"><i class="ri-information-line"></i> ${data.ignored} pelanggan diabaikan (SID kosong)</p>` : ''}
                                </div>
                            `,
                            showCancelButton: false,
                            showDenyButton: false,
                            showCloseButton: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            },
                            buttonsStyling: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tidak Ada Yang Terkirim',
                            text: data.message || 'Tidak ada notifikasi yang berhasil dikirim. Pastikan pelanggan memiliki SID yang valid.',
                            showCancelButton: false,
                            showDenyButton: false,
                            showCloseButton: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-warning'
                            },
                            buttonsStyling: false
                        });
                    }
                })
                .catch(err => {
                    console.error('Error detail:', err);
                    btn.prop('disabled', false).html(originalText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Gagal mengirim notifikasi. Silakan coba lagi atau hubungi administrator.',
                        showCancelButton: false,
                        showDenyButton: false,
                        showCloseButton: false,
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-danger'
                        },
                        buttonsStyling: false
                    });
                });
            }
        });
        
        } catch (err) {
            console.error('Error fetching tagihan IDs:', err);
            btn.prop('disabled', false).html(originalText);
            Swal.fire({
                icon: 'error',
                title: 'Gagal Memuat Data',
                text: 'Tidak dapat mengambil data tagihan. Silakan refresh halaman.',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-danger'
                },
                buttonsStyling: false
            });
        }
    });

    // ========================================
    // TOMBOL BROADCAST INFO/IKLAN - 2 BUTTON KONFIRMASI
    // ========================================
    $('#send-broadcast-info').on('click', function() {
        // MODAL INPUT - 2 BUTTON: KIRIM & BATAL
        Swal.fire({
            title: '<i class="ri-megaphone-line me-2"></i>Kirim Info/Iklan',
            html: `
                <div class="text-start">
                    <label for="swal-input-message" class="form-label fw-bold">Pesan yang akan dikirim:</label>
                    <textarea
                        id="swal-input-message"
                        class="form-control"
                        rows="4"
                        placeholder="Contoh: Promo spesial bulan ini! Diskon 50% untuk semua paket internet"
                        maxlength="500"
                    ></textarea>
                    <small class="text-muted d-block mt-2">
                        <i class="ri-information-line"></i> Maksimal 500 karakter
                    </small>
                </div>
            `,
            showCancelButton: true,
            showDenyButton: false,
            showCloseButton: false,
            confirmButtonText: '<i class="ri-send-plane-fill me-2"></i>Kirim Sekarang',
            cancelButtonText: '<i class="ri-close-line me-2"></i>Batal',
            confirmButtonColor: '#17a2b8',
            cancelButtonColor: '#6c757d',
            reverseButtons: false,
            allowOutsideClick: false,
            focusConfirm: false,
            customClass: {
                confirmButton: 'btn btn-info me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false,
            preConfirm: () => {
                const message = document.getElementById('swal-input-message').value.trim();
                if (!message) {
                    Swal.showValidationMessage('Pesan tidak boleh kosong!');
                    return false;
                }
                if (message.length < 10) {
                    Swal.showValidationMessage('Pesan minimal 10 karakter!');
                    return false;
                }
                return message;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const message = result.value;
                const btn = $('#send-broadcast-info');
                const originalText = btn.html();
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...');
                showLoading();

                fetch("", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: message })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    hideLoading();
                    btn.prop('disabled', false).html(originalText);

                    console.log('Response data:', data);

                    if(data.success && data.sent > 0){
                        // ? MODAL SUCCESS - HANYA 1 BUTTON
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Terkirim!',
                            html: `
                                <div class="text-center">
                                    <p class="mb-2"><strong class="text-info fs-4">${data.sent}</strong> notifikasi info berhasil dikirim</p>
                                    ${data.ignored > 0 ? `<p class="text-muted small mb-0"><i class="ri-information-line"></i> ${data.ignored} pelanggan diabaikan (SID kosong)</p>` : ''}
                                </div>
                            `,
                            showCancelButton: false,
                            showDenyButton: false,
                            showCloseButton: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-info'
                            },
                            buttonsStyling: false
                        });
                    } else {
                        // ? MODAL WARNING - HANYA 1 BUTTON
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tidak Ada Yang Terkirim',
                            text: data.message || 'Tidak ada notifikasi yang berhasil dikirim. Pastikan pelanggan memiliki SID yang valid.',
                            showCancelButton: false,
                            showDenyButton: false,
                            showCloseButton: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-warning'
                            },
                            buttonsStyling: false
                        });
                    }
                })
                .catch(err => {
                    console.error('Error detail:', err);
                    hideLoading();
                    btn.prop('disabled', false).html(originalText);
                    // ? MODAL ERROR - HANYA 1 BUTTON
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Gagal mengirim notifikasi. Silakan coba lagi atau hubungi administrator.',
                        showCancelButton: false,
                        showDenyButton: false,
                        showCloseButton: false,
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-danger'
                        },
                        buttonsStyling: false
                    });
                });
            }
        });
    });
});
</script>

<style>
/* Custom SweetAlert2 Styling */
.swal2-input,
.swal2-textarea {
    border: 2px solid #e4e4e7 !important;
    border-radius: 8px !important;
    padding: 12px !important;
    font-size: 14px !important;
}

.swal2-input:focus,
.swal2-textarea:focus {
    border-color: #18181b !important;
    box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b !important;
}

.swal2-validation-message {
    background: #fef2f2 !important;
    color: #dc2626 !important;
    border: 1px solid #fecaca !important;
    border-radius: 6px !important;
    padding: 10px !important;
    margin-top: 10px !important;
}

/* Spinning icon animation */
.ri-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.2em;
}
</style>
@endsection
