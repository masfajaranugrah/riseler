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
      font-family: DejaVu Sans, sans-serif;
      font-size: 10px;
      color: #111827;
    }
    .header {
      margin-bottom: 10px;
    }
    .title {
      font-size: 16px;
      font-weight: 700;
      margin: 0 0 4px 0;
    }
    .meta {
      font-size: 10px;
      color: #374151;
      margin: 0;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      table-layout: fixed;
    }
    th, td {
      border: 1px solid #d1d5db;
      padding: 5px 6px;
      vertical-align: top;
      text-align: left;
      word-break: break-word;
      overflow-wrap: anywhere;
      font-size: 10px;
    }
    th {
      background: #f3f4f6;
      font-weight: 700;
    }
    .col-no { width: 3%; }
    .col-nama { width: 10%; }
    .col-teknisi { width: 15%; }
    .col-wilayah { width: 8%; }
    .col-alamat { width: 11%; }
    .col-tarikan { width: 8%; }
    .col-jenis { width: 7%; }
    .col-sisa { width: 8%; }
    .col-ket { width: 16%; }
    .col-tanggal { width: 14%; }
    .text-center {
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="header">
    <p class="title">Laporan Penarikan Kabel</p>
    <p class="meta">
      Tanggal: {{ $date ? \Carbon\Carbon::parse($date)->format('d-m-Y') : 'Semua tanggal' }}
    </p>
    <p class="meta">Wilayah: {{ $wilayah ?: 'Semua wilayah' }}</p>
    <p class="meta">Search: {{ ($search ?? '') !== '' ? $search : '-' }}</p>
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
    </tbody>
  </table>
</body>
</html>
