@extends('layouts/layoutMaster')

@section('title', 'Daftar Paket')

{{-- VENDOR STYLE --}}
@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/animate-css/animate.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

{{-- VENDOR SCRIPT --}}
@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ambil semua tombol delete
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = btn.closest('form');

            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

        // Search filter removed - using server side search
});
</script>
@endsection

@section('content')
{{-- Header dengan Tombol Add Paket --}}
<div class="card mb-4 border-0 shadow-sm">
  <div class="card-body p-4">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h4 class="mb-1 fw-bold text-dark">Daftar Paket Internet</h4>
        <p class="text-muted mb-0 small">Kelola semua paket layanan internet Anda</p>
      </div>
      <a href="{{route('paket.add')}}" class="btn btn-primary px-4 py-2 shadow-sm hover-lift">
        <i class="ri-add-line ri-18px me-1"></i>
        <span class="fw-semibold">Tambah Paket</span>
      </a>
    </div>
  </div>
</div>

{{-- Search Bar --}}
<div class="card search-card mb-4">
  <div class="card-body p-4">
    <form action="{{ url()->current() }}" method="GET" class="w-100" id="paketSearchForm">
      <div class="input-group">
        <span class="input-group-text"><i class="ri-search-line"></i></span>
        <input
          type="text"
          name="search"
          class="form-control"
          placeholder="Cari paket berdasarkan nama, harga, atau kecepatan..."
          value="{{ request('search') }}"
          autocomplete="off"
          id="paketSearchInput"
        >
        <button class="btn btn-search" type="submit">
          <i class="ri-search-2-line me-1"></i>Cari
        </button>
        @if(request('search'))
        <a href="{{ url()->current() }}" class="btn btn-outline-secondary" style="border:none; padding:0 14px; font-weight:600;">
          <i class="ri-close-line"></i>
        </a>
        @endif
      </div>

      @if(request('search'))
      <div class="search-meta" id="searchMeta">
        <span class="search-chip" id="searchChip"><i class="ri-filter-3-line me-1"></i>"{{ request('search') }}"</span>
        <small class="text-muted">Ditemukan <span id="searchCount">{{ $pakets->count() }}</span> paket pada halaman ini</small>
      </div>
      @endif
    </form>
  </div>
</div>

<div class="row g-4">
  @if($pakets->isEmpty())
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
          <div class="avatar avatar-xl mb-3 mx-auto">
            <div class="avatar-initial bg-label-primary rounded-circle">
              <i class="ri-inbox-line ri-36px"></i>
            </div>
          </div>
          <h5 class="mb-2">Belum Ada Paket</h5>
          <p class="text-muted mb-4">Mulai dengan menambahkan paket internet pertama Anda</p>
      <a href="{{route('paket.add')}}" class="btn btn-primary">
    <i class="ri-add-line me-1"></i> Tambah Paket Baru
</a>
        </div>
      </div>
    </div>
  @else
    @foreach($pakets as $paket)
      <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 paket-card" data-searchable="{{ strtolower($paket->nama_paket.' '.($paket->harga ?? '').' '.($paket->kecepatan ?? '').' Mbps') }}">
        <div class="card h-100 border-0 shadow-sm hover-card position-relative overflow-hidden">
          {{-- Accent Border Top --}}
          <div class="card-accent"></div>

          <div class="card-body p-4 d-flex flex-column">
            {{-- Header Card --}}
            <div class="d-flex align-items-start mb-4">
              <div class="flex-shrink-0 me-3">
                <div class="avatar avatar-lg">
                  <div class="avatar-initial bg-gradient-primary rounded-3 shadow-sm">
                    <i class="ri-wifi-line ri-24px"></i>
                  </div>
                </div>
              </div>
              <div class="flex-grow-1 overflow-hidden">
                <h5 class="card-title mb-2 fw-bold text-truncate">{{ $paket->nama_paket }}</h5>
                <div class="d-flex align-items-center gap-2 mb-2">
                  <span class="badge bg-success text-white px-3 py-2 fw-semibold rounded-pill shadow-sm">
                    Rp {{ number_format(floatval($paket->harga), 0, ',', '.') }}
                  </span>
                </div>
              </div>
            </div>

            {{-- Info Section --}}
            <div class="mb-4 pb-3 border-bottom">
              <div class="d-flex align-items-center text-muted">
                <i class="ri-speed-line ri-18px me-2 text-primary"></i>
                <span class="small">Kecepatan:</span>
                <strong class="ms-auto text-dark">{{ $paket->kecepatan ?? '-' }} Mbps</strong>
              </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex gap-2 mt-auto">
              <a href="{{ route('paket.edit', $paket->id) }}"
                 class="btn btn-warning btn-sm flex-fill d-flex align-items-center justify-content-center gap-1 py-2 shadow-sm hover-lift">
                <i class="ri-edit-line ri-16px"></i>
                <span class="fw-medium">Edit</span>
              </a>

              <form action="{{ route('paket.destroy', $paket->id) }}" method="POST" class="flex-fill">
                @csrf
                @method('DELETE')
                <button type="button"
                        class="btn btn-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-1 py-2 shadow-sm hover-lift btn-delete">
                  <i class="ri-delete-bin-line ri-16px"></i>
                  <span class="fw-medium">Hapus</span>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    @endforeach
    <div class="col-12 d-none" id="searchEmpty">
      <div class="card border-0 shadow-sm text-center py-5">
        <div class="avatar avatar-xl mb-3 mx-auto">
          <div class="avatar-initial bg-label-primary rounded-circle">
            <i class="ri-inbox-line ri-36px"></i>
          </div>
        </div>
        <h5 class="mb-2">Tidak ada paket yang sesuai</h5>
        <p class="text-muted mb-3">Coba kata kunci lain atau hapus filter pencarian</p>
        <button class="btn btn-outline-primary" type="button" id="clearPaketSearch">
          <i class="ri-refresh-line me-1"></i>Reset Pencarian
        </button>
      </div>
    </div>
  @endif
</div>

{{-- Pagination --}}
@if($pakets->hasPages())
<div class="row mt-4">
    <div class="col-12">
        <div class="pagination-wrapper p-3 d-flex justify-content-between align-items-center border rounded-3 bg-white shadow-sm">
            <div class="pagination-info text-muted small fw-medium">
              Menampilkan <strong>{{ $pakets->firstItem() ?? 0 }}</strong> - <strong>{{ $pakets->lastItem() ?? 0 }}</strong> dari <strong>{{ $pakets->total() }}</strong> paket
            </div>
            <div>
              {{ $pakets->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endif

<style>
/* ========================================= */
/* SHADCN UI STYLE - BLACK & WHITE */
/* ========================================= */
:root {
  --primary-color: #18181b;
  --gray-bg: #fafafa;
  --gray-border: #e4e4e7;
  --text-muted: #71717a;
}

/* Card Hover Effects */
.hover-card {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border-radius: 12px;
  border: 1px solid #e4e4e7;
}

.hover-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12) !important;
  border-color: #18181b;
}

/* Card Accent Border */
.card-accent {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: #18181b;
  border-radius: 12px 12px 0 0;
}

/* Avatar Gradient - Black */
.avatar-initial.bg-gradient-primary {
  background: #18181b !important;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fafafa;
}

/* Button Hover Effects */
.hover-lift {
  transition: all 0.2s ease-in-out;
}

.hover-lift:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Primary Button - Black */
.btn-primary {
  background: #18181b !important;
  border: 1px solid #18181b !important;
  color: #fafafa !important;
}

.btn-primary:hover {
  background: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

/* Warning Button - Black */
.btn-warning {
  background: #18181b !important;
  border: 1px solid #18181b !important;
  color: #fafafa !important;
}

.btn-warning:hover {
  background: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

/* Danger Button - Black */
.btn-danger {
  background: #18181b !important;
  border: 1px solid #18181b !important;
  color: #fafafa !important;
}

.btn-danger:hover {
  background: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

/* Outline Button */
.btn-outline-primary {
  background: transparent !important;
  border: 1px solid #e4e4e7 !important;
  color: #18181b !important;
}

.btn-outline-primary:hover {
  background: #18181b !important;
  border-color: #18181b !important;
  color: #fafafa !important;
}

/* Badge Styling - Black */
.badge.bg-success {
  background: #18181b !important;
  color: #fafafa !important;
  font-size: 0.875rem;
  letter-spacing: 0.3px;
  border-radius: 9999px;
}

/* Card Title */
.card-title {
  font-size: 1.125rem;
  line-height: 1.4;
  color: #18181b;
}

/* Search Bar */
.search-card {
  border: none;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.search-card .input-group {
  border: 1.5px solid #e4e4e7;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: none;
}

.search-card .input-group:focus-within {
  border-color: #18181b;
  box-shadow: 0 0 0 0.2rem rgba(24, 24, 27, 0.1);
}

.search-card .input-group-text {
  background: #fafafa;
  border: none;
  color: #18181b;
  font-weight: 600;
}

.search-card .form-control {
  border: none;
  padding: 0.9rem 1rem;
  font-weight: 500;
  color: #18181b;
}

.search-card .form-control::placeholder {
  color: #a1a1aa;
}

.search-card .form-control:focus {
  box-shadow: none;
}

.search-card .btn-search {
  border: none;
  background: #18181b;
  color: #fafafa;
  font-weight: 600;
  padding: 0.85rem 1.4rem;
  transition: all 0.2s ease;
}

.search-card .btn-search:hover {
  background: #27272a;
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(24, 24, 27, 0.25);
}

.search-meta {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-wrap: wrap;
  margin-top: 0.75rem;
}

.search-chip {
  background: #f4f4f5;
  color: #18181b;
  padding: 6px 12px;
  border-radius: 9999px;
  font-weight: 600;
  font-size: 0.85rem;
  border: 1px solid #e4e4e7;
}

/* Empty State */
.avatar-xl {
  width: 80px;
  height: 80px;
}

.bg-label-primary {
  background: #f4f4f5 !important;
  color: #18181b !important;
}

/* Text Colors */
.text-primary {
  color: #18181b !important;
}

.text-muted {
  color: #71717a !important;
}

.text-dark {
  color: #18181b !important;
}

/* Info Section Border */
.border-bottom {
  border-color: #e4e4e7 !important;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
  .hover-card:hover {
    transform: translateY(-4px);
  }
}

/* Card Body Spacing */
.card-body {
  border-radius: 12px;
}

/* Header Card */
.card.mb-4.border-0.shadow-sm {
  border: 1px solid #e4e4e7 !important;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06) !important;
}

/* Icon Sizing */
.ri-24px {
  font-size: 24px;
}

.ri-18px {
  font-size: 18px;
}

.ri-16px {
  font-size: 16px;
}

.ri-36px {
  font-size: 36px;
}
/* Hide duplicate pagination summary from Laravel Links */
.pagination-wrapper nav .text-muted {
    display: none !important;
}
</style>
@endsection
