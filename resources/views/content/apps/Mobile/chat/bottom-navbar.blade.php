@php
use Illuminate\Support\Facades\Auth;
$user = Auth::guard('customer')->user();
@endphp

<!-- Bottom Navigation -->
<div class="bottom-nav">
    <button class="tab-btn {{ $active === 'home' ? 'active' : '' }}" onclick="window.location.href='/dashboard/customer/tagihan/home'">
        <i class="bi bi-house-door-fill"></i>
        <span>Home</span>
    </button>

    <button class="tab-btn {{ $active === 'tagihan' ? 'active' : '' }}" onclick="window.location.href='/dashboard/customer/tagihan'">
        <i class="bi bi-receipt"></i>
        <span>Tagihan</span>
    </button>

    <button class="tab-btn {{ $active === 'invoice' ? 'active' : '' }}" onclick="window.location.href='/dashboard/customer/tagihan/selesai'">
        <i class="bi bi-file-earmark-text"></i>
        <span>Kwitansi</span>
    </button>

    <button class="tab-btn {{ $active === 'chat' ? 'active' : '' }}" onclick="window.location.href='/dashboard/customer/chat'">
        <i class="bi bi-chat-dots"></i>
        <span>Chat</span>
    </button>

    <button id="btn-profile" class="tab-btn {{ $active === 'profile' ? 'active' : '' }}">
        <i class="bi bi-person-circle"></i>
        <span>Profile</span>
    </button>
</div>

<!-- Profile Modal -->
<div id="profile-overlay" class="profile-overlay">
    <div class="profile-modal">
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="bi bi-person-circle"></i>
            </div>
            <div class="profile-info">
                <h5>{{ $user->nama_lengkap ?? 'Nama Pelanggan' }}</h5>
                <p>{{ $user->no_whatsapp }}</p>
            </div>
        </div>
        
        <div class="profile-divider"></div>
        
        
        <div class="profile-divider"></div>
        
        <button id="btn-logout" class="logout-btn">
            <i class="bi bi-box-arrow-right"></i>
            <span>Keluar</span>
        </button>
    </div>
</div>

<style>
/* Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

/* Bottom Navbar */
.bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 72px;
    background: #ffffff;
    display: flex;
    justify-content: space-around;
    align-items: center;
    box-shadow: 0 -2px 16px rgba(0,0,0,0.08);
    border-top: 1px solid #e2e8f0;
    z-index: 999;
    font-family: 'Inter', sans-serif;
}

.bottom-nav .tab-btn {
    background: none;
    border: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    color: #94a3b8;
    position: relative;
    transition: all 0.2s ease;
    cursor: pointer;
    padding: 8px 16px;
    border-radius: 12px;
}

.bottom-nav .tab-btn:hover {
    background: #f8fafc;
}

.bottom-nav .tab-btn i {
    font-size: 1.5rem;
}

.bottom-nav .tab-btn span {
    font-size: 0.6875rem;
    font-weight: 600;
    letter-spacing: -0.01em;
}

/* Active tab */
.bottom-nav .tab-btn.active {
    color: #0f172a;
}

.bottom-nav .tab-btn.active::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 32px;
    height: 3px;
    background: #0f172a;
    border-radius: 0 0 3px 3px;
}

/* Profile Overlay */
.profile-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    display: none;
    align-items: flex-end;
    z-index: 1000;
    animation: fadeIn 0.2s ease;
}

.profile-overlay.show {
    display: flex;
}

/* Profile Modal */
.profile-modal {
    background: #ffffff;
    border-radius: 24px 24px 0 0;
    width: 100%;
    max-width: 680px;
    margin: 0 auto;
    padding: 24px;
    animation: slideUp 0.3s ease;
    box-shadow: 0 -4px 24px rgba(0,0,0,0.12);
}

/* Profile Header */
.profile-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
}

.profile-avatar {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.profile-avatar i {
    font-size: 2.5rem;
    color: #64748b;
}

.profile-info h5 {
    margin: 0;
    color: #0f172a;
    font-size: 1.125rem;
    font-weight: 700;
    letter-spacing: -0.01em;
}

.profile-info p {
    margin: 4px 0 0 0;
    color: #64748b;
    font-size: 0.875rem;
}

/* Divider */
.profile-divider {
    height: 1px;
    background: #f1f5f9;
    margin: 20px 0;
}

/* Profile Menu */
.profile-menu {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 12px;
    color: #0f172a;
    font-size: 0.9375rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: 'Inter', sans-serif;
}

.menu-item:hover {
    background: #f1f5f9;
    border-color: #e2e8f0;
}

.menu-item i:first-child {
    font-size: 1.25rem;
    color: #64748b;
}

.menu-item span {
    flex: 1;
}

.menu-item .arrow {
    font-size: 0.875rem;
    color: #cbd5e1;
}

/* Logout Button */
.logout-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 16px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 12px;
    color: #dc2626;
    font-size: 0.9375rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-top: 20px;
    font-family: 'Inter', sans-serif;
}

.logout-btn:hover {
    background: #fee2e2;
    border-color: #fca5a5;
}

.logout-btn i {
    font-size: 1.125rem;
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideUp {
    from {
        transform: translateY(100%);
    }
    to {
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 576px) {
    .profile-modal {
        padding: 20px;
    }

    .profile-header {
        gap: 12px;
    }

    .profile-avatar {
        width: 56px;
        height: 56px;
    }

    .profile-avatar i {
        font-size: 2rem;
    }

    .profile-info h5 {
        font-size: 1rem;
    }

    .profile-info p {
        font-size: 0.8125rem;
    }
}

/* Prevent body scroll when modal is open */
body.modal-open {
    overflow: hidden;
}
</style>

<script>
// Profile Modal Toggle
const btnProfile = document.getElementById('btn-profile');
const overlay = document.getElementById('profile-overlay');

btnProfile.addEventListener('click', (e) => {
    e.stopPropagation();
    overlay.classList.toggle('show');
    document.body.classList.toggle('modal-open');
});

// Close modal when clicking overlay
overlay.addEventListener('click', (e) => {
    if (e.target === overlay) {
        overlay.classList.remove('show');
        document.body.classList.remove('modal-open');
    }
});

// Close modal on escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && overlay.classList.contains('show')) {
        overlay.classList.remove('show');
        document.body.classList.remove('modal-open');
    }
});

// Logout with confirmation
document.getElementById('btn-logout').addEventListener('click', () => {
    Swal.fire({
        title: 'Keluar dari Akun?',
        text: 'Anda akan keluar dari aplikasi',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#94a3b8',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Keluar...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Logout request
            fetch('/customer/logout', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (response.ok) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Anda telah keluar dari akun',
                        icon: 'success',
                        confirmButtonColor: '#0f172a',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '/';
                    });
                } else {
                    throw new Error('Logout failed');
                }
            })
            .catch(() => {
                Swal.fire({
                    title: 'Error',
                    text: 'Gagal keluar dari akun',
                    icon: 'error',
                    confirmButtonColor: '#dc2626'
                });
            });
        }
    });
});
</script>