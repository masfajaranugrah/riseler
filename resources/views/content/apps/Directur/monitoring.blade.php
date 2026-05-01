@extends('layouts/layoutMaster')

@section('title', $pageTitle)

@php
use Illuminate\Support\Str;
@endphp

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
<style>
  :root {
    --primary-color: #0f172a;
    --secondary-color: #f8fafc;
    --border-color: #e2e8f0;
    --text-primary: #0f172a;
    --text-secondary: #64748b;
    --text-muted: #94a3b8;
    --radius: 0.75rem;
    --radius-lg: 1rem;
    --shadow: 0 1px 3px rgba(15, 23, 42, 0.08), 0 1px 2px rgba(15, 23, 42, 0.04);
  }

  body {
    background: #f8fafc;
    color: var(--text-primary);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  }

  .stat-cards {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.75rem;
    margin-bottom: 1rem;
  }

  .stat-card {
    background: #fff;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 1.2rem 1.3rem;
    box-shadow: var(--shadow);
    display: flex;
    align-items: center;
    gap: 0.875rem;
  }

  .stat-card-icon {
    width: 46px;
    height: 46px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
  }

  .icon-total { background: #e2e8f0; color: #0f172a; }
  .icon-noted { background: #dcfce7; color: #166534; }
  .icon-marketing { background: #dbeafe; color: #1d4ed8; }

  .stat-value {
    font-size: 1.45rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.25rem;
  }

  .stat-label {
    color: var(--text-secondary);
    font-size: 0.76rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-weight: 600;
  }

  .card-main {
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
    background: #fff;
    overflow: hidden;
  }

  .page-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
  }

  .page-header h4 {
    margin: 0 0 0.2rem;
    font-size: 1.15rem;
    font-weight: 700;
  }

  .page-header p {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.84rem;
  }

  .btn-shadcn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    min-height: 38px;
    padding: 0 0.95rem;
    border-radius: 0.7rem;
    border: 1px solid var(--border-color);
    background: #fff;
    color: var(--text-primary);
    text-decoration: none;
    font-size: 0.82rem;
    font-weight: 600;
  }

  .btn-shadcn:hover {
    background: var(--secondary-color);
    color: var(--text-primary);
  }

  .btn-shadcn-icon {
    width: 38px;
    padding: 0;
  }

  .stage-switcher {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    padding: 0.9rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
    background: #fff;
  }

  .stage-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.65rem 0.95rem;
    border-radius: 999px;
    border: 1px solid var(--border-color);
    background: #fff;
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 600;
  }

  .stage-pill.is-active {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: #fff;
  }

  .toolbar {
    padding: 0.9rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
    background: var(--secondary-color);
  }

  .search-box {
    position: relative;
    max-width: 360px;
  }

  .search-box i {
    position: absolute;
    left: 0.8rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
  }

  .search-box input {
    width: 100%;
    height: 40px;
    border: 1px solid var(--border-color);
    border-radius: 0.7rem;
    padding: 0 0.9rem 0 2.35rem;
    background: #fff;
    outline: none;
  }

  .table-wrap {
    overflow-x: auto;
  }

  .table-clean {
    width: 100%;
    border-collapse: collapse;
  }

  .table-clean thead th {
    background: var(--secondary-color);
    color: var(--text-secondary);
    font-size: 0.74rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 0.8rem 1rem;
    border-bottom: 1px solid var(--border-color);
    white-space: nowrap;
  }

  .table-clean tbody td {
    padding: 0.9rem 1rem;
    border-bottom: 1px solid var(--border-color);
    vertical-align: top;
    font-size: 0.83rem;
  }

  .table-clean tbody tr:hover {
    background: #fbfdff;
  }

  .cell-name {
    font-weight: 700;
    font-size: 0.85rem;
    margin-bottom: 0.15rem;
  }

  .cell-sub {
    font-size: 0.72rem;
    color: var(--text-muted);
  }

  .cell-note {
    max-width: 320px;
    color: var(--text-secondary);
    white-space: pre-wrap;
    line-height: 1.5;
  }

  .cell-note-compact,
  .cell-address-compact {
    max-width: 200px;
    color: var(--text-secondary);
    line-height: 1.4;
    font-size: 0.8rem;
  }

  .cell-address {
    max-width: 220px;
    color: var(--text-secondary);
    line-height: 1.45;
  }

  .cell-wa {
    color: #15803d;
    text-decoration: none;
    font-weight: 600;
  }

  .badge-status {
    display: inline-flex;
    align-items: center;
    padding: 0.3rem 0.65rem;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 700;
  }

  .badge-progress {
    background: #fef3c7;
    color: #92400e;
  }

  .badge-approve {
    background: #dcfce7;
    color: #166534;
  }

  .badge-reject {
    background: #fee2e2;
    color: #b91c1c;
  }

  .badge-default {
    background: #e2e8f0;
    color: #475569;
  }

  .stage-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.35rem 0.7rem;
    border-radius: 999px;
    background: #eff6ff;
    color: #1d4ed8;
    font-size: 0.74rem;
    font-weight: 700;
  }

  .desktop-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.4rem;
    flex-wrap: wrap;
  }

  .btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 0.5rem;
    border: 1px solid var(--border-color);
    color: #334155;
    background: #fff;
    text-decoration: none;
    font-size: 0.86rem;
  }

  .btn-action:hover {
    background: #f8fafc;
    color: #0f172a;
  }

  .btn-action-danger {
    color: #dc2626;
    border-color: #fecaca;
  }

  .btn-action-danger:hover {
    background: #fef2f2;
    color: #b91c1c;
  }

  .detail-label {
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.3rem;
  }

  .detail-section {
    padding: 0.85rem;
    background: var(--secondary-color);
    border-radius: var(--radius);
    border: 1px solid var(--border-color);
  }

  .detail-row {
    display: flex;
    justify-content: space-between;
    gap: 0.6rem;
    padding: 0.3rem 0;
    font-size: 0.82rem;
  }

  .detail-row:not(:last-child) {
    border-bottom: 1px solid var(--border-color);
  }

  .detail-row-label {
    color: var(--text-secondary);
    white-space: nowrap;
  }

  .detail-row-value {
    color: var(--text-primary);
    font-weight: 600;
    text-align: right;
  }

  .mobile-cards {
    display: none;
    padding: 0.8rem;
  }

  .m-card {
    background: #fff;
    border: 1px solid var(--border-color);
    border-radius: 1rem;
    padding: 1rem;
    box-shadow: var(--shadow);
    margin-bottom: 0.8rem;
  }

  .m-card-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 0.75rem;
    margin-bottom: 0.7rem;
  }

  .m-card-name {
    font-size: 0.95rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
  }

  .m-detail {
    font-size: 0.82rem;
    color: var(--text-secondary);
    margin-bottom: 0.35rem;
    line-height: 1.45;
  }

  .m-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
    margin-top: 0.65rem;
  }

  .m-actions .btn-shadcn {
    width: 100%;
    min-height: 34px;
    padding: 0 0.7rem;
    font-size: 0.78rem;
    border-radius: 0.55rem;
  }

  .m-actions .btn-danger-soft {
    border-color: #fecaca;
    color: #dc2626;
    background: #fff;
  }

  .m-actions .btn-danger-soft:hover {
    background: #fef2f2;
    color: #b91c1c;
  }

  .empty-state {
    padding: 3rem 1.5rem;
    text-align: center;
    color: var(--text-secondary);
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

  @media (max-width: 991px) {
    .stat-cards {
      grid-template-columns: 1fr;
    }

    .table-wrap {
      display: none;
    }

    .mobile-cards {
      display: block;
    }
  }

  @media (max-width: 767px) {
    .page-header,
    .stage-switcher,
    .toolbar,
    .pagination-wrapper {
      padding-left: 1rem;
      padding-right: 1rem;
    }

    .page-header {
      flex-direction: column;
      align-items: flex-start;
    }

    .search-box {
      max-width: 100%;
    }
  }
</style>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const escapeHtml = (value) => {
        const text = value ?? '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };
    const searchInput = document.getElementById('tableSearch');
    const tableRows = document.querySelectorAll('tr[data-searchable]');
    const mobileCards = document.querySelectorAll('.m-card[data-searchable]');
    const noResultsRow = document.getElementById('noResultsRow');

    if (searchInput) {
        searchInput.addEventListener('search', function() {
            this.form.submit();
        });
    }

    document.querySelectorAll('.btn-delete-directur').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const form = this.closest('form.delete-directur-form');
            if (!form) return;

            Swal.fire({
                title: 'Hapus data ini?',
                text: 'Data pelanggan yang dihapus tidak bisa dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn btn-outline-secondary',
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    $(document).on('click', '.btn-detail-directur', function (e) {
        e.preventDefault();
        const target = $(this).closest('tr').length ? $(this).closest('tr') : $(this).closest('.m-card');
        if (!target.length) return;

        const data = {
            nomerId: target.data('nomer-id'),
            nama: target.data('nama'),
            marketing: target.data('marketing-name'),
            marketingEmail: target.data('marketing-email'),
            whatsapp: target.data('whatsapp'),
            progress: target.data('progress'),
            status: target.data('status'),
            note: target.attr('data-progress-note'),
            alamat: target.data('alamat'),
            kecamatan: target.data('kecamatan'),
            kabupaten: target.data('kabupaten'),
            createdAt: target.data('created-at'),
        };

        const safe = {
            nomerId: escapeHtml(data.nomerId),
            nama: escapeHtml(data.nama),
            marketing: escapeHtml(data.marketing),
            marketingEmail: escapeHtml(data.marketingEmail),
            whatsapp: escapeHtml(data.whatsapp),
            progress: escapeHtml(data.progress),
            status: escapeHtml(data.status),
            note: escapeHtml(data.note),
            alamat: escapeHtml(data.alamat),
            kecamatan: escapeHtml(data.kecamatan),
            kabupaten: escapeHtml(data.kabupaten),
            createdAt: escapeHtml(data.createdAt),
        };

        const html = `
            <div class="text-center mb-3 pb-3" style="border-bottom: 1px solid var(--border-color);">
                <div class="cell-name" style="font-size: 1rem;">${safe.nama || '-'}</div>
                <span class="cell-sub">${safe.nomerId || '-'}</span>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="detail-label">Info Progress</div>
                    <div class="detail-section">
                        <div class="detail-row">
                            <span class="detail-row-label">Status</span>
                            <span class="detail-row-value">${safe.status || '-'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-row-label">Tahap</span>
                            <span class="detail-row-value">${safe.progress || '-'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-row-label">Diinput Oleh</span>
                            <span class="detail-row-value">${safe.marketing || '-'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-row-label">Email</span>
                            <span class="detail-row-value">${safe.marketingEmail || '-'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-row-label">WhatsApp</span>
                            <span class="detail-row-value">${safe.whatsapp || '-'}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-label">Alamat</div>
                    <div class="detail-section">
                        <div class="detail-row">
                            <span class="detail-row-label">Jalan</span>
                            <span class="detail-row-value">${safe.alamat || '-'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-row-label">Kecamatan</span>
                            <span class="detail-row-value">${safe.kecamatan || '-'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-row-label">Kabupaten</span>
                            <span class="detail-row-value">${safe.kabupaten || '-'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-row-label">Dibuat</span>
                            <span class="detail-row-value">${safe.createdAt || '-'}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="detail-label">Catatan Progress</div>
                    <div class="detail-section">
                        <div style="white-space: pre-wrap; color: var(--text-secondary); line-height: 1.5; font-size: 0.82rem;">
                            ${safe.note || 'Belum ada catatan progress.'}
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#detailModal .modal-body').html(html);
        $('#detailModal').modal('show');
    });
});
</script>
@endsection

@section('content')
@php
    $routeMap = [
        'all' => 'directur.pelanggan',
        'belum-progres' => 'directur.progres.belum-progres',
        'tarik-kabel' => 'directur.progres.tarik-kabel',
        'aktivasi' => 'directur.progres.aktivasi',
        'registrasi' => 'directur.progres.registrasi',
        'approve' => 'directur.approve',
    ];
@endphp

<div class="card-main">
    <div class="page-header">
        <div>
            <h4>{{ $pageTitle }}</h4>
            <p>{{ $pageDescription }}</p>
        </div>
        <a href="{{ route($routeMap[$selectedStageKey]) }}" class="btn-shadcn btn-shadcn-icon" title="Refresh">
            <i class="ri-refresh-line"></i>
        </a>
    </div>

    <div class="toolbar">
        <form action="{{ route($routeMap[$selectedStageKey]) }}" method="GET" class="d-flex gap-2">
            <div class="search-box">
                <i class="ri-search-line"></i>
                <input type="search" name="search" id="tableSearch" placeholder="Cari pelanggan, catatan, atau nama marketing..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn-shadcn btn-shadcn-icon" title="Cari">
                <i class="ri-search-line"></i>
            </button>
        </form>
    </div>

    <div class="table-wrap">
        <table class="table-clean">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Detail</th>
                    <th>Pelanggan</th>
                    <th>Nama User Input</th>
                    <th>Kontak</th>
                    <th>Progress</th>
                    <th>Catatan</th>
                    <th>Status</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pelanggan as $key => $p)
                @php
                    $currentProgress = blank($p->progres)
                        ? \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES
                        : $p->progres;
                    $statusKey = strtolower($p->status ?? 'pending');
                    $isPendingStage = \Illuminate\Support\Str::startsWith(
                        strtoupper(trim((string)($p->progress_note ?? ''))),
                        '[PENDING]'
                    );
                    $statusLabel = $statusKey === 'approve'
                        ? 'Approve'
                        : ($statusKey === 'reject'
                            ? 'Reject'
                            : (($isPendingStage && $currentProgress !== \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES) ? 'Pending' : ($currentProgress !== \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES ? 'Progres' : 'Belum Diproses')));
                    $statusClass = match(true) {
                        $statusKey === 'approve' => 'badge-approve',
                        $statusKey === 'reject' => 'badge-reject',
                        $currentProgress !== \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES => 'badge-progress',
                        default => 'badge-default'
                    };
                    $compactNote = \Illuminate\Support\Str::limit(
                        preg_replace('/\s+/', ' ', trim(preg_replace('/^\[PENDING\]\s*/i', '', preg_replace('/\*\(Diupdate oleh:.*?\)\*/s', '', $p->progress_note ?? 'Belum ada catatan progress.')))),
                        52
                    );
                    $updatedLog = 'Diupdate pada ' . optional($p->updated_at)->translatedFormat('d M Y H:i');
                    $updatedBy = optional($p->user)->name ?? '-';
                @endphp
                <tr
                    data-searchable="{{ strtolower($p->nama_lengkap . ' ' . $p->nomer_id . ' ' . ($p->progress_note ?? '') . ' ' . optional($p->user)->name . ' ' . optional($p->user)->email . ' ' . ($p->progres ?? '')) }}"
                    data-nomer-id="{{ $p->nomer_id }}"
                    data-nama="{{ $p->nama_lengkap }}"
                    data-marketing-name="{{ optional($p->user)->name }}"
                    data-marketing-email="{{ optional($p->user)->email }}"
                    data-whatsapp="{{ $p->no_whatsapp }}"
                    data-progress="{{ $currentProgress }}"
                    data-status="{{ $statusLabel }}"
                    data-progress-note="{{ e($p->progress_note ?: 'Belum ada catatan progress.') }}"
                    data-alamat="{{ $p->alamat_jalan }}"
                    data-kecamatan="{{ $p->kecamatan }}"
                    data-kabupaten="{{ $p->kabupaten }}"
                    data-created-at="{{ optional($p->created_at)->translatedFormat('d M Y H:i') }}">
                    <td>{{ $pelanggan->firstItem() + $key }}</td>
                    <td>
                        <button type="button" class="btn-action btn-detail-directur" title="Detail">
                            <i class="ri-eye-line"></i>
                        </button>
                    </td>
                    <td>
                        <div class="cell-name">{{ $p->nama_lengkap }}</div>
                        <div class="cell-sub">{{ $p->nomer_id }}</div>
                    </td>
                    <td>
                        <div class="cell-name">{{ optional($p->user)->name ?? '-' }}</div>
                    </td>
                    <td>
                        @if($p->no_whatsapp)
                        <a href="https://wa.me/{{ $p->no_whatsapp }}" target="_blank" class="cell-wa">{{ $p->no_whatsapp }}</a>
                        @else
                        <span class="cell-sub">-</span>
                        @endif
                    </td>
                    <td><span class="stage-badge">{{ $currentProgress }}</span></td>
                    <td>
                        <div class="cell-note-compact">{{ $compactNote }}</div>
                        <div class="cell-sub" style="margin-top: 0.35rem; font-size: 0.65rem;">
                            {{ $updatedLog }}
                        </div>
                    </td>
                    <td><span class="badge-status {{ $statusClass }}">{{ $statusLabel }}</span></td>
                    <td>
                        <div class="cell-address-compact">{{ \Illuminate\Support\Str::limit($p->alamat_jalan ?: '-', 38) }}</div>
                    </td>
                    <td>
                        <div class="desktop-actions">
                            <a href="{{ route('directur.pelanggan.edit', $p->id) }}" class="btn-action" title="Edit">
                                <i class="ri-edit-2-line"></i>
                            </a>
                            <form action="{{ route('directur.pelanggan.delete', $p->id) }}" method="POST" class="d-inline delete-directur-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-action btn-action-danger btn-delete-directur" title="Hapus">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10">
                        <div class="empty-state">Belum ada data pelanggan marketing di halaman ini.</div>
                    </td>
                </tr>
                @endforelse
                <tr id="noResultsRow" style="display:none;">
                    <td colspan="10">
                        <div class="empty-state">Tidak ada hasil yang cocok dengan pencarian.</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mobile-cards">
        @forelse($pelanggan as $p)
        @php
            $currentProgress = blank($p->progres)
                ? \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES
                : $p->progres;
            $statusKey = strtolower($p->status ?? 'pending');
            $isPendingStage = \Illuminate\Support\Str::startsWith(
                strtoupper(trim((string)($p->progress_note ?? ''))),
                '[PENDING]'
            );
            $statusLabel = $statusKey === 'approve'
                ? 'Approve'
                : ($statusKey === 'reject'
                    ? 'Reject'
                    : (($isPendingStage && $currentProgress !== \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES) ? 'Pending' : ($currentProgress !== \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES ? 'Progres' : 'Belum Diproses')));
            $statusClass = match(true) {
                $statusKey === 'approve' => 'badge-approve',
                $statusKey === 'reject' => 'badge-reject',
                $currentProgress !== \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES => 'badge-progress',
                default => 'badge-default',
            };
        @endphp
        <div
            class="m-card"
            data-searchable="{{ strtolower($p->nama_lengkap . ' ' . ($p->progress_note ?? '') . ' ' . optional($p->user)->name . ' ' . ($p->progres ?? '')) }}"
            data-nomer-id="{{ $p->nomer_id }}"
            data-nama="{{ $p->nama_lengkap }}"
            data-marketing-name="{{ optional($p->user)->name }}"
            data-marketing-email="{{ optional($p->user)->email }}"
            data-whatsapp="{{ $p->no_whatsapp }}"
            data-progress="{{ $currentProgress }}"
            data-status="{{ $statusLabel }}"
            data-progress-note="{{ e($p->progress_note ?: 'Belum ada catatan progress.') }}"
            data-alamat="{{ $p->alamat_jalan }}"
            data-kecamatan="{{ $p->kecamatan }}"
            data-kabupaten="{{ $p->kabupaten }}"
            data-created-at="{{ optional($p->created_at)->translatedFormat('d M Y H:i') }}">
            <div class="m-card-top">
                <div>
                    <div class="m-card-name">{{ $p->nama_lengkap }}</div>
                    <div class="cell-sub">{{ $p->nomer_id }}</div>
                </div>
                <span class="badge-status {{ $statusClass }}">{{ $statusLabel }}</span>
            </div>
            <div class="m-detail"><strong>Diinput oleh:</strong> {{ optional($p->user)->name ?? '-' }}</div>
            <div class="m-detail"><strong>Progress:</strong> {{ $currentProgress }}</div>
            <div class="m-detail"><strong>Catatan:</strong> {{ Str::limit(preg_replace('/\s+/', ' ', trim(preg_replace('/^\[PENDING\]\s*/i', '', $p->progress_note ?? 'Belum ada catatan progress.'))), 60) }}</div>
            <div class="m-detail"><strong>Log:</strong> {{ optional($p->updated_at)->translatedFormat('d M Y H:i') }} - {{ optional($p->user)->name ?? '-' }}</div>
            <div class="m-actions">
                <button type="button" class="btn-shadcn btn-detail-directur">
                    <i class="ri-eye-line"></i> Detail
                </button>
                <a href="{{ route('directur.pelanggan.edit', $p->id) }}" class="btn-shadcn">
                    <i class="ri-edit-2-line"></i> Edit
                </a>
                <form action="{{ route('directur.pelanggan.delete', $p->id) }}" method="POST" class="delete-directur-form">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn-shadcn btn-danger-soft btn-delete-directur" style="width:100%;">
                        <i class="ri-delete-bin-line"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="empty-state">Belum ada data pelanggan marketing di halaman ini.</div>
        @endforelse
    </div>

    @include('components.marketing-pagination', ['paginator' => $pelanggan])
</div>

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
