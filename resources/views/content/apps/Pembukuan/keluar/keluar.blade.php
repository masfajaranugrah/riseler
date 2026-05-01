@extends('layouts/layoutMaster')

@section('title', 'Pengeluaran Administrasi - Pembukuan')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
])
<style>
.category-header {
    cursor: pointer;
    transition: background-color 0.2s ease;
}
.category-header:hover {
    background-color: #e9ecef !important;
}
.category-detail {
    display: none;
}
.category-detail.show {
    display: table-row;
}
.detail-table {
    margin: 0;
}
.detail-table td {
    padding: 0.35rem 0.75rem;
    font-size: 0.85rem;
    border-bottom: 1px solid #f0f0f0;
}
.detail-table tr:last-child td {
    border-bottom: none;
}
.toggle-icon {
    transition: transform 0.2s ease;
    display: inline-block;
}
.toggle-icon.rotated {
    transform: rotate(90deg);
}
.badge-kode {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}
.card {
    border-radius: 12px;
}
.no-data-badge {
    color: #999;
    font-style: italic;
    font-size: 0.85rem;
}
</style>
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
])
@endsection

@section('content')

{{-- Filter Bulan/Tahun --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="fw-bold mb-0">
                    <i class="ri-arrow-up-line me-2"></i>Pengeluaran Administrasi
                    <span class="badge bg-secondary ms-2">{{ $periodeLabel }}</span>
                </h5>
            </div>
            <div class="col-md-6">
                <form method="GET" action="{{ route('pembukuan.keluar') }}" class="d-flex gap-2 justify-content-end flex-wrap">
                    <select name="bulan" class="form-select form-select-sm" style="width: auto;">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $bulan == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->locale('id')->isoFormat('MMMM') }}
                            </option>
                        @endfor
                    </select>
                    <select name="tahun" class="form-select form-select-sm" style="width: auto;">
                        @for ($y = date('Y') - 3; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="btn btn-sm btn-dark"><i class="ri-filter-line me-1"></i>Filter</button>
                    <a href="{{ route('pembukuan.keluar.export', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-sm btn-success">
                        <i class="ri-file-excel-2-line me-1"></i>Export Excel
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Ringkasan Total --}}
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted"><i class="ri-money-dollar-circle-line me-1"></i>Total Pengeluaran</h6>
                <h3 class="fw-bold text-danger">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</h3>
                <small class="text-muted">Periode: {{ $periodeLabel }}</small>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Pengeluaran Grouped --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">
            <i class="ri-list-check-2 me-1"></i>Rincian Pengeluaran per Kategori
        </h6>
        <small class="text-muted"><i class="ri-information-line me-1"></i>Klik kategori untuk melihat rincian</small>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:40px">#</th>
                    <th style="width:60px">Kode</th>
                    <th>Kategori</th>
                    <th class="text-end" style="width:180px">Total</th>
                    <th class="text-center" style="width:80px">Detail</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($pengeluaranGrouped as $index => $group)
                <tr class="category-header" data-target="detail-{{ $index }}">
                    <td>{{ $no++ }}</td>
                    <td><span class="badge bg-label-primary badge-kode">{{ $group['kode'] }}</span></td>
                    <td class="fw-semibold">{{ $group['kategori'] }}</td>
                    <td class="text-end fw-semibold {{ $group['jumlah'] > 0 ? 'text-danger' : '' }}">
                        Rp {{ number_format($group['jumlah'], 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        @if(count($group['items']) > 0)
                            <span class="toggle-icon" id="icon-{{ $index }}"><i class="ri-arrow-right-s-line"></i></span>
                            <span class="badge bg-label-secondary">{{ count($group['items']) }}</span>
                        @else
                            <span class="no-data-badge">-</span>
                        @endif
                    </td>
                </tr>
                @if(count($group['items']) > 0)
                <tr class="category-detail" id="detail-{{ $index }}">
                    <td colspan="5" class="p-0" style="background: #fafbfc;">
                        <div class="px-4 py-2">
                            <table class="table detail-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:40px; font-size:0.8rem" class="text-muted">No</th>
                                        <th style="width:120px; font-size:0.8rem" class="text-muted">Tanggal</th>
                                        <th style="font-size:0.8rem" class="text-muted">Keterangan</th>
                                        <th class="text-end" style="width:160px; font-size:0.8rem" class="text-muted">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($group['items'] as $idx => $item)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item['tanggal'])->format('d-m-Y') }}</td>
                                        <td>{{ $item['keterangan'] }}</td>
                                        <td class="text-end">Rp {{ number_format($item['jumlah'], 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr style="border-top: 2px solid #dee2e6;">
                                        <td colspan="3" class="text-end fw-bold" style="font-size:0.85rem">Subtotal {{ $group['kategori'] }}</td>
                                        <td class="text-end fw-bold text-danger" style="font-size:0.85rem">Rp {{ number_format($group['jumlah'], 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
            <tfoot class="table-secondary">
                <tr>
                    <th colspan="3" class="fw-bold">Total Pengeluaran</th>
                    <th class="text-end fw-bold text-danger">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle category detail
        document.querySelectorAll('.category-header').forEach(function(header) {
            header.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const detailRow = document.getElementById(targetId);
                const icon = document.getElementById('icon-' + targetId.replace('detail-', ''));
                
                if (detailRow) {
                    detailRow.classList.toggle('show');
                    if (icon) {
                        icon.classList.toggle('rotated');
                    }
                }
            });
        });
    });
</script>
@endsection
