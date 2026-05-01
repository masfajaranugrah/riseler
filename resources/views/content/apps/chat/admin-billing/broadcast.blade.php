@extends('layouts/layoutMaster')

@section('title', 'Broadcast Pembayaran - Admin')

@use('Illuminate\Support\Facades\Auth')

@section('vendor-style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('vendor-script')
    @vite(['resources/js/bootstrap.js'])
@endsection

@section('page-style')
<style>
    :root {
        --bg: #0b1020;
        --card: #0f172a;
        --accent: #22c55e;
        --accent-dark: #16a34a;
    }
    body {
        background: radial-gradient(circle at 20% 20%, rgba(14,165,233,0.08), transparent 28%),
                    radial-gradient(circle at 80% 0%, rgba(34,197,94,0.1), transparent 30%),
                    #0b1020;
    }
    .layout-wrapper { background: transparent !important; }
    .page-container {
        max-width: 960px;
        margin: 40px auto 80px;
        padding: 0 20px;
    }
    .hero {
        color: #e2e8f0;
        margin-bottom: 24px;
    }
    .hero h1 {
        font-size: 28px;
        font-weight: 800;
        margin-bottom: 8px;
    }
    .hero p {
        color: #94a3b8;
        margin: 0;
    }
    .card-glass {
        background: rgba(15, 23, 42, 0.75);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 20px;
        box-shadow: 0 30px 80px rgba(0,0,0,0.28);
        padding: 24px;
        backdrop-filter: blur(8px);
        color: #e2e8f0;
    }
    label { font-weight: 700; font-size: 14px; }
    select, textarea {
        width: 100%;
        background: #0b1020;
        color: #e2e8f0;
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 12px;
        padding: 12px 12px;
        font-size: 15px;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }
    select:focus, textarea:focus {
        border-color: #22c55e;
        box-shadow: 0 0 0 3px rgba(34,197,94,0.15);
    }
    textarea { min-height: 120px; resize: vertical; }
    .btn-send {
        width: 100%;
        border: none;
        border-radius: 14px;
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: #fff;
        font-weight: 800;
        font-size: 17px;
        padding: 14px 16px;
        margin-top: 12px;
        box-shadow: 0 15px 40px rgba(34,197,94,0.35);
        cursor: pointer;
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .btn-send:disabled { opacity: .6; cursor: not-allowed; }
    .btn-send:hover { transform: translateY(-1px); box-shadow: 0 18px 50px rgba(34,197,94,0.4); }

    .progress-box {
        margin-top: 18px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 14px;
        padding: 12px;
    }
    .progress-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        color: #cbd5e1;
        margin-bottom: 8px;
    }
    .progress-bar-track {
        width: 100%;
        height: 8px;
        background: rgba(255,255,255,0.06);
        border-radius: 99px;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #22c55e, #0ea5e9);
        transition: width .2s ease;
    }
    .pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,0.08);
        color: #cbd5e1;
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 13px;
    }
</style>
@endsection

@section('content')
<div class="page-container">
    <div class="hero">
        <h1>Broadcast Pembayaran</h1>
        <p>Kirim salam, kata bijak, atau pengingat tagihan ke semua pelanggan tanpa mengganggu percakapan lain.</p>
    </div>

    <div class="card-glass">
        <form id="bcForm">
            @csrf
            <div style="display:flex; gap:12px; align-items:center; margin-bottom:10px;">
                <span class="pill"><i class="fas fa-shield-check"></i> Admin Billing</span>
                <span class="pill" id="bcStatusPill" style="display:none;"></span>
            </div>

            <div style="margin-bottom:12px;">
                <label class="mb-1"><i class="fas fa-bullhorn"></i> Tipe Broadcast</label>
                <select id="bcType">
                    <option value="greeting">Salam (pagi/siang/sore/malam)</option>
                    <option value="quote">Kata Bijak</option>
                    <option value="billing">Pengingat Tagihan</option>
                    <option value="custom">Custom</option>
                </select>
            </div>

            <div style="margin-bottom:12px;" id="variantWrap">
                <label class="mb-1">Varian Salam</label>
                <select id="bcVariant">
                    <option value="pagi">Pagi</option>
                    <option value="siang">Siang</option>
                    <option value="sore">Sore</option>
                    <option value="malam">Malam</option>
                </select>
            </div>

            <div style="margin-bottom:12px;">
                <label class="mb-1">Isi pesan (opsional kecuali Custom)</label>
                <textarea id="bcMessage" placeholder="Isi pesan bila ingin menimpa template"></textarea>
            </div>

            <button type="submit" class="btn-send" id="bcSend">Kirim ke semua pelanggan</button>

            <div class="progress-box" id="progressBox" style="display:none;">
                <div class="progress-head">
                    <span id="progressText">Menunggu...</span>
                    <span id="progressPct">0%</span>
                </div>
                <div class="progress-bar-track">
                    <div class="progress-bar-fill" id="progressFill"></div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const typeEl = document.getElementById('bcType');
    const variantWrap = document.getElementById('variantWrap');
    const variantEl = document.getElementById('bcVariant');
    const msgEl = document.getElementById('bcMessage');
    const btn = document.getElementById('bcSend');
    const pill = document.getElementById('bcStatusPill');
    const box = document.getElementById('progressBox');
    const pText = document.getElementById('progressText');
    const pPct = document.getElementById('progressPct');
    const pFill = document.getElementById('progressFill');
    let pollId = null;
    let total = 0;

    const toggleVariant = () => {
        variantWrap.style.display = typeEl.value === 'greeting' ? 'block' : 'none';
    };
    typeEl.addEventListener('change', toggleVariant);
    toggleVariant();

    document.getElementById('bcForm').addEventListener('submit', (e) => {
        e.preventDefault();
        const payload = { type: typeEl.value };
        if (payload.type === 'greeting') payload.variant = variantEl.value;
        if (msgEl.value.trim()) payload.message = msgEl.value.trim();
        if (payload.type === 'custom' && !payload.message) {
            alert('Isi pesan wajib untuk custom');
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Mengirim...';
        pill.style.display = 'none';
        setProgress(0, 0);
        box.style.display = 'block';

        axios.post('/admin-chat/broadcast', payload)
            .then(res => {
                total = res.data.total || 0;
                setProgress(0, total);
                pill.textContent = `Broadcast dimulai (${total} pelanggan)`;
                pill.style.display = 'inline-flex';
                pill.style.background = 'rgba(34,197,94,0.15)';
                pill.style.color = '#22c55e';
                if (res.data.broadcast_id) startPoll(res.data.broadcast_id);
            })
            .catch(err => {
                alert(err.response?.data?.error || 'Gagal kirim broadcast');
                box.style.display = 'none';
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = 'Kirim ke semua pelanggan';
            });
    });

    function startPoll(id) {
        stopPoll();
        pollId = setInterval(() => {
            axios.get(`/admin-chat/broadcast/${id}/progress`)
                .then(res => {
                    const d = res.data || {};
                    const t = d.total || total || 0;
                    setProgress(d.done || 0, t);
                    if (d.status === 'completed') {
                        stopPoll();
                        pill.textContent = `Selesai ${d.done}/${t}`;
                        pill.style.background = 'rgba(14,165,233,0.15)';
                        pill.style.color = '#0ea5e9';
                    } else if (d.status === 'failed') {
                        stopPoll();
                        pill.textContent = 'Gagal: ' + (d.error || 'unknown');
                        pill.style.background = 'rgba(239,68,68,0.15)';
                        pill.style.color = '#ef4444';
                        alert(pill.textContent);
                    }
                })
                .catch(() => {
                    stopPoll();
                    pill.textContent = 'Gagal memantau progress';
                    pill.style.background = 'rgba(239,68,68,0.15)';
                    pill.style.color = '#ef4444';
                });
        }, 2000);
    }

    function stopPoll() {
        if (pollId) clearInterval(pollId);
        pollId = null;
    }

    function setProgress(done, t) {
        const hasTotal = t > 0;
        const pct = hasTotal ? Math.floor((done / t) * 100) : 0;
        pText.textContent = hasTotal ? `${done}/${t} dikirim` : `${done} dikirim...`;
        pPct.textContent = hasTotal ? `${pct}%` : '...';
        pFill.style.width = hasTotal ? `${Math.min(pct,100)}%` : '0%';
    }
});
</script>
@endsection
