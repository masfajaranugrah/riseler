<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Invoice</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background: #ffffff;
    color: #1a1a1a;
    padding-bottom: 90px;
}

.top-header {
    background: #000000;
    color: #ffffff;
    padding: 2.5rem 0 2rem;
    margin-bottom: 2rem;
    border-bottom: 1px solid #e5e5e5;
}

.top-header h4 {
    font-size: 1.75rem;
    font-weight: 600;
    letter-spacing: -0.5px;
    margin: 0;
}

.top-header p {
    font-size: 0.95rem;
    color: #a0a0a0;
    margin-top: 0.5rem;
}

#app-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem 2rem;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 1.5rem;
}

.card-invoice {
    background: #ffffff;
    border: 1px solid #e5e5e5;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.card-invoice:hover {
    border-color: #1a1a1a;
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.card-header {
    background: #1a1a1a;
    color: #ffffff;
    padding: 1.25rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #333;
}

.invoice-number {
    font-size: 0.95rem;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.badge-new {
    background: #ffffff;
    color: #000000;
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.card-body {
    padding: 1.5rem;
}

.invoice-info {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #f5f5f5;
}

.info-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.info-label {
    font-size: 0.85rem;
    color: #666666;
    font-weight: 500;
}

.info-value {
    font-size: 0.9rem;
    color: #1a1a1a;
    font-weight: 500;
    text-align: right;
    max-width: 60%;
    word-break: break-word;
}

.price-section {
    background: #f9f9f9;
    padding: 1.25rem;
    border-radius: 8px;
    margin: 1.5rem 0;
    text-align: center;
}

.price-label {
    font-size: 0.8rem;
    color: #666666;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 0.5rem;
}

.price-amount {
    font-size: 1.75rem;
    font-weight: 700;
    color: #000000;
    letter-spacing: -0.5px;
}

.invoice-footer {
    display: flex;
    gap: 0.75rem;
}

.btn-action {
    flex: 1;
    padding: 0.75rem;
    border: none;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    text-decoration: none;
}

.btn-view {
    background: #ffffff;
    color: #1a1a1a;
    border: 2px solid #1a1a1a;
}

.btn-view:hover {
    background: #1a1a1a;
    color: #ffffff;
    transform: translateY(-1px);
}

.btn-download {
    background: #000000;
    color: #ffffff;
}

.btn-download:hover {
    background: #333333;
    transform: translateY(-1px);
}

.kwitansi-unavailable {
    text-align: center;
    padding: 1rem;
    background: #fff3cd;
    border-radius: 8px;
    color: #856404;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.alert-empty {
    background: #f9f9f9;
    border: 1px solid #e5e5e5;
    border-radius: 12px;
    padding: 3rem 2rem;
    text-align: center;
    grid-column: 1 / -1;
}

.alert-empty i {
    font-size: 3rem;
    color: #cccccc;
    margin-bottom: 1rem;
}

.alert-empty strong {
    display: block;
    font-size: 1.1rem;
    color: #1a1a1a;
    margin-bottom: 0.5rem;
}

.alert-empty p {
    color: #666666;
    font-size: 0.95rem;
}

.bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 70px;
    background: #000000;
    border-top: 1px solid #333333;
    display: flex;
    justify-content: space-around;
    align-items: center;
    z-index: 1000;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
}

.nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.35rem;
    color: #808080;
    text-decoration: none;
    padding: 0.5rem 1rem;
    transition: color 0.2s;
    font-size: 0.8rem;
}

.nav-item.active {
    color: #ffffff;
}

.nav-item i {
    font-size: 1.4rem;
}

@media (max-width: 768px) {
    #app-container {
        grid-template-columns: 1fr;
        padding: 0 1rem 2rem;
        gap: 1.25rem;
    }
    
    .top-header {
        padding: 2rem 0 1.5rem;
    }
    
    .top-header h4 {
        font-size: 1.5rem;
    }
    
    .price-amount {
        font-size: 1.5rem;
    }
    
    .info-value {
        font-size: 0.85rem;
        max-width: 55%;
    }
    
    .btn-action {
        font-size: 0.85rem;
        padding: 0.65rem;
    }
}

@media (max-width: 480px) {
    .top-header h4 {
        font-size: 1.35rem;
    }
    
    .invoice-footer {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .btn-action {
        width: 100%;
    }
}
</style>

</head>

<body>

<div class="top-header">
    <div class="container text-center">
        <h4>Invoice Anda</h4>
        <p>Kelola dan pantau semua tagihan Anda</p>
    </div>
</div>

<div id="app-container">

    {{-- Jika Tidak Ada Data --}}
    @if ($tagihans->isEmpty())
        <div class="alert-empty">
            <i class="bi bi-receipt"></i>
            <strong>Belum Ada Invoice</strong>
            <p>Tidak ada tagihan yang tersedia untuk akun Anda saat ini</p>
        </div>
    @endif

    {{-- LOOP SEMUA TAGIHAN --}}
    @foreach ($tagihans as $tagihan)

        @php
            $isNew = false;
            if ($tagihan->tanggal_pembayaran) {
                $days = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($tagihan->tanggal_pembayaran));
                $isNew = $days <= 7;
            }
        @endphp

        <div class="card-invoice">
            <div class="card-header">
                <span class="invoice-number">{{ $tagihan->nomer_id }}</span>
                @if ($isNew)
                    <span class="badge-new">NEW</span>
                @endif
            </div>

            <div class="card-body">

                <div class="invoice-info">
                    <div class="info-row">
                        <span class="info-label">Nama</span>
                        <span class="info-value">{{ $tagihan->pelanggan->nama_lengkap ?? '-' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Tanggal Invoice</span>
                        <span class="info-value">
                            {{ $tagihan->tanggal_mulai ? \Carbon\Carbon::parse($tagihan->tanggal_mulai)->format('d M Y') : '-' }}
                        </span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Jatuh Tempo</span>
                        <span class="info-value">
                            {{ $tagihan->tanggal_berakhir ? \Carbon\Carbon::parse($tagihan->tanggal_berakhir)->format('d M Y') : '-' }}
                        </span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Tanggal Bayar</span>
                        <span class="info-value">
                            {{ $tagihan->tanggal_pembayaran ? \Carbon\Carbon::parse($tagihan->tanggal_pembayaran)->format('d M Y') : '-' }}
                        </span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Metode Pembayaran</span>
                        <span class="info-value">{{ $tagihan->rekening->nama_bank ?? '-' }}</span>
                    </div>
                </div>

                <div class="price-section">
                    <div class="price-label">Total Tagihan</div>
                    <div class="price-amount">
                        Rp {{ number_format($tagihan->paket->harga ?? 0, 0, ',', '.') }}
                    </div>
                </div>

                <div class="invoice-footer">
                    @if ($tagihan->kwitansi)
                     

@php
    $token = request('token'); // WAJIB ada
@endphp

<a class="btn-action btn-view"
   href="{{ route('webview.kwitansi.preview', ['id' => $tagihan->id, 'token' => $token]) }}">
    <i class="bi bi-eye"></i> Lihat
</a>

<a class="btn-action btn-download"
   href="{{ route('webview.kwitansi.download', ['id' => $tagihan->id, 'token' => $token]) }}">
    <i class="bi bi-download"></i> Download
</a>

                    @else
                        <div class="kwitansi-unavailable">
                            <i class="bi bi-exclamation-circle"></i>
                            <span>Kwitansi belum tersedia</span>
                        </div>
                    @endif
                </div>

            </div>
        </div>

    @endforeach

</div>

</body>
</html>
