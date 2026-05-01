@extends('layouts/layoutMaster')

@section('title', 'BBP - Ringkasan Rugi Laba')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
          <h4 class="mb-1">Ringkasan BBP - Rugi Laba</h4>
          <p class="text-muted mb-0">Periode: {{ $periodeLabel }}</p>
        </div>
        <form method="GET" action="{{ route('pembukuan.bbp.ringkasan') }}" class="d-flex gap-2">
          <select name="bulan" class="form-select">
            @for($m = 1; $m <= 12; $m++)
              <option value="{{ $m }}" {{ (int)$bulan === $m ? 'selected' : '' }}>
                {{ \Carbon\Carbon::create()->month($m)->locale('id')->isoFormat('MMMM') }}
              </option>
            @endfor
          </select>
          <select name="tahun" class="form-select">
            @for($y = (int)date('Y') - 3; $y <= (int)date('Y') + 1; $y++)
              <option value="{{ $y }}" {{ (int)$tahun === $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
          </select>
          <button type="submit" class="btn btn-primary">Terapkan</button>
        </form>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-md-3"><div class="card"><div class="card-body"><small class="text-muted">Pendapatan Kotor</small><h5 class="mb-0">Rp {{ number_format($pendapatanKotor, 0, ',', '.') }}</h5></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><small class="text-muted">Pendapatan Bersih</small><h5 class="mb-0">Rp {{ number_format($pendapatanBersih, 0, ',', '.') }}</h5></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><small class="text-muted">Pemasukan</small><h5 class="mb-0">Rp {{ number_format($pemasukan, 0, ',', '.') }}</h5></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><small class="text-muted">Pengeluaran</small><h5 class="mb-0">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h5></div></div></div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header"><h5 class="mb-0">Ringkasan Administrasi</h5></div>
        <div class="table-responsive">
          <table class="table mb-0">
            <tbody>
              <tr><td>Omset Seharusnya (Total biaya langganan pelanggan aktif)</td><td class="text-end fw-semibold">Rp {{ number_format($omsetSeharusnya, 0, ',', '.') }}</td></tr>
              <tr><td>Omset Realisasi (periode berjalan)</td><td class="text-end fw-semibold">Rp {{ number_format($omsetRealisasi, 0, ',', '.') }}</td></tr>
              <tr><td>Potongan PPN</td><td class="text-end fw-semibold">Rp {{ number_format($potonganPpn, 0, ',', '.') }}</td></tr>
              <tr><td>Piutang</td><td class="text-end fw-semibold {{ $piutang > 0 ? 'text-warning' : '' }}">Rp {{ number_format($piutang, 0, ',', '.') }}</td></tr>
              <tr><td>Rugi/Laba</td><td class="text-end fw-bold {{ $rugiLaba >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($rugiLaba, 0, ',', '.') }}</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header"><h6 class="mb-0">Catatan Rumus</h6></div>
        <div class="card-body small text-muted">
          <div>1. Pendapatan Kotor = Pemasukan + Potongan PPN</div>
          <div>2. Pendapatan Bersih = Pemasukan</div>
          <div>3. Rugi/Laba = Pendapatan Bersih - Pengeluaran</div>
          <div>4. Piutang = Omset Seharusnya - Omset Realisasi</div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
