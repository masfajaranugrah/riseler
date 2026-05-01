@extends('layouts/layoutMaster')

@section('title', 'Laporan Harian')

@section('page-style')
<style>
body { background: #f5f5f9; }
:root {
  --primary: #18181b;
  --gray-border: #e4e4e7;
  --card-shadow: 0 2px 8px rgba(0,0,0,0.08);
  --border-radius: 12px;
}
.card {
  border: none;
  border-radius: var(--border-radius);
  box-shadow: var(--card-shadow);
  background: white;
}
.export-card {
  max-width: 600px;
  margin: 0 auto;
}
.export-icon {
  width: 80px; height: 80px;
  border-radius: 50%;
  background: #18181b;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 1.5rem;
  box-shadow: 0 4px 20px rgba(24,24,27,0.3);
}
.export-icon i { font-size: 2rem; color: white; }
.form-label { font-weight: 600; font-size: 0.8rem; color: #18181b; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 0.4rem; }
.form-select {
  border: 1px solid var(--gray-border);
  border-radius: 8px;
  padding: 0.6rem 1rem;
  font-size: 0.875rem;
  color: #18181b;
  transition: all 0.2s;
}
.form-select:focus { border-color: #18181b; box-shadow: 0 0 0 2px rgba(24,24,27,0.1); outline: none; }
.btn-export {
  background: #18181b;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 0.75rem 2rem;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  width: 100%;
  justify-content: center;
}
.btn-export:hover {
  background: #27272a;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(24,24,27,0.3);
}
.info-box {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 10px;
  padding: 1rem 1.25rem;
  margin-top: 1.5rem;
}
.info-box ul { margin: 0; padding-left: 1.25rem; }
.info-box li { font-size: 0.85rem; color: #166534; margin-bottom: 0.25rem; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
.card { animation: fadeIn 0.4s ease-out; }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">

  {{-- Header --}}
  <div class="mb-4">
    <h4 class="fw-bold mb-1" style="color:#18181b;"><i class="ri-bar-chart-box-line me-2"></i>Laporan Harian</h4>
    <p class="text-muted mb-0" style="font-size:0.875rem;">Export laporan keuangan harian per bulan dalam format Excel</p>
  </div>

  <div class="export-card">
    <div class="card p-5">

      {{-- Icon --}}
      <div class="text-center">
        <div class="export-icon">
          <i class="ri-file-excel-2-line"></i>
        </div>
        <h5 class="fw-bold mb-1" style="color:#18181b;">Export Laporan Harian</h5>
        <p class="text-muted" style="font-size:0.875rem;">Pilih bulan dan tahun untuk mengunduh laporan</p>
      </div>

      <hr class="my-4" style="border-color:#e4e4e7;">

      {{-- Form --}}
      <form method="GET" action="{{ route('laporan.harian.export') }}">
        <div class="row g-3 mb-4">
          <div class="col-6">
            <label class="form-label">Bulan</label>
            <select name="filter_month" class="form-select">
              @foreach(range(1,12) as $m)
                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                  {{ \Carbon\Carbon::createFromDate(null, $m, 1)->locale('id')->isoFormat('MMMM') }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-6">
            <label class="form-label">Tahun</label>
            <select name="filter_year" class="form-select">
              @foreach(range(date('Y')-2, date('Y')+1) as $y)
                <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <button type="submit" class="btn-export">
          <i class="ri-download-line"></i> Download Excel
        </button>
      </form>

      {{-- Info --}}
      <div class="info-box">
        <div class="d-flex align-items-center gap-2 mb-2">
          <i class="ri-information-circle-line" style="color:#16a34a;font-size:1.1rem;"></i>
          <strong style="font-size:0.85rem;color:#166534;">Isi setiap sheet (per tanggal):</strong>
        </div>
        <ul>
          <li><strong>KAS BANDWIDTH</strong> – Pembayaran cash + outstanding bulan sebelumnya + pengeluaran harian</li>
          <li><strong>KAS REGISTRASI</strong> – Pemasukan & pengeluaran dari kas registrasi</li>
          <li><strong>REKENING BANK JMK</strong> – Semua pembayaran via bank (BSI, BRI, dll)</li>
          <li><strong>LAPORAN HARIAN</strong> – Ringkasan saldo awal, pemasukan, pengeluaran & saldo akhir</li>
          <li>Sheet hanya dibuat untuk tanggal yang memiliki transaksi</li>
        </ul>
      </div>

    </div>
  </div>

</div>
@endsection
