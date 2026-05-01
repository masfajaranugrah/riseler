<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Invoice</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('css/customer-system.css') }}">

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
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
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

.btn-download.is-loading {
    opacity: 0.7;
    pointer-events: none;
}

.btn-share-wa {
    background: #16a34a;
    color: #ffffff;
}

.btn-share-wa:hover {
    background: #15803d;
    transform: translateY(-1px);
}

.btn-share-mail {
    background: #f8fafc;
    color: #0f172a;
    border: 1px solid #cbd5e1;
}

.btn-share-mail:hover {
    background: #e2e8f0;
    color: #0f172a;
    transform: translateY(-1px);
}

.invoice-verification {
    margin-bottom: 14px;
    padding: 12px;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    background: #f8fafc;
    display: flex;
    align-items: center;
    gap: 12px;
}

.invoice-verification img {
    width: 76px;
    height: 76px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
}

.verification-title {
    font-size: 0.86rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 2px;
}

.verification-subtitle {
    font-size: 0.78rem;
    color: #64748b;
    margin-bottom: 8px;
}

.verification-code {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 999px;
    background: #e2e8f0;
    color: #334155;
    font-size: 0.74rem;
    font-weight: 600;
    letter-spacing: 0.02em;
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
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .btn-action {
        width: 100%;
    }

    .invoice-verification {
        align-items: flex-start;
    }
}
</style>

</head>

<body>

<div class="top-header">
    <div class="container text-center">
        <h4>Kwitansi Anda</h4>
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

                @if ($tagihan->kwitansi)
                    @php
                        $paymentDate = $tagihan->tanggal_pembayaran
                            ? \Carbon\Carbon::parse($tagihan->tanggal_pembayaran)->format('Y-m-d H:i:s')
                            : '-';
                        $verificationPayload = implode('|', [
                            (string) $tagihan->id,
                            (string) ($tagihan->nomer_id ?? ''),
                            (string) ($tagihan->pelanggan_id ?? ''),
                            $paymentDate,
                            (string) ($tagihan->status_pembayaran ?? ''),
                            (string) ($tagihan->kwitansi ?? ''),
                        ]);
                        $verificationCode = strtoupper(substr(hash_hmac('sha256', $verificationPayload, (string) config('app.key')), 0, 16));
                        $verifyUrl = \Illuminate\Support\Facades\URL::signedRoute('kwitansi.verify', [
                            'tagihan_id' => $tagihan->id,
                            'code' => $verificationCode,
                        ]);
                        $waText = rawurlencode("Halo, berikut kwitansi pembayaran {$tagihan->nomer_id}: {$verifyUrl}");
                        $mailSubject = rawurlencode("Kwitansi Pembayaran {$tagihan->nomer_id}");
                        $mailBody = rawurlencode("Berikut tautan kwitansi pembayaran Anda:\n{$verifyUrl}");
                    @endphp
                    <div class="invoice-verification">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($verifyUrl) }}"
                            alt="QR Verifikasi Kwitansi {{ $tagihan->nomer_id }}">
                        <div>
                            <div class="verification-title">QR Verifikasi Kwitansi</div>
                            <div class="verification-subtitle">Scan QR untuk cek dokumen asli di sistem.</div>
                            <span class="verification-code">{{ $verificationCode }}</span>
                        </div>
                    </div>
                @endif

                <div class="invoice-footer">
                    @if ($tagihan->kwitansi)
                        {{-- Preview Button --}}
                        <a class="btn-action btn-view" 
                
                           href="{{ route('kwitansi.preview', $tagihan->id) }}"
                           title="Lihat kwitansi di browser">
                            <i class="bi bi-eye"></i> Lihat
                        </a>
                        
                        {{-- Download Button - Buka di Tab Baru Sama Seperti Lihat --}}
                        <a class="btn-action btn-download" 
                           data-download-url="{{ route('kwitansi.download', $tagihan->id) }}"
                           data-filename="Kwitansi_{{ $tagihan->nomer_id }}.pdf"
                           href="{{ route('kwitansi.download', $tagihan->id) }}"
                           title="Lihat kwitansi">
                            <i class="bi bi-download"></i> Download
                        </a>

                        <a class="btn-action btn-share-wa"
                           target="_blank"
                           rel="noopener noreferrer"
                           href="https://wa.me/?text={{ $waText }}"
                           title="Bagikan ke WhatsApp">
                            <i class="bi bi-whatsapp"></i> Share WA
                        </a>

                        <a class="btn-action btn-share-mail"
                           href="mailto:?subject={{ $mailSubject }}&body={{ $mailBody }}"
                           title="Bagikan via Email">
                            <i class="bi bi-envelope"></i> Share Email
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

@include('content.apps.Customer.tagihan.bottom-navbar', ['active' => 'invoice'])

<script>
    function parseFileNameFromDisposition(disposition) {
        if (!disposition) return null;

        const utf8Match = disposition.match(/filename\*=UTF-8''([^;]+)/i);
        if (utf8Match && utf8Match[1]) {
            return decodeURIComponent(utf8Match[1]);
        }

        const asciiMatch = disposition.match(/filename="?([^"]+)"?/i);
        return asciiMatch && asciiMatch[1] ? asciiMatch[1] : null;
    }

    function isIosDevice() {
        return /iPad|iPhone|iPod/i.test(navigator.userAgent);
    }

    function isStandalonePwa() {
        return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    }

    async function saveBlobForDevice(blob, fileName) {
        // Android Chrome modern
        if (window.showSaveFilePicker) {
            try {
                const handle = await window.showSaveFilePicker({
                    suggestedName: fileName,
                    types: [
                        {
                            description: 'PDF Document',
                            accept: { 'application/pdf': ['.pdf'] }
                        }
                    ]
                });
                const writable = await handle.createWritable();
                await writable.write(blob);
                await writable.close();
                return true;
            } catch (error) {
                if (error.name === 'AbortError') return true;
            }
        }

        // iOS / Android share sheet fallback
        if (navigator.share && navigator.canShare && typeof File !== 'undefined') {
            try {
                const file = new File([blob], fileName, { type: blob.type || 'application/pdf' });
                if (navigator.canShare({ files: [file] })) {
                    await navigator.share({
                        title: 'Kwitansi Pembayaran',
                        text: 'Berikut kwitansi pembayaran Anda.',
                        files: [file]
                    });
                    return true;
                }
            } catch (error) {
                if (error.name === 'AbortError') return true;
            }
        }

        // Generic browser fallback
        const blobUrl = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = blobUrl;
        link.download = fileName;
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // iOS PWA sometimes ignores download attribute; open document in current tab for manual save/share.
        if (isIosDevice() && isStandalonePwa()) {
            window.location.href = blobUrl;
        }

        setTimeout(() => URL.revokeObjectURL(blobUrl), 10000);
        return true;
    }

    async function handleKwitansiDownload(anchorElement) {
        const url = anchorElement.dataset.downloadUrl || anchorElement.getAttribute('href');
        const fallbackName = anchorElement.dataset.filename || 'kwitansi.pdf';

        const originalHtml = anchorElement.innerHTML;
        anchorElement.classList.add('is-loading');
        anchorElement.innerHTML = '<i class="bi bi-arrow-repeat"></i> Menyiapkan...';

        try {
            const response = await fetch(url, {
                method: 'GET',
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const blob = await response.blob();
            const disposition = response.headers.get('content-disposition');
            const fileName = parseFileNameFromDisposition(disposition) || fallbackName;

            await saveBlobForDevice(blob, fileName);
        } catch (error) {
            // fallback to direct URL if fetch fails
            window.location.href = url;
        } finally {
            anchorElement.classList.remove('is-loading');
            anchorElement.innerHTML = originalHtml;
        }
    }

    document.querySelectorAll('.btn-download[data-download-url]').forEach((btn) => {
        btn.addEventListener('click', function (event) {
            event.preventDefault();
            handleKwitansiDownload(this);
        });
    });
</script>

</body>
</html>
