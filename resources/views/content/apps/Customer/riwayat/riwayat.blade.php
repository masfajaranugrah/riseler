@php
    $user = auth('customer')->user();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Riwayat Pembayaran</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    background: #f8fafc;
    font-family: 'Inter', sans-serif;
    padding: 0 0 90px 0;
    min-height: 100vh;
    color: #0f172a;
}

.container { max-width: 680px; padding: 0 16px; }

/* Header */
.page-header {
    padding: 24px 0 20px;
    display: flex;
    align-items: center;
    gap: 14px;
}

.back-btn {
    width: 40px; height: 40px;
    border-radius: 12px; background: #fff;
    border: 1px solid #e2e8f0;
    display: flex; align-items: center; justify-content: center;
    color: #0f172a; font-size: 1.1rem;
    cursor: pointer; transition: all 0.15s ease;
    flex-shrink: 0;
}

.back-btn:hover { background: #f1f5f9; }

.page-title { font-size: 1.375rem; font-weight: 700; color: #0f172a; }
.page-sub { font-size: 0.8125rem; color: #64748b; margin-top: 2px; }

/* Stats */
.stats-row {
    display: flex; gap: 12px;
    margin-bottom: 24px;
}

.stat-card {
    flex: 1;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 16px;
    text-align: center;
}

.stat-number {
    font-size: 1.5rem; font-weight: 800;
    color: #0f172a; margin-bottom: 2px;
}

.stat-label {
    font-size: 0.6875rem; font-weight: 600;
    color: #94a3b8; text-transform: uppercase;
    letter-spacing: 0.04em;
}

.stat-card.highlight {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
    border-color: transparent;
}

.stat-card.highlight .stat-number { color: #38bdf8; }
.stat-card.highlight .stat-label { color: #94a3b8; }

/* Year filter */
.year-filter {
    display: flex; gap: 8px;
    margin-bottom: 20px;
    overflow-x: auto;
    padding: 2px 0;
}

.year-pill {
    padding: 8px 18px;
    border-radius: 100px;
    border: 1px solid #e2e8f0;
    background: white;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
    font-family: 'Inter', sans-serif;
}

.year-pill:hover { border-color: #94a3b8; color: #0f172a; }
.year-pill.active { background: #0f172a; color: white; border-color: #0f172a; }

/* Payment items */
.payment-list {
    display: flex; flex-direction: column; gap: 10px;
}

.payment-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 14px;
    transition: all 0.15s ease;
}

.payment-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.payment-icon {
    width: 46px; height: 46px;
    border-radius: 12px;
    background: #f0fdf4;
    display: flex; align-items: center; justify-content: center;
    color: #22c55e; font-size: 1.25rem;
    flex-shrink: 0;
}

.payment-info { flex: 1; min-width: 0; }

.payment-period {
    font-size: 0.9rem; font-weight: 700;
    color: #0f172a; margin-bottom: 2px;
}

.payment-date {
    font-size: 0.75rem; color: #94a3b8;
    font-weight: 500;
}

.payment-method {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 0.6875rem; font-weight: 600;
    background: #f1f5f9; color: #64748b;
    padding: 3px 8px; border-radius: 6px;
    margin-top: 4px;
}

.payment-amount {
    text-align: right; flex-shrink: 0;
}

.payment-price {
    font-size: 0.9375rem; font-weight: 700;
    color: #0f172a;
}

.payment-status {
    font-size: 0.6875rem; font-weight: 600;
    color: #22c55e; margin-top: 2px;
}

/* Empty */
.empty-state {
    text-align: center; padding: 60px 20px;
}

.empty-icon {
    width: 72px; height: 72px;
    border-radius: 50%; margin: 0 auto 16px;
    background: #f1f5f9;
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; color: #cbd5e1;
}

.empty-title { font-size: 1rem; font-weight: 600; color: #64748b; margin-bottom: 4px; }
.empty-sub { font-size: 0.8125rem; color: #94a3b8; }

/* Bottom nav */
.bottom-nav {
    position: fixed; bottom: 0; left: 0; right: 0;
    height: 72px; background: #ffffff;
    display: flex; justify-content: space-around; align-items: center;
    box-shadow: 0 -2px 16px rgba(0,0,0,0.08);
    border-top: 1px solid #e2e8f0; z-index: 999;
}

.bottom-nav .tab-btn {
    background: none; border: none;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center; gap: 4px;
    color: #94a3b8; cursor: pointer; padding: 8px 16px; border-radius: 12px;
    transition: all 0.2s ease;
}

.bottom-nav .tab-btn:hover { background: #f8fafc; }
.bottom-nav .tab-btn i { font-size: 1.5rem; }
.bottom-nav .tab-btn span { font-size: 0.6875rem; font-weight: 600; }
</style>
</head>
<body>
<div class="container">

    <!-- Header -->
    <div class="page-header">
        <button class="back-btn" onclick="history.back()">
            <i class="bi bi-arrow-left"></i>
        </button>
        <div>
            <div class="page-title">Riwayat Pembayaran</div>
            <div class="page-sub">Histori tagihan yang sudah lunas</div>
        </div>
    </div>

    <!-- Stats -->
    @php
        $totalBayar = $tagihans->sum(function($t) { return $t->paket->harga ?? 0; });
        $years = $tagihans->map(function($t) {
            return \Carbon\Carbon::parse($t->tanggal_mulai)->format('Y');
        })->unique()->sortDesc()->values();
    @endphp

    <div class="stats-row">
        <div class="stat-card highlight">
            <div class="stat-number">{{ $tagihans->count() }}</div>
            <div class="stat-label">Total Bayar</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="font-size:1.1rem;">Rp {{ number_format($totalBayar, 0, ',', '.') }}</div>
            <div class="stat-label">Total Nominal</div>
        </div>
    </div>

    <!-- Year Filter -->
    @if($years->count() > 1)
    <div class="year-filter">
        <button class="year-pill active" data-year="all">Semua</button>
        @foreach($years as $year)
        <button class="year-pill" data-year="{{ $year }}">{{ $year }}</button>
        @endforeach
    </div>
    @endif

    <!-- Payment List -->
    <div class="payment-list" id="payment-list">
        @forelse($tagihans as $tagihan)
        @php
            $paket = $tagihan->paket;
            $periode = \Carbon\Carbon::parse($tagihan->tanggal_mulai);
            $yearData = $periode->format('Y');
        @endphp
        <div class="payment-card" data-year="{{ $yearData }}">
            <div class="payment-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="payment-info">
                <div class="payment-period">{{ $periode->translatedFormat('F Y') }}</div>
                <div class="payment-date">
                    Dibayar: {{ $tagihan->updated_at ? \Carbon\Carbon::parse($tagihan->updated_at)->translatedFormat('d M Y') : '-' }}
                </div>
                @if($tagihan->rekening)
                <div class="payment-method">
                    <i class="bi bi-bank"></i>
                    {{ $tagihan->rekening->nama_bank ?? '-' }}
                </div>
                @endif
            </div>
            <div class="payment-amount">
                <div class="payment-price">Rp {{ number_format($paket->harga ?? 0, 0, ',', '.') }}</div>
                <div class="payment-status">✓ Lunas</div>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-receipt"></i></div>
            <div class="empty-title">Belum Ada Riwayat</div>
            <div class="empty-sub">Riwayat pembayaran Anda akan muncul di sini</div>
        </div>
        @endforelse
    </div>

</div>

<!-- Bottom Navigation -->
<div class="bottom-nav">
    <button class="tab-btn" onclick="window.location.href='/dashboard/customer/tagihan/home'">
        <i class="bi bi-house-door-fill"></i><span>Home</span>
    </button>
    <button class="tab-btn" onclick="window.location.href='/dashboard/customer/tagihan'">
        <i class="bi bi-receipt"></i><span>Tagihan</span>
    </button>
    <button class="tab-btn" onclick="window.location.href='/dashboard/customer/tagihan/selesai'">
        <i class="bi bi-file-earmark-text"></i><span>Kwitansi</span>
    </button>
    
    <button class="tab-btn" onclick="window.location.href='/dashboard/customer/profile'">
        <i class="bi bi-person-circle"></i><span>Profile</span>
    </button>
</div>

<script>
// Year filter
document.querySelectorAll('.year-pill').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.year-pill').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const year = btn.dataset.year;
        document.querySelectorAll('.payment-card').forEach(card => {
            if (year === 'all' || card.dataset.year === year) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>
</body>
</html>
