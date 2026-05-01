<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Invoice Tagihan</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Axios -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: #f8fafc;
    font-family: 'Inter', sans-serif;
    padding: 24px 0 100px;
    min-height: 100vh;
    color: #0f172a;
}

.container {
    max-width: 680px;
}

.invoice-container {
    display: flex;
    flex-direction: column;
}

/* Header Section */
.header-section {
    margin-bottom: 32px;
}

.header-section h4 {
    color: #0f172a;
    font-weight: 700;
    font-size: 1.75rem;
    margin-bottom: 6px;
}

.header-section p {
    color: #64748b;
    font-size: 0.95rem;
}

/* Card Invoice */
.card-invoice {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    margin-bottom: 20px;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.card-invoice:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
}

/* Card Priority untuk belum bayar */
.card-invoice.priority {
    border: 2px solid #fecaca;
    box-shadow: 0 4px 16px rgba(239,68,68,0.12);
    order: -1;
}

.card-invoice.priority:hover {
    box-shadow: 0 6px 20px rgba(239,68,68,0.16);
}

/* Card Header */
.card-header-invoice {
    background: #0f172a;
    padding: 20px 24px;
    color: white;
    border-bottom: 1px solid #1e293b;
}

.card-header-invoice h5 {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 4px;
    letter-spacing: -0.01em;
}

.card-header-invoice small {
    font-size: 0.875rem;
    color: #94a3b8;
}

/* Card Body */
.card-body {
    padding: 24px;
}

/* Info Section */
.info-section {
    background: #f8fafc;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid #f1f5f9;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #f1f5f9;
}

.info-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.info-item:first-child {
    padding-top: 0;
}

.info-label {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
}

.info-value {
    font-size: 0.875rem;
    color: #0f172a;
    font-weight: 600;
}

/* PPN Notice */
.ppn-notice {
    background: #fffbeb;
    border: 1px solid #fef3c7;
    border-left: 3px solid #f59e0b;
    padding: 12px 16px;
    border-radius: 8px;
    margin-top: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.ppn-notice i {
    color: #f59e0b;
    font-size: 1.1rem;
}

.ppn-notice p {
    margin: 0;
    color: #92400e;
    font-weight: 500;
    font-size: 0.875rem;
}

/* Price Section */
.price-section {
    text-align: center;
    padding: 24px 0;
    margin: 20px 0;
}

.price-section .period-label {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
    margin-bottom: 16px;
}

.price-amount {
    font-size: 2rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 8px;
    letter-spacing: -0.02em;
}

.price-text {
    font-size: 0.8125rem;
    color: #94a3b8;
}

/* Divider */
.divider {
    height: 1px;
    background: #f1f5f9;
    margin: 20px 0;
}

/* Status Badge */
.status-wrapper {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid #f1f5f9;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 20px;
    font-size: 0.8125rem;
    font-weight: 600;
    border-radius: 100px;
    letter-spacing: 0.02em;
}

.status-lunas { 
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.status-verifikasi { 
    background: #fffbeb;
    color: #92400e;
    border: 1px solid #fef3c7;
}

.status-belum { 
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

/* Button Bayar */
.btn-bayar {
    margin-top: 16px;
    border-radius: 8px;
    padding: 10px 24px;
    font-weight: 600;
    font-size: 0.9375rem;
    background: #0f172a;
    border: none;
    color: white;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-bayar:hover {
    background: #1e293b;
    transform: translateY(-1px);
}

.btn-bayar:active {
    transform: translateY(0);
}

/* Empty State */
.empty-state {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 48px 24px;
    text-align: center;
    margin-top: 40px;
}

.empty-state i {
    font-size: 3.5rem;
    color: #cbd5e1;
    margin-bottom: 20px;
}

.empty-state h5 {
    color: #0f172a;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 1.125rem;
}

.empty-state p {
    color: #64748b;
    margin-bottom: 24px;
    line-height: 1.6;
    font-size: 0.9375rem;
}

.empty-state .btn {
    border-radius: 8px;
    padding: 10px 24px;
    font-weight: 600;
    background: #0f172a;
    border: none;
    transition: all 0.2s ease;
    font-size: 0.9375rem;
}

.empty-state .btn:hover {
    background: #1e293b;
    transform: translateY(-1px);
}

/* Bottom Navbar */
.bottom-nav {
    position: fixed;
    bottom: 0; 
    left: 0; 
    right: 0;
    height: 72px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-around;
    align-items: center;
    z-index: 1000;
}

.bottom-nav button {
    background: none;
    border: none;
    text-align: center;
    color: #94a3b8;
    transition: all 0.2s ease;
    padding: 8px 16px;
    border-radius: 8px;
}

.bottom-nav button:hover,
.bottom-nav button.active {
    color: #0f172a;
}

.bottom-nav button i {
    font-size: 1.5rem;
    display: block;
    margin-bottom: 4px;
}

.bottom-nav button span {
    font-size: 0.6875rem;
    font-weight: 600;
}

/* Responsive */
@media (max-width: 480px) {
    body {
        padding: 16px 0 100px;
    }

    .header-section h4 {
        font-size: 1.5rem;
    }
    
    .price-amount {
        font-size: 1.75rem;
    }
    
    .card-body {
        padding: 20px 16px;
    }
    
    .info-section {
        padding: 16px;
    }
}

.cursor-pointer {
    cursor: pointer;
}

/* SweetAlert Custom Styling */
.swal2-popup {
    border-radius: 16px;
    padding: 24px;
}

.swal2-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #0f172a;
}

.swal2-confirm {
    border-radius: 8px !important;
    font-weight: 600 !important;
    padding: 10px 24px !important;
}

.swal2-cancel {
    border-radius: 8px !important;
    font-weight: 600 !important;
    padding: 10px 24px !important;
}
</style>
</head>

<body>

<div class="container">
    <div class="header-section">
        <h4>Tagihan</h4>
        <p>Kelola pembayaran tagihan Anda</p>
    </div>

    <div class="invoice-container">
    @forelse($tagihans as $tagihan)
    @php
        $pelanggan = $tagihan->pelanggan ?? null;
        $paket = $tagihan->paket ?? null;
        $isPriority = $tagihan->status_pembayaran !== 'lunas' && $tagihan->status_pembayaran !== 'proses_verifikasi';
    @endphp

    <div class="card card-invoice {{ $isPriority ? 'priority' : '' }}">

        <div class="card-header-invoice">
            <h5>Invoice Tagihan</h5>
            <small>PT. Jernih Multi Komunikasi</small>
        </div>

        <div class="card-body">

          <div class="info-section">
            <div class="info-item">
                <span class="info-label">No. ID</span>
                <span class="info-value">{{ $pelanggan->nomer_id ?? '-' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Nama</span>
                <span class="info-value">{{ $pelanggan->nama_lengkap ?? '-' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Tanggal Invoice</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($tagihan->tanggal_mulai)->format('d M Y') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Jatuh Tempo</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($tagihan->tanggal_berakhir)->format('d M Y') }}</span>
            </div>
          </div>

          <div class="ppn-notice">
              <i class="bi bi-info-circle-fill"></i>
              <p>Harga sudah termasuk PPN</p>
          </div>

          <div class="price-section">
              <p class="period-label">
                  Periode: {{ \Carbon\Carbon::parse($tagihan->tanggal_berakhir)->translatedFormat('F Y') }}
              </p>

              <div class="price-amount">
                  Rp {{ number_format($paket->harga ?? 0, 0, ',', '.') }}
              </div>

              <p class="price-text">
                  {{ ucwords(\NumberFormatter::create('id_ID', \NumberFormatter::SPELLOUT)->format($paket->harga ?? 0)) }} rupiah
              </p>
          </div>

          <div class="status-wrapper">
              @if($tagihan->status_pembayaran === 'lunas')
                  <span class="status-badge status-lunas">
                      <i class="bi bi-check-circle-fill"></i> Lunas
                  </span>
              @elseif($tagihan->status_pembayaran === 'proses_verifikasi')
                  <span class="status-badge status-verifikasi">
                      <i class="bi bi-clock-fill"></i> Menunggu Verifikasi
                  </span>
              @else
                  <span class="status-badge status-belum">
                      <i class="bi bi-exclamation-circle-fill"></i> Belum Bayar
                  </span>
                  <div>
                      <button class="btn btn-bayar bayar-btn" data-id="{{ $tagihan->id }}">
                          <i class="bi bi-wallet2"></i> Bayar Sekarang
                      </button>
                  </div>
              @endif
          </div>

        </div>
    </div>

    @empty
    <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <h5>Tidak Ada Tagihan</h5>
        <p>Saat ini tidak ada tagihan yang perlu dibayar.<br>Untuk melihat riwayat pembayaran, klik tombol di bawah.</p>
        <a href="https://layanan.jernih.net.id/dashboard/customer/tagihan/selesai">
            <button class="btn btn-primary">
                <i class="bi bi-receipt"></i> Lihat Kwitansi
            </button>
        </a>
    </div>
    @endforelse
    </div>
</div>

<script>
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

/* ================================
   FUNGSI KOMPRES GAMBAR (iOS/Android)
================================== */
function compressImage(file, maxWidth = 1280, quality = 0.6) {
    return new Promise((resolve, reject) => {
        if (file.type === "application/pdf") {
            resolve(file);
            return;
        }

        const reader = new FileReader();
        reader.onload = (event) => {
            const img = new Image();
            img.onload = () => {
                const canvas = document.createElement("canvas");
                const ctx = canvas.getContext("2d");

                let ratio = img.width / img.height;
                if (img.width > maxWidth) {
                    canvas.width = maxWidth;
                    canvas.height = maxWidth / ratio;
                } else {
                    canvas.width = img.width;
                    canvas.height = img.height;
                }

                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                canvas.toBlob(
                    (blob) => {
                        if (!blob) reject("Gagal mengonversi gambar.");
                        else resolve(new File([blob], file.name, { type: "image/jpeg" }));
                    },
                    "image/jpeg",
                    quality
                );
            };
            img.src = event.target.result;
        };
        reader.onerror = () => reject("Gagal membaca file.");
        reader.readAsDataURL(file);
    });
}

/* ================================
   EVENT BAYAR
================================== */
document.querySelectorAll('.bayar-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const tagihanId = btn.dataset.id;
        const rekenings = @json($rekenings);

        let htmlRekening = '<div class="d-flex flex-column gap-2" style="text-align: left;">';
        rekenings.forEach(r => {
            htmlRekening += `
            <label class="card p-3 border cursor-pointer" style="cursor: pointer; transition: all 0.2s ease; border-radius: 8px;">
                <input type="radio" name="type_pembayaran" value="${r.id}" style="margin-right:10px;">
                <strong style="color: #0f172a;">${r.nama_bank}</strong> - <span style="color: #64748b;">${r.nomor_rekening}</span>
            </label>`;
        });
        htmlRekening += '</div>';

        Swal.fire({
            title: 'Pilih Rekening Tujuan',
            html: htmlRekening,
            showCancelButton: true,
            confirmButtonText: 'Lanjutkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#94a3b8',
            preConfirm: () => {
                const selected = document.querySelector('input[name="type_pembayaran"]:checked');
                if (!selected) Swal.showValidationMessage('Pilih salah satu rekening!');
                return selected ? selected.value : null;
            }
        }).then(result => {
            if (!result.isConfirmed) return;
            const selectedRekening = rekenings.find(r => r.id == result.value);

            Swal.fire({
                title: 'Upload Bukti Pembayaran',
                html: `
                    <div style="background: #f8fafc; padding: 16px; border-radius: 8px; margin-bottom: 16px; text-align: left; border: 1px solid #e2e8f0;">
                        <p style="margin: 0; color: #0f172a; font-weight: 600; font-size: 0.9375rem;">${selectedRekening.nama_bank}</p>
                        <p style="margin: 4px 0 0 0; color: #64748b; font-size: 0.875rem;">${selectedRekening.nomor_rekening}</p>
                        <p style="margin: 4px 0 0 0; color: #64748b; font-size: 0.875rem;">A.N ${selectedRekening.nama_pemilik}</p>
                    </div>
                    <input type="file" id="bukti-pembayaran" class="swal2-file" accept="image/*,application/pdf" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                `,
                showCancelButton: true,
                confirmButtonText: 'Kirim',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#0f172a',
                cancelButtonColor: '#94a3b8',
                showLoaderOnConfirm: true,

                preConfirm: async () => {
                    const fileInput = document.getElementById('bukti-pembayaran');
                    if (!fileInput.files.length) return Swal.showValidationMessage('Pilih file bukti pembayaran!');

                    let file = fileInput.files[0];

                    try { file = await compressImage(file); }
                    catch (e) { return Swal.showValidationMessage("Gagal kompres gambar: " + e); }

                    const formData = new FormData();
                    formData.append('bukti_pembayaran', file);
                    formData.append('type_pembayaran', selectedRekening.id);
                    formData.append('_method', 'PUT');

                    return fetch(`/dashboard/customer/tagihan/${tagihanId}`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => { if (!data.success) throw new Error(data.message); return data; })
                    .catch(err => Swal.showValidationMessage(`Gagal upload: ${err.message}`));
                }
            }).then(uploadResult => {
                if (uploadResult.isConfirmed) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Bukti pembayaran telah dikirim',
                        icon: 'success',
                        confirmButtonColor: '#0f172a'
                    }).then(() => location.reload());
                }
            });
        });
    });
});

// Hover effect untuk rekening cards
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('change', function(e) {
        if (e.target.name === 'type_pembayaran') {
            document.querySelectorAll('label.card.cursor-pointer').forEach(label => {
                if (label.querySelector('input:checked')) {
                    label.style.borderColor = '#0f172a';
                    label.style.backgroundColor = '#f8fafc';
                } else {
                    label.style.borderColor = '#e2e8f0';
                    label.style.backgroundColor = '#fff';
                }
            });
        }
    });
    
    document.addEventListener('mouseover', function(e) {
        if (e.target.closest('label.card.cursor-pointer')) {
            const label = e.target.closest('label.card.cursor-pointer');
            if (!label.querySelector('input:checked')) {
                label.style.borderColor = '#cbd5e1';
            }
        }
    });
    
    document.addEventListener('mouseout', function(e) {
        if (e.target.closest('label.card.cursor-pointer')) {
            const label = e.target.closest('label.card.cursor-pointer');
            if (!label.querySelector('input:checked')) {
                label.style.borderColor = '#e2e8f0';
            }
        }
    });
});
</script>

</body>
</html>