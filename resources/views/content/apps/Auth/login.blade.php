@php
  $configData = Helper::appClasses();
  $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', 'Login')

@section('page-style')
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      min-height: 100vh;
      background: #f4f4f5;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }

    .login-container {
      width: 100%;
      max-width: 400px;
    }

    .login-card {
      background: #ffffff;
      border: 1px solid #e4e4e7;
      border-radius: 12px;
      padding: 2.5rem;
      box-shadow:
        0 1px 2px 0 rgba(0, 0, 0, 0.03),
        0 4px 8px -2px rgba(0, 0, 0, 0.05),
        0 12px 24px -4px rgba(0, 0, 0, 0.08);
    }

    .brand-section {
      text-align: center;
      margin-bottom: 2rem;
    }

    .brand-logo {
      width: 56px;
      height: 56px;
      background: #18181b;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.25rem;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .brand-logo svg {
      width: 28px;
      height: 28px;
      color: #ffffff;
    }

    .brand-title {
      font-size: 1.5rem;
      font-weight: 600;
      color: #09090b;
      letter-spacing: -0.025em;
      margin-bottom: 0.5rem;
    }

    .brand-subtitle {
      font-size: 0.875rem;
      color: #71717a;
      font-weight: 400;
    }

    .form-group {
      margin-bottom: 1.25rem;
    }

    .form-label {
      display: block;
      font-size: 0.875rem;
      font-weight: 500;
      color: #09090b;
      margin-bottom: 0.5rem;
    }

    .form-input {
      width: 100%;
      padding: 0.75rem 1rem;
      background: #ffffff;
      border: 1px solid #d4d4d8;
      border-radius: 8px;
      font-size: 0.875rem;
      color: #09090b;
      transition: all 0.15s ease;
      outline: none;
    }

    .form-input::placeholder {
      color: #a1a1aa;
    }

    .form-input:hover {
      border-color: #a1a1aa;
    }

    .form-input:focus {
      border-color: #18181b;
      box-shadow: 0 0 0 3px rgba(24, 24, 27, 0.1);
    }

    .password-wrapper {
      position: relative;
    }

    .password-wrapper .form-input {
      padding-right: 2.75rem;
    }

    .password-toggle {
      position: absolute;
      right: 0.875rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #71717a;
      cursor: pointer;
      padding: 0;
      transition: color 0.15s ease;
    }

    .password-toggle:hover {
      color: #18181b;
    }

    .btn-login {
      width: 100%;
      padding: 0.75rem 1rem;
      background: #18181b;
      border: none;
      border-radius: 8px;
      font-size: 0.875rem;
      font-weight: 500;
      color: #ffffff;
      cursor: pointer;
      transition: all 0.15s ease;
      margin-top: 0.75rem;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .btn-login:hover {
      background: #27272a;
    }

    .btn-login:active {
      background: #3f3f46;
      transform: translateY(1px);
    }

    .footer-text {
      text-align: center;
      margin-top: 1.5rem;
      font-size: 0.75rem;
      color: #a1a1aa;
    }

    @media (max-width: 480px) {
      body {
        background: #ffffff;
      }

      .login-card {
        padding: 2rem 1.5rem;
        border: none;
        box-shadow: none;
      }

      .brand-title {
        font-size: 1.25rem;
      }

      .brand-logo {
        width: 48px;
        height: 48px;
      }

      .brand-logo svg {
        width: 24px;
        height: 24px;
      }
    }
  </style>
@endsection

@section('content')
  <div class="login-container">
    <div class="login-card">
      <!-- Brand Section -->
      <div class="brand-section">
        <div class="brand-logo">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
          </svg>
        </div>
        <h1 class="brand-title">{{ 'Laporan' }}</h1>
        <p class="brand-subtitle">{{ $brandSubtitle ?? 'Masuk ke dashboard Anda' }}</p>
      </div>

      <!-- Login Form -->
      <form id="formAuthentication" action="{{ route($loginActionRoute ?? 'login.create') }}" method="POST">
        @csrf

        <div class="form-group">
          <label class="form-label" for="email">Email</label>
          <input type="email" class="form-input" id="email" name="email" placeholder="nama@email.com" required autofocus>
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div class="password-wrapper">
            <input type="password" class="form-input" id="password" name="password" placeholder="********" required>
            <button type="button" class="password-toggle" onclick="togglePassword()">
              <!-- Eye Open Icon (show password) -->
              <svg id="eye-open" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              <!-- Eye Closed Icon (hide password) - hidden by default -->
              <svg id="eye-closed" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display: none;">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
              </svg>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-login">
          Masuk
        </button>
      </form>

      <p class="footer-text">
        {{  (date('Y') . ' PT. Jernih Multi Komunikasi') }}
      </p>
    </div>
  </div>

  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const eyeOpen = document.getElementById('eye-open');
      const eyeClosed = document.getElementById('eye-closed');

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeOpen.style.display = 'none';
        eyeClosed.style.display = 'block';
      } else {
        passwordInput.type = 'password';
        eyeOpen.style.display = 'block';
        eyeClosed.style.display = 'none';
      }
    }
  </script>
@endsection