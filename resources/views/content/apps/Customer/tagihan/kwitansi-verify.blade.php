<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Kwitansi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            margin: 0;
            background: #f8fafc;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #0f172a;
        }
        .verify-wrap {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px;
        }
        .verify-card {
            width: 100%;
            max-width: 620px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }
        .verify-head {
            padding: 22px 24px;
            border-bottom: 1px solid #eef2f7;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .verify-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 22px;
            flex-shrink: 0;
        }
        .verify-icon.valid {
            background: #ecfdf3;
            color: #16a34a;
        }
        .verify-icon.invalid {
            background: #fef2f2;
            color: #dc2626;
        }
        .verify-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0;
        }
        .verify-sub {
            margin: 2px 0 0;
            font-size: 0.86rem;
            color: #64748b;
        }
        .verify-body {
            padding: 22px 24px;
        }
        .verify-alert {
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 0.9rem;
            margin-bottom: 16px;
        }
        .verify-alert.valid {
            background: #ecfdf3;
            border: 1px solid #bbf7d0;
            color: #166534;
        }
        .verify-alert.invalid {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }
        .meta-list {
            display: grid;
            gap: 10px;
        }
        .meta-item {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            border-bottom: 1px dashed #e2e8f0;
            padding-bottom: 8px;
            font-size: 0.9rem;
        }
        .meta-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .meta-label {
            color: #64748b;
            font-weight: 500;
        }
        .meta-value {
            font-weight: 700;
            text-align: right;
            word-break: break-word;
        }
        .verify-foot {
            border-top: 1px solid #eef2f7;
            padding: 14px 24px;
            font-size: 0.8rem;
            color: #64748b;
            background: #f8fafc;
        }
    </style>
</head>
<body>
    <div class="verify-wrap">
        <div class="verify-card">
            <div class="verify-head">
                <div class="verify-icon {{ $isValid ? 'valid' : 'invalid' }}">
                    <i class="bi {{ $isValid ? 'bi-patch-check-fill' : 'bi-exclamation-octagon-fill' }}"></i>
                </div>
                <div>
                    <h1 class="verify-title">{{ $isValid ? 'Kwitansi Valid' : 'Kwitansi Tidak Valid' }}</h1>
                    <p class="verify-sub">Sistem verifikasi anti-pemalsuan JernihNet</p>
                </div>
            </div>

            <div class="verify-body">
                @if ($isValid && $tagihan)
                    <div class="verify-alert valid">
                        Dokumen ini <strong>asli</strong> dan terdaftar di sistem.
                    </div>

                    <div class="meta-list">
                        <div class="meta-item">
                            <span class="meta-label">Nomor Kwitansi</span>
                            <span class="meta-value">{{ $tagihan->nomer_id }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Nama Pelanggan</span>
                            <span class="meta-value">{{ $tagihan->pelanggan->nama_lengkap ?? '-' }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Tanggal Pembayaran</span>
                            <span class="meta-value">{{ $tagihan->tanggal_pembayaran ? \Carbon\Carbon::parse($tagihan->tanggal_pembayaran)->format('d M Y') : '-' }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Nominal</span>
                            <span class="meta-value">Rp {{ number_format($tagihan->paket->harga ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Kode Verifikasi</span>
                            <span class="meta-value">{{ $expectedCode }}</span>
                        </div>
                    </div>
                @else
                    <div class="verify-alert invalid">
                        Dokumen ini <strong>tidak dapat diverifikasi</strong>. Kemungkinan link berubah, kode salah, atau dokumen bukan dari sistem resmi.
                    </div>

                    <div class="meta-list">
                        <div class="meta-item">
                            <span class="meta-label">Kode dari URL</span>
                            <span class="meta-value">{{ $providedCode ?: '-' }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Status Signature</span>
                            <span class="meta-value">{{ $hasValidSignature ? 'Valid' : 'Tidak Valid' }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <div class="verify-foot">
                Halaman ini hanya untuk verifikasi keaslian kwitansi. Jangan gunakan screenshot tanpa QR/link verifikasi resmi.
            </div>
        </div>
    </div>
</body>
</html>
