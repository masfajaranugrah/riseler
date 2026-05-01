@extends('layouts/layoutMaster')

@section('title', 'Approval Pelanggan Marketing')

@section('vendor-style')
<style>
  :root {
    --c-bg: #f8fafc;
    --c-card: #ffffff;
    --c-border: #e2e8f0;
    --c-text: #0f172a;
    --c-text-secondary: #64748b;
    --c-text-muted: #94a3b8;
    --c-primary: #0f172a;
    --c-primary-hover: #1e293b;
    --c-focus-ring: rgba(15, 23, 42, 0.08);
    --radius: 0.5rem;
    --radius-lg: 0.75rem;
    --shadow: 0 1px 3px 0 rgb(0 0 0/0.1), 0 1px 2px -1px rgb(0 0 0/0.1);
  }

  body { background: var(--c-bg); }

  /* ── Stat Cards ── */
  .stat-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.875rem;
    margin-bottom: 1.25rem;
  }
  @media (max-width: 767px) { .stat-row { grid-template-columns: repeat(2, 1fr); } }

  .stat-card {
    background: var(--c-card);
    border: 1px solid var(--c-border);
    border-radius: var(--radius-lg);
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.875rem;
    box-shadow: var(--shadow);
    cursor: pointer;
    transition: border-color 0.15s, box-shadow 0.15s;
    text-decoration: none;
  }
  .stat-card:hover { border-color: var(--c-primary); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
  .stat-card.active { border-color: var(--c-primary); background: var(--c-primary); }
  .stat-card.active .stat-value,
  .stat-card.active .stat-label { color: #fff; }
  .stat-icon { width: 38px; height: 38px; border-radius: var(--radius); display: flex; align-items: center; justify-content: center; font-size: 1.125rem; flex-shrink: 0; }
  .icon-all { background: #f1f5f9; color: var(--c-text); }
  .icon-pending { background: #fef9c3; color: #92400e; }
  .icon-approve { background: #dcfce7; color: #166534; }
  .icon-reject { background: #fee2e2; color: #991b1b; }
  .stat-value { font-size: 1.25rem; font-weight: 700; color: var(--c-text); line-height: 1; }
  .stat-label { font-size: 0.75rem; color: var(--c-text-secondary); margin-top: 0.125rem; }

  /* ── Main Card ── */
  .main-card {
    background: var(--c-card);
    border: 1px solid var(--c-border);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
    overflow: hidden;
  }
  .page-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--c-border);
    background: var(--c-bg);
  }
  .page-header h4 { font-size: 1.1rem; font-weight: 700; color: var(--c-text); margin: 0; }
  .page-header p { font-size: 0.8125rem; color: var(--c-text-secondary); margin: 0.125rem 0 0; }

  /* ── Toolbar ── */
  .toolbar { padding: 0.875rem 1.5rem; border-bottom: 1px solid var(--c-border); display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; }
  .search-box { position: relative; flex: 1; max-width: 360px; }
  .search-icon { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--c-text-muted); font-size: 0.875rem; }
  .search-box input {
    width: 100%;
    padding: 0.5rem 0.75rem 0.5rem 2rem;
    border: 1px solid var(--c-border);
    border-radius: var(--radius);
    font-size: 0.8125rem;
    color: var(--c-text);
    height: 36px;
    outline: none;
    background: var(--c-card);
    transition: border-color 0.15s;
  }
  .search-box input:focus { border-color: var(--c-primary); }

  /* ── Table ── */
  .table-clean { width: 100%; border-collapse: collapse; }
  .table-clean thead th {
    padding: 0.75rem 1rem;
    font-size: 0.6875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--c-text-muted);
    border-bottom: 1px solid var(--c-border);
    background: var(--c-bg);
    white-space: nowrap;
  }
  .table-clean tbody tr { border-bottom: 1px solid var(--c-border); transition: background 0.1s; }
  .table-clean tbody tr:hover { background: #f8fafc; }
  .table-clean tbody td { padding: 0.875rem 1rem; font-size: 0.8125rem; color: var(--c-text); vertical-align: middle; }

  /* ── Badges ── */
  .badge-status { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.625rem; border-radius: 999px; font-size: 0.6875rem; font-weight: 600; }
  .badge-approve { background: #dcfce7; color: #166534; }
  .badge-pending { background: #fef9c3; color: #92400e; }
  .badge-reject { background: #fee2e2; color: #991b1b; }

  /* ── Progress Stepper (inline) ── */
  .stepper-mini { display: flex; align-items: center; gap: 0.5rem; }
  .step-dot {
    width: 22px; height: 22px; border-radius: 50%;
    border: 2px solid var(--c-border);
    background: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.625rem; color: var(--c-text-muted);
    position: relative;
    flex-shrink: 0;
  }
  .step-dot.done { background: #fff; border-color: #16a34a; color: #16a34a; font-size: 0.75rem; }
  .step-dot.current { background: #16a34a; border-color: #16a34a; color: #fff; box-shadow: 0 0 0 3px rgba(22,163,74,0.15); }
  .step-line { flex: 1; height: 2px; background: var(--c-border); }
  .step-line.done { background: #16a34a; }
  .step-label { font-size: 0.6875rem; color: var(--c-text-muted); white-space: nowrap; }

  /* ── Buttons ── */
  .btn-sh { display: inline-flex; align-items: center; justify-content: center; gap: 0.375rem; padding: 0 0.75rem; height: 32px; font-size: 0.75rem; font-weight: 500; border-radius: var(--radius); border: 1px solid var(--c-border); background: var(--c-card); color: var(--c-text); cursor: pointer; text-decoration: none; white-space: nowrap; transition: all 0.15s; }
  .btn-sh:hover { background: var(--c-bg); color: var(--c-text); }
  .btn-sh-approve { background: #dcfce7; border-color: #bbf7d0; color: #166534; }
  .btn-sh-approve:hover { background: #bbf7d0; color: #14532d; }
  .btn-sh-reject { background: #fee2e2; border-color: #fecaca; color: #991b1b; }
  .btn-sh-reject:hover { background: #fecaca; color: #7f1d1d; }
  .btn-sh-primary { background: var(--c-primary); border-color: var(--c-primary); color: #fff; }
  .btn-sh-primary:hover { background: var(--c-primary-hover); color: #fff; }

  /* ── Cell helpers ── */
  .cell-name { font-weight: 600; color: var(--c-text); }
  .cell-sub { font-size: 0.75rem; color: var(--c-text-muted); margin-top: 0.125rem; }
  .cell-wa { color: #16a34a; font-size: 0.8125rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem; }
  .cell-wa:hover { text-decoration: underline; }

  /* ── Empty state ── */
  .empty-state { text-align: center; padding: 3rem 1rem; color: var(--c-text-muted); }
  .empty-state i { font-size: 2rem; display: block; margin-bottom: 0.5rem; }
  .empty-state p { margin: 0; font-size: 0.875rem; }

  /* ── Toast / Alert ── */
  .alert-bar { padding: 0.75rem 1.25rem; border-radius: var(--radius); font-size: 0.8125rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; }
  .alert-bar.success { background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; }
  .alert-bar.error { background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; }

  /* ── Pagination ── */
  .pagination-wrapper { padding: 0.875rem 1.5rem; border-top: 1px solid var(--c-border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; }
  .pagination-info { font-size: 0.8125rem; color: var(--c-text-secondary); }
  .pagination { margin: 0; }

  /* ── Modal ── */
  .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 1050; align-items: center; justify-content: center; }
  .modal-overlay.show { display: flex; }
  .modal-box { background: var(--c-card); border-radius: var(--radius-lg); box-shadow: 0 20px 60px rgba(0,0,0,0.15); width: 100%; max-width: 460px; margin: 1rem; animation: modalIn 0.2s ease; }
  @keyframes modalIn { from { opacity:0; transform: scale(0.95); } to { opacity:1; transform: scale(1); } }
  .modal-box header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--c-border); display: flex; align-items: center; justify-content: space-between; }
  .modal-box header h6 { font-weight: 700; font-size: 0.9375rem; margin: 0; }
  .modal-box .m-body { padding: 1.25rem 1.5rem; }
  .modal-box .m-footer { padding: 0.875rem 1.25rem; border-top: 1px solid var(--c-border); display: flex; justify-content: flex-end; gap: 0.5rem; background: var(--c-bg); border-radius: 0 0 var(--radius-lg) var(--radius-lg); }
  .form-label { font-size: 0.8125rem; font-weight: 500; color: var(--c-text); margin-bottom: 0.375rem; display: block; }
  .form-control { width: 100%; border: 1px solid var(--c-border); border-radius: var(--radius); padding: 0.5rem 0.75rem; font-size: 0.8125rem; outline: none; transition: border-color 0.15s; }
  .form-control:focus { border-color: var(--c-primary); box-shadow: 0 0 0 3px var(--c-focus-ring); }

  /* ── Detail Modal Stepper ── */
  .stepper-full { display: flex; align-items: flex-start; justify-content: center; gap: 0; margin: 1.5rem 0 2rem; position: relative; }
  .stepper-full::before { content: ''; position: absolute; top: 16px; left: calc(16.67% + 13px); right: calc(16.67% + 13px); height: 2px; background: var(--c-border); z-index: 0; }
  .sf-step { text-align: center; flex: 1; z-index: 1; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; }
  .sf-circle { width: 32px; height: 32px; border-radius: 50%; border: 2px solid var(--c-border); background: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8125rem; font-weight: 600; color: var(--c-text-muted); transition: all 0.2s; }
  .sf-label { font-size: 0.6875rem; font-weight: 500; color: var(--c-text-muted); }
  .sf-step.done .sf-circle { border-color: #16a34a; color: #16a34a; }
  .sf-step.done .sf-label { color: #16a34a; }
  .sf-step.active .sf-circle { border-color: #16a34a; background: #16a34a; color: #fff; box-shadow: 0 0 0 5px rgba(22,163,74,0.12); }
  .sf-step.active .sf-label { color: #16a34a; font-weight: 700; }

  .detail-label { font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--c-text-muted); margin-bottom: 0.5rem; }
  .detail-section { background: var(--c-bg); border: 1px solid var(--c-border); border-radius: var(--radius); padding: 0.875rem; }
  .detail-row { display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem; padding: 0.3rem 0; }
  .detail-row + .detail-row { border-top: 1px solid var(--c-border); }
  .detail-row-label { font-size: 0.75rem; color: var(--c-text-muted); white-space: nowrap; }
  .detail-row-value { font-size: 0.8125rem; font-weight: 500; color: var(--c-text); text-align: right; }

  /* Mobile cards */
  .mobile-cards { padding: 0.75rem; display: flex; flex-direction: column; gap: 0.75rem; }
  .m-card { background: var(--c-card); border: 1px solid var(--c-border); border-radius: var(--radius-lg); padding: 1rem; }
  .m-card-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.625rem; }
  .badge-id { background: var(--c-bg); border: 1px solid var(--c-border); border-radius: var(--radius); padding: 0.2rem 0.5rem; font-size: 0.6875rem; font-weight: 600; color: var(--c-text-secondary); }
  .m-card-name { font-weight: 700; font-size: 0.9375rem; margin-bottom: 0.375rem; }
  .m-card-detail { font-size: 0.8125rem; color: var(--c-text-secondary); margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.375rem; }
  .m-card-actions { display: flex; gap: 0.5rem; margin-top: 0.875rem; padding-top: 0.875rem; border-top: 1px solid var(--c-border); }
  .m-card-stepper { margin: 0.625rem 0; display: flex; align-items: center; gap: 0.25rem; }

  @media (max-width: 767px) {
    .page-header { padding: 1rem; }
    .toolbar { padding: 0.75rem 1rem; }
  }
</style>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Server-side search event ──
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('search', function() {
            this.form.submit();
        });
    }

    // ── Detail Modal ──
    const detailModal = document.getElementById('detailModal');
    document.querySelectorAll('.btn-detail').forEach(btn => {
        btn.addEventListener('click', function () {
            const row = this.closest('[data-nama]');
            const d = {
                id:           row.dataset.id,
                nomerId:      row.dataset.nomerId,
                nama:         row.dataset.nama,
                whatsapp:     row.dataset.whatsapp,
                alamat:       row.dataset.alamat,
                rt:           row.dataset.rt,
                rw:           row.dataset.rw,
                kecamatan:    row.dataset.kecamatan,
                kabupaten:    row.dataset.kabupaten,
                tanggal:      row.dataset.tanggal,
                status:       row.dataset.status,
                progres:      row.dataset.progres,
                progressNote: row.dataset.progressNote,
                deskripsi:    row.dataset.deskripsi,
                marketing:    row.dataset.marketing,
            };

            // Status badge
            const badges = {
                approve: '<span class="badge-status badge-approve">Approve</span>',
                pending: '<span class="badge-status badge-pending">Belum Diproses</span>',
                proses: '<span class="badge-status badge-pending">Progress</span>',
                reject: '<span class="badge-status badge-reject">Reject</span>'
            };
            const statusBadge = badges[d.status] || `<span class="badge-status">${d.status}</span>`;

            // Stepper
            const steps = ['Belum Diproses', 'Tarik Kabel', 'Aktivasi', 'Registrasi'];
            const curIdx = steps.indexOf(d.progres);
            const isApproved = (d.status || '').toLowerCase() === 'approve';
            let sfHtml = '<div class="stepper-full">';
            steps.forEach((s, i) => {
                let cls = '';
                let icon = i + 1;
                if (isApproved) {
                    cls = 'done';
                    icon = '<i class="ri-check-line"></i>';
                } else if (curIdx !== -1) {
                    if (i < curIdx) { cls = 'done'; icon = '<i class="ri-check-line"></i>'; }
                    else if (i === curIdx) { cls = 'active'; }
                }
                sfHtml += `<div class="sf-step ${cls}"><div class="sf-circle">${icon}</div><div class="sf-label">${s}</div></div>`;
            });
            sfHtml += '</div>';

            document.getElementById('detailContent').innerHTML = `
                <div class="text-center mb-3 pb-3" style="border-bottom:1px solid var(--c-border)">
                    <div class="cell-name" style="font-size:1rem">${d.nama}</div>
                    <span class="badge-id mt-1">${d.nomerId}</span>
                </div>
                <div class="detail-label">Tahapan Progres</div>
                ${sfHtml}
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="detail-label">Info Kontak</div>
                        <div class="detail-section">
                            <div class="detail-row"><span class="detail-row-label">WhatsApp</span><a href="https://wa.me/${d.whatsapp}" class="cell-wa" target="_blank"><i class="ri-whatsapp-line"></i>${d.whatsapp||'-'}</a></div>
                            <div class="detail-row"><span class="detail-row-label">Status</span><span>${statusBadge}</span></div>
                            <div class="detail-row"><span class="detail-row-label">Tgl Mulai</span><span class="detail-row-value">${d.tanggal||'-'}</span></div>
                            <div class="detail-row"><span class="detail-row-label">Marketing</span><span class="detail-row-value">${d.marketing||'-'}</span></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-label">Alamat</div>
                        <div class="detail-section">
                            <div class="detail-row-value mb-1">${d.alamat||'-'}</div>
                            <div class="detail-row-label">RT ${d.rt||'-'}/RW ${d.rw||'-'}, ${d.kecamatan||'-'}, ${d.kabupaten||'-'}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="detail-label">Catatan Progres</div>
                        <div class="detail-section" style="white-space:pre-wrap">${d.progressNote||'-'}</div>
                    </div>
                    ${d.deskripsi ? `<div class="col-12"><div class="detail-label">Deskripsi</div><div class="detail-section" style="white-space:pre-wrap">${d.deskripsi}</div></div>` : ''}
                </div>
            `;
            detailModal.classList.add('show');
        });
    });

    // ── Close Detail Modal ──
    document.querySelectorAll('.close-detail').forEach(btn => {
        btn.addEventListener('click', () => detailModal.classList.remove('show'));
    });
    detailModal.addEventListener('click', e => { if (e.target === detailModal) detailModal.classList.remove('show'); });

    // ── Reject Modal ──
    const rejectModal = document.getElementById('rejectModal');
    let rejectForm = null;
    document.querySelectorAll('.btn-reject').forEach(btn => {
        btn.addEventListener('click', function () {
            rejectForm = this.closest('form.reject-form');
            document.getElementById('reject_nama').textContent = this.closest('[data-nama]').dataset.nama;
            rejectModal.classList.add('show');
        });
    });
    document.getElementById('confirmReject').addEventListener('click', function () {
        if (rejectForm) rejectForm.submit();
    });
    document.querySelectorAll('.close-reject').forEach(btn => {
        btn.addEventListener('click', () => rejectModal.classList.remove('show'));
    });
    rejectModal.addEventListener('click', e => { if (e.target === rejectModal) rejectModal.classList.remove('show'); });
});
</script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1">

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="alert-bar success"><i class="ri-checkbox-circle-line"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert-bar error"><i class="ri-error-warning-line"></i> {{ session('error') }}</div>
    @endif

    {{-- Stat Cards --}}
    <div class="stat-row">
        <a href="{{ route('admin.marketing.approval', ['status'=>'all']) }}"
           class="stat-card {{ $statusFilter==='all' ? 'active' : '' }}">
            <div class="stat-icon icon-all"><i class="ri-group-line"></i></div>
            <div><div class="stat-value">{{ $totalCount }}</div><div class="stat-label">Total</div></div>
        </a>
        <a href="{{ route('admin.marketing.approval', ['status'=>'proses']) }}"
           class="stat-card {{ $statusFilter==='proses' ? 'active' : '' }}">
            <div class="stat-icon icon-pending"><i class="ri-time-line"></i></div>
            <div><div class="stat-value">{{ $pendingCount }}</div><div class="stat-label">Belum Diproses</div></div>
        </a>
        <a href="{{ route('admin.marketing.approval', ['status'=>'approve']) }}"
           class="stat-card {{ $statusFilter==='approve' ? 'active' : '' }}">
            <div class="stat-icon icon-approve"><i class="ri-checkbox-circle-line"></i></div>
            <div><div class="stat-value">{{ $approveCount }}</div><div class="stat-label">Approved</div></div>
        </a>
        <a href="{{ route('admin.marketing.approval', ['status'=>'reject']) }}"
           class="stat-card {{ $statusFilter==='reject' ? 'active' : '' }}">
            <div class="stat-icon icon-reject"><i class="ri-close-circle-line"></i></div>
            <div><div class="stat-value">{{ $rejectCount }}</div><div class="stat-label">Rejected</div></div>
        </a>
    </div>

    {{-- Main Card --}}
    <div class="main-card">
        <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
            <div>
                <h4>Approval Pelanggan Marketing</h4>
                <p>Review dan setujui data pelanggan yang dikirim oleh tim marketing</p>
            </div>
            <a href="{{ route('admin.marketing.approval') }}" class="btn-sh"><i class="ri-refresh-line"></i> Refresh</a>
        </div>

        {{-- Toolbar --}}
        <div class="toolbar">
            <form action="{{ route('admin.marketing.approval') }}" method="GET" class="d-flex gap-2">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
                <div class="search-box">
                    <i class="ri-search-line search-icon"></i>
                    <input type="search" name="search" placeholder="Cari pelanggan..." value="{{ $search }}">
                </div>
                <button type="submit" class="btn-sh btn-sh-primary" title="Cari"><i class="ri-search-line"></i></button>
            </form>
        </div>

        {{-- Desktop Table --}}
        <div class="table-responsive d-none d-md-block">
            <table class="table-clean">
                <thead>
                    <tr>
                        <th style="width:44px; text-align:center">#</th>
                        <th>Pelanggan</th>
                        <th>Kontak</th>
                        <th>Progres Pemasangan</th>
                        <th>Status</th>
                        <th>Marketing</th>
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pelanggan as $key => $p)
                    <tr data-searchable="{{ strtolower($p->nama_lengkap . ' ' . $p->nomer_id . ' ' . $p->no_whatsapp . ' ' . $p->kecamatan . ' ' . $p->status . ' ' . $p->progres) }}"
                        data-id="{{ $p->id }}"
                        data-nomer-id="{{ $p->nomer_id }}"
                        data-nama="{{ $p->nama_lengkap }}"
                        data-whatsapp="{{ $p->no_whatsapp }}"
                        data-alamat="{{ $p->alamat_jalan }}"
                        data-rt="{{ $p->rt }}"
                        data-rw="{{ $p->rw }}"
                        data-kecamatan="{{ $p->kecamatan }}"
                        data-kabupaten="{{ $p->kabupaten }}"
                        data-tanggal="{{ $p->tanggal_mulai ? \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') : '' }}"
                        data-status="{{ $p->status }}"
                        data-progres="{{ $p->progres }}"
                        data-progress-note="{{ $p->progress_note }}"
                        data-deskripsi="{{ $p->deskripsi }}"
                        data-marketing="{{ optional($p->user)->name }}">

                        <td style="text-align:center; color:var(--c-text-muted); font-size:0.75rem">{{ $pelanggan->firstItem() + $key }}</td>
                        <td>
                            <div class="cell-name">{{ $p->nama_lengkap }}</div>
                            <div class="cell-sub">{{ $p->nomer_id }}</div>
                        </td>
                        <td>
                            @if($p->no_whatsapp)
                            <a href="https://wa.me/{{ $p->no_whatsapp }}" class="cell-wa" target="_blank">
                                <i class="ri-whatsapp-line"></i> {{ $p->no_whatsapp }}
                            </a>
                            @else
                            <span style="color:var(--c-text-muted)">-</span>
                            @endif
                        </td>
                        {{-- Progres Stepper Mini --}}
                        <td>
                            @php
                                $steps = \App\Models\Pelanggan::PROGRES_STAGES;
                                $curIdx = array_search($p->progres, $steps);
                                $isApproved = strtolower($p->status ?? '') === 'approve';
                            @endphp
                            <div class="stepper-mini">
                                @foreach($steps as $si => $step)
                                    @php
                                        $dotClass = '';
                                        $icon = $si + 1;
                                        if ($isApproved) {
                                            $dotClass = 'done';
                                            $icon = '✓';
                                        } elseif ($curIdx !== false) {
                                            if ($si < $curIdx) { $dotClass = 'done'; $icon = '✓'; }
                                            elseif ($si == $curIdx) { $dotClass = 'current'; }
                                        }
                                    @endphp
                                    <div class="step-dot {{ $dotClass }}" title="{{ $step }}">{{ $icon }}</div>
                                    @if(!$loop->last)
                                    <div class="step-line {{ ($isApproved || ($curIdx !== false && $si < $curIdx)) ? 'done' : '' }}"></div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="cell-sub mt-1">{{ $p->progres ?? \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES }}</div>
                        </td>
                        <td>
                            @php
                                $sKey = strtolower($p->status ?? 'pending');
                                $bClass = match($sKey) { 'approve' => 'badge-approve', 'reject' => 'badge-reject', 'proses', 'pending' => 'badge-pending', default => 'badge-pending' };
                                $statusLabel = match($sKey) {
                                    'pending' => 'Belum Diproses',
                                    'proses' => 'Progress',
                                    default => ucfirst($p->status ?? 'Belum Diproses'),
                                };
                            @endphp
                            <span class="badge-status {{ $bClass }}">{{ $statusLabel }}</span>
                        </td>
                        <td>
                            <div class="cell-name" style="font-weight:500">{{ optional($p->user)->name ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="d-flex justify-content-end gap-1 flex-wrap">
                                {{-- Detail --}}
                                <button class="btn-sh btn-detail" title="Detail"><i class="ri-eye-line"></i></button>

                                {{-- Update Progres --}}
                                <form action="{{ route('admin.marketing.progres', $p->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <select name="progres" onchange="this.form.submit()" class="btn-sh" style="padding: 0 0.5rem; height:32px; cursor:pointer">
                                        @foreach(\App\Models\Pelanggan::PROGRES_STAGES as $pg)
                                        <option value="{{ $pg }}" {{ $p->progres === $pg ? 'selected' : '' }}>{{ $pg }}</option>
                                        @endforeach
                                    </select>
                                </form>

                                {{-- Approve --}}
                                @if($p->status !== 'approve')
                                <form action="{{ route('admin.marketing.approve', $p->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn-sh btn-sh-approve" title="Approve">
                                        <i class="ri-check-line"></i> Approve
                                    </button>
                                </form>
                                @endif

                                {{-- Reject --}}
                                @if($p->status !== 'reject')
                                <form action="{{ route('admin.marketing.reject', $p->id) }}" method="POST" class="d-inline reject-form">
                                    @csrf
                                    <input type="hidden" name="reject_note" class="reject-note-input">
                                    <button type="button" class="btn-sh btn-sh-reject btn-reject" title="Reject">
                                        <i class="ri-close-line"></i> Reject
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
                                <p>Tidak ada data pelanggan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="d-md-none mobile-cards">
            @forelse($pelanggan as $p)
            <div class="m-card"
                data-searchable="{{ strtolower($p->nama_lengkap . ' ' . $p->nomer_id . ' ' . $p->no_whatsapp) }}"
                data-id="{{ $p->id }}"
                data-nomer-id="{{ $p->nomer_id }}"
                data-nama="{{ $p->nama_lengkap }}"
                data-whatsapp="{{ $p->no_whatsapp }}"
                data-alamat="{{ $p->alamat_jalan }}"
                data-rt="{{ $p->rt }}"
                data-rw="{{ $p->rw }}"
                data-kecamatan="{{ $p->kecamatan }}"
                data-kabupaten="{{ $p->kabupaten }}"
                data-tanggal="{{ $p->tanggal_mulai ? \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') : '' }}"
                data-status="{{ $p->status }}"
                data-progres="{{ $p->progres }}"
                data-progress-note="{{ $p->progress_note }}"
                data-deskripsi="{{ $p->deskripsi }}"
                data-marketing="{{ optional($p->user)->name }}">

                <div class="m-card-top">
                    <span class="badge-id">{{ $p->nomer_id }}</span>
                    @php
                        $sKey = strtolower($p->status ?? 'pending');
                        $bClass = match($sKey) { 'approve' => 'badge-approve', 'reject' => 'badge-reject', 'proses', 'pending' => 'badge-pending', default => 'badge-pending' };
                        $statusLabel = match($sKey) {
                            'pending' => 'Belum Diproses',
                            'proses' => 'Progress',
                            default => ucfirst($p->status ?? 'Belum Diproses'),
                        };
                    @endphp
                    <span class="badge-status {{ $bClass }}">{{ $statusLabel }}</span>
                </div>
                <div class="m-card-name">{{ $p->nama_lengkap }}</div>

                {{-- Mini Stepper --}}
                @php $steps = \App\Models\Pelanggan::PROGRES_STAGES; $curIdx = array_search($p->progres, $steps); $isApproved = strtolower($p->status ?? '') === 'approve'; @endphp
                <div class="m-card-stepper">
                    @foreach($steps as $si => $step)
                        @php
                            $dC = '';
                            $icon = $si + 1;
                            if ($isApproved) {
                                $dC = 'done';
                                $icon = '✓';
                            } elseif ($curIdx !== false) {
                                if ($si < $curIdx) { $dC = 'done'; $icon = '✓'; }
                                elseif ($si == $curIdx) { $dC = 'current'; }
                            }
                        @endphp
                        <div class="step-dot {{ $dC }}" title="{{ $step }}" style="width:20px;height:20px;font-size:0.6rem">{{ $icon }}</div>
                        @if(!$loop->last)
                        <div class="step-line {{ ($isApproved || ($curIdx !== false && $si < $curIdx)) ? 'done' : '' }}" style="height:2px;min-width:20px;flex:1"></div>
                        @endif
                    @endforeach
                    <span class="cell-sub" style="margin-left:0.5rem">{{ $p->progres ?? \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES }}</span>
                </div>

                @if($p->no_whatsapp)
                <div class="m-card-detail">
                    <a href="https://wa.me/{{ $p->no_whatsapp }}" class="cell-wa"><i class="ri-whatsapp-line"></i> {{ $p->no_whatsapp }}</a>
                </div>
                @endif
                <div class="m-card-detail"><i class="ri-user-line"></i> {{ optional($p->user)->name ?? '-' }}</div>

                <div class="m-card-actions flex-wrap">
                    <button class="btn-sh btn-detail" style="flex:1"><i class="ri-eye-line"></i> Detail</button>
                    @if($p->status !== 'approve')
                    <form action="{{ route('admin.marketing.approve', $p->id) }}" method="POST" style="flex:1">
                        @csrf
                        <button type="submit" class="btn-sh btn-sh-approve" style="width:100%"><i class="ri-check-line"></i> Approve</button>
                    </form>
                    @endif
                    @if($p->status !== 'reject')
                    <form action="{{ route('admin.marketing.reject', $p->id) }}" method="POST" class="reject-form" style="flex:1">
                        @csrf
                        <input type="hidden" name="reject_note" class="reject-note-input">
                        <button type="button" class="btn-sh btn-sh-reject btn-reject" style="width:100%"><i class="ri-close-line"></i> Reject</button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="empty-state"><i class="ri-inbox-line"></i><p>Tidak ada data.</p></div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($pelanggan->hasPages())
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Menampilkan <strong>{{ $pelanggan->firstItem() }}</strong>–<strong>{{ $pelanggan->lastItem() }}</strong>
                dari <strong>{{ $pelanggan->total() }}</strong> data
            </div>
            <div>{{ $pelanggan->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
        </div>
        @else
        <div class="pagination-wrapper">
            <div class="pagination-info">Total <strong>{{ $pelanggan->total() }}</strong> data</div>
        </div>
        @endif
    </div>
</div>

{{-- ── Detail Modal ── --}}
<div class="modal-overlay" id="detailModal">
    <div class="modal-box">
        <header>
            <h6>Detail Pelanggan</h6>
            <button class="btn-sh close-detail" style="padding:0;width:28px;height:28px;border-radius:50%"><i class="ri-close-line"></i></button>
        </header>
        <div class="m-body" id="detailContent" style="max-height:75vh;overflow-y:auto"></div>
        <div class="m-footer">
            <button class="btn-sh close-detail">Tutup</button>
        </div>
    </div>
</div>

{{-- ── Reject Modal ── --}}
<div class="modal-overlay" id="rejectModal">
    <div class="modal-box">
        <header>
            <h6>Konfirmasi Penolakan</h6>
            <button class="btn-sh close-reject" style="padding:0;width:28px;height:28px;border-radius:50%"><i class="ri-close-line"></i></button>
        </header>
        <div class="m-body">
            <p style="font-size:0.875rem;color:var(--c-text-secondary);margin-bottom:1rem">
                Anda akan menolak data pelanggan: <strong id="reject_nama"></strong>
            </p>
            <label class="form-label">Alasan Penolakan <span style="color:var(--c-text-muted)">(opsional)</span></label>
            <textarea class="form-control" id="rejectNoteInput" rows="3"
                placeholder="Contoh: Data KTP tidak valid, lokasi belum terjangkau..."></textarea>
        </div>
        <div class="m-footer">
            <button class="btn-sh close-reject">Batal</button>
            <button class="btn-sh btn-sh-reject" id="confirmReject">
                <i class="ri-close-line"></i> Ya, Tolak
            </button>
        </div>
    </div>
</div>

<script>
// Sync textarea value to hidden input before submit
document.getElementById('confirmReject').addEventListener('click', function () {
    document.querySelectorAll('.reject-form').forEach(f => {
        f.querySelector('.reject-note-input').value = document.getElementById('rejectNoteInput').value;
    });
});
</script>
@endsection
