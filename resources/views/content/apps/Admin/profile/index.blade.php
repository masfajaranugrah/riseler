@extends('layouts/layoutMaster')

@section('title', 'Profile Administrator')

@section('vendor-style')
<style>
  :root {
    --card-shadow: 0 2px 8px rgba(0,0,0,0.08);
    --card-hover-shadow: 0 4px 16px rgba(0,0,0,0.12);
    --border-radius: 12px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --primary-color: #18181b;
    --gray-border: #e4e4e7;
  }

  .card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    background: white;
    transition: var(--transition);
  }

  .card:hover {
    box-shadow: var(--card-hover-shadow);
  }

  .card-header-custom {
    background: #ffffff !important;
    border-bottom: 1px solid var(--gray-border);
    padding: 1.5rem;
    border-radius: var(--border-radius) var(--border-radius) 0 0;
    color: #18181b;
  }

  .card-header-custom h4 { color: #18181b !important; }
  .card-header-custom p { color: #71717a !important; }
  .card-header-custom i { color: #18181b !important; }

  .btn-add {
    background: #18181b !important;
    color: #fafafa !important;
    border: 1px solid #18181b !important;
    padding: 10px 24px;
    border-radius: 8px;
    font-weight: 600;
  }

  .btn-add:hover {
    background: #27272a !important;
    border-color: #27272a !important;
    color: #fff !important;
  }

  .info-box {
    border-radius: 12px;
    padding: 1.25rem;
    border: 1px solid #d1d5db;
    background: #f9fafb;
  }

  .password-box {
    border-radius: 12px;
    padding: 1.25rem;
    border: 1px solid #bbf7d0;
    background: #f0fdf4;
  }

  .password-rules {
    font-size: 0.8125rem;
    color: #6b7280;
  }

  .password-checklist {
    margin: 0.5rem 0 0;
    padding-left: 0;
    list-style: none;
  }

  .password-checklist li {
    font-size: 0.8125rem;
    color: #9ca3af;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }

  .password-checklist li.ok {
    color: #16a34a;
    font-weight: 600;
  }

  .password-checklist li::before {
    content: '•';
    font-size: 1rem;
    line-height: 1;
  }

  .btn-save-password {
    width: 100%;
    background: #18181b !important;
    border: 1px solid #18181b !important;
    color: #fff !important;
    font-weight: 600;
    border-radius: 8px;
  }

  .btn-save-password:hover {
    background: #27272a !important;
    border-color: #27272a !important;
  }

  .modal .btn-secondary {
    background: #fff !important;
    color: #111827 !important;
    border: 1px solid #d1d5db !important;
  }

  .modal .btn-secondary:hover {
    background: #f3f4f6 !important;
    border-color: #9ca3af !important;
  }

  .profile-table {
    border-radius: 8px;
    overflow: hidden;
  }

  .profile-table thead th {
    background: #f3f4f6;
    font-size: 0.75rem;
    text-transform: uppercase;
    color: #374151;
    border: none;
    padding: 0.9rem 1rem;
  }

  .profile-table tbody td {
    padding: 1rem;
    border-color: #eef2f7;
    vertical-align: middle;
  }

  .badge-dark {
    background: #111827;
    color: #fff;
    border-radius: 6px;
    padding: 0.35rem 0.6rem;
    font-size: 0.75rem;
  }
</style>
@endsection

@section('content')
@php
  $user = auth()->user();
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header-custom d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
          <div>
            <h4 class="mb-1 fw-bold"><i class="ri-user-settings-line me-2"></i>Profile Administrator</h4>
            <p class="mb-0 opacity-75 small">Kelola data akun dan keamanan password administrator</p>
          </div>
          <div></div>
        </div>

        <div class="card-body">
          <div class="row g-3 mb-3">
            <div class="col-lg-6">
              <div class="info-box h-100">
                <h5 class="mb-2"><i class="ri-id-card-line me-2"></i>Informasi Akun</h5>
                <p class="text-muted mb-0 small">Detail akun login administrator.</p>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="password-box h-100">
                <h5 class="mb-2 text-success"><i class="ri-shield-keyhole-line me-2"></i>Keamanan Password</h5>
                <p class="mb-0 password-rules">Gunakan kombinasi huruf besar, angka, dan simbol untuk menjaga keamanan akun.</p>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table profile-table mb-0">
              <thead>
                <tr>
                  <th style="width: 30%;">Field</th>
                  <th>Value</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="fw-semibold">Nama</td>
                  <td>{{ $user->name ?? '-' }}</td>
                </tr>
                <tr>
                  <td class="fw-semibold">Email</td>
                  <td>{{ $user->email ?? '-' }} <span class="text-muted">(tidak bisa diubah)</span></td>
                </tr>
                <tr>
                  <td class="fw-semibold">Password</td>
                  <td>********</td>
                </tr>
              </tbody>
            </table>
          </div>

          <hr class="my-4">
          <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
              <i class="ri-lock-password-line me-1"></i> Ganti Password
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="changePasswordModalLabel">Ganti Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="change-password-form">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Password Baru</label>
            <input type="password" name="new_password" id="new_password" class="form-control" minlength="8" required>
          </div>
          <div class="mb-2">
            <label class="form-label fw-semibold">Verifikasi Password Baru</label>
            <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" minlength="8" required>
          </div>
          <ul class="password-checklist">
            <li id="rule-length">Minimal 8 karakter</li>
            <li id="rule-uppercase">Mengandung huruf besar (A-Z)</li>
            <li id="rule-number">Mengandung angka (0-9)</li>
            <li id="rule-symbol">Mengandung simbol (contoh: !@#$)</li>
            <li id="rule-match">Password dan verifikasi sama</li>
          </ul>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-save-password">Simpan Password Baru</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  const newPasswordInput = document.getElementById('new_password');
  const confirmPasswordInput = document.getElementById('new_password_confirmation');
  const ruleLength = document.getElementById('rule-length');
  const ruleUppercase = document.getElementById('rule-uppercase');
  const ruleNumber = document.getElementById('rule-number');
  const ruleSymbol = document.getElementById('rule-symbol');
  const ruleMatch = document.getElementById('rule-match');

  function toggleRule(el, ok) {
    el.classList.toggle('ok', ok);
  }

  function refreshPasswordRules() {
    const newPassword = newPasswordInput.value || '';
    const confirmPassword = confirmPasswordInput.value || '';

    toggleRule(ruleLength, newPassword.length >= 8);
    toggleRule(ruleUppercase, /[A-Z]/.test(newPassword));
    toggleRule(ruleNumber, /\d/.test(newPassword));
    toggleRule(ruleSymbol, /[^A-Za-z0-9]/.test(newPassword));
    toggleRule(ruleMatch, newPassword.length > 0 && newPassword === confirmPassword);
  }

  newPasswordInput.addEventListener('input', refreshPasswordRules);
  confirmPasswordInput.addEventListener('input', refreshPasswordRules);

  document.getElementById('change-password-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('new_password_confirmation').value;
    const strongPassword = /^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;

    if (!strongPassword.test(newPassword)) {
      Swal.fire('Password belum valid', 'Gunakan minimal 8 karakter, huruf besar, angka, dan simbol.', 'warning');
      return;
    }

    if (newPassword !== confirmPassword) {
      Swal.fire('Verifikasi tidak sama', 'Password baru dan verifikasi harus sama.', 'warning');
      return;
    }

    Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    try {
      const response = await fetch("{{ route('admin.profile.password.update') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
          new_password: newPassword,
          new_password_confirmation: confirmPassword
        })
      });

      const payload = await response.json();
      if (!response.ok || !payload.success) {
        throw new Error(payload.message || 'Gagal memperbarui password');
      }

      await Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'Password berhasil diganti. Anda akan logout otomatis.',
        timer: 1800,
        showConfirmButton: false
      });

      window.location.href = payload.redirect || '/dashboard/auth/login';
    } catch (error) {
      Swal.fire('Gagal', error.message || 'Terjadi kesalahan saat mengganti password.', 'error');
    }
  });
</script>
@endsection
