@extends('layouts/layoutMaster')

@section('title', 'Buku Besar Pembukuan')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
])
<style>
/* ========================================= */
/* SHADCN UI STYLE - BLACK & WHITE */
/* ========================================= */
:root {
  --primary-color: #18181b;
  --gray-border: #e4e4e7;
  --gray-bg: #f4f4f5;
}

/* Modern Card Styles */
.stats-card {
  border-radius: 12px;
  transition: transform 0.2s, box-shadow 0.2s;
  border: none;
}

.card-header-modern {
  background: #18181b;
  color: #fafafa;
  border-radius: 12px 12px 0 0 !important;
  padding: 1.25rem 1.5rem;
  border: none;
}

.card-header-modern h5 { color: #fafafa !important; }
.card-header-modern small { color: rgba(255,255,255,0.75) !important; }

.table-modern {
  border-radius: 8px;
  overflow: hidden;
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
}

.table-modern tbody tr {
  transition: all 0.2s;
  border-bottom: 1px solid #e4e4e7;
}

.table-modern tbody tr:hover:not(.row-total) {
  background-color: #f4f4f5 !important;
}

.row-pemasukan { background-color: rgba(24, 24, 27, 0.02); }
.row-pengeluaran { background-color: rgba(24, 24, 27, 0.04); }

.row-total {
  background: #18181b !important;
  border-top: 3px solid #18181b !important;
  font-weight: 700;
}

.row-total td { color: #fafafa !important; }
.row-total:hover { background: #18181b !important; transform: none !important; }
.row-total .text-success { color: #4ade80 !important; }
.row-total .text-danger { color: #f87171 !important; }
.row-total .text-muted { color: rgba(255,255,255,0.7) !important; }
.row-total .badge.bg-primary { background: #fafafa !important; color: #18181b !important; }

.filter-container {
  display: flex;
  gap: 10px;
  align-items: center;
  flex-wrap: wrap;
}

.filter-select {
  min-width: 130px;
  border-radius: 8px;
  border: 1px solid rgba(255,255,255,0.3);
  background: rgba(255,255,255,0.1);
  color: #fafafa;
  font-weight: 600;
  padding: 0.5rem 1rem;
  cursor: pointer;
  transition: all 0.3s;
}

.filter-select:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2);
  border-color: #fafafa;
  background: rgba(255,255,255,0.15);
}

.filter-select option { color: #18181b; background: #fff; }
.filter-select:disabled { opacity: 0.6; pointer-events: none; cursor: not-allowed; }

.btn-outline-light {
  border: 1px solid rgba(255,255,255,0.5);
  color: #fafafa;
  background: transparent;
  transition: all 0.3s;
  border-radius: 8px;
}

.btn-outline-light:hover {
  background: rgba(255, 255, 255, 0.15);
  border-color: #fafafa;
  color: #fafafa;
}

.filter-mode-badge {
  display: inline-block;
  padding: 0.35rem 0.65rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 600;
  margin-left: 0.5rem;
}

.mode-harian {
  background: rgba(255, 255, 255, 0.15);
  color: #fafafa;
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.mode-bulanan {
  background: rgba(255, 255, 255, 0.15);
  color: #fafafa;
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.dataTables_wrapper .dataTables_filter { display: none; }
.dataTables_wrapper .dataTables_length { display: none; }
.dataTables_wrapper .dataTables_info { padding-top: 1rem; font-size: 0.875rem; }
.dataTables_wrapper .dataTables_paginate { padding-top: 1rem; }

.loading-overlay {
  position: fixed;
  inset: 0;
  background: rgba(24, 24, 27, 0.5);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.loading-overlay .spinner-border { width: 3rem; height: 3rem; }

/* Empty state styling */
.empty-state { text-align: center; padding: 3rem 1rem; }
.empty-state i { font-size: 4rem; opacity: 0.3; color: #71717a; }

/* Badge Styles */
.badge.bg-label-primary { background: #18181b !important; color: #fafafa !important; font-weight: 600; }
.badge.bg-success { background: #18181b !important; color: #fafafa !important; }
.badge.bg-danger { background: #18181b !important; color: #fafafa !important; }
.badge.bg-primary { background: #18181b !important; color: #fafafa !important; }

/* Pagination */
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
}
.pagination .page-item .page-link:hover { background-color: #f4f4f5; border-color: #18181b; }
.pagination .page-item.active .page-link { background-color: #18181b !important; border-color: #18181b !important; color: #fafafa !important; }
</style>
@endsection

@section('content')

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay d-none">
    <div class="spinner-border text-light" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<!-- Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-header card-header-modern d-flex justify-content-between align-items-center flex-wrap">
        <div class="mb-2 mb-md-0">
            <h5 class="mb-0 fw-bold">
                <i class="ri-book-line me-2"></i>Laporan Debit & Credit
                
                @if(($filterMode ?? 'harian') == 'harian')
                    <span class="filter-mode-badge mode-harian">
                        <i class="ri-calendar-check-line me-1"></i>Hari Ini
                    </span>
                @else
                    <span class="filter-mode-badge mode-bulanan">
                        <i class="ri-calendar-line me-1"></i>Per Bulan
                    </span>
                @endif
            </h5>
            <small class="opacity-75">
                @if(($filterMode ?? 'harian') == 'harian')
                    Transaksi hari ini: <strong>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</strong>
                @else
                    @php
                        $bulanNama = [
                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                        ];
                        $currentBulan = $bulan ?? date('m');
                        $currentTahun = $tahun ?? date('Y');
                    @endphp
                    <input type="hidden" id="inputBulan" value="{{ $bulan ?? date('m') }}">
                    <input type="hidden" id="inputTahun" value="{{ $tahun ?? date('Y') }}">
                    Periode: <strong>{{ $bulanNama[$currentBulan] ?? 'Unknown' }} {{ $currentTahun }}</strong>
                @endif
            </small>
        </div>
        
        <!-- Filter Periode Bulanan -->
        <div class="filter-container">
            <select name="bulan" class="filter-select" id="filterBulan">
                @php
                    $selectedBulan = $bulan ?? date('m');
                @endphp
                <option value="01" {{ $selectedBulan == '01' ? 'selected' : '' }}>Januari</option>
                <option value="02" {{ $selectedBulan == '02' ? 'selected' : '' }}>Februari</option>
                <option value="03" {{ $selectedBulan == '03' ? 'selected' : '' }}>Maret</option>
                <option value="04" {{ $selectedBulan == '04' ? 'selected' : '' }}>April</option>
                <option value="05" {{ $selectedBulan == '05' ? 'selected' : '' }}>Mei</option>
                <option value="06" {{ $selectedBulan == '06' ? 'selected' : '' }}>Juni</option>
                <option value="07" {{ $selectedBulan == '07' ? 'selected' : '' }}>Juli</option>
                <option value="08" {{ $selectedBulan == '08' ? 'selected' : '' }}>Agustus</option>
                <option value="09" {{ $selectedBulan == '09' ? 'selected' : '' }}>September</option>
                <option value="10" {{ $selectedBulan == '10' ? 'selected' : '' }}>Oktober</option>
                <option value="11" {{ $selectedBulan == '11' ? 'selected' : '' }}>November</option>
                <option value="12" {{ $selectedBulan == '12' ? 'selected' : '' }}>Desember</option>
            </select>
            
            <select name="tahun" class="filter-select" id="filterTahun">
                @php
                    $selectedTahun = $tahun ?? date('Y');
                @endphp
                @for($i = date('Y') - 2; $i <= date('Y') + 2; $i++)
                    <option value="{{ $i }}" {{ $selectedTahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
            
            <!-- Tombol Reset Filter -->
            @if(request('bulan') && request('tahun'))
            <button type="button" class="btn btn-sm btn-outline-light" id="btnResetFilter" title="Kembali ke Hari Ini">
                <i class="ri-refresh-line"></i> Reset
            </button>
            @endif
            
            <!-- Tombol Export -->
            <a href="{{ route('pembukuan.masuk.export', ['bulan' => request('bulan', date('m')), 'tahun' => request('tahun', date('Y'))]) }}" class="btn btn-sm btn-success" title="Export Excel">
                <i class="ri-file-excel-2-line"></i> Export
            </a>
        </div>
    </div>
    
    <div class="card-body p-0">
        @if(count($ledgerData) > 0)
        <!-- JIKA ADA DATA: Tampilkan Table dengan DataTables -->
        <div class="table-responsive p-3">
            <table class="table table-modern table-hover align-middle mb-0" id="ledgerTable">
                <thead>
                    <tr>
                        <th style="width: 20%;"><i class="ri-calendar-line me-2"></i>Tanggal</th>
                        <th style="width: 32%;" class="text-start">
                            <i class="ri-arrow-down-circle-line me-2 text-success"></i>DEBIT
                            <small class="d-block text-muted" style="font-size: 0.7rem; font-weight: normal;">(Pemasukan)</small>
                        </th>
                        <th style="width: 32%;" class="text-start">
                            <i class="ri-arrow-up-circle-line me-2 text-danger"></i>CREDIT
                            <small class="d-block text-muted" style="font-size: 0.7rem; font-weight: normal;">(Pengeluaran)</small>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ledgerData as $item)
                    <!-- Satu Baris per Tanggal (Debit + Credit) -->
                    <tr>
                        <td>
                            <span class="badge bg-label-primary">
                                {{ \Carbon\Carbon::parse($item['tanggal'])->format('d M Y') }}
                            </span>
                        </td>
                        <td class="text-start">
                            @if($item['total_masuk'] > 0)
                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 fw-bold">
                                <i class="ri-add-line me-1"></i>Rp {{ number_format($item['total_masuk'],0,',','.') }}
                            </span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-start">
                            @if($item['total_keluar'] > 0)
                            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 fw-bold">
                                <i class="ri-subtract-line me-1"></i>Rp {{ number_format($item['total_keluar'],0,',','.') }}
                            </span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    
                    <!-- Baris Total -->
                    <tr class="row-total">
                        <td>
                            <span class="badge bg-primary px-4 py-2 fw-bold">
                                <i class="ri-calculator-line me-1"></i>TOTAL
                            </span>
                        </td>
                        <td class="text-start">
                            <div class="d-flex flex-column">
                                <small class="text-muted mb-1">Total DEBIT (Pemasukan)</small>
                                <span class="text-success fw-bold fs-6">
                                    Rp {{ number_format($todayTotalMasuk ?? 0,0,',','.') }}
                                </span>
                            </div>
                        </td>
                        <td class="text-start">
                            <div class="d-flex flex-column">
                                <small class="text-muted mb-1">Total CREDIT (Pengeluaran)</small>
                                <span class="text-danger fw-bold fs-6">
                                    Rp {{ number_format($todayTotalKeluar ?? 0,0,',','.') }}
                                </span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @else
        <!-- JIKA TIDAK ADA DATA: Tampilkan Empty State (TANPA DataTables) -->
        <div class="empty-state">
            <i class="ri-inbox-line mb-3"></i>
            @if(($filterMode ?? 'harian') == 'harian')
                <h5 class="text-muted">Tidak ada transaksi hari ini</h5>
                <p class="text-muted mb-0">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
            @else
                @php
                    $bulanNama = [
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                    ];
                    $currentBulan = $bulan ?? date('m');
                    $currentTahun = $tahun ?? date('Y');
                @endphp
                <h5 class="text-muted">Tidak ada transaksi untuk periode ini</h5>
                <p class="text-muted mb-0">{{ $bulanNama[$currentBulan] ?? 'Unknown' }} {{ $currentTahun }}</p>
            @endif
        </div>
        @endif
    </div>
</div>

@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
])
@endsection

@section('page-script')
<script>
(function() {
    'use strict';
    
    console.log('=== PEMBUKUAN SCRIPT LOADED ===');
    console.log('Has Data:', {{ count($ledgerData) > 0 ? 'true' : 'false' }});
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initApp);
    } else {
        initApp();
    }
    
    function initApp() {
        console.log('=== DOM READY ===');
        
        const filterBulan = document.getElementById('filterBulan');
        const filterTahun = document.getElementById('filterTahun');
        const btnResetFilter = document.getElementById('btnResetFilter');
        const loadingOverlay = document.getElementById('loadingOverlay');
        const hasData = {{ count($ledgerData) > 0 ? 'true' : 'false' }};
        
        // HANYA Initialize DataTables JIKA ADA DATA
        if (hasData && typeof jQuery !== 'undefined' && typeof jQuery.fn.DataTable !== 'undefined') {
            try {
                jQuery('#ledgerTable').DataTable({
                    paging: true,
                    pageLength: 50,
                    lengthMenu: [25, 50, 100],
                    searching: false,
                    ordering: true,
                    info: true,
                    responsive: false,
                    order: [[0, 'asc']],
                    columnDefs: [
                        { orderable: true, targets: [0] },
                        { orderable: false, targets: [1, 2] }
                    ],
                    language: {
                        paginate: {
                            previous: '<i class="ri-arrow-left-s-line"></i>',
                            next: '<i class="ri-arrow-right-s-line"></i>'
                        },
                        info: "Menampilkan _START_ - _END_ dari _TOTAL_ baris",
                        infoEmpty: "Tidak ada data",
                        zeroRecords: "Tidak ada transaksi",
                        emptyTable: "Tidak ada transaksi"
                    },
                    drawCallback: function() {
                        const totalRow = document.querySelector('#ledgerTable tbody tr.row-total');
                        if (totalRow) {
                            document.querySelector('#ledgerTable tbody').appendChild(totalRow);
                        }
                    }
                });
                console.log('DataTable initialized successfully');
            } catch (e) {
                console.error('DataTable initialization failed:', e);
            }
        } else {
            console.log('Skipping DataTables initialization - No data or libraries not available');
        }
        
        // Filter handlers
        if (filterBulan) {
            filterBulan.addEventListener('change', handleFilterChange);
        }
        
        if (filterTahun) {
            filterTahun.addEventListener('change', handleFilterChange);
        }
        
        if (btnResetFilter) {
            btnResetFilter.addEventListener('click', function() {
                console.log('Reset button clicked');
                showLoading();
                window.location.href = '{{ route("pembukuan.index") }}';
            });
        }
        
        function handleFilterChange() {
            const bulan = filterBulan ? filterBulan.value : '';
            const tahun = filterTahun ? filterTahun.value : '';
            
            console.log('=== FILTER CHANGED ===');
            console.log('Bulan:', bulan, 'Tahun:', tahun);
            
            if (bulan && tahun) {
                console.log('Applying filter...');
                applyFilter(bulan, tahun);
            }
        }
        
        function applyFilter(bulan, tahun) {
            if (filterBulan) filterBulan.disabled = true;
            if (filterTahun) filterTahun.disabled = true;
            
            showLoading();
            
            const url = '{{ route("pembukuan.index") }}?bulan=' + bulan + '&tahun=' + tahun;
            console.log('Redirecting to:', url);
            
            setTimeout(function() {
                window.location.href = url;
            }, 300);
        }
        
        function showLoading() {
            if (loadingOverlay) {
                loadingOverlay.classList.remove('d-none');
            }
        }
        
        console.log('=== INITIALIZATION COMPLETE ===');
    }
})();
</script>
@endsection
