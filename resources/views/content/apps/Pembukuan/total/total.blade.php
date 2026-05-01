@extends('layouts.layoutMaster')

@section('title', 'Buku Besar Pembukuan')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
])
<style>
.card {
    border-radius: 12px;
}
.table-bordered th, .table-bordered td {
    border-color: #e9ecef;
}
.table-light {
    background-color: #f8f9fa;
}
.table-secondary {
    background-color: #e9ecef;
}
.btn-xs {
    padding: 0.15rem 0.4rem;
    font-size: 0.75rem;
    line-height: 1.2;
    border-radius: 0.2rem;
}
.spin {
    animation: spin 1s linear infinite;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endsection

@section('content')
<!-- Filter Bulan/Tahun -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="fw-bold mb-0"><i class="ri-calendar-line me-2"></i>Periode: {{ $firstMonth['label'] ?? '-' }}</h5>
            </div>
            <div class="col-md-6">
                <div class="d-flex gap-2 justify-content-end flex-wrap">
                    <form method="GET" action="{{ route('pembukuan.total') }}" class="d-flex gap-2">
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
                    </form>
                    <a href="{{ route('pembukuan.total.export', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-sm btn-dark">
                        <i class="ri-file-excel-2-line me-1"></i>Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Saldo Awal Table -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0"><i class="ri-wallet-3-line me-1"></i>Saldo Awal</h6>
            @if(auth()->check())
                <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#modalSaldoAwal">
                    <i class="ri-edit-2-line me-1"></i>Isi Manual
                </button>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kategori</th>
                        <th class="text-end">Nominal</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Omset Internet Dedicated</td>
                        <td class="text-end">Rp {{ number_format($saldoAwal->omset_dedicated ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modalSaldoAwal">
                                <i class="ri-pencil-line"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>Total Omset Home Net Kotor</td>
                        <td class="text-end">Rp {{ number_format($saldoAwal->omset_homenet_kotor ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modalSaldoAwal">
                                <i class="ri-pencil-line"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>Total Home Net Bersih</td>
                        <td class="text-end">Rp {{ number_format($saldoAwal->omset_homenet_bersih ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modalSaldoAwal">
                                <i class="ri-pencil-line"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="table-secondary">
                        <td class="fw-bold">Total Saldo Awal</td>
                        <td class="text-end fw-bold">Rp {{ number_format(($saldoAwal->omset_dedicated ?? 0) + ($saldoAwal->omset_homenet_bersih ?? 0), 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recap Pemasukan Table -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0"><i class="ri-arrow-down-line me-1"></i>Pemasukan</h6>
            @if(auth()->check())
                <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#modalPemasukan">
                    <i class="ri-edit-2-line me-1"></i>Isi Manual
                </button>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kategori</th>
                        <th class="text-end">Nominal</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table-light">
                        <td colspan="3" class="fw-bold text-muted">
                            <i class="ri-edit-line me-1"></i>Input Manual
                        </td>
                    </tr>
                    <tr>
                        <td class="ps-4">Registrasi</td>
                        <td class="text-end">Rp {{ number_format($firstMonth['pemasukan']['registrasi'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modalPemasukan">
                                <i class="ri-pencil-line"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="table-light">
                        <td colspan="3" class="fw-bold text-muted">
                            <i class="ri-robot-line me-1"></i>Dedicated (Otomatis dari Tagihan)
                        </td>
                    </tr>
                    <tr>
                        <td class="ps-4">Pemasukan Dedicated Kotor</td>
                        <td class="text-end">Rp {{ number_format($firstMonth['pemasukan']['dedicatedKotor'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary"><i class="ri-lock-line"></i> Auto</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="ps-4">
                            <small class="text-muted">
                                <i class="ri-list-check-2 me-1"></i>
                                Pembayaran Lunas Dedicated {{ $firstMonth['pemasukan']['dedicatedPaidLabel'] ?? '-' }}
                            </small>
                            @if(!empty($firstMonth['pemasukan']['dedicatedPaidItems']))
                                <div class="mt-1">
                                    @foreach($firstMonth['pemasukan']['dedicatedPaidItems'] as $item)
                                        <div class="small text-muted">
                                            - {{ $item['nama_pelanggan'] ?? '-' }} | {{ $item['nama_paket'] ?? '-' }} | Rp {{ number_format($item['nominal'] ?? 0, 0, ',', '.') }}
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="small text-muted mt-1">Tidak ada pembayaran lunas paket dedicated.</div>
                            @endif
                        </td>
                        <td class="text-end fw-semibold">Rp {{ number_format($firstMonth['pemasukan']['dedicatedKotor'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary"><i class="ri-lock-line"></i> Auto</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="ps-4">Potongan / Pengembalian</td>
                        <td class="text-end">Rp {{ number_format($firstMonth['pemasukan']['potonganDedicated'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modalPemasukan">
                                <i class="ri-pencil-line"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="ps-4 fw-semibold">Pemasukan Dedicated Bersih</td>
                        <td class="text-end fw-semibold">Rp {{ number_format($firstMonth['pemasukan']['dedicatedBersih'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary"><i class="ri-lock-line"></i> Auto</span>
                        </td>
                    </tr>
                    <tr class="table-light">
                        <td colspan="3" class="fw-bold text-muted">
                            <i class="ri-robot-line me-1"></i>Home Net (Otomatis dari Tagihan)
                        </td>
                    </tr>
                    <tr>
                        <td class="ps-4">Pemasukan Home Net Kotor</td>
                        <td class="text-end">Rp {{ number_format($firstMonth['pemasukan']['homeNetKotor'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary"><i class="ri-lock-line"></i> Auto</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="ps-4">
                            Potongan / Pengembalian
                            <br><small class="text-muted"><i class="ri-arrow-right-s-line"></i>dari Beban Komitmen/Fee</small>
                        </td>
                        <td class="text-end">Rp {{ number_format($firstMonth['pemasukan']['potonganHomeNet'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary"><i class="ri-lock-line"></i> Auto</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="ps-4 fw-semibold">Pemasukan Home Net Bersih</td>
                        <td class="text-end fw-semibold">Rp {{ number_format($firstMonth['pemasukan']['homeNetBersih'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary"><i class="ri-lock-line"></i> Auto</span>
                        </td>
                    </tr>
                    <tr class="table-secondary">
                        <td class="fw-bold">Total Pemasukan</td>
                        <td class="text-end fw-bold">Rp {{ number_format($firstMonth['totalPemasukan'] ?? 0, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recap Pengeluaran Table -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0">
                <i class="ri-arrow-up-line me-1"></i>Pengeluaran 
                <span class="badge bg-secondary ms-2">{{ $firstMonth['pengeluaranPeriodeLabel'] ?? '-' }}</span>
            </h6>
            <small class="text-muted">
                <i class="ri-information-line me-1"></i>
                Tutup Buku: Pengeluaran diambil dari bulan sebelumnya
            </small>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:60px">Kode</th>
                        <th>Kategori</th>
                        <th class="text-end">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($firstMonth['pengeluaran']) && is_array($firstMonth['pengeluaran']))
                        @foreach($firstMonth['pengeluaran'] as $item)
                        <tr>
                            <td>{{ $item['kode'] }}</td>
                            <td>{{ $item['kategori'] }}</td>
                            <td class="text-end">Rp {{ number_format($item['jumlah'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    @endif
                    <tr class="table-secondary">
                        <td colspan="2" class="fw-bold">Total Pengeluaran</td>
                        <td class="text-end fw-bold">Rp {{ number_format($firstMonth['totalPengeluaran'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recap Piutang Table -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0"><i class="ri-money-dollar-circle-line me-1"></i>Piutang</h6>
            @if(auth()->check())
                <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#modalPiutang">
                    <i class="ri-edit-2-line me-1"></i>Isi Manual
                </button>
            @endif
        </div>
        <div class="table-responsive mb-3">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kategori</th>
                        <th class="text-end">Nominal</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $firstMonth['piutang']['dedicatedLabel'] ?? 'Piutang Dedicated' }}</td>
                        <td class="text-end">Rp {{ number_format($firstMonth['piutang']['dedicated'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modalPiutang">
                                <i class="ri-pencil-line"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ $firstMonth['piutang']['homeNetLabel'] ?? 'Piutang HomeNet' }}</td>
                        <td class="text-end">Rp {{ number_format($firstMonth['piutang']['homeNet'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modalPiutang">
                                <i class="ri-pencil-line"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ $firstMonth['piutang']['bulanSebelumnyaLabel'] ?? 'Piutang Bulan Sebelumnya' }}</td>
                        <td class="text-end">Rp {{ number_format($firstMonth['piutang']['bulanSebelumnya'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modalPiutang">
                                <i class="ri-pencil-line"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ $firstMonth['piutang']['periodeSebelumnyaLabel'] ?? 'Piutang Periode Sebelumnya' }}</td>
                        <td class="text-end">Rp {{ number_format($firstMonth['piutang']['periodeSebelumnya'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modalPiutang">
                                <i class="ri-pencil-line"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ $firstMonth['piutang']['tahunLaluLabel'] ?? 'Piutang Tahun Lalu' }}</td>
                        <td class="text-end">Rp {{ number_format($firstMonth['piutang']['tahunLalu'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modalPiutang">
                                <i class="ri-pencil-line"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="table-secondary">
                        <td class="fw-bold">Total Piutang</td>
                        <td class="text-end fw-bold">Rp {{ number_format($firstMonth['totalPiutang'] ?? 0, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Saldo Awal -->
<div class="modal fade" id="modalSaldoAwal" tabindex="-1" aria-labelledby="modalSaldoAwalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSaldoAwalLabel">
                    <i class="ri-edit-2-line me-2"></i>Edit Saldo Awal - {{ $firstMonth['label'] ?? '-' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formSaldoAwal" method="POST" action="{{ route('saldo-awal.store') }}">
                @csrf
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="omset_dedicated" class="form-label">Total Omset Internet Dedicated</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control currency-input" id="omset_dedicated" 
                                   name="omset_dedicated" 
                                   value="{{ old('omset_dedicated', number_format($saldoAwal->omset_dedicated ?? 0, 0, ',', '.')) }}" 
                                   placeholder="0">
                        </div>
                        @error('omset_dedicated')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="omset_homenet_kotor" class="form-label">Total Omset Home Net Kotor</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control currency-input" id="omset_homenet_kotor" 
                                   name="omset_homenet_kotor" 
                                   value="{{ old('omset_homenet_kotor', number_format($saldoAwal->omset_homenet_kotor ?? 0, 0, ',', '.')) }}" 
                                   placeholder="0">
                        </div>
                        @error('omset_homenet_kotor')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="omset_homenet_bersih" class="form-label">Total Home Net Bersih</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control currency-input" id="omset_homenet_bersih" 
                                   name="omset_homenet_bersih" 
                                   value="{{ old('omset_homenet_bersih', number_format($saldoAwal->omset_homenet_bersih ?? 0, 0, ',', '.')) }}" 
                                   placeholder="0">
                        </div>
                        @error('omset_homenet_bersih')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark">
                        <i class="ri-save-line me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Pemasukan -->
<div class="modal fade" id="modalPemasukan" tabindex="-1" aria-labelledby="modalPemasukanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPemasukanLabel">
                    <i class="ri-edit-2-line me-2"></i>Edit Pemasukan Manual - {{ $firstMonth['label'] ?? '-' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPemasukan" method="POST" action="{{ route('saldo-awal.store') }}">
                @csrf
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="pemasukan_registrasi" class="form-label">Registrasi</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control currency-input" id="pemasukan_registrasi" 
                                       name="pemasukan_registrasi" 
                                       value="{{ number_format($saldoAwal->pemasukan_registrasi ?? 0, 0, ',', '.') }}" 
                                       placeholder="0">
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-3">
                    <h6 class="text-muted mb-3"><i class="ri-robot-line me-1"></i>Dedicated (Kotor Otomatis dari Tagihan)</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Pemasukan Dedicated Kotor (Otomatis)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" 
                                       value="{{ number_format($firstMonth['pemasukan']['dedicatedKotor'] ?? 0, 0, ',', '.') }}" 
                                       disabled readonly>
                            </div>
                            <small class="text-muted"><i class="ri-lock-line me-1"></i>Dihitung otomatis dari tagihan Dedicated Lunas</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pemasukan_dedicated_potongan" class="form-label">Potongan / Pengembalian Dedicated</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control currency-input" id="pemasukan_dedicated_potongan" 
                                       name="pemasukan_dedicated_potongan" 
                                       value="{{ number_format($saldoAwal->pemasukan_dedicated_potongan ?? 0, 0, ',', '.') }}" 
                                       placeholder="0">
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-3">
                    <h6 class="text-muted mb-3"><i class="ri-edit-line me-1"></i>Home Net (Input Manual)</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="pemasukan_homenet_kotor" class="form-label">Pemasukan Home Net Kotor</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control currency-input" id="pemasukan_homenet_kotor" 
                                       name="pemasukan_homenet_kotor" 
                                       value="{{ number_format($saldoAwal->pemasukan_homenet_kotor ?? 0, 0, ',', '.') }}" 
                                       placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pemasukan_homenet_potongan" class="form-label">Potongan / Pengembalian</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control currency-input" id="pemasukan_homenet_potongan" 
                                       name="pemasukan_homenet_potongan" 
                                       value="{{ number_format($saldoAwal->pemasukan_homenet_potongan ?? 0, 0, ',', '.') }}" 
                                       placeholder="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="pemasukan_homenet_bersih" class="form-label">Pemasukan Home Net Bersih</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control currency-input" id="pemasukan_homenet_bersih" 
                                       name="pemasukan_homenet_bersih" 
                                       value="{{ number_format($saldoAwal->pemasukan_homenet_bersih ?? 0, 0, ',', '.') }}" 
                                       placeholder="0">
                            </div>
                            <small class="text-muted">Biasanya = Home Net Kotor - Potongan</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark">
                        <i class="ri-save-line me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Piutang -->
<div class="modal fade" id="modalPiutang" tabindex="-1" aria-labelledby="modalPiutangLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPiutangLabel">
                    <i class="ri-edit-2-line me-2"></i>Edit Piutang Manual - {{ $firstMonth['label'] ?? '-' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPiutang" method="POST" action="{{ route('saldo-awal.store') }}">
                @csrf
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="piutang_dedicated" class="form-label">{{ $firstMonth['piutang']['dedicatedLabel'] ?? 'Piutang Dedicated' }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control currency-input" id="piutang_dedicated" 
                                           name="piutang_dedicated" 
                                           value="{{ number_format($saldoAwal->piutang_dedicated ?? 0, 0, ',', '.') }}" 
                                           placeholder="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="piutang_homenet" class="form-label">{{ $firstMonth['piutang']['homeNetLabel'] ?? 'Piutang HomeNet' }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control currency-input" id="piutang_homenet" 
                                           name="piutang_homenet" 
                                           value="{{ number_format($saldoAwal->piutang_homenet ?? 0, 0, ',', '.') }}" 
                                           placeholder="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-3">
                    
                    <div class="mb-3">
                        <label for="piutang_bulan_sebelumnya" class="form-label">{{ $firstMonth['piutang']['bulanSebelumnyaLabel'] ?? 'Piutang Bulan Sebelumnya' }}</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control currency-input" id="piutang_bulan_sebelumnya" 
                                   name="piutang_bulan_sebelumnya" 
                                   value="{{ number_format($saldoAwal->piutang_bulan_sebelumnya ?? 0, 0, ',', '.') }}" 
                                   placeholder="0">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="piutang_periode_sebelumnya" class="form-label">{{ $firstMonth['piutang']['periodeSebelumnyaLabel'] ?? 'Piutang Periode Sebelumnya' }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control currency-input" id="piutang_periode_sebelumnya" 
                                           name="piutang_periode_sebelumnya" 
                                           value="{{ number_format($saldoAwal->piutang_periode_sebelumnya ?? 0, 0, ',', '.') }}" 
                                           placeholder="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="piutang_tahun_lalu" class="form-label">{{ $firstMonth['piutang']['tahunLaluLabel'] ?? 'Piutang Tahun Lalu' }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control currency-input" id="piutang_tahun_lalu" 
                                           name="piutang_tahun_lalu" 
                                           value="{{ number_format($saldoAwal->piutang_tahun_lalu ?? 0, 0, ',', '.') }}" 
                                           placeholder="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-secondary mb-0">
                        <small>
                            <i class="ri-information-line me-1"></i>
                            <strong>Total Piutang:</strong> akan dihitung otomatis dari semua kategori di atas
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark">
                        <i class="ri-save-line me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
])
@endsection

@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    console.log('Script loaded');
    
    // Currency input formatting
    $(document).on('input', '.currency-input', function() {
        let value = $(this).val().replace(/[^\d]/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        $(this).val(value);
    });

    // Reset form when modal opens
    $('#modalSaldoAwal').on('shown.bs.modal', function () {
        $('.currency-input').each(function() {
            let rawValue = $(this).data('raw-value') || '0';
            $(this).val(parseInt(rawValue).toLocaleString('id-ID'));
        });
    });

    // Store raw values when modal opens
    $('#modalSaldoAwal').on('show.bs.modal', function () {
        $('.currency-input').each(function() {
            let rawValue = $(this).val().replace(/[^\d]/g, '') || '0';
            $(this).data('raw-value', rawValue);
        });
    });

    // Form submission with AJAX
    $('#formSaldoAwal').on('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted');
        
        // Convert currency format to number
        let omset_dedicated = $('#omset_dedicated').val().replace(/[^\d]/g, '') || '0';
        let omset_homenet_kotor = $('#omset_homenet_kotor').val().replace(/[^\d]/g, '') || '0';
        let omset_homenet_bersih = $('#omset_homenet_bersih').val().replace(/[^\d]/g, '') || '0';
        
        let formData = new FormData(this);
        formData.set('omset_dedicated', omset_dedicated);
        formData.set('omset_homenet_kotor', omset_homenet_kotor);
        formData.set('omset_homenet_bersih', omset_homenet_bersih);
        
        console.log('Form data:', Object.fromEntries(formData));
        
        let submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="ri-loader-4-line me-1 spin"></i>Menyimpan...');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log('Success:', response);
                
                $('#modalSaldoAwal').modal('hide');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message || 'Data berhasil disimpan',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                
                let errorMessage = 'Terjadi kesalahan';
                
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = [];
                    $.each(xhr.responseJSON.errors, function(field, messages) {
                        errors.push(messages[0]);
                    });
                    errorMessage = errors.join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: errorMessage
                });
                
                submitBtn.prop('disabled', false).html('<i class="ri-save-line me-1"></i>Simpan');
            }
        });
    });

    // Form Pemasukan submission with AJAX
    $('#formPemasukan').on('submit', function(e) {
        e.preventDefault();
        console.log('Form Pemasukan submitted');
        
        // Convert currency format to number
        let pemasukan_registrasi = $('#pemasukan_registrasi').val().replace(/[^\d]/g, '') || '0';
        let pemasukan_dedicated_potongan = $('#pemasukan_dedicated_potongan').val().replace(/[^\d]/g, '') || '0';
        let pemasukan_homenet_kotor = $('#pemasukan_homenet_kotor').val().replace(/[^\d]/g, '') || '0';
        let pemasukan_homenet_potongan = $('#pemasukan_homenet_potongan').val().replace(/[^\d]/g, '') || '0';
        let pemasukan_homenet_bersih = $('#pemasukan_homenet_bersih').val().replace(/[^\d]/g, '') || '0';
        
        let formData = new FormData(this);
        formData.set('pemasukan_registrasi', pemasukan_registrasi);
        formData.set('pemasukan_dedicated_potongan', pemasukan_dedicated_potongan);
        formData.set('pemasukan_homenet_kotor', pemasukan_homenet_kotor);
        formData.set('pemasukan_homenet_potongan', pemasukan_homenet_potongan);
        formData.set('pemasukan_homenet_bersih', pemasukan_homenet_bersih);
        
        console.log('Form Pemasukan data:', Object.fromEntries(formData));
        
        let submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="ri-loader-4-line me-1 spin"></i>Menyimpan...');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log('Success:', response);
                
                $('#modalPemasukan').modal('hide');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message || 'Data pemasukan berhasil disimpan',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                
                let errorMessage = 'Terjadi kesalahan';
                
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = [];
                    $.each(xhr.responseJSON.errors, function(field, messages) {
                        errors.push(messages[0]);
                    });
                    errorMessage = errors.join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: errorMessage
                });
                
                submitBtn.prop('disabled', false).html('<i class="ri-save-line me-1"></i>Simpan');
            }
        });
    });

    // Form Piutang submission with AJAX
    $('#formPiutang').on('submit', function(e) {
        e.preventDefault();
        console.log('Form Piutang submitted');
        
        // Convert currency format to number
        let piutang_dedicated = $('#piutang_dedicated').val().replace(/[^\d]/g, '') || '0';
        let piutang_homenet = $('#piutang_homenet').val().replace(/[^\d]/g, '') || '0';
        
        let formData = new FormData(this);
        formData.set('piutang_dedicated', piutang_dedicated);
        formData.set('piutang_homenet', piutang_homenet);
        
        console.log('Form Piutang data:', Object.fromEntries(formData));
        
        let submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="ri-loader-4-line me-1 spin"></i>Menyimpan...');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log('Success:', response);
                
                $('#modalPiutang').modal('hide');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message || 'Data piutang berhasil disimpan',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                
                let errorMessage = 'Terjadi kesalahan';
                
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = [];
                    $.each(xhr.responseJSON.errors, function(field, messages) {
                        errors.push(messages[0]);
                    });
                    errorMessage = errors.join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: errorMessage
                });
                
                submitBtn.prop('disabled', false).html('<i class="ri-save-line me-1"></i>Simpan');
            }
        });
    });

    // Show session messages
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            html: '{!! implode("<br>", $errors->all()) !!}'
        });
    @endif
});
</script>
@endsection
