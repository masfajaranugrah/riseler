<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Daftar Invoice</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

/* App Container */
#app-container {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

/* Card Invoice */
.card-invoice {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.card-invoice:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
}

.card-invoice.new-invoice {
    border: 2px solid #bbf7d0;
    box-shadow: 0 4px 16px rgba(16,185,129,0.12);
}

.card-invoice.new-invoice:hover {
    box-shadow: 0 6px 20px rgba(16,185,129,0.16);
}

/* Card Header */
.card-header {
    background: #0f172a;
    color: white;
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #1e293b;
}

.card-header .invoice-title {
    font-size: 0.9375rem;
    font-weight: 600;
    letter-spacing: -0.01em;
}

.badge-new {
    background: #10b981;
    color: white;
    padding: 4px 10px;
    border-radius: 100px;
    font-size: 0.6875rem;
    font-weight: 600;
    letter-spacing: 0.03em;
}

/* Card Body */
.card-body {
    padding: 24px;
}

/* Invoice Info Grid */
.invoice-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.info-label {
    font-size: 0.8125rem;
    color: #64748b;
    font-weight: 500;
}

.info-value {
    font-size: 0.9375rem;
    color: #0f172a;
    font-weight: 600;
}

/* Price Section */
.price-section {
    text-align: center;
    padding: 20px 0;
    background: #f8fafc;
    border-radius: 12px;
    margin: 20px 0;
    border: 1px solid #f1f5f9;
}

.price-amount {
    font-size: 1.75rem;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.02em;
}

/* Invoice Footer */
.invoice-footer {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    padding-top: 20px;
    border-top: 1px solid #f1f5f9;
}

.btn-action {
    padding: 10px 20px;
    font-size: 0.875rem;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
}

.btn-view {
    background: #0f172a;
    color: white;
}

.btn-view:hover {
    background: #1e293b;
    transform: translateY(-1px);
    color: white;
}

.btn-download {
    background: #f8fafc;
    color: #0f172a;
    border: 1px solid #e2e8f0;
}

.btn-download:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    transform: translateY(-1px);
}

/* Empty State */
.empty-state {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 48px 24px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
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
    font-size: 0.9375rem;
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
@media (max-width: 576px) {
    body {
        padding: 16px 0 100px;
    }

    .header-section h4 {
        font-size: 1.5rem;
    }

    .card-body {
        padding: 20px 16px;
    }

    .invoice-info {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .invoice-footer {
        flex-direction: column;
        gap: 8px;
    }

    .btn-action {
        width: 100%;
        justify-content: center;
    }

    .card-header {
        flex-direction: column;
        gap: 8px;
        align-items: flex-start;
    }
}

/* Loading Animation */
@keyframes shimmer {
    0% { opacity: 0.6; }
    50% { opacity: 1; }
    100% { opacity: 0.6; }
}

.loading {
    animation: shimmer 1.5s ease-in-out infinite;
}
</style>
</head>

<body class="container">
    <div class="header-section">
        <h4>Kwitansi</h4>
        <p>Riwayat pembayaran dan kwitansi Anda</p>
    </div>

    <div id="app-container"></div>

    <div class="bottom-nav">
        @include('content.apps.Customer.tagihan.bottom-navbar', ['active' => 'invoice'])
    </div>

    <script>
    let oldInvoices = {};

    async function fetchInvoice() {
        try {
            const res = await fetch('/dashboard/customer/tagihan/selesai/json');
            const data = await res.json();
            const invoices = data.data || [];
            const container = document.getElementById('app-container');

            let updated = false;
            const newInvoices = {};
            invoices.forEach(inv => {
                newInvoices[inv.id] = inv;
                if(!oldInvoices[inv.id] || oldInvoices[inv.id].kwitansi !== inv.kwitansi) updated = true;
            });

            if(!updated && Object.keys(oldInvoices).length>0) return;
            oldInvoices = newInvoices;

            container.innerHTML = "";

            if(invoices.length === 0){
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-receipt"></i>
                        <h5>Belum Ada Kwitansi</h5>
                        <p>Kwitansi akan muncul setelah pembayaran Anda diverifikasi</p>
                    </div>`;
                return;
            }

            const invoicesNew = [];
            const invoicesOld = [];
            const now = new Date();

            invoices.forEach(invoice => {
                let isNew = false;
                if(invoice.tanggal_pembayaran){
                    const bayarDate = new Date(invoice.tanggal_pembayaran);
                    const diffDays = Math.ceil(Math.abs(now - bayarDate) / (1000*60*60*24));
                    if(diffDays <= 7) isNew = true;
                }
                if(isNew) invoicesNew.push(invoice); else invoicesOld.push(invoice);
            });

            const finalInvoices = [...invoicesNew, ...invoicesOld];
            finalInvoices.forEach(invoice => {
                const nama = invoice.nama_pelanggan ?? '-';
                const typePembayaran = invoice.type_pembayaran ?? '-';
                const idInvoice = invoice.nomer_id ?? '-';
                const harga = invoice.harga ?? 0;

                const tanggalMulai = invoice.tanggal_mulai
                    ? new Date(invoice.tanggal_mulai).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'})
                    : '-';

                const tanggalBerakhir = invoice.tanggal_berakhir
                    ? new Date(invoice.tanggal_berakhir).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'})
                    : '-';

                const tanggalBayar = invoice.tanggal_pembayaran
                    ? new Date(invoice.tanggal_pembayaran).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'})
                    : '-';

                const hargaFormatted = new Intl.NumberFormat('id-ID').format(harga);

                const isNew = invoicesNew.includes(invoice);
                const cardClass = isNew ? 'card-invoice new-invoice' : 'card-invoice';
                const badgeNew = isNew ? `<span class="badge-new">BARU</span>` : "";

                container.innerHTML += `
                <div class="${cardClass}">
                    <div class="card-header">
                        <span class="invoice-title">Invoice ${idInvoice}</span>
                        ${badgeNew}
                    </div>

                    <div class="card-body">
                        <div class="invoice-info">
                            <div class="info-item">
                                <span class="info-label">Nama Pelanggan</span>
                                <span class="info-value">${nama}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Tanggal Invoice</span>
                                <span class="info-value">${tanggalMulai}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Jatuh Tempo</span>
                                <span class="info-value">${tanggalBerakhir}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Tanggal Bayar</span>
                                <span class="info-value">${tanggalBayar}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Metode Pembayaran</span>
                                <span class="info-value">${typePembayaran}</span>
                            </div>
                        </div>

                        <div class="price-section">
                            <div class="price-amount">Rp ${hargaFormatted}</div>
                        </div>

                        <div class="invoice-footer">
                            ${
                                invoice.kwitansi
                                ? `<a href="/${invoice.kwitansi}" target="_blank" class="btn-action btn-view">
                                        <i class="bi bi-eye"></i> Lihat Kwitansi
                                   </a>`
                                : `<span style="color: #94a3b8; font-size: 0.875rem;">Kwitansi tidak tersedia</span>`
                            }

                            ${
                                invoice.kwitansi
                                ? `<button class="btn-action btn-download" onclick='downloadKuitansi(${JSON.stringify(invoice)})'>
                                        <i class="bi bi-download"></i> Download
                                   </button>`
                                : ""
                            }
                        </div>
                    </div>
                </div>`;
            });

        } catch(e){ 
            console.error(e);
            document.getElementById('app-container').innerHTML = `
                <div class="empty-state">
                    <i class="bi bi-exclamation-triangle"></i>
                    <h5>Terjadi Kesalahan</h5>
                    <p>Tidak dapat memuat data. Silakan coba lagi.</p>
                </div>`;
        }
    }

    function downloadKuitansi(invoice){
        if(!invoice.kwitansi){
            Swal.fire({
                title: 'Error',
                text: 'Kuitansi belum tersedia',
                icon: 'error',
                confirmButtonColor: '#0f172a'
            });
            return;
        }

        const url = `/${invoice.kwitansi}`;
        
        // Show toast notification
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        Toast.fire({
            icon: 'info',
            title: 'Mengunduh kwitansi...'
        });

        // Download in background
        fetch(url)
            .then(res => { 
                if(!res.ok) throw new Error('File tidak ditemukan'); 
                return res.blob(); 
            })
            .then(blob => {
                const fileName = `Kwitansi-${invoice.pelanggan_id}.pdf`;
                
                // Check if browser supports File System Access API (Chrome Android)
                if (window.showSaveFilePicker) {
                    // Modern browsers with file picker
                    window.showSaveFilePicker({
                        suggestedName: fileName,
                        types: [{
                            description: 'PDF Files',
                            accept: {'application/pdf': ['.pdf']},
                        }],
                    }).then(fileHandle => {
                        return fileHandle.createWritable();
                    }).then(writable => {
                        return writable.write(blob).then(() => writable.close());
                    }).then(() => {
                        Toast.fire({
                            icon: 'success',
                            title: 'Kwitansi berhasil disimpan'
                        });
                    }).catch(err => {
                        if (err.name !== 'AbortError') {
                            // Fallback to standard download
                            standardDownload(blob, fileName, Toast);
                        }
                    });
                } 
                // Check if Share API is available (iOS/Android)
                else if (navigator.share && navigator.canShare && navigator.canShare({files: [new File([blob], fileName, {type: 'application/pdf'})]})) {
                    const file = new File([blob], fileName, {type: 'application/pdf'});
                    navigator.share({
                        title: 'Kwitansi Pembayaran',
                        text: 'Kwitansi pembayaran Anda',
                        files: [file]
                    }).then(() => {
                        Toast.fire({
                            icon: 'success',
                            title: 'File berhasil dibagikan'
                        });
                    }).catch(err => {
                        if (err.name !== 'AbortError') {
                            // Fallback to standard download
                            standardDownload(blob, fileName, Toast);
                        }
                    });
                }
                else {
                    // Standard download fallback
                    standardDownload(blob, fileName, Toast);
                }
            })
            .catch(err => {
                Toast.fire({
                    icon: 'error',
                    title: 'Gagal mengunduh: ' + err.message
                });
            });
    }

    function standardDownload(blob, fileName, Toast) {
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = fileName;
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        
        setTimeout(() => {
            document.body.removeChild(link);
            URL.revokeObjectURL(link.href);
            
            Toast.fire({
                icon: 'success',
                title: 'Kwitansi berhasil diunduh'
            });
        }, 100);
    }

    // Initial load
    fetchInvoice();
    
    // Auto refresh every 5 seconds
    setInterval(fetchInvoice, 5000);
    </script>
</body>
</html>