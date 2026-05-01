@extends('layouts/layoutMaster')

@section('title', 'Status Pelanggan')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
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

/* ========== STATS CARDS ========== */
.stats-card {
  border-radius: var(--border-radius);
  padding: 1.5rem;
  background: #18181b;
  border: none;
  transition: var(--transition);
  color: #fafafa;
}

.stats-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(24, 24, 27, 0.3);
}

.stats-card h2,
.stats-card .fw-bold {
  color: #fafafa !important;
}

.stats-card .text-muted {
  color: rgba(250, 250, 250, 0.7) !important;
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

.bg-label-primary {
  background: rgba(250, 250, 250, 0.15) !important;
  color: #fafafa !important;
}

.bg-label-success {
  background: rgba(250, 250, 250, 0.15) !important;
  color: #fafafa !important;
}

.bg-label-secondary {
  background: rgba(250, 250, 250, 0.15) !important;
  color: #fafafa !important;
}

.bg-label-info {
  background: #18181b !important;
  color: #fafafa !important;
}

.bg-label-dark {
  background: #18181b !important;
  color: #fafafa !important;
}

.text-success {
  color: #18181b !important;
}

.text-secondary {
  color: #71717a !important;
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
  transform: translateY(-2px) !important;
}

.btn-success,
.btn.btn-success {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn-success:hover,
.btn.btn-success:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

.btn-secondary,
.btn.btn-secondary {
  background: transparent !important;
  background-color: transparent !important;
  border: 1px solid #e4e4e7 !important;
  color: #18181b !important;
}

.btn-secondary:hover,
.btn.btn-secondary:hover {
  background: #f4f4f5 !important;
  background-color: #f4f4f5 !important;
  border-color: #18181b !important;
  color: #18181b !important;
}

/* ========== FORM CONTROLS ========== */
.form-control,
.form-select {
  border-radius: 8px;
  border: 1px solid #e4e4e7;
  padding: 0.625rem 1rem;
  transition: var(--transition);
  color: #18181b;
}

.form-control:focus,
.form-select:focus {
  border-color: #18181b;
  box-shadow: 0 0 0 3px rgba(24, 24, 27, 0.1);
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

.table-modern tbody tr:not(.empty-state-row):hover {
  background-color: #f4f4f5 !important;
}

.table-modern tbody td {
  padding: 1rem;
  vertical-align: middle;
  border-bottom: 1px solid #e4e4e7;
  color: #18181b;
  white-space: nowrap;
}

.status-icon {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* ========== BADGES ========== */
.badge {
  border-radius: 9999px !important;
  font-weight: 500 !important;
  padding: 0.35rem 0.75rem !important;
}

.badge.bg-success {
  background: #18181b !important;
  color: #fafafa !important;
}

.badge.bg-secondary {
  background: #f4f4f5 !important;
  color: #71717a !important;
  border: 1px solid #e4e4e7;
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
.pagination-wrapper > div:last-child p,
.pagination-wrapper div:last-child > p,
nav[role="navigation"] > div:first-child,
nav[role="navigation"] > div > p {
  display: none !important;
}

/* ========== LOADING OVERLAY ========== */
.loading-overlay {
  position: fixed;
  inset: 0;
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

/* ========== EMPTY STATE ========== */
.empty-state-row td {
  background: #fafbfc !important;
  border: none !important;
}

.empty-state-content {
  padding: 3rem 1rem;
}

table.dataTable tbody tr.empty-state-row,
table.dataTable tbody tr.empty-state-row:hover {
  background: #fafbfc !important;
}

/* ========== SCROLLBAR ========== */
.table-responsive::-webkit-scrollbar {
  height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb {
  background: #18181b;
  border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
  background: #27272a;
}

/* ========== HIDE DATATABLES CONTROLS ========== */
.dataTables_info,
.dataTables_paginate,
.dataTables_length {
  display: none !important;
}

/* ========== RESPONSIVE ========== */
@media (max-width: 768px) {
  .pagination-wrapper {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }

  .stats-card {
    margin-bottom: 1rem;
  }

  .table-responsive {
    margin-bottom: 1rem;
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
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const loadScript = (src) => new Promise((resolve, reject) => {
      const s = document.createElement('script');
      s.src = src;
      s.onload = resolve;
      s.onerror = reject;
      document.head.appendChild(s);
    });

    const ensureJquery = () => {
      if (window.jQuery) return Promise.resolve();
      return loadScript('https://code.jquery.com/jquery-3.7.1.min.js');
    };

    const ensureDataTables = () => {
      if (window.jQuery && $.fn.DataTable) return Promise.resolve();
      const css = document.createElement('link');
      css.rel = 'stylesheet';
      css.href = 'https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css';
      document.head.appendChild(css);

      const jsCore = loadScript('https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js');
      const jsBs = loadScript('https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js');
      return Promise.all([jsCore, jsBs]);
    };

    ensureJquery()
      .then(ensureDataTables)
      .then(() => {
        const $table = $('.datatables-status');
        if (!$table.length) return;

        const hasData = $table.find('tbody tr').not(':has(td[colspan])').length > 0;

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
              language: {
                zeroRecords: "Tidak ada data yang sesuai",
                emptyTable: "Tidak ada data tersedia"
              },
              columnDefs: [
                { orderable: false, targets: [0, 6, 7] },
                { width: '5%', targets: 0 },
                { width: '20%', targets: 1 },
                { width: '12%', targets: 2 },
                { width: '20%', targets: 3 },
                { width: '10%', targets: 4 },
                { width: '12%', targets: 5 },
                { width: '10%', targets: 6 },
                { width: '11%', targets: 7 }
              ]
            });
          } catch (error) {
            console.warn('DataTables initialization error:', error);
          }
        }
      })
      .catch((error) => {
        console.warn('DataTables gagal dimuat:', error);
      });

    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
      statusFilter.addEventListener('change', function () {
        this.form.submit();
      });
    }

    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
      filterForm.addEventListener('submit', function () {
        const overlay = document.querySelector('.loading-overlay');
        if (overlay) overlay.style.display = 'flex';
      });
    }
  });
</script>
@endsection

@section('content')
<div class="loading-overlay">
  <div class="spinner-border spinner-border-custom text-light" role="status">
    <span class="visually-hidden">Loading...</span>
  </div>
</div>

<div class="container-fluid px-4 py-4">
 

 

  {{-- Filter & Search --}}
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('pelanggan.status.active') }}" id="filterForm">
        <div class="row g-3 align-items-end">
          <div class="col-md-5">
            <label class="form-label small fw-semibold mb-2">
              <i class="ri-search-line me-1"></i>Pencarian
            </label>
            <input
              type="text"
              name="search"
              class="form-control"
              placeholder="Cari nama, No. ID, WhatsApp, alamat, paket..."
              value="{{ request('search') }}">
          </div>

          <div class="col-md-3">
            <label class="form-label small fw-semibold mb-2">
              <i class="ri-filter-3-line me-1"></i>Filter Status
            </label>
            <select
              name="status_filter"
              id="statusFilter"
              class="form-select">
              <option value="">Semua Status</option>
              <option value="Active" {{ request('status_filter') == 'Active' ? 'selected' : '' }}>Active</option>
              <option value="Inactive" {{ request('status_filter') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>

          <div class="col-md-4">
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary flex-grow-1" style="height: 42px !important;">
                <i class="ri-search-line me-1 text-white"></i>Cari
              </button>

              @if(request('status_filter') || request('search'))
                <a href="{{ route('pelanggan.status.active') }}" class="btn btn-secondary">
                  <i class="ri-refresh-line me-1"></i>Reset
                </a>
              @endif
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- Data Table --}}
<div class="card border-0 shadow-sm">
  <div class="card-header-custom">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <h4 class="mb-1 fw-bold">
          <i class="ri-user-follow-line me-2"></i>Status Pelanggan
        </h4>
        <p class="mb-0 opacity-75 small">Monitor status login dan aktivitas pelanggan secara real-time.</p>
      </div>

      <div class="d-flex align-items-center gap-2">
        {{-- Badge total data --}}
        @if($pelanggan->total() > 0)
          <span class="badge" style="background-color: #f4f4f5; color: #18181b; padding: 0 20px; height: 42px; display: inline-flex; align-items: center; font-size: 0.9rem; border-radius: 50px !important; border: 1px solid #e4e4e7;">
            <i class="ri-database-2-line me-2"></i>
            {{ $pelanggan->total() }} Data Total
          </span>
        @endif

        {{-- Export Excel di kanan header --}}
        <a href="{{ url('/pelanggan/export') }}" class="btn btn-primary" style="background-color: #18181b !important; border-color: #18181b !important; color: white !important; height: 42px !important; display: inline-flex !important; align-items: center !important; padding: 0 20px !important; border-radius: 8px !important;">
          <i class="ri-file-excel-2-line me-1" style="color: #ffffff !important;"></i> Export Excel
        </a>
      </div>
    </div>
  </div>
    <div class="card-body p-0">
      <div class="table-responsive p-3">
        <table class="datatables-status table table-modern table-hover nowrap" style="width: 100%;">
          <thead>
            <tr>
              <th><i class="ri-hashtag me-1"></i>No</th>
              <th><i class="ri-user-3-line me-1"></i>Nama</th>
              <th><i class="ri-whatsapp-line me-1"></i>No. WhatsApp</th>
              <th><i class="ri-map-pin-line me-1"></i>Alamat</th>
              <th><i class="ri-barcode-line me-1"></i>No. ID</th>
              <th><i class="ri-box-3-line me-1"></i>Paket</th>
              <th><i class="ri-shield-check-line me-1"></i>Status</th>
              <th><i class="ri-time-line me-1"></i>Login Terakhir</th>
            </tr>
          </thead>
          <tbody>
            @forelse($pelanggan as $index => $item)
              @php
                $isActive    = optional($item->loginStatus)->is_active;
                $loggedInAt  = optional($item->loginStatus)->logged_in_at;
                $no          = ($pelanggan->currentPage() - 1) * $pelanggan->perPage() + $index + 1;
              @endphp
              <tr>
                <td class="fw-bold text-center">{{ $no }}</td>

                <td>
                  <div class="d-flex align-items-center">
                  
                    <span class="fw-semibold">{{ $item->nama_lengkap }}</span>
                  </div>
                </td>

                <td>
                  <a
                    href="https://wa.me/{{ $item->no_whatsapp }}"
                    target="_blank"
                    class="text-decoration-none">
                    <code style="background: #18181b; padding: 6px 12px; border-radius: 6px; font-size: 0.875rem; font-weight: 600; color: #fafafa;">
                      <i class="ri-whatsapp-line me-1" style="color: #fafafa;"></i>{{ $item->no_whatsapp }}
                    </code>
                  </a>
                </td>

                <td>
                  <div style="min-width: 200px; max-width: 250px;">
                    <div class="text-truncate">{{ $item->alamat_jalan ?? '-' }}</div>
                    <small class="text-muted">
                      RT {{ $item->rt ?? '-' }}/RW {{ $item->rw ?? '-' }}, {{ $item->kecamatan ?? '-' }}
                    </small>
                  </div>
                </td>

                <td>
                  <span class="badge bg-label-dark" style="padding: 8px 12px; font-size: 0.85rem; font-family: monospace;">
                    {{ $item->nomer_id ?? '-' }}
                  </span>
                </td>

                <td>
                  <span class="badge bg-label-info">
                    <i class="ri-box-line me-1"></i>{{ optional($item->paket)->nama_paket ?? '-' }}
                  </span>
                </td>

                <td>
                  @if($isActive)
                    <span class="badge bg-success">
                      <i class="ri-checkbox-circle-line me-1"></i>Active
                    </span>
                  @else
                    <span class="badge bg-secondary">
                      <i class="ri-close-circle-line me-1"></i>Inactive
                    </span>
                  @endif
                </td>

                <td>
                  @if($loggedInAt)
                    <div>
                      <small class="d-block fw-semibold">
                        {{ $loggedInAt->timezone(config('app.timezone'))->format('d M Y') }}
                      </small>
                      <small class="text-muted">
                        {{ $loggedInAt->timezone(config('app.timezone'))->format('H:i') }} WIB
                      </small>
                    </div>
                  @else
                    <span class="text-muted small">Belum pernah login</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr class="empty-state-row">
                <td colspan="8" class="text-center">
                  <div class="empty-state-content">
                    <div class="mb-3">
                      <i class="ri-inbox-line" style="font-size: 4rem; color: #ddd;"></i>
                    </div>

                    @if(request('search') || request('status_filter'))
                      <h5 class="text-muted mb-2">
                        <i class="ri-search-eye-line me-2"></i>Data Tidak Ditemukan
                      </h5>
                      <p class="text-muted mb-3">
                        Tidak ada data yang sesuai dengan pencarian atau filter yang Anda pilih.
                      </p>

                      <div class="mb-3">
                        @if(request('search'))
                          <span class="badge bg-label-primary me-2" style="padding: 8px 16px;">
                            <i class="ri-search-line me-1"></i>
                            Pencarian: "{{ request('search') }}"
                          </span>
                        @endif

                        @if(request('status_filter'))
                          <span class="badge bg-label-info" style="padding: 8px 16px;">
                            <i class="ri-filter-line me-1"></i>
                            Status: {{ request('status_filter') }}
                          </span>
                        @endif
                      </div>

                      <a href="{{ route('pelanggan.status.active') }}" class="btn btn-primary mt-2">
                        <i class="ri-refresh-line me-1"></i>Reset Filter &amp; Tampilkan Semua Data
                      </a>
                    @else
                      <h5 class="text-muted mb-2">
                        <i class="ri-user-unfollow-line me-2"></i>Belum Ada Data Pelanggan
                      </h5>
                      <p class="text-muted">
                        Saat ini belum ada data pelanggan yang terdaftar dalam sistem.
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

    @if($pelanggan->hasPages())
      <div class="pagination-wrapper">
        <div class="pagination-info">
          Menampilkan <strong>{{ $pelanggan->firstItem() ?? 0 }}</strong> - <strong>{{ $pelanggan->lastItem() ?? 0 }}</strong>
          dari <strong>{{ $pelanggan->total() }}</strong> data
        </div>
        <div>
          {{ $pelanggan->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
      </div>
    @endif
  </div>

</div>
@endsection
