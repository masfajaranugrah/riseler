@extends('layouts/layoutMaster')

@section('title', 'Potongan PPN')

@section('content')
<style>
  /* Shadcn-like Minimalist Overrides */
  .table > :not(caption) > * > * {
    border-bottom-color: #e4e4e7;
  }
  .pagination .page-item .page-link {
    color: #09090b;
    border-color: transparent;
    background-color: #f4f4f5;
    font-weight: 500;
  }
  .pagination .page-item.active .page-link {
    background-color: #09090b !important;
    border-color: #09090b !important;
    color: #ffffff !important;
  }
  .pagination .page-item.disabled .page-link {
    color: #a1a1aa;
    background-color: #fafafa;
  }
  .pagination .page-link:hover:not(.active) {
    background-color: #e4e4e7;
  }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card border-0 shadow-sm rounded-4 mb-4" style="border: 1px solid #e4e4e7 !important;">
    <div class="card-header border-0 bg-white pt-4 pb-3 px-4">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
          <h4 class="mb-1 fw-bold text-dark d-flex align-items-center" style="color: #09090b !important;">
            <i class="ri-money-dollar-circle-line me-2"></i>Data Potongan PPN
          </h4>
          <p class="mb-0 text-muted small" style="color: #71717a !important;">Kelola dan monitor potongan PPN dari laba perusahaan</p>
        </div>
        <div class="d-flex align-items-center gap-2">
          <form action="{{ route('income.ppn') }}" method="GET" class="d-flex gap-2">
            <div class="position-relative">
              <i class="ri-search-line position-absolute" style="top:50%; transform:translateY(-50%); left:12px; color:#71717a;"></i>
              <input type="text" class="form-control rounded-3 shadow-none" style="padding-left:36px; min-width:250px; border: 1px solid #e4e4e7;" name="search" value="{{ request('search') }}" placeholder="Cari kode, pelanggan, keterangan...">
            </div>
            <button type="submit" class="btn rounded-3 px-4 fw-medium d-flex align-items-center" style="background-color: #09090b; color: white;">
              <i class="ri-search-line me-1"></i>Cari
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Date Filter Section -->
    <div class="px-4 py-3" style="background-color: #f8fafc; border-top: 1px solid #e4e4e7; border-bottom: 1px solid #e4e4e7;">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h6 class="mb-0 fw-bold d-flex align-items-center" style="color: #09090b;">
            <i class="ri-calendar-event-line me-2"></i>Total Potongan PPN Per Bulan
          </h6>
          <small style="color: #a1a1aa;">Periode: {{ $monthLabel }}</small>
        </div>
        <form action="{{ route('income.ppn') }}" method="GET" class="d-flex gap-2">
          @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
          @endif
          <select class="form-select bg-white shadow-sm fw-medium rounded-3" style="border: 1px solid #e4e4e7; color: #09090b; min-width: 120px;" name="filter_month" onchange="this.form.submit()">
            @for ($m = 1; $m <= 12; $m++)
              <option value="{{ $m }}" {{ (int)$filterMonth === $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->locale('id')->isoFormat('MMMM') }}</option>
            @endfor
          </select>
          <select class="form-select bg-white shadow-sm fw-medium rounded-3" style="border: 1px solid #e4e4e7; color: #09090b;" name="filter_year" onchange="this.form.submit()">
            @for ($y = (int)date('Y') - 3; $y <= (int)date('Y') + 1; $y++)
              <option value="{{ $y }}" {{ (int)$filterYear === $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
          </select>
        </form>
      </div>
    </div>

    <!-- Total Bar -->
    <div class="text-white px-4 py-3 d-flex justify-content-between align-items-center mb-0" style="background-color: #27272a;">
       <div class="d-flex align-items-center">
           <i class="ri-money-dollar-circle-fill me-2 fs-5" style="color: #a1a1aa;"></i>
           <span class="fw-semibold">Total {{ $monthLabel }}</span>
       </div>
       <strong class="fs-5 fw-bold">Rp {{ number_format($monthlyTotal, 0, ',', '.') }}</strong>
    </div>

    <div class="card-body p-0 bg-white">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="width: 100%;">
          <thead style="background-color: #ffffff;">
            <tr>
              <th class="text-uppercase fw-bold py-3 px-4" style="color: #a1a1aa; font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 1px solid #e4e4e7;"># No</th>
              <th class="text-uppercase fw-bold py-3" style="color: #a1a1aa; font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 1px solid #e4e4e7;"><i class="ri-user-3-line me-1"></i>Pelanggan</th>
              <th class="text-uppercase fw-bold py-3" style="color: #a1a1aa; font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 1px solid #e4e4e7;"><i class="ri-barcode-line me-1"></i>No. ID</th>
              <th class="text-uppercase fw-bold py-3" style="color: #a1a1aa; font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 1px solid #e4e4e7;"><i class="ri-file-text-line me-1"></i>Keterangan</th>
              <th class="text-uppercase fw-bold py-3" style="color: #a1a1aa; font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 1px solid #e4e4e7;"><i class="ri-money-dollar-circle-line me-1"></i>Jumlah</th>
              <th class="text-uppercase fw-bold py-3" style="color: #a1a1aa; font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 1px solid #e4e4e7;"><i class="ri-calendar-line me-1"></i>Tanggal</th>
            </tr>
          </thead>
          <tbody class="border-top-0 bg-white">
            @forelse($potonganPpn as $idx => $item)
              <tr>
                <td class="fw-semibold px-4" style="width: 60px; color: #a1a1aa;">{{ $potonganPpn->firstItem() + $idx }}</td>
                <td>
                    <strong class="text-uppercase fw-bold" style="color: #09090b; font-size: 0.85rem;">{{ $item->pelanggan->nama_lengkap ?? '-' }}</strong>
                </td>
                <td>
                    <span class="badge rounded-pill px-3 py-1 fw-medium" style="background-color: #e4e4e7; color: #3f3f46; font-size: 0.75rem;">{{ $item->pelanggan->nomer_id ?? '-' }}</span>
                </td>
                <td>
                    <span style="color: #a1a1aa; font-size: 0.85rem;">{{ $item->keterangan }}</span>
                </td>
                <td>
                    <div class="fw-bold" style="color: #09090b; font-size: 0.85rem; line-height: 1;">Rp</div>
                    <div class="fw-bold" style="color: #09090b; font-size: 0.95rem;">{{ number_format($item->jumlah, 0, ',', '.') }}</div>
                </td>
                <td style="color: #a1a1aa; font-size: 0.85rem;">{{ \Carbon\Carbon::parse($item->tanggal_masuk)->translatedFormat('d M Y') }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-5" style="color: #a1a1aa;">
                    <i class="ri-inbox-line fs-1 opacity-50 mb-3 d-block"></i>
                    Belum ada data potongan PPN.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="p-4 d-flex flex-column flex-lg-row justify-content-between align-items-center gap-3 bg-white" style="border-top: 1px solid #e4e4e7;">
        @php
          $from = $potonganPpn->firstItem() ?? 0;
          $to = $potonganPpn->lastItem() ?? 0;
          $total = $potonganPpn->total();
          $currentPage = $potonganPpn->currentPage();
          $lastPage = max(1, $potonganPpn->lastPage());
          $startPage = max(1, $currentPage - 3);
          $endPage = min($lastPage, $currentPage + 3);
        @endphp

        <div class="fw-medium" style="color: #a1a1aa;">
          Menampilkan {{ $from }} - {{ $to }} dari {{ $total }} data potongan PPN
        </div>

        <nav aria-label="Pagination Potongan PPN">
          <ul class="pagination mb-0 gap-2">
            <li class="page-item {{ $currentPage <= 1 ? 'disabled' : '' }}">
              <a class="page-link rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;" href="{{ $currentPage <= 1 ? '#' : $potonganPpn->url($currentPage - 1) }}" aria-label="Previous">
                <span aria-hidden="true">&lsaquo;</span>
              </a>
            </li>

            @if($startPage > 1)
              <li class="page-item"><a class="page-link rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;" href="{{ $potonganPpn->url(1) }}">1</a></li>
              @if($startPage > 2)
                <li class="page-item disabled"><span class="page-link rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;">...</span></li>
              @endif
            @endif

            @for($page = $startPage; $page <= $endPage; $page++)
              <li class="page-item {{ $page === $currentPage ? 'active' : '' }}">
                <a class="page-link rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;" href="{{ $potonganPpn->url($page) }}">{{ $page }}</a>
              </li>
            @endfor

            @if($endPage < $lastPage)
              @if($endPage < $lastPage - 1)
                <li class="page-item disabled"><span class="page-link rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;">...</span></li>
              @endif
              <li class="page-item"><a class="page-link rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;" href="{{ $potonganPpn->url($lastPage) }}">{{ $lastPage }}</a></li>
            @endif

            <li class="page-item {{ $currentPage >= $lastPage ? 'disabled' : '' }}">
              <a class="page-link rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;" href="{{ $currentPage >= $lastPage ? '#' : $potonganPpn->url($currentPage + 1) }}" aria-label="Next">
                <span aria-hidden="true">&rsaquo;</span>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</div>
@endsection
