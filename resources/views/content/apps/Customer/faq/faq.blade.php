<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FAQ & Bantuan</title>
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
    display: flex; align-items: center; gap: 14px;
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

.page-title { font-size: 1.375rem; font-weight: 700; }
.page-sub { font-size: 0.8125rem; color: #64748b; margin-top: 2px; }

/* Search */
.search-box {
    position: relative; margin-bottom: 24px;
}

.search-box input {
    width: 100%;
    padding: 14px 16px 14px 46px;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    background: white;
    font-size: 0.9rem;
    font-family: 'Inter', sans-serif;
    color: #0f172a;
    transition: all 0.15s ease;
}

.search-box input:focus {
    outline: none;
    border-color: #0f172a;
    box-shadow: 0 0 0 3px rgba(15,23,42,0.08);
}

.search-box i {
    position: absolute;
    left: 16px; top: 50%;
    transform: translateY(-50%);
    color: #94a3b8; font-size: 1.1rem;
}

/* Section */
.faq-section { margin-bottom: 28px; }

.faq-section-title {
    font-size: 0.8125rem; font-weight: 600;
    color: #94a3b8; text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 12px;
    padding-left: 4px;
}

/* Accordion */
.faq-item {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    margin-bottom: 8px;
    overflow: hidden;
    transition: all 0.15s ease;
}

.faq-item:hover { border-color: #cbd5e1; }

.faq-item.open {
    border-color: #0f172a;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.faq-question {
    padding: 16px 18px;
    display: flex; align-items: center; gap: 14px;
    cursor: pointer;
    transition: background 0.15s ease;
    user-select: none;
}

.faq-question:hover { background: #f8fafc; }

.faq-q-icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; flex-shrink: 0;
}

.faq-q-icon.blue  { background: #eff6ff; color: #3b82f6; }
.faq-q-icon.amber { background: #fffbeb; color: #f59e0b; }
.faq-q-icon.green { background: #f0fdf4; color: #22c55e; }
.faq-q-icon.rose  { background: #fff1f2; color: #f43f5e; }
.faq-q-icon.slate { background: #f1f5f9; color: #64748b; }

.faq-q-text {
    flex: 1; font-size: 0.9rem;
    font-weight: 600; color: #0f172a;
}

.faq-arrow {
    color: #cbd5e1; font-size: 0.875rem;
    transition: transform 0.25s ease;
}

.faq-item.open .faq-arrow { transform: rotate(180deg); color: #0f172a; }

.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.faq-item.open .faq-answer { max-height: 500px; }

.faq-answer-inner {
    padding: 0 18px 18px 70px;
    color: #64748b;
    font-size: 0.8125rem;
    line-height: 1.7;
}

/* Contact card */
.contact-card {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
    border-radius: 16px;
    padding: 24px;
    color: white;
    text-align: center;
    margin-top: 8px;
}

.contact-card h5 {
    font-size: 1rem; font-weight: 700;
    margin-bottom: 6px;
}

.contact-card p {
    font-size: 0.8125rem; color: #94a3b8;
    margin-bottom: 16px;
}

.contact-btns {
    display: flex; gap: 10px;
    justify-content: center;
}

.contact-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 0.8125rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
    border: none;
    font-family: 'Inter', sans-serif;
}

.contact-btn.wa {
    background: #22c55e; color: white;
}

.contact-btn.wa:hover { background: #16a34a; }

.contact-btn.chat {
    background: rgba(255,255,255,0.1);
    color: white;
    border: 1px solid rgba(255,255,255,0.2);
}

.contact-btn.chat:hover { background: rgba(255,255,255,0.15); }

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
            <div class="page-title">FAQ & Bantuan</div>
            <div class="page-sub">Pertanyaan yang sering diajukan</div>
        </div>
    </div>

    <!-- Search -->
    <div class="search-box">
        <i class="bi bi-search"></i>
        <input type="text" id="faq-search" placeholder="Cari pertanyaan...">
    </div>

    <!-- Pembayaran -->
    <div class="faq-section">
        <div class="faq-section-title">?? Pembayaran</div>

        <div class="faq-item" data-search="cara bayar tagihan pembayaran">
            <div class="faq-question" onclick="toggleFaq(this)">
                <div class="faq-q-icon blue"><i class="bi bi-credit-card"></i></div>
                <div class="faq-q-text">Bagaimana cara bayar tagihan?</div>
                <i class="bi bi-chevron-down faq-arrow"></i>
            </div>
            <div class="faq-answer">
                <div class="faq-answer-inner">
                    1. Buka menu <b>Tagihan</b> di navigasi bawah<br>
                    2. Pilih tagihan yang ingin dibayar<br>
                    3. Klik <b>"Upload Bukti Pembayaran"</b><br>
                    4. Pilih rekening tujuan transfer<br>
                    5. Upload foto bukti transfer Anda<br>
                    6. Tunggu verifikasi dari admin (maks 1x24 jam)
                </div>
            </div>
        </div>

        <div class="faq-item" data-search="metode pembayaran transfer rekening">
            <div class="faq-question" onclick="toggleFaq(this)">
                <div class="faq-q-icon green"><i class="bi bi-bank"></i></div>
                <div class="faq-q-text">Metode pembayaran apa saja?</div>
                <i class="bi bi-chevron-down faq-arrow"></i>
            </div>
            <div class="faq-answer">
                <div class="faq-answer-inner">
                    Saat ini kami menerima pembayaran melalui <b>transfer bank</b>. Pilihan rekening tujuan akan muncul saat Anda memilih untuk membayar tagihan. Pastikan transfer sesuai nominal tagihan.
                </div>
            </div>
        </div>

        <div class="faq-item" data-search="verifikasi berapa lama proses">
            <div class="faq-question" onclick="toggleFaq(this)">
                <div class="faq-q-icon amber"><i class="bi bi-clock-history"></i></div>
                <div class="faq-q-text">Berapa lama proses verifikasi?</div>
                <i class="bi bi-chevron-down faq-arrow"></i>
            </div>
            <div class="faq-answer">
                <div class="faq-answer-inner">
                    Verifikasi pembayaran biasanya memerlukan waktu <b>1–24 jam kerja</b>. Anda akan mendapat notifikasi segera setelah pembayaran diverifikasi. Jika lebih dari 24 jam belum diverifikasi, silakan hubungi admin via Chat.
                </div>
            </div>
        </div>
    </div>

    <!-- Internet -->
    <div class="faq-section">
        <div class="faq-section-title">?? Internet & Koneksi</div>

        <div class="faq-item" data-search="internet lambat slow loading">
            <div class="faq-question" onclick="toggleFaq(this)">
                <div class="faq-q-icon rose"><i class="bi bi-speedometer2"></i></div>
                <div class="faq-q-text">Internet saya lambat, apa yang harus dilakukan?</div>
                <i class="bi bi-chevron-down faq-arrow"></i>
            </div>
            <div class="faq-answer">
                <div class="faq-answer-inner">
                    Coba langkah berikut:<br>
                    1. <b>Restart router</b> — cabut kabel power, tunggu 30 detik, lalu pasang kembali<br>
                    2. <b>Periksa jumlah perangkat</b> yang terhubung ke WiFi<br>
                    3. <b>Pindah ke dekat router</b> untuk sinyal yang lebih kuat<br>
                    4. <b>Cek kabel</b> — pastikan tidak ada kabel yang rusak atau kendur<br><br>
                    Jika masih lambat, hubungi kami via Chat untuk pengecekan lebih lanjut.
                </div>
            </div>
        </div>

        <div class="faq-item" data-search="restart router modem cara">
            <div class="faq-question" onclick="toggleFaq(this)">
                <div class="faq-q-icon slate"><i class="bi bi-arrow-repeat"></i></div>
                <div class="faq-q-text">Bagaimana cara restart router?</div>
                <i class="bi bi-chevron-down faq-arrow"></i>
            </div>
            <div class="faq-answer">
                <div class="faq-answer-inner">
                    1. <b>Cabut kabel power</b> router dari stop kontak<br>
                    2. Tunggu <b>30 detik</b><br>
                    3. <b>Pasang kembali</b> kabel power<br>
                    4. Tunggu 1–2 menit hingga lampu indikator menyala normal<br>
                    5. Coba koneksi kembali
                </div>
            </div>
        </div>

        <div class="faq-item" data-search="password wifi ganti ubah">
            <div class="faq-question" onclick="toggleFaq(this)">
                <div class="faq-q-icon blue"><i class="bi bi-wifi"></i></div>
                <div class="faq-q-text">Bagaimana cara ganti password WiFi?</div>
                <i class="bi bi-chevron-down faq-arrow"></i>
            </div>
            <div class="faq-answer">
                <div class="faq-answer-inner">
                    Untuk mengganti password WiFi, silakan hubungi <b>admin via Chat</b> atau WhatsApp. Teknisi kami akan membantu mengubah password WiFi Anda secara remote.
                </div>
            </div>
        </div>
    </div>

    <!-- Akun -->
    <div class="faq-section">
        <div class="faq-section-title">?? Akun & Langganan</div>

        <div class="faq-item" data-search="upgrade paket ganti ubah kecepatan">
            <div class="faq-question" onclick="toggleFaq(this)">
                <div class="faq-q-icon green"><i class="bi bi-lightning-charge"></i></div>
                <div class="faq-q-text">Bagaimana cara upgrade paket internet?</div>
                <i class="bi bi-chevron-down faq-arrow"></i>
            </div>
            <div class="faq-answer">
                <div class="faq-answer-inner">
                    Hubungi admin melalui fitur <b>Chat</b> atau WhatsApp untuk informasi upgrade paket. Tim kami akan membantu menjelaskan pilihan paket dan prosesnya.
                </div>
            </div>
        </div>

        <div class="faq-item" data-search="tunggakan telat bayar denda isolir">
            <div class="faq-question" onclick="toggleFaq(this)">
                <div class="faq-q-icon rose"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="faq-q-text">Apa yang terjadi jika saya telat bayar?</div>
                <i class="bi bi-chevron-down faq-arrow"></i>
            </div>
            <div class="faq-answer">
                <div class="faq-answer-inner">
                    Jika pembayaran melewati jatuh tempo, koneksi internet Anda akan di-<b>isolir</b> (diputus sementara). Koneksi akan aktif kembali setelah Anda melunasi tagihan. Segera bayar tepat waktu agar tidak mengalami gangguan layanan.
                </div>
            </div>
        </div>
    </div>

    <!-- Kontak -->
    <div class="contact-card">
        <h5>Masih butuh bantuan?</h5>
        <p>Hubungi tim kami untuk bantuan lebih lanjut</p>
        <div class="contact-btns">
            <a href="/dashboard/customer/chat" class="contact-btn chat">
                <i class="bi bi-chat-dots"></i> Chat
            </a>
        </div>
    </div>

</div>

@include('content.apps.Customer.tagihan.bottom-navbar', ['active' => 'profile'])

<script>
function toggleFaq(el) {
    const item = el.closest('.faq-item');
    const wasOpen = item.classList.contains('open');
    // Close all
    document.querySelectorAll('.faq-item.open').forEach(i => i.classList.remove('open'));
    // Toggle current
    if (!wasOpen) item.classList.add('open');
}

// Search
document.getElementById('faq-search').addEventListener('input', function() {
    const query = this.value.toLowerCase().trim();
    document.querySelectorAll('.faq-item').forEach(item => {
        const text = (item.dataset.search + ' ' + item.querySelector('.faq-q-text').textContent).toLowerCase();
        item.style.display = (!query || text.includes(query)) ? '' : 'none';
    });

    // Show/hide section titles
    document.querySelectorAll('.faq-section').forEach(section => {
        const visibleItems = section.querySelectorAll('.faq-item[style=""], .faq-item:not([style])');
        const title = section.querySelector('.faq-section-title');
        if (title) title.style.display = visibleItems.length ? '' : 'none';
    });
});
</script>
</body>
</html>
