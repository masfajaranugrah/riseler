
@extends('layouts/layoutMaster')

@section('title', 'Approval Pelanggan')

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
<style>
  /* ========== SHADCN-LIKE THEME (Black & White) ========== */
  :root {
    --primary-color: #0f172a;
    --secondary-color: #f8fafc;
    --border-color: #e2e8f0;
    --text-primary: #0f172a;
    --text-secondary: #64748b;
    --text-muted: #94a3b8;
    --radius: 0.5rem;
    --radius-lg: 0.75rem;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
  }

  body {
    background-color: #f8fafc;
    color: var(--text-primary);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  }

  /* Card */
  .card-main {
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
    background: #fff;
    overflow: hidden;
  }

  /* Page Header */
  .page-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
    background: #fff;
  }

  .page-header h4 {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.125rem;
    letter-spacing: -0.025em;
  }

  .page-header p {
    font-size: 0.8125rem;
    color: var(--text-secondary);
    margin-bottom: 0;
  }

  /* Toolbar */
  .toolbar {
    padding: 0.75rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
    background: var(--secondary-color);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
  }

  /* Search */
  .search-box {
    position: relative;
    flex: 1;
    min-width: 200px;
    max-width: 320px;
  }

  .search-box .search-icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 0.875rem;
    pointer-events: none;
  }

  .search-box input {
    width: 100%;
    border: 1px solid var(--border-color);
    border-radius: var(--radius);
    padding: 0.5rem 0.75rem 0.5rem 2.25rem;
    font-size: 0.8125rem;
    color: var(--text-primary);
    background: #fff;
    transition: border-color 0.15s, box-shadow 0.15s;
    outline: none;
    height: 36px;
  }

  .search-box input::placeholder {
    color: var(--text-muted);
  }

  .search-box input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08);
  }

  /* Buttons */
  .btn-shadcn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.375rem;
    padding: 0 0.875rem;
    height: 36px;
    font-size: 0.8125rem;
    font-weight: 500;
    border-radius: var(--radius);
    border: 1px solid var(--border-color);
    background: #fff;
    color: var(--text-primary);
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
    white-space: nowrap;
  }

  .btn-shadcn:hover {
    background: var(--secondary-color);
    color: var(--text-primary);
  }

  .btn-shadcn-primary {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: #fff;
  }

  .btn-shadcn-primary:hover {
    background: #1e293b;
    border-color: #1e293b;
    color: #fff;
  }

  .btn-shadcn-icon {
    width: 36px;
    padding: 0;
  }

  .btn-shadcn-sm {
    height: 30px;
    padding: 0 0.625rem;
    font-size: 0.75rem;
  }

  .btn-shadcn-sm.btn-shadcn-icon {
    width: 30px;
    padding: 0;
  }

  .btn-shadcn-danger {
    border-color: #fecaca;
    color: #dc2626;
    background: #fff;
  }

  .btn-shadcn-danger:hover {
    background: #fef2f2;
    border-color: #fca5a5;
    color: #b91c1c;
  }

  /* Stat Cards */
  .stat-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
    margin-bottom: 1rem;
  }

  .stat-card {
    background: #fff;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: var(--shadow-sm);
    transition: box-shadow 0.15s;
  }

  .stat-card:hover {
    box-shadow: var(--shadow);
  }

  .stat-card-icon {
    width: 44px;
    height: 44px;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
  }

  .stat-card-icon.icon-total {
    background: #f1f5f9;
    color: #0f172a;
  }

  .stat-card-icon.icon-approve {
    background: #dcfce7;
    color: #15803d;
  }

  .stat-card-icon.icon-pending {
    background: #fef3c7;
    color: #92400e;
  }

  .stat-card-body .stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1;
    margin-bottom: 0.25rem;
  }

  .stat-card-body .stat-label {
    font-size: 0.75rem;
    color: var(--text-secondary);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.04em;
  }

  @media (max-width: 767px) {
    .stat-cards {
      grid-template-columns: repeat(3, 1fr);
      gap: 0.5rem;
    }
    .stat-card {
      padding: 0.75rem;
      flex-direction: column;
      align-items: flex-start;
      gap: 0.5rem;
    }
    .stat-card-icon {
      width: 36px;
      height: 36px;
      font-size: 1rem;
    }
    .stat-card-body .stat-value {
      font-size: 1.125rem;
    }
  }

  /* Table */
  .table-clean {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
  }

  .table-clean thead th {
    background: var(--secondary-color);
    color: var(--text-secondary);
    font-weight: 500;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.625rem 1rem;
    border-bottom: 1px solid var(--border-color);
    white-space: nowrap;
  }

  .table-clean tbody td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--border-color);
    font-size: 0.8125rem;
    color: var(--text-primary);
    vertical-align: middle;
  }

  .table-clean tbody tr {
    transition: background-color 0.1s;
  }

  .table-clean tbody tr:hover {
    background-color: var(--secondary-color);
  }

  .table-clean tbody tr:last-child td {
    border-bottom: none;
  }

  .table-clean tbody tr.row-hidden {
    display: none;
  }

  /* Cell styling */
  .cell-name {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.8125rem;
    margin-bottom: 0.125rem;
  }

  .cell-sub {
    font-size: 0.6875rem;
    color: var(--text-muted);
    font-family: 'SF Mono', 'Fira Code', monospace;
  }

  .cell-wa {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    color: var(--text-secondary);
    font-size: 0.8125rem;
    text-decoration: none;
    transition: color 0.15s;
  }

  .cell-wa:hover {
    color: #16a34a;
  }

  .cell-wa i {
    color: #16a34a;
  }

  .cell-address {
    max-width: 180px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-size: 0.8125rem;
    color: var(--text-primary);
  }

  .cell-address-sub {
    font-size: 0.6875rem;
    color: var(--text-muted);
  }

  .cell-date {
    font-size: 0.8125rem;
    color: var(--text-secondary);
    white-space: nowrap;
  }

  /* Badges */
  .badge-status {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.625rem;
    border-radius: 9999px;
    font-size: 0.6875rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    line-height: 1;
  }

  .badge-approve {
    background: #dcfce7;
    color: #15803d;
  }

  .badge-pending {
    background: #fef3c7;
    color: #92400e;
  }

  .badge-reject {
    background: #fee2e2;
    color: #b91c1c;
  }

  .badge-default {
    background: #f1f5f9;
    color: #475569;
  }

  .badge-id {
    display: inline-flex;
    align-items: center;
    padding: 0.125rem 0.5rem;
    border-radius: var(--radius);
    font-size: 0.6875rem;
    font-weight: 500;
    background: #f1f5f9;
    color: #475569;
    font-family: 'SF Mono', 'Fira Code', monospace;
  }

  /* Pagination */
  .pagination-wrapper {
    padding: 0.875rem 1.25rem;
    border-top: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.625rem;
    background: #fff;
  }

  .pagination-info {
    font-size: 0.75rem;
    color: var(--text-secondary);
  }

  .pagination-info strong {
    color: var(--text-primary);
    font-weight: 600;
  }

  .pagination {
    margin-bottom: 0;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.375rem;
    flex-wrap: wrap;
  }

  .pagination .page-item {
    margin: 0 !important;
  }

  .pagination .page-item .page-link {
    width: 36px;
    height: 36px;
    min-width: 36px;
    min-height: 36px;
    border-radius: 999px !important;
    border: 1px solid #d1d5db;
    color: #1f2937;
    background: #fff;
    font-size: 0.95rem;
    font-weight: 600;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
  }

  .pagination .page-item.active .page-link {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: #fff;
    box-shadow: 0 6px 14px -8px rgba(15, 23, 42, 0.75);
  }

  .pagination .page-item.disabled .page-link {
    color: #cbd5e1;
    background: #f8fafc;
    border-color: #e5e7eb;
    opacity: 1;
  }

  .pagination .page-item .page-link:hover {
    background: #f8fafc;
    color: #0f172a;
  }

  .pagination .page-item.active .page-link:hover {
    background-color: #1e293b;
    color: #fff;
  }

  .mobile-cards {
    padding: 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .m-card {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.25rem;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    transition: all 0.2s ease;
  }

  .m-card:hover {
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
    border-color: #cbd5e1;
  }

  .m-card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.875rem;
  }

  .m-card-top .badge-id {
    background: #f1f5f9;
    color: #475569;
    font-size: 0.75rem;
    padding: 0.25rem 0.625rem;
    border-radius: 9999px;
    font-weight: 600;
    letter-spacing: 0.02em;
  }

  .m-card-name {
    font-weight: 700;
    font-size: 1.125rem;
    color: #0f172a;
    line-height: 1.2;
    margin-bottom: 0.5rem;
  }

  .m-card-creator {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.8125rem;
    color: #64748b;
    background: #f8fafc;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    margin-bottom: 1.25rem;
  }
  .m-card-creator i {
    color: #94a3b8;
  }

  .m-card-detail-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    padding-bottom: 1.25rem;
    border-bottom: 1px solid #f1f5f9;
    margin-bottom: 1.25rem;
  }

  .m-card-detail {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    font-size: 0.8125rem;
    color: #475569;
    line-height: 1.4;
  }

  .m-card-detail i {
    font-size: 0.875rem;
    color: #94a3b8;
    margin-top: 0.125rem;
  }

  .m-card-detail a {
    color: #10b981;
    font-weight: 500;
    text-decoration: none;
  }
  .m-card-detail a:hover { text-decoration: underline; }

  .m-card-actions {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.5rem;
    margin-top: 1.25rem;
  }

  .m-card-actions .btn-shadcn {
    width: 100%;
    justify-content: center;
    height: 36px;
    font-size: 0.8125rem;
    border-radius: 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #475569;
    box-shadow: none;
    font-weight: 500;
  }
  .m-card-actions .btn-shadcn:hover {
    background: #f1f5f9;
    color: #0f172a;
  }
  .m-card-actions .btn-shadcn-danger {
    color: #ef4444;
  }
  .m-card-actions .btn-shadcn-danger:hover {
    background: #fef2f2;
    border-color: #fca5a5;
    color: #dc2626;
  }

  /* Detail Modal */
  .detail-label {
    font-size: 0.6875rem;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.375rem;
  }

  .detail-section {
    padding: 0.875rem;
    background: var(--secondary-color);
    border-radius: var(--radius);
    border: 1px solid var(--border-color);
  }

  .detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.375rem 0;
  }

  .detail-row:not(:last-child) {
    border-bottom: 1px solid var(--border-color);
  }

  .detail-row-label {
    font-size: 0.8125rem;
    color: var(--text-secondary);
  }

  .detail-row-value {
    font-size: 0.8125rem;
    font-weight: 500;
    color: var(--text-primary);
  }

  /* Empty State */
  .empty-state {
    padding: 3rem 1rem;
    text-align: center;
  }

  .empty-state i {
    font-size: 2.5rem;
    color: var(--border-color);
    margin-bottom: 0.75rem;
    display: block;
  }

  .empty-state p {
    color: var(--text-secondary);
    font-size: 0.8125rem;
    margin: 0;
  }

  /* Loading */
  .loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.85);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(4px);
  }

  /* No results from client-side search */
  .no-results-row {
    display: none;
  }

  .no-results-row td {
    text-align: center;
    padding: 2rem 1rem !important;
    color: var(--text-secondary);
  }

  /* Responsive */
  @media (max-width: 767px) {
    .page-header {
      padding: 1rem;
    }
    .toolbar {
      padding: 0.75rem 1rem;
    }
    .search-box {
      max-width: 100%;
      order: 10;
      min-width: 100%;
    }
    .stats-bar {
      padding: 0.75rem 1rem;
      gap: 1rem;
    }
    .pagination-wrapper {
      padding: 0.75rem 1rem;
      justify-content: center;
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
<script>
document.addEventListener("DOMContentLoaded", function() {
    // ====== Server-side search (lintas semua halaman data) ======
    const serverSearchInput = document.querySelector('input[name="search"]');
    if (serverSearchInput) {
        serverSearchInput.addEventListener('search', function() {
            this.form.submit();
        });
    }

    // ====== Detail Modal ======
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        const target = $(this).closest('tr').length ? $(this).closest('tr') : $(this).closest('.m-card');

        const data = {
            nomerId: target.data('nomer-id'),
            nama: target.data('nama'),
            whatsapp: target.data('whatsapp'),
            alamat: target.data('alamat'),
            rt: target.data('rt'),
            rw: target.data('rw'),
            kecamatan: target.data('kecamatan'),
            kabupaten: target.data('kabupaten'),
            tanggal: target.data('tanggal-mulai'),
            foto: target.data('foto-ktp'),
            status: target.data('status'),
            marketing: target.data('marketing-name'),
            email: target.data('marketing-email'),
            created: target.data('created-at'),
            progress_note: target.attr('data-progress-note'),
            deskripsi: target.attr('data-deskripsi')
        };

        let statusBadge = '';
        if (data.status === 'approve') statusBadge = '<span class="badge-status badge-approve">Approve</span>';
        else if (data.status === 'pending') statusBadge = '<span class="badge-status badge-pending">Belum Diproses</span>';
        else if (data.status === 'proses') statusBadge = '<span class="badge-status badge-pending">Progress</span>';
        else if (data.status === 'reject') statusBadge = '<span class="badge-status badge-reject">Reject</span>';
        else statusBadge = '<span class="badge-status badge-default">' + data.status + '</span>';

        const html = `
            <div class="text-center mb-3 pb-3" style="border-bottom: 1px solid var(--border-color);">
                <div class="cell-name" style="font-size: 1rem;">${data.nama}</div>
                <span class="badge-id mt-1">${data.nomerId}</span>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="detail-label">Info Kontak</div>
                    <div class="detail-section">
                        <div class="detail-row">
                            <span class="detail-row-label">WhatsApp</span>
                            <a href="https://wa.me/${data.whatsapp}" class="cell-wa" target="_blank">
                                <i class="ri-whatsapp-line"></i> ${data.whatsapp || '-'}
                            </a>
                        </div>
                        <div class="detail-row">
                            <span class="detail-row-label">Status</span>
                            <span>${statusBadge}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-row-label">Tgl Gabung</span>
                            <span class="detail-row-value">${data.tanggal || '-'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-row-label">Diinput Oleh</span>
                            <span class="detail-row-value">${data.marketing || '-'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-row-label">Email User</span>
                            <span class="detail-row-value">${data.email || '-'}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-label">Alamat</div>
                    <div class="detail-section">
                        <div class="detail-row-value mb-1">${data.alamat || '-'}</div>
                        <div class="detail-row-label">RT ${data.rt || '-'}/RW ${data.rw || '-'}, ${data.kecamatan || '-'}, ${data.kabupaten || '-'}</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="detail-label">Catatan & Deskripsi</div>
                    <div class="detail-section">
                        <div class="detail-row">
                            <span class="detail-row-label">Catatan Progres</span>
                            <span class="detail-row-value" style="white-space: pre-wrap;">${data.progress_note || '-'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-row-label">Deskripsi</span>
                            <span class="detail-row-value" style="white-space: pre-wrap;">${data.deskripsi || '-'}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="detail-label">Foto KTP</div>
                    <div class="detail-section text-center">
                        ${data.foto ? `<img src="${data.foto}" class="img-fluid rounded" style="max-height: 200px;">` : '<span class="detail-row-label">Tidak ada foto</span>'}
                    </div>
                </div>
            </div>
        `;

        $('#detailModal .modal-body').html(html);
        $('#detailModal').modal('show');
    });

    // ====== Delete Confirmation ======
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Hapus Data?',
            text: "Data yang dihapus tidak dapat dikembalikan",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#e2e8f0',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-dark me-2',
                cancelButton: 'btn btn-outline-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    });
});
</script>
@endsection

@section('content')
<!-- Loading Overlay -->
<div class="loading-overlay">
    <div class="spinner-border text-dark" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<div class="card-main">
    <!-- Page Header -->
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <div>
            <h4>Approval Pelanggan</h4>
            <p>Data pelanggan yang sudah disetujui (Approve)</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('marketing.approve') }}" class="btn-shadcn btn-shadcn-icon" title="Refresh">
                <i class="ri-refresh-line"></i>
            </a>
        </div>
    </div>

    <!-- Toolbar: Search -->
    <div class="toolbar">
        <!-- Server-side search (lintas semua data + pagination) -->
        <form action="{{ route('marketing.approve') }}" method="GET" class="d-flex gap-2">
            <div class="search-box">
                <i class="ri-search-line search-icon"></i>
                <input type="search" name="search" placeholder="Cari pelanggan, ID, WhatsApp..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn-shadcn btn-shadcn-icon" title="Cari">
                <i class="ri-search-line"></i>
            </button>
        </form>
    </div>

    <!-- Table Content -->
    <div class="card-body p-0">
        <!-- Desktop Table -->
        <div class="table-responsive d-none d-md-block">
            <table class="table-clean">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">#</th>
                        <th>Pelanggan</th>
                        <th>Kontak</th>
                        <th>Alamat</th>
                        <th>Status</th>
                        <th>Tgl Gabung</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="pelangganTableBody">
                    @forelse($pelanggan as $key => $p)
                    <tr data-searchable="{{ strtolower($p->nama_lengkap . ' ' . $p->nomer_id . ' ' . $p->no_whatsapp . ' ' . $p->alamat_jalan . ' ' . $p->kecamatan . ' ' . ($p->status ?? '')) }}"
                        data-nomer-id="{{ $p->nomer_id }}"
                        data-nama="{{ $p->nama_lengkap }}"
                        data-whatsapp="{{ $p->no_whatsapp }}"
                        data-alamat="{{ $p->alamat_jalan }}"
                        data-rt="{{ $p->rt }}"
                        data-rw="{{ $p->rw }}"
                        data-kecamatan="{{ $p->kecamatan }}"
                        data-kabupaten="{{ $p->kabupaten }}"
                        data-tanggal-mulai="{{ $p->tanggal_mulai ? \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') : '' }}"
                        data-foto-ktp="{{ $p->foto_ktp ? asset('storage/' . $p->foto_ktp) : '' }}"
                        data-status="{{ $p->status }}"
                        data-marketing-name="{{ optional($p->user)->name }}"
                        data-marketing-email="{{ optional($p->user)->email }}"
                        data-created-at="{{ $p->created_at }}"
                        data-progress-note="{{ $p->progress_note }}"
                        data-deskripsi="{{ $p->deskripsi }}">

                        <td style="text-align: center; color: var(--text-muted); font-size: 0.75rem;">{{ $pelanggan->firstItem() + $key }}</td>
                        <td>
                            <div class="cell-name">{{ $p->nama_lengkap }}</div>
                            <div class="cell-sub">{{ $p->nomer_id }}</div>
                            <div class="cell-sub">Dibuat oleh: {{ optional($p->user)->name ?? '-' }}</div>
                        </td>
                        <td>
                            @if($p->no_whatsapp)
                            <a href="https://wa.me/{{ $p->no_whatsapp }}" target="_blank" class="cell-wa">
                                <i class="ri-whatsapp-line"></i> {{ $p->no_whatsapp }}
                            </a>
                            @else
                            <span style="color: var(--text-muted);">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="cell-address" title="{{ $p->alamat_jalan }}">{{ $p->alamat_jalan ?? '-' }}</div>
                            @if($p->kecamatan)
                            <div class="cell-address-sub">{{ $p->kecamatan }}</div>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusKey = strtolower($p->status ?? 'pending');
                                $badgeClass = match($statusKey) {
                                    'approve' => 'badge-approve',
                                    'pending' => 'badge-pending',
                                    'proses' => 'badge-pending',
                                    'reject' => 'badge-reject',
                                    default => 'badge-default',
                                };
                                $statusLabel = match($statusKey) {
                                    'pending' => 'Belum Diproses',
                                    'proses' => 'Progress',
                                    default => ucfirst($p->status ?? 'Belum Diproses'),
                                };
                            @endphp
                            <span class="badge-status {{ $badgeClass }}">{{ $statusLabel }}</span>
                        </td>
                        <td>
                            <span class="cell-date">{{ $p->tanggal_mulai ? \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') : '-' }}</span>
                        </td>
                        <td>
                            @php
                                $isOwner = true; // Allow all marketing users to update
                            @endphp
                            <div class="d-flex justify-content-end gap-1">
                                <button class="btn-shadcn btn-shadcn-sm btn-shadcn-icon btn-detail" title="Detail">
                                    <i class="ri-eye-line"></i>
                                </button>
                                @if($isOwner)
                                <a href="{{ route('marketing.pelanggan.edit', $p->id) }}" class="btn-shadcn btn-shadcn-sm btn-shadcn-icon" title="Edit">
                                    <i class="ri-edit-2-line"></i>
                                </a>
                                <form action="{{ route('marketing.pelanggan.delete', $p->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-shadcn btn-shadcn-sm btn-shadcn-icon btn-shadcn-danger btn-delete" title="Hapus">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="ri-inbox-line"></i>
                                <p>Tidak ada data pelanggan ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                    <tr class="no-results-row" id="noResultsRow">
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="ri-search-line"></i>
                                <p>Tidak ada hasil yang cocok dengan pencarian.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mobile View (Cards) -->
        <div class="d-md-none mobile-cards">
            @forelse($pelanggan as $p)
            <div class="m-card"
                data-searchable="{{ strtolower($p->nama_lengkap . ' ' . $p->nomer_id . ' ' . $p->no_whatsapp . ' ' . $p->alamat_jalan) }}"
                data-nomer-id="{{ $p->nomer_id }}"
                data-nama="{{ $p->nama_lengkap }}"
                data-whatsapp="{{ $p->no_whatsapp }}"
                data-alamat="{{ $p->alamat_jalan }}"
                data-rt="{{ $p->rt }}"
                data-rw="{{ $p->rw }}"
                data-kecamatan="{{ $p->kecamatan }}"
                data-kabupaten="{{ $p->kabupaten }}"
                data-tanggal-mulai="{{ $p->tanggal_mulai ? \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') : '' }}"
                data-foto-ktp="{{ $p->foto_ktp ? asset('storage/' . $p->foto_ktp) : '' }}"
                data-status="{{ $p->status }}"
                data-marketing-name="{{ optional($p->user)->name }}"
                data-marketing-email="{{ optional($p->user)->email }}"
                data-created-at="{{ $p->created_at }}"
                data-progress-note="{{ $p->progress_note }}"
                data-deskripsi="{{ $p->deskripsi }}">

                <div class="m-card-top">
                    <span class="badge-id">{{ $p->nomer_id }}</span>
                    @php
                        $statusKey = strtolower($p->status ?? 'pending');
                        $badgeClass = match($statusKey) {
                            'approve' => 'badge-approve',
                            'pending' => 'badge-pending',
                            'proses' => 'badge-pending',
                            'reject' => 'badge-reject',
                            default => 'badge-default',
                        };
                        $statusLabel = match($statusKey) {
                            'pending' => 'Belum Diproses',
                            'proses' => 'Progress',
                            default => ucfirst($p->status ?? 'Belum Diproses'),
                        };
                    @endphp
                    <span class="badge-status {{ $badgeClass }}">{{ $statusLabel }}</span>
                </div>

                <div class="m-card-name">{{ $p->nama_lengkap }}</div>
                @php
                    $isOwner = true; // Allow all marketing users to update
                @endphp
                <div class="m-card-creator">
                    <i class="ri-user-star-line"></i>
                    <span>Dibuat oleh: <strong>{{ optional($p->user)->name ?? '-' }}</strong></span>
                </div>
                
                <div class="m-card-detail-group">
                    <div class="m-card-detail">
                        <i class="ri-map-pin-line"></i>
                        <span>{{ Str::limit($p->alamat_jalan ?? '-', 45) }}</span>
                    </div>
                    @if($p->no_whatsapp)
                    <div class="m-card-detail">
                        <i class="ri-whatsapp-line"></i>
                        <a href="https://wa.me/{{ $p->no_whatsapp }}" target="_blank">{{ $p->no_whatsapp }}</a>
                    </div>
                    @endif
                </div>

                <div class="m-card-actions">
                    <button class="btn-shadcn btn-shadcn-sm btn-detail">
                        <i class="ri-eye-line"></i> Detail
                    </button>
                    @if($isOwner)
                    <a href="{{ route('marketing.pelanggan.edit', $p->id) }}" class="btn-shadcn btn-shadcn-sm">
                        <i class="ri-edit-2-line"></i> Edit
                    </a>
                    <form action="{{ route('marketing.pelanggan.delete', $p->id) }}" method="POST" style="width: 100%;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn-shadcn btn-shadcn-sm btn-shadcn-danger btn-delete">
                            <i class="ri-delete-bin-line"></i> Hapus
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="empty-state">
                <i class="ri-inbox-line"></i>
                <p>Tidak ada data pelanggan.</p>
            </div>
            @endforelse
        </div>

        @include('components.marketing-pagination', ['paginator' => $pelanggan])
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="box-shadow: var(--shadow); border-radius: var(--radius-lg);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-color); padding: 1rem 1.25rem;">
                <h6 class="modal-title" style="font-weight: 700; font-size: 0.9375rem;">Detail Pelanggan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 0.625rem;"></button>
            </div>
            <div class="modal-body" style="padding: 1.25rem;">
                <!-- Content injected via JS -->
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border-color); padding: 0.75rem 1.25rem; background: var(--secondary-color);">
                <button type="button" class="btn-shadcn" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection
