@extends('layouts/layoutMaster')

@section('title', 'Data Laba Masuk')

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

.card-header-custom i:not(.btn-add i) {
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

/* ========== BANK SUMMARY CARDS ========== */
.bank-summary-card {
  background: #ffffff;
  border: 1px solid var(--gray-border);
  border-radius: 10px;
  padding: 1.25rem;
  transition: var(--transition);
}

.bank-summary-card:hover {
  border-color: #18181b;
  box-shadow: 0 4px 12px rgba(24, 24, 27, 0.1);
}

.bank-summary-card .bank-name {
  color: #71717a;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.bank-summary-card .bank-icon {
  color: #18181b;
}

.bank-summary-card .bank-total {
  color: #18181b;
  font-size: 1.25rem;
  font-weight: 700;
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

.bg-label-primary {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
}

/* ========== PAGINATION STYLES ========== */
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
  padding: 1.5rem 2rem;
  border-top: 1px solid #e4e4e7;
  background: #fafafa;
  border-radius: 0 0 16px 16px;
}

.income-icon {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: #18181b !important;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 2rem;
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

.income-header-info {
  text-align: center;
  padding: 1.5rem;
  background: #fafafa;
  border-radius: 10px;
  margin-bottom: 1.5rem;
  border: 1px solid #e4e4e7;
}

.income-amount {
  font-size: 1.75rem;
  font-weight: 700;
  color: #18181b;
  margin-bottom: 0.5rem;
}

.income-category {
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

  .pagination-wrapper {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
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

  .empty-state {
    padding: 3rem 1rem;
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
<script>
document.addEventListener("DOMContentLoaded", function() {
    function showLoading() {
        $('.loading-overlay').css('display', 'flex');
    }

    function hideLoading() {
        $('.loading-overlay').fadeOut(300);
    }

    // ? HANYA INISIALISASI DATATABLES JIKA ADA DATA (untuk sorting only, no paging/search)
    @if($incomes->count() > 0)
        const dtIncomeTable = $('.datatables-income').DataTable({
            paging: false,
            searching: false,
            ordering: true,
            info: false,
            responsive: false,
            dom: 'rt',
            columnDefs: [
              { orderable: false, targets: [0, -1] }
            ],
            language: {
                emptyTable: "Tidak ada data laba masuk tersedia",
                zeroRecords: "Tidak ditemukan data yang sesuai"
            }
        });
    @endif

    // Event Detail - gunakan event delegation
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const tr = $(this).closest('tr');
        const kode = tr.data('kode') || '-';
        const kategori = tr.data('kategori') || '-';
        const jumlah = tr.data('jumlah') || 0;
        const keterangan = tr.data('keterangan') || '-';
        const tanggalMasuk = tr.data('tanggal-masuk') || '-';
        const jamMasuk = tr.data('jam-masuk') || '-';

        const html = `
            <div class="income-header-info">
                <div class="income-icon mx-auto">
                    <i class="ri-money-dollar-circle-line"></i>
                </div>
                <div class="income-amount">Rp ${parseInt(jumlah).toLocaleString('id-ID')}</div>
                <div class="income-category">
                    <i class="ri-bookmark-line me-2"></i>${kategori}
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-information-line"></i>Informasi Pemasukan</h6>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-barcode-line"></i>Kode Transaksi
                    </span>
                    <span class="detail-value"><strong>${kode}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-bookmark-3-line"></i>Kategori
                    </span>
                    <span class="detail-value">
                        <span class="badge bg-label-primary">${kategori}</span>
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-money-dollar-box-line"></i>Jumlah Laba
                    </span>
                    <span class="detail-value">
                        <strong style="color: #18181b; font-size: 1.1rem;">Rp ${parseInt(jumlah).toLocaleString('id-ID')}</strong>
                    </span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-calendar-event-line"></i>Waktu Pemasukan</h6>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-calendar-check-line"></i>Tanggal Masuk
                    </span>
                    <span class="detail-value">${tanggalMasuk}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-time-line"></i>Jam Masuk
                    </span>
                    <span class="detail-value">${jamMasuk}</span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-file-text-line"></i>Keterangan</h6>
                <div class="detail-item">
                    <span class="detail-value">${keterangan}</span>
                </div>
            </div>
        `;

        $('#detailModal .modal-body').html(html);
        $('#detailModal').modal('show');
    });

    // Event DELETE - gunakan event delegation
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Konfirmasi Penghapusan',
            text: 'Yakin ingin menghapus data laba masuk ini? Data tidak dapat dikembalikan!',
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
                        text: 'Data laba masuk berhasil dihapus.',
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
                    <i class="ri-money-dollar-circle-line me-2 text-primary"></i>Data Laba Masuk
                </h4>
                <p class="mb-0 text-muted small">
                    Kelola dan monitor pemasukan laba perusahaan
                    @if(request('filter_month') || request('filter_year'))
                        <span class="badge bg-label-dark ms-2">
                            <i class="ri-filter-line me-1"></i>Filter: {{ $monthLabel ?? '-' }}
                        </span>
                    @endif
                </p>
            </div>
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <!-- Search Input -->
                <form action="{{ route('income.index') }}" method="GET" class="d-flex align-items-center">
                    @if(request('filter_month'))
                        <input type="hidden" name="filter_month" value="{{ request('filter_month') }}">
                    @endif
                    @if(request('filter_year'))
                        <input type="hidden" name="filter_year" value="{{ request('filter_year') }}">
                    @endif
                    <div class="input-group" style="width: 300px;">
                        <span class="input-group-text bg-white border-end-0" style="border-color: #e4e4e7;">
                            <i class="ri-search-line text-muted"></i>
                        </span>
                        <input type="text" 
                               class="form-control border-start-0 ps-0" 
                               name="search" 
                               placeholder="Cari kode, kategori..." 
                               value="{{ request('search') }}"
                               style="border-color: #e4e4e7;">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-search-line me-1" style="color: #fff !important;"></i>Cari
                        </button>
                        @if(request('search'))
                            <a href="{{ route('income.index', request()->except('search')) }}" class="btn btn-outline-danger" title="Clear search">
                                <i class="ri-close-line"></i>
                            </a>
                        @endif
                    </div>
                </form>
                
                <div class="d-flex gap-2">
                    <a href="{{ route('income.export', request()->only(['filter_month', 'filter_year', 'search'])) }}" class="btn btn-outline-secondary" title="Export Excel">
                        <i class="ri-file-excel-line me-1"></i>Export
                    </a>
                </div>
                <a href="{{ route('income.create') }}" class="btn btn-primary btn-add">
                    <i class="ri-add-circle-line me-2"></i>Tambah Laba Masuk
                </a>
            </div>
        </div>
        
        {{-- Search Result Info --}}
        @if(request('search'))
        <div class="mt-3 pt-3 border-top">
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-muted">
                    <i class="ri-search-line me-1"></i>
                    Hasil pencarian untuk: <strong>"{{ request('search') }}"</strong>
                    <span class="badge bg-label-dark ms-2">{{ $incomes->total() }} data</span>
                </span>
                <a href="{{ route('income.index', request()->except('search')) }}" class="btn btn-sm btn-outline-danger">
                    <i class="ri-close-line me-1"></i>Hapus Filter
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- Total Pemasukan Per Tanggal -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h6 class="mb-0 fw-bold"><i class="ri-calendar-check-line me-2"></i>Total Pemasukan Per Tanggal</h6>
                <small class="text-muted">Periode: {{ $monthLabel ?? '-' }}</small>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                <form action="{{ route('income.index') }}" method="GET" class="d-flex align-items-center gap-2">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <select name="filter_month" class="form-select form-select-sm border-0 bg-light fw-semibold" style="width: auto; cursor: pointer;" onchange="this.form.submit()">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $filterMonth == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->locale('id')->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                    <select name="filter_year" class="form-select form-select-sm border-0 bg-light fw-semibold" style="width: auto; cursor: pointer;" onchange="this.form.submit()">
                        @foreach(range(date('Y'), date('Y') - 5) as $y)
                            <option value="{{ $y }}" {{ $filterYear == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-hover mb-0">
                    <thead class="bg-light sticky-top">
                        <tr>
                            <th class="ps-4">Tanggal</th>
                            <th class="text-end pe-4">Total Pemasukan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailyTotals as $daily)
                            <tr>
                                <td class="ps-4 border-bottom-0">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-light rounded-circle me-3 d-flex align-items-center justify-content-center">
                                            <i class="ri-calendar-line text-muted"></i>
                                        </div>
                                        <div>
                                            <span class="fw-semibold d-block text-dark">
                                                {{ \Carbon\Carbon::parse($daily->date)->locale('id')->translatedFormat('l, d F Y') }}
                                            </span>
                                            @if(\Carbon\Carbon::parse($daily->date)->isToday())
                                                <span class="badge bg-label-success" style="font-size: 0.7rem;">Hari Ini</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end pe-4 border-bottom-0">
                                    <span class="fw-bold text-dark">Rp {{ number_format($daily->total, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center py-4 text-muted">Belum ada data pemasukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($dailyTotals->count() > 0)
                    <tfoot style="background-color: #18181b;">
                        <tr>
                            <td class="ps-4 fw-bold py-3" style="color: #ffffff !important;">
                                <i class="ri-money-dollar-circle-line me-2" style="color: #ffffff !important;"></i>Total {{ $monthLabel ?? 'Bulan Ini' }}
                            </td>
                            <td class="text-end pe-4 fw-bold py-3" style="font-size: 1.1rem; color: #ffffff !important;">
                                Rp {{ number_format($monthlyTotal ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- TABLE SECTION --}}
    <div class="card-body p-0">
        <div class="table-responsive p-3">
            @if($incomes->count() > 0)
                <table class="datatables-income table table-modern table-hover">
                    <thead>
                        <tr>
                            <th><i class="ri-hashtag me-1"></i>No</th>
                            <th><i class="ri-barcode-line me-1"></i>Kode</th>
                            <th><i class="ri-folder-line me-1"></i>Kategori</th>
                            <th><i class="ri-file-text-line me-1"></i>Keterangan</th>
                            <th><i class="ri-bank-card-line me-1"></i>Tipe Pembayaran</th>
                            <th><i class="ri-money-dollar-box-line me-1"></i>Jumlah</th>
                            <th><i class="ri-calendar-check-line me-1"></i>Tanggal Masuk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($incomes as $i)
                        <tr>
                            <td class="text-muted fw-semibold">{{ ($incomes->firstItem() ?? 1) + $loop->index }}</td>

                            <td>
                                <span class="badge bg-label-dark">{{ $i->kode ?? '-' }}</span>
                            </td>

                            <td>
                                <strong style="font-size:0.9rem;">{{ $i->kategori ?? '-' }}</strong>
                            </td>

                            <td>
                                <span class="text-muted">{{ $i->keterangan ?: '-' }}</span>
                            </td>

                            <td>
                                @if(strtolower($i->tipe_pembayaran ?? '') === 'cash' || empty($i->tipe_pembayaran))
                                    <span class="badge" style="background:#f4f4f5;color:#18181b;border:1px solid #e4e4e7;">
                                        <i class="ri-money-dollar-circle-line me-1"></i>Cash / Tunai
                                    </span>
                                @else
                                    <span class="badge" style="background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;">
                                        <i class="ri-bank-line me-1"></i>Transfer
                                    </span>
                                @endif
                            </td>

                            <td>
                                <strong style="color: #18181b;">Rp {{ number_format($i->jumlah, 0, ',', '.') }}</strong>
                            </td>

                            <td>{{ \Carbon\Carbon::parse($i->tanggal_masuk)->format('d M Y H:i') }}</td>
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
                    <h5>Belum ada data laba masuk</h5>
                    <p>Mulai tambahkan data laba masuk untuk mengelola pemasukan</p>
                    <a href="{{ route('income.create') }}" class="btn btn-primary">
                        <i class="ri-add-circle-line me-2"></i>Tambah Laba Masuk Pertama
                    </a>
                </div>
            @endif
        </div>

    <div class="pagination-wrapper">
      <div class="pagination-info">
        Menampilkan <strong>{{ $incomes->firstItem() ?? 0 }}</strong> - <strong>{{ $incomes->lastItem() ?? 0 }}</strong>
        dari <strong>{{ $incomes->total() }}</strong> data
      </div>
      <div>
        {{ $incomes->appends(request()->query())->onEachSide(1)->links('pagination::custom-always') }}
      </div>
    </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary py-4">
                <h5 class="modal-title text-white fw-bold">
                    <i class="ri-information-line me-2"></i>Detail Laba Masuk
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
