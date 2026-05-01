<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Laporan Penarikan Kabel</title>
  <style>
    @page {
      margin: 16px 18px;
    }
    body {
      font-family: 'Helvetica', 'Arial', sans-serif;
      font-size: 9px;
      color: #111827;
      line-height: 1.3;
      margin: 0;
      padding: 0;
    }
    .header {
      margin-bottom: 8px;
      border-bottom: 2px solid #333;
      padding-bottom: 6px;
    }
    .title {
      font-size: 14px;
      font-weight: bold;
      margin: 0 0 4px 0;
      text-transform: uppercase;
    }
    .meta {
      font-size: 9px;
      color: #374151;
      margin: 1px 0;
    }
    .date-section-title {
      font-size: 11px;
      font-weight: bold;
      margin: 8px 0 4px 0;
      padding: 4px 8px;
      background: #e5e7eb;
      border-left: 4px solid #333;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 4px;
      table-layout: fixed;
    }
    th, td {
      border: 1px solid #999;
      padding: 3px 5px;
      vertical-align: middle;
      text-align: left;
      word-wrap: break-word;
      font-size: 8px;
    }
    th {
      background: #f0f0f0;
      font-weight: bold;
    }
    tr {
      page-break-inside: avoid;
    }
    .col-no { width: 3%; }
    .col-nama { width: 12%; }
    .col-teknisi { width: 12%; }
    .col-wilayah { width: 8%; }
    .col-alamat { width: 15%; }
    .col-tarikan { width: 7%; }
    .col-jenis { width: 6%; }
    .col-sisa { width: 7%; }
    .col-ket { width: 18%; }
    .col-tanggal { width: 12%; }
    .text-center {
      text-align: center;
    }
    .page-break {
      page-break-before: always;
    }
    .summary-row td {
      font-weight: bold;
      background: #f9fafb;
      border-top: 2px solid #666;
    }
  </style>
</head>
<body>

{{-- ============================================ --}}
{{-- MODE 1: Group by date (filter per bulan)     --}}
{{-- Setiap tanggal = halaman baru di PDF         --}}
{{-- ============================================ --}}
@if($groupByDate && $groupedData && $groupedData->count() > 0)

  @foreach($groupedData as $dateKey => $items)
    @if(!$loop->first)
      <div class="page-break"></div>
    @endif

    <div class="header">
      <p class="title">Laporan Penarikan Kabel</p>
      <p class="meta">{{ $periodeLabel }}</p>
      <p class="meta">Wilayah: {{ $wilayah ?: 'Semua wilayah' }}</p>
      @if(($search ?? '') !== '')
        <p class="meta">Search: {{ $search }}</p>
      @endif
      <p class="meta">Dicetak: {{ $printedAt->format('d-m-Y H:i') }}</p>
    </div>

    <div class="date-section-title">
      Tanggal: {{ \Carbon\Carbon::parse($dateKey)->translatedFormat('l, d F Y') }}
      ({{ $items->count() }} data)
    </div>

    <table>
      <thead>
        <tr>
          <th class="col-no">#</th>
          <th class="col-nama">Nama Pelanggan</th>
          <th class="col-teknisi">Nama Teknisi</th>
          <th class="col-wilayah">Wilayah</th>
          <th class="col-alamat">Alamat</th>
          <th class="col-tarikan">Tarikan</th>
          <th class="col-jenis">Jenis</th>
          <th class="col-sisa">Sisa Kabel</th>
          <th class="col-ket">Keterangan</th>
          <th class="col-tanggal">Jam Input</th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $item)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->nama_pelanggan }}</td>
            <td>{{ optional($item->employee)->full_name ?: '-' }}</td>
            <td>{{ $item->wilayah ?: '-' }}</td>
            <td>{{ $item->alamat }}</td>
            <td>{{ rtrim(rtrim(number_format((float) $item->tarikan_meter, 2, '.', ''), '0'), '.') }} M</td>
            <td>{{ strtoupper($item->jenis_kabel) }}</td>
            <td>{{ rtrim(rtrim(number_format((float) $item->sisi_core, 2, '.', ''), '0'), '.') }} M</td>
            <td>{{ $item->keterangan ?: '-' }}</td>
            <td>{{ optional($item->created_at)->format('H:i') }}</td>
          </tr>
        @endforeach
        <tr class="summary-row">
          <td colspan="5" style="text-align: right;">Total tarikan {{ \Carbon\Carbon::parse($dateKey)->format('d-m-Y') }}:</td>
          <td>{{ rtrim(rtrim(number_format($items->sum('tarikan_meter'), 2, '.', ''), '0'), '.') }} M</td>
          <td colspan="4"></td>
        </tr>
      </tbody>
    </table>
  @endforeach

{{-- ============================================ --}}
{{-- MODE 2: Tabel biasa (filter tanggal / semua) --}}
{{-- ============================================ --}}
@else

  <div class="header">
    <p class="title">Laporan Penarikan Kabel</p>
    <p class="meta">{{ $periodeLabel }}</p>
    <p class="meta">Wilayah: {{ $wilayah ?: 'Semua wilayah' }}</p>
    @if(($search ?? '') !== '')
      <p class="meta">Search: {{ $search }}</p>
    @endif
    <p class="meta">Dicetak: {{ $printedAt->format('d-m-Y H:i') }}</p>
  </div>

  <table>
    <thead>
      <tr>
        <th class="col-no">#</th>
        <th class="col-nama">Nama Pelanggan</th>
        <th class="col-teknisi">Nama Teknisi</th>
        <th class="col-wilayah">Wilayah</th>
        <th class="col-alamat">Alamat</th>
        <th class="col-tarikan">Tarikan</th>
        <th class="col-jenis">Jenis</th>
        <th class="col-sisa">Sisa Kabel</th>
        <th class="col-ket">Keterangan</th>
        <th class="col-tanggal">Tanggal Input</th>
      </tr>
    </thead>
    <tbody>
      @forelse($laporanKabel as $item)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $item->nama_pelanggan }}</td>
          <td>{{ optional($item->employee)->full_name ?: '-' }}</td>
          <td>{{ $item->wilayah ?: '-' }}</td>
          <td>{{ $item->alamat }}</td>
          <td>{{ rtrim(rtrim(number_format((float) $item->tarikan_meter, 2, '.', ''), '0'), '.') }} M</td>
          <td>{{ strtoupper($item->jenis_kabel) }}</td>
          <td>{{ rtrim(rtrim(number_format((float) $item->sisi_core, 2, '.', ''), '0'), '.') }} M</td>
          <td>{{ $item->keterangan ?: '-' }}</td>
          <td>{{ optional($item->created_at)->format('d-m-Y H:i') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="10" class="text-center">Tidak ada data.</td>
        </tr>
      @endforelse

      @if($laporanKabel->count() > 0)
        <tr class="summary-row">
          <td colspan="5" style="text-align: right;">Total tarikan:</td>
          <td>{{ rtrim(rtrim(number_format($laporanKabel->sum('tarikan_meter'), 2, '.', ''), '0'), '.') }} M</td>
          <td colspan="4"></td>
        </tr>
      @endif
    </tbody>
  </table>

@endif

</body>
</html>