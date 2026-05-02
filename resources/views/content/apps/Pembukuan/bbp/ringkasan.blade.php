@extends('layouts/layoutMaster')

@section('title', 'BBP - Ringkasan Rugi Laba')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4 p-lg-5">
      <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3 mb-4">
        <div>
          <h3 class="mb-1 fw-bold text-dark">Ringkasan BBP - Rugi Laba</h3>
          <p class="text-muted mb-0">Periode: {{ $periodeLabel }}</p>
        </div>
        <form method="GET" action="{{ route('pembukuan.bbp.ringkasan') }}" class="row g-2 align-items-center">
          <div class="col-12 col-md-auto">
            <select name="bulan" class="form-select" style="min-width: 150px;">
              @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ (int)$bulan === $m ? 'selected' : '' }}>
                  {{ \Carbon\Carbon::create()->month($m)->locale('id')->isoFormat('MMMM') }}
                </option>
              @endfor
            </select>
          </div>
          <div class="col-12 col-md-auto">
            <select name="tahun" class="form-select" style="min-width: 130px;">
              @for($y = (int)date('Y') - 3; $y <= (int)date('Y') + 1; $y++)
                <option value="{{ $y }}" {{ (int)$tahun === $y ? 'selected' : '' }}>{{ $y }}</option>
              @endfor
            </select>
          </div>
          <div class="col-12 col-md-auto">
            <button type="submit" class="btn btn-dark w-100">Terapkan</button>
          </div>
        </form>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
          <div class="border rounded-3 p-3 h-100 bg-light-subtle">
            <small class="text-muted d-block">Pendapatan Kotor</small>
            <h4 class="mb-0 fw-bold">Rp {{ number_format($pendapatanKotor, 0, ',', '.') }}</h4>
          </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
          <div class="border rounded-3 p-3 h-100 bg-light-subtle">
            <small class="text-muted d-block">Pendapatan Bersih</small>
            <h4 class="mb-0 fw-bold">Rp {{ number_format($pendapatanBersih, 0, ',', '.') }}</h4>
          </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
          <div class="border rounded-3 p-3 h-100 bg-light-subtle">
            <small class="text-muted d-block">Pemasukan</small>
            <h4 class="mb-0 fw-bold">Rp {{ number_format($pemasukan, 0, ',', '.') }}</h4>
          </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
          <div class="border rounded-3 p-3 h-100 bg-light-subtle">
            <small class="text-muted d-block">Pengeluaran</small>
            <h4 class="mb-0 fw-bold">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h4>
          </div>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-12 col-xl-8">
          <div class="border rounded-3 h-100">
            <div class="px-3 px-lg-4 py-3 border-bottom">
              <h5 class="mb-0 fw-bold">Ringkasan Administrasi</h5>
            </div>
            <div class="table-responsive">
              <table class="table mb-0">
                <tbody>
                  <tr>
                    <td class="ps-3 ps-lg-4">Omset Seharusnya <span class="text-muted">(total biaya langganan pelanggan aktif)</span></td>
                    <td class="text-end pe-3 pe-lg-4 fw-semibold">Rp {{ number_format($omsetSeharusnya, 0, ',', '.') }}</td>
                  </tr>
                  <tr>
                    <td class="ps-3 ps-lg-4">Omset Realisasi <span class="text-muted">(periode berjalan)</span></td>
                    <td class="text-end pe-3 pe-lg-4 fw-semibold">Rp {{ number_format($omsetRealisasi, 0, ',', '.') }}</td>
                  </tr>
                  <tr>
                    <td class="ps-3 ps-lg-4">Potongan PPN</td>
                    <td class="text-end pe-3 pe-lg-4 fw-semibold">Rp {{ number_format($potonganPpn, 0, ',', '.') }}</td>
                  </tr>
                  <tr>
                    <td class="ps-3 ps-lg-4">Piutang</td>
                    <td class="text-end pe-3 pe-lg-4 fw-bold {{ $piutang > 0 ? 'text-warning' : '' }}">Rp {{ number_format($piutang, 0, ',', '.') }}</td>
                  </tr>
                  <tr>
                    <td class="ps-3 ps-lg-4">Total Hutang <span class="text-muted">(periode berjalan)</span></td>
                    <td class="text-end pe-3 pe-lg-4 fw-bold text-danger">Rp {{ number_format($totalHutang ?? 0, 0, ',', '.') }}</td>
                  </tr>
                  <tr>
                    <td class="ps-3 ps-lg-4">Rugi/Laba</td>
                    <td class="text-end pe-3 pe-lg-4 fw-bold {{ $rugiLaba >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($rugiLaba, 0, ',', '.') }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-12 col-xl-4">
          <div class="border rounded-3 h-100 p-3 p-lg-4">
            <h6 class="fw-bold mb-3">Catatan Rumus</h6>
            <ol class="text-muted mb-0 ps-3" style="line-height:1.8;">
              <li>Pendapatan Kotor = Pemasukan + Potongan PPN</li>
              <li>Pendapatan Bersih = Pemasukan</li>
              <li>Rugi/Laba = Pendapatan Bersih - Pengeluaran</li>
              <li>Piutang = Omset Seharusnya - Omset Realisasi</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
