@extends('layouts/layoutMaster')

@section('title', 'Status Baca Tagihan')

@section('vendor-style')
<style>
/* ========================================= */
/* MODERN CLEAN STYLES - Status Baca Matrix */
/* ========================================= */
:root {
  --card-shadow: 0 2px 8px rgba(0,0,0,0.08);
  --card-hover-shadow: 0 4px 16px rgba(0,0,0,0.12);
  --border-radius: 12px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

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

/* Stats Card */
.stats-card {
  border-radius: var(--border-radius);
  padding: 1.5rem;
  background: #ffffff;
  color: #0f172a;
  border: 1px solid #e5e7eb;
  transition: var(--transition);
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

/* Matrix Table */
.matrix-table {
  border-collapse: separate;
  border-spacing: 0;
  min-width: 1200px;
}

.matrix-table th {
  background: #f8fafc;
  border-bottom: 2px solid #e2e8f0;
  padding: 1rem 0.5rem;
  font-weight: 600;
  color: #0f172a;
  font-size: 0.8rem;
  text-transform: uppercase;
  text-align: center;
  letter-spacing: 0.5px;
  white-space: nowrap;
}

.matrix-table th.col-pelanggan {
  text-align: left;
  padding-left: 1rem;
  min-width: 250px;
  position: sticky;
  left: 0;
  background: #f8fafc;
  z-index: 2;
  border-right: 1px solid #e2e8f0;
}

.matrix-table td {
  padding: 0.75rem 0.5rem;
  border-bottom: 1px solid #e5e7eb;
  vertical-align: middle;
  text-align: center;
}

.matrix-table td.col-pelanggan {
  text-align: left;
  padding-left: 1rem;
  position: sticky;
  left: 0;
  background: #ffffff;
  z-index: 1;
  border-right: 1px solid #e2e8f0;
}

.matrix-table tbody tr:hover td {
  background: #f1f5f9;
}

/* Status Icons */
.status-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  border-radius: 6px;
  font-size: 1.1rem;
  cursor: help;
  transition: transform 0.2s;
}

.status-icon:hover {
  transform: scale(1.15);
}

.status-read {
  background: #f1f5f9;
  color: #18181b;
  border: 1px solid #e2e8f0;
}

.status-unread {
  background: #fff;
  color: #71717a;
  border: 1px dashed #e4e4e7;
}

.status-empty {
  color: #cbd5e1;
  font-size: 1rem;
}

/* Form Controls */
.form-select, .form-control {
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  padding: 0.5rem 1rem;
  transition: var(--transition);
}

.form-select:focus, .form-control:focus {
  border-color: #111827;
  box-shadow: 0 0 0 3px rgba(17, 24, 39, 0.12);
}

/* Empty State */
.empty-state {
  padding: 4rem 2rem;
  text-align: center;
  color: #71717a;
}

.empty-state i {
  font-size: 3rem;
  margin-bottom: 1rem;
  color: #d4d4d8;
}

/* Search input */
.search-input {
  position: relative;
}

.search-input i {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #a1a1aa;
}

.search-input input {
  padding-left: 36px !important;
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

/* Tooltip Customization */
.tooltip-inner {
  text-align: left;
  padding: 8px 12px;
  border-radius: 6px;
  font-size: 0.8rem;
}

/* Modern Pagination */
.pagination-modern {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  flex-wrap: nowrap;
  gap: 0.45rem;
}

.pagination-pages {
  display: flex;
  align-items: center;
  flex-wrap: nowrap;
  white-space: nowrap;
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
  transition: all 0.2s ease;
}

.page-dot-btn:hover:not(:disabled):not(.active) {
  background: #e5e7eb;
}

.page-dot-btn.active {
  background: #0f111a;
  color: #ffffff;
  box-shadow: 0 6px 14px rgba(15, 17, 26, 0.2);
}

.page-dot-btn:disabled {
  opacity: 0.55;
  cursor: not-allowed;
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

@media (max-width: 992px) {
  .pagination-modern {
    transform: scale(0.85);
    transform-origin: right center;
  }
}

@media (max-width: 768px) {
  .pagination-modern {
    transform: scale(0.72);
  }
}
</style>
@endsection
@section('content')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-1" style="color: #18181b;">
      <i class="ri-table-alt-line me-2"></i>Status Baca Tagihan
    </h4>
    <p class="text-muted mb-0" style="font-size: 0.875rem;">
      Rekap status baca tagihan per pelanggan dalam satu tahun (Tabel Matriks)
    </p>
  </div>
</div>

{{-- Stats Cards --}}
<div class="row mb-4" id="statsRow">
  <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
    <div class="stats-card">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <p class="mb-1 text-muted" style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Total Pelanggan JMK-GK</p>
          <h2 class="mb-0 fw-bold" id="statTotal" style="font-size: 2rem;">0</h2>
        </div>
        <div class="stats-icon">
          <i class="ri-file-list-3-line"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
    <div class="stats-card">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <p class="mb-1 text-muted" style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Pelanggan Sudah Baca</p>
          <h2 class="mb-0 fw-bold" id="statRead" style="font-size: 2rem; color: #18181b;">0</h2>
        </div>
        <div class="stats-icon" style="background: #f4f4f5; color: #18181b;">
          <i class="ri-check-double-line"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
    <div class="stats-card">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <p class="mb-1 text-muted" style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Pelanggan Belum Baca</p>
          <h2 class="mb-0 fw-bold" id="statUnread" style="font-size: 2rem; color: #18181b;">0</h2>
        </div>
        <div class="stats-icon" style="background: #f4f4f5; color: #71717a;">
          <i class="ri-eye-off-line"></i>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Main Table Card --}}
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3" style="background: transparent; padding: 1.5rem; border-bottom: 1px solid #f0f0f0;">
    <div class="d-flex align-items-center gap-3 flex-wrap">
      {{-- Year Filter --}}
      <div class="d-flex align-items-center gap-2">
        <label class="form-label mb-0 fw-semibold" style="font-size: 0.875rem; white-space: nowrap;">Tahun:</label>
        <select id="filterYear" class="form-select" style="width: 120px; font-weight: 600;">
          @php $currentYear = date('Y'); @endphp
          @for($y = $currentYear + 1; $y >= 2023; $y--)
            <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
          @endfor
        </select>
      </div>

    </div>

    {{-- Search --}}
    <div class="search-input">
      <i class="ri-search-line"></i>
      <input type="text" id="searchInput" class="form-control" placeholder="Cari ID / Nama..." style="width: 250px;">
    </div>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive" style="max-height: 70vh;">
      <table class="table matrix-table mb-0" id="tagihanReadTable">
        <thead>
          <tr>
            <th style="width: 50px; text-align: center; position: sticky; left: 0; background: #f8fafc; z-index: 3;">No</th>
            <th class="col-pelanggan">Pelanggan</th>
            <th>Jan</th>
            <th>Feb</th>
            <th>Mar</th>
            <th>Apr</th>
            <th>Mei</th>
            <th>Jun</th>
            <th>Jul</th>
            <th>Ags</th>
            <th>Sep</th>
            <th>Okt</th>
            <th>Nov</th>
            <th>Des</th>
          </tr>
        </thead>
        <tbody id="tagihanReadBody">
          {{-- Data akan diisi dari JS --}}
        </tbody>
      </table>
    </div>
    
    {{-- Pagination Controls --}}
    <div class="d-flex justify-content-between align-items-center p-3 border-top flex-wrap gap-3" id="paginationControlsContainer" style="display: none !important;">
      <div class="text-muted" style="font-size: 0.875rem;">
        Menampilkan <span id="pageInfoStart" class="fw-semibold">0</span> - <span id="pageInfoEnd" class="fw-semibold">0</span> dari <span id="pageInfoTotal" class="fw-semibold">0</span> pelanggan
      </div>
      <div class="pagination-modern">
        <button class="page-dot-btn nav-btn" id="btnPrevPage" disabled aria-label="Halaman sebelumnya">
          <i class="ri-arrow-left-s-line"></i>
        </button>
        <div class="pagination-pages" id="paginationPages"></div>
        <button class="page-dot-btn nav-btn" id="btnNextPage" disabled aria-label="Halaman selanjutnya">
          <i class="ri-arrow-right-s-line"></i>
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
  let allData = [];
  let currentPage = 1;
  let lastPage = 1;
  let perPage = 40;
  let searchTimeout = null;

  
  // Inisialisasi tooltip dari bootstrap
  function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl, { html: true });
    });
  }

  function showLoading() {
    // Loading animation disabled by request.
  }

  function hideLoading() {
    // Loading animation disabled by request.
  }

  function getStatusIcon(data) {
    if (!data.ada) {
      return '<span class="status-empty">-</span>';
    }
    
    if (data.is_read) {
      const tooltipMsg = `Lunas/Belum: <b>${data.status_pembayaran}</b><br>Dibaca pd: <b>${data.read_at}</b>`;
      return `<span class="status-icon status-read" data-bs-toggle="tooltip" data-bs-placement="top" title="${tooltipMsg}"><i class="ri-check-line"></i></span>`;
    }
    
    const tooltipMsg = `Lunas/Belum: <b>${data.status_pembayaran}</b><br><i>Belum dibaca</i>`;
    return `<span class="status-icon status-unread" data-bs-toggle="tooltip" data-bs-placement="top" title="${tooltipMsg}"><i class="ri-close-line"></i></span>`;
  }

  function renderTable(data) {
    const tbody = document.getElementById('tagihanReadBody');

    if (!data || data.length === 0) {
      tbody.innerHTML = `
        <tr class="empty-state-row">
          <td colspan="14">
            <div class="empty-state">
              <i class="ri-folder-user-line d-block"></i>
              <h6 class="fw-semibold mb-1">Tidak ada data</h6>
              <p class="mb-0 text-muted">Tidak ditemukan pelanggan untuk pencarian/filter ini.</p>
            </div>
          </td>
        </tr>`;
      
      document.getElementById('paginationControlsContainer').style.setProperty('display', 'none', 'important');
      return;
    }

    tbody.innerHTML = data.map((item, idx) => `
      <tr>
        <td style="text-align: center; color: #71717a; font-weight: 600; position: sticky; left: 0; background: #fff; z-index: 1;">${((currentPage - 1) * perPage) + idx + 1}</td>
        <td class="col-pelanggan">
          <div class="fw-semibold" style="color: #18181b;">${item.nama_lengkap}</div>
          <div style="font-size: 0.75rem; color: #71717a; font-family: monospace;">${item.nomer_id} &bull; ${item.no_whatsapp}</div>
        </td>
        ${[1,2,3,4,5,6,7,8,9,10,11,12].map(m => `
          <td>${getStatusIcon(item.tagihans_matrix[m])}</td>
        `).join('')}
      </tr>
    `).join('');

    initTooltips();
  }

  function getVisiblePageItems(current, total) {
    if (total <= 10) {
      return Array.from({ length: total }, (_, i) => i + 1);
    }

    if (current <= 5) {
      return [1, 2, 3, 4, 5, 6, 7, 8, 'ellipsis', total - 1, total];
    }

    if (current >= total - 4) {
      return [1, 2, 'ellipsis', total - 7, total - 6, total - 5, total - 4, total - 3, total - 2, total - 1, total];
    }

    return [1, 2, 'ellipsis', current - 1, current, current + 1, 'ellipsis', total - 1, total];
  }

  function renderPaginationPages() {
    const pagesContainer = document.getElementById('paginationPages');
    if (!pagesContainer) return;

    const items = getVisiblePageItems(currentPage, lastPage)
      .filter((item, idx, arr) => item === 'ellipsis' || (Number.isInteger(item) && item >= 1 && item <= lastPage))
      .filter((item, idx, arr) => !(item === 'ellipsis' && arr[idx - 1] === 'ellipsis'));

    pagesContainer.innerHTML = items.map(item => {
      if (item === 'ellipsis') {
        return `<span class="page-ellipsis">...</span>`;
      }
      const active = item === currentPage ? 'active' : '';
      return `<button class="page-dot-btn ${active}" data-page="${item}" aria-label="Halaman ${item}">${item}</button>`;
    }).join('');
  }

  function updatePagination(pagination) {
    currentPage = pagination.current_page;
    lastPage = pagination.last_page;
    perPage = pagination.per_page || perPage;
    
    const container = document.getElementById('paginationControlsContainer');
    
    if (pagination.total === 0 || lastPage <= 1) {
      container.style.setProperty('display', 'none', 'important');
      return;
    }
    
    container.style.setProperty('display', 'flex', 'important');
    
    // Info text (gunakan data server agar stabil)
    const start = pagination.from || 0;
    const end = pagination.to || 0;
    
    document.getElementById('pageInfoStart').textContent = start;
    document.getElementById('pageInfoEnd').textContent = end;
    document.getElementById('pageInfoTotal').textContent = pagination.total;
    
    // Buttons
    document.getElementById('btnPrevPage').disabled = (currentPage <= 1);
    document.getElementById('btnNextPage').disabled = (currentPage >= lastPage);
    renderPaginationPages();
  }

  function updateStats(stats) {
    document.getElementById('statTotal').textContent = stats.total;
    document.getElementById('statRead').textContent = stats.sudah_baca;
    document.getElementById('statUnread').textContent = stats.belum_baca;
  }

  function fetchData(page = 1) {
    showLoading();
    const year = document.getElementById('filterYear').value;
    const search = document.getElementById('searchInput').value;
    
    const url = new URL(window.location.origin + '/dashboard/admin/tagihan/status-baca/data');
    url.searchParams.append('year', year);
    url.searchParams.append('page', page);
    if (search) {
      url.searchParams.append('search', search);
    }
    
    fetch(url, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
      .then(async (res) => {
        if (res.redirected) {
          window.location.href = res.url;
          return null;
        }

        const contentType = res.headers.get('content-type') || '';
        const bodyText = await res.text();

        if (!res.ok) {
          throw new Error(`HTTP ${res.status}`);
        }

        if (!contentType.includes('application/json')) {
          throw new Error('Response bukan JSON');
        }

        try {
          return JSON.parse(bodyText);
        } catch (e) {
          throw new Error('JSON tidak valid');
        }
      })
      .then(json => {
        if (!json) return;
        if (json.status) {
          const pagination = json.data.pagination || {};
          const rows = json.data.pelanggans || [];

          // Jika halaman diminta sudah tidak valid (mis. habis filter/delete), lompat ke halaman terakhir.
          if (rows.length === 0 && (pagination.total || 0) > 0 && (pagination.current_page || 1) > (pagination.last_page || 1)) {
            fetchData(pagination.last_page || 1);
            return;
          }

          allData = json.data.pelanggans;
          updateStats(json.data.statistics);
          renderTable(allData);
          updatePagination(pagination);
        } else {
          throw new Error(json.message || 'Data gagal dimuat');
        }
        hideLoading();
      })
      .catch(err => {
        console.error('Error fetching data:', err);
        const tbody = document.getElementById('tagihanReadBody');
        tbody.innerHTML = `
          <tr class="empty-state-row">
            <td colspan="14">
              <div class="empty-state">
                <i class="ri-alert-line d-block"></i>
                <h6 class="fw-semibold mb-1">Gagal memuat data</h6>
                <p class="mb-0 text-muted">${err.message || 'Terjadi kesalahan saat mengambil data.'}</p>
              </div>
            </td>
          </tr>`;
        document.getElementById('paginationControlsContainer').style.setProperty('display', 'none', 'important');
        hideLoading();
      });
  }

  // Events
  document.getElementById('filterYear').addEventListener('change', () => fetchData(1));
  
  // Debounce search
  document.getElementById('searchInput').addEventListener('input', () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      fetchData(1);
    }, 500);
  });
  
  // Pagination clicks
  document.getElementById('btnPrevPage').addEventListener('click', () => {
    if (currentPage > 1) fetchData(currentPage - 1);
  });
  
  document.getElementById('btnNextPage').addEventListener('click', () => {
    if (currentPage < lastPage) fetchData(currentPage + 1);
  });

  document.getElementById('paginationPages').addEventListener('click', (e) => {
    const pageBtn = e.target.closest('[data-page]');
    if (!pageBtn) return;
    const targetPage = Number(pageBtn.getAttribute('data-page'));
    if (!Number.isNaN(targetPage) && targetPage >= 1 && targetPage <= lastPage && targetPage !== currentPage) {
      fetchData(targetPage);
    }
  });

  // Initial load
  fetchData(1);
});
</script>
@endsection
