@php
use Illuminate\Support\Facades\Auth;
$user = Auth::guard('customer')->user();
@endphp

<!-- Bottom Navigation -->
<div class="bottom-nav">
    <button class="tab-btn {{ $active === 'home' ? 'active' : '' }}"
        onclick="window.location.href='/dashboard/customer/tagihan/home'">
        <i class="bi bi-house-door-fill"></i>
        <span>Home</span>
    </button>

    <button class="tab-btn {{ $active === 'tagihan' ? 'active' : '' }}"
        onclick="window.location.href='/dashboard/customer/tagihan'">
        <i class="bi bi-receipt"></i>
        <span>Tagihan</span>
    </button>

    <button class="tab-btn {{ $active === 'invoice' ? 'active' : '' }}"
        onclick="window.location.href='/dashboard/customer/tagihan/selesai'">
        <i class="bi bi-file-earmark-text"></i>
        <span>Kwitansi</span>
    </button>

    <button class="tab-btn {{ $active === 'chat' ? 'active' : '' }}"
        onclick="window.location.href='/dashboard/customer/chat'">
        <i class="bi bi-chat-dots"></i>
        <span>Chat</span>
    </button>

    <button class="tab-btn {{ $active === 'profile' ? 'active' : '' }}"
        onclick="window.location.href='/dashboard/customer/profile'">
        <i class="bi bi-person-circle"></i>
        <span>Profile</span>
    </button>
</div>

<style>
    /* Google Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    /* Bottom Navbar */
    .bottom-nav {
        position: fixed;
        bottom: calc(10px + env(safe-area-inset-bottom));
        left: 50%;
        transform: translateX(-50%);
        width: min(92vw, 420px);
        height: 64px;
        background: #1f2326;
        display: flex;
        justify-content: space-around;
        align-items: center;
        padding: 8px 16px;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.26), inset 0 1px 0 rgba(255,255,255,0.06);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 9999px;
        z-index: 999;
        font-family: 'Inter', sans-serif;
    }

    .bottom-nav .tab-btn {
        background: none;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #cfd6dd;
        position: relative;
        transition: all 0.2s ease;
        cursor: pointer;
        width: 44px;
        height: 44px;
        padding: 0;
        border-radius: 9999px;
        line-height: 0;
    }

    .bottom-nav .tab-btn:hover {
        background: rgba(255, 255, 255, 0.08);
    }

    .bottom-nav .tab-btn i {
        font-size: 1.35rem;
        line-height: 1;
        display: block;
        margin: 0;
        padding: 0;
    }

    .bottom-nav .tab-btn span {
        display: none;
    }

    /* Active tab */
    .bottom-nav .tab-btn.active {
        width: 48px;
        height: 48px;
        color: #111827;
        background: #ffffff;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.22);
        line-height: 0;
    }

    .bottom-nav .tab-btn.active::before {
        content: none;
    }

</style>
