@php
  $selectedMonthLabel = \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1)->translatedFormat('F Y');
@endphp

@extends('layouts/blankLayout')

@section('title', 'Absensi')

@section('page-style')
<style>
  :root {
    --bg: #f4f5f7;
    --card: #fff;
    --text: #111827;
    --muted: #6b7280;
    --line: #e5e7eb;
    --blue: #1f6feb;
    --green: #10b981;
    --orange: #f59e0b;
  }
  body { margin: 0; background: var(--bg); font-family: 'Nunito', 'Segoe UI', sans-serif; color: var(--text); }
  .app { max-width: 430px; margin: 0 auto; min-height: 100vh; padding: 14px 14px 26px; }
  .topbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
  .back { text-decoration: none; color: #111; font-size: 24px; }
  .title { font-size: 20px; font-weight: 800; }
  .month-filter { background: var(--card); border: 1px solid var(--line); border-radius: 14px; padding: 10px 12px; margin-bottom: 14px; }
  .month-form { display: grid; grid-template-columns: 1fr 1fr auto; gap: 8px; }
  select, .btn { border: 1px solid var(--line); border-radius: 10px; padding: 10px; font-size: 14px; background: #fff; }
  .btn { background: var(--blue); color: #fff; border-color: var(--blue); cursor: pointer; }
  .actions { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 14px; }
  .action-btn { text-decoration: none; text-align: center; background: var(--blue); color: #fff; border-radius: 999px; padding: 10px 12px; font-weight: 700; font-size: 14px; }
  .card { background: var(--card); border: 1px solid var(--line); border-radius: 14px; overflow: hidden; }
  .row { padding: 14px; border-bottom: 1px solid var(--line); }
  .row:last-child { border-bottom: none; }
  .head { display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-bottom: 4px; }
  .date { font-size: 16px; font-weight: 800; }
  .badge { font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 999px; }
  .badge-complete { background: #ecfdf5; color: #059669; }
  .badge-open { background: #fff7ed; color: #d97706; }
  .meta { font-size: 13px; color: #374151; margin-top: 2px; }
  .meta small { color: var(--muted); }
  .row-actions { margin-top: 10px; display: flex; justify-content: flex-end; }
  .btn-delete {
    border: 1px solid #ef4444;
    background: #fff1f2;
    color: #b91c1c;
    border-radius: 999px;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
  }
  .empty { background: var(--card); border: 1px solid var(--line); border-radius: 14px; padding: 16px; text-align: center; color: var(--muted); }
</style>
@endsection

@section('content')
<div class="app">
  <div class="topbar">
    <a class="back" href="{{ route('karyawan.home') }}">←</a>
    <div class="title">Absensi</div>
    <div style="width:24px;"></div>
  </div>

  <div class="month-filter">
    <form method="GET" action="{{ route('absensi.index') }}" class="month-form">
      <select name="month">
        @for ($m = 1; $m <= 12; $m++)
          <option value="{{ $m }}" {{ (int)$selectedMonth === $m ? 'selected' : '' }}>
            {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
          </option>
        @endfor
      </select>
      <select name="year">
        @for ($y = now()->year; $y >= now()->year - 3; $y--)
          <option value="{{ $y }}" {{ (int)$selectedYear === $y ? 'selected' : '' }}>{{ $y }}</option>
        @endfor
      </select>
      <button class="btn" type="submit">Filter</button>
    </form>
  </div>

  <div class="actions">
    <a class="action-btn" href="{{ route('absensi.capture', ['action' => 'checkin']) }}">+ Masuk</a>
    <a class="action-btn" href="{{ route('absensi.capture', ['action' => 'checkout']) }}">+ Pulang</a>
  </div>

  @if($attendances->isEmpty())
    <div class="empty">Belum ada data absensi untuk {{ $selectedMonthLabel }}.</div>
  @else
    <div class="card">
      @foreach($attendances as $absen)
        @php
          $isComplete = !empty($absen->time_in) && !empty($absen->time_out);
        @endphp
        <div class="row">
          <div class="head">
            <div class="date">{{ \Carbon\Carbon::parse($absen->date)->translatedFormat('d F Y') }}</div>
            <span class="badge {{ $isComplete ? 'badge-complete' : 'badge-open' }}">
              {{ $isComplete ? 'Selesai' : 'Belum Pulang' }}
            </span>
          </div>
          <div class="meta"><small>Masuk:</small> {{ $absen->time_in ? \Carbon\Carbon::parse($absen->time_in)->timezone('Asia/Jakarta')->format('H:i:s') : '--:--:--' }}</div>
          <div class="meta"><small>Pulang:</small> {{ $absen->time_out ? \Carbon\Carbon::parse($absen->time_out)->timezone('Asia/Jakarta')->format('H:i:s') : '--:--:--' }}</div>
          <div class="meta"><small>Lembur:</small> {{ $absen->lembur_in ? \Carbon\Carbon::parse($absen->lembur_in)->timezone('Asia/Jakarta')->format('H:i') : '-' }} - {{ $absen->lembur_out ? \Carbon\Carbon::parse($absen->lembur_out)->timezone('Asia/Jakarta')->format('H:i') : '-' }}</div>
          <div class="row-actions">
            <form method="POST" action="{{ route('absensi.destroy', $absen) }}" onsubmit="return confirm('Hapus data absensi ini beserta semua fotonya?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn-delete">Hapus</button>
            </form>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
