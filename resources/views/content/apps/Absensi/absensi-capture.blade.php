@php
  $userName = $user->name ?? 'Karyawan';
  $userNik = $user->nik ?? 'ID-'.$user->id; 
  $jamSekarang = now()->format('H:i:s');
  
  $btnText = 'Catat Jam Masuk';
  if ($action === 'checkout') $btnText = 'Catat Jam Pulang';
  if ($action === 'lembur_in') $btnText = 'Mulai Lembur';
  if ($action === 'lembur_out') $btnText = 'Selesai Lembur';
@endphp

@extends('layouts/blankLayout')

@section('title', $title)

@section('page-style')
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  
  body {
    background: #000;
    margin: 0;
    font-family: 'Inter', 'Nunito', sans-serif;
    overflow: hidden;
    height: 100vh;
    height: 100dvh;
  }

  /* ─── CAMERA VIEW (fullscreen) ──────────────────── */
  #camera-view {
    position: fixed;
    inset: 0;
    background: #000;
    display: flex;
    flex-direction: column;
    z-index: 100;
  }

  #video-preview {
    width: 100%;
    flex: 1;
    object-fit: cover;
    display: block;
    background: #000;
  }

  .cam-header {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    padding-top: calc(16px + env(safe-area-inset-top, 0px));
    background: linear-gradient(180deg, rgba(0,0,0,0.6) 0%, transparent 100%);
    z-index: 10;
  }

  .cam-header-title {
    font-weight: 700;
    color: #fff;
    font-size: 1rem;
    text-shadow: 0 1px 4px rgba(0,0,0,0.4);
  }

  .cam-close-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    background: rgba(255,255,255,0.18);
    backdrop-filter: blur(8px);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    text-decoration: none;
    transition: background 0.2s;
  }

  .cam-close-btn:hover { background: rgba(255,255,255,0.3); color: #fff; }

  .cam-bottom {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 32px;
    padding-bottom: calc(32px + env(safe-area-inset-bottom, 0px));
    background: linear-gradient(0deg, rgba(0,0,0,0.7) 0%, transparent 100%);
    z-index: 10;
  }

  .shutter-btn {
    width: 76px;
    height: 76px;
    border-radius: 50%;
    background: #fff;
    border: 5px solid rgba(255,255,255,0.4);
    box-shadow: 0 0 0 5px rgba(255,255,255,0.2), 0 8px 28px rgba(0,0,0,0.5);
    cursor: pointer;
    position: relative;
    transition: transform 0.15s;
    flex-shrink: 0;
  }

  .shutter-btn::before {
    content: '';
    position: absolute;
    inset: 5px;
    border-radius: 50%;
    background: #fff;
    transition: all 0.15s;
  }

  .shutter-btn:active { transform: scale(0.9); }
  .shutter-btn:active::before { inset: 10px; }

  /* ─── REVIEW VIEW (fullscreen, scrollable) ──────── */
  #review-view {
    position: fixed;
    inset: 0;
    background: #fff;
    display: none;
    flex-direction: column;
    z-index: 100;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
  }

  .review-header {
    display: flex;
    align-items: center;
    padding: 14px 20px;
    padding-top: calc(14px + env(safe-area-inset-top, 0px));
    background: #fff;
    border-bottom: 1px solid #E5E7EB;
    position: sticky;
    top: 0;
    z-index: 10;
  }

  .review-header a {
    font-size: 22px;
    color: #111827;
    text-decoration: none;
    margin-right: 14px;
  }

  .review-header h1 {
    font-size: 16px;
    font-weight: 700;
    color: #111827;
    margin: 0;
  }

  .review-body {
    flex: 1;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .review-user {
    text-align: center;
    margin-bottom: 16px;
  }

  .review-user-name {
    font-weight: 800;
    font-size: 16px;
    color: #111827;
  }

  .review-user-nik {
    color: #6B7280;
    font-size: 13px;
    margin-top: 2px;
  }

  .review-photo {
    width: 160px;
    height: 210px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    margin-bottom: 20px;
  }

  .review-time {
    text-align: center;
    margin-bottom: 16px;
  }

  .review-time-label {
    color: #6B7280;
    font-size: 13px;
    margin-bottom: 6px;
  }

  .review-time-value {
    font-size: 28px;
    font-weight: 800;
    color: #111827;
  }

  .review-coords {
    background: #EEF2FF;
    border-radius: 10px;
    padding: 12px 16px;
    text-align: center;
    font-size: 12px;
    color: #4338CA;
    font-family: 'JetBrains Mono', monospace;
    width: 100%;
    max-width: 340px;
    margin-bottom: 8px;
  }

  .review-coords a {
    color: #4338CA;
    text-decoration: underline;
  }

  .review-status {
    text-align: center;
    font-size: 12px;
    color: #6B7280;
    margin-bottom: 6px;
  }

  .review-refresh {
    text-align: center;
    font-size: 12px;
    margin-bottom: 20px;
  }

  .review-refresh a {
    color: #0D6EFD;
    font-weight: 600;
    text-decoration: none;
  }

  /* Fixed bottom submit */
  .review-submit {
    padding: 16px 20px;
    padding-bottom: calc(16px + env(safe-area-inset-bottom, 0px));
    background: #fff;
    border-top: 1px solid #E5E7EB;
    position: sticky;
    bottom: 0;
    z-index: 10;
  }

  .btn-submit {
    width: 100%;
    background: #0D6EFD;
    color: white;
    border: none;
    padding: 14px;
    border-radius: 99px;
    font-weight: 600;
    font-size: 15px;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: background 0.2s;
  }

  .btn-submit:hover { background: #0b5ed7; }
  .btn-submit:disabled { opacity: 0.5; cursor: not-allowed; }
</style>
@endsection

@section('content')

{{-- ═══ CAMERA VIEW ═══ --}}
<div id="camera-view">
  <video id="video-preview" autoplay playsinline muted></video>
  
  <div class="cam-header">
    <span class="cam-header-title">{{ $title }}</span>
    <a href="{{ route('karyawan.home') }}" class="cam-close-btn"><i class="ri-close-line"></i></a>
  </div>

  <div class="cam-bottom">
    <div class="shutter-btn" id="btn-capture"></div>
  </div>
</div>

{{-- ═══ REVIEW VIEW ═══ --}}
<div id="review-view">
  <div class="review-header">
    <a href="#" id="btn-retake"><i class="ri-arrow-left-line"></i></a>
    <h1>{{ $title }}</h1>
  </div>

  <div class="review-body">
    <div class="review-user">
      <div class="review-user-name">{{ $userName }}</div>
      <div class="review-user-nik">{{ $userNik }}</div>
    </div>

    <img id="photo-result" src="" alt="Foto" class="review-photo">

    <div class="review-time">
      <div class="review-time-label">Jam {{ str_replace('Catat Jam ', '', $title) }}</div>
      <div class="review-time-value" id="current-time">{{ $jamSekarang }}</div>
    </div>

    <div class="review-coords" id="location-coords" style="display: none;">
      📍 <span id="coords-text"></span>
      <br>
      <a href="#" id="gmaps-link" target="_blank">🗺️ Lihat di Google Maps</a>
    </div>
    <div class="review-status" id="location-status">Mendapatkan lokasi...</div>
    <div class="review-refresh">
      <a href="#" id="btn-refresh-loc">🔄 Perbaharui lokasi</a>
    </div>
  </div>

  <form id="attendance-form" action="{{ route('absensi.kirim') }}" method="POST" enctype="multipart/form-data" class="review-submit">
    @csrf
    <input type="hidden" name="action" value="{{ $action }}">
    <input type="hidden" name="redirect_to_home" value="1">
    <input type="hidden" name="latitude" id="lat-input">
    <input type="hidden" name="longitude" id="lng-input">
    <input type="file" name="photo" id="photo-input" style="display: none;">

    <button type="submit" class="btn-submit" id="btn-submit" disabled>
      <i class="ri-login-box-line"></i> {{ $btnText }}
    </button>
  </form>
</div>

<script>
  const video = document.getElementById('video-preview');
  const photoResult = document.getElementById('photo-result');
  const cameraView = document.getElementById('camera-view');
  const reviewView = document.getElementById('review-view');
  const btnCapture = document.getElementById('btn-capture');
  const btnRetake = document.getElementById('btn-retake');
  const btnSubmit = document.getElementById('btn-submit');
  const photoInput = document.getElementById('photo-input');
  const latInput = document.getElementById('lat-input');
  const lngInput = document.getElementById('lng-input');
  const locationCoords = document.getElementById('location-coords');
  const coordsText = document.getElementById('coords-text');
  const gmapsLink = document.getElementById('gmaps-link');
  const locationStatus = document.getElementById('location-status');
  const btnRefreshLoc = document.getElementById('btn-refresh-loc');
  
  let stream = null;
  let watchId = null;

  // ─── Camera ───────────────────────────────────────
  async function startCamera() {
    try {
      stream = await navigator.mediaDevices.getUserMedia({ 
        video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } }, 
        audio: false 
      });
      video.srcObject = stream;
    } catch (err) {
      alert("Tidak dapat mengakses kamera. Pastikan Anda memberikan izin.");
    }
  }

  function stopCamera() {
    if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
  }

  // Capture
  btnCapture.addEventListener('click', () => {
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
    
    const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
    photoResult.src = dataUrl;
    
    fetch(dataUrl).then(r => r.blob()).then(blob => {
      const dt = new DataTransfer();
      dt.items.add(new File([blob], "attendance.jpg", { type: "image/jpeg" }));
      photoInput.files = dt.files;
    });

    stopCamera();
    cameraView.style.display = 'none';
    reviewView.style.display = 'flex';
    document.getElementById('current-time').innerText = new Date().toLocaleTimeString('id-ID', { hour12: false });
  });

  // Retake — kembali ke kamera
  btnRetake.addEventListener('click', (e) => {
    e.preventDefault();
    reviewView.style.display = 'none';
    cameraView.style.display = 'flex';
    startCamera();
  });

  // ─── GPS ──────────────────────────────────────────
  function setLocation(lat, lng) {
    latInput.value = lat;
    lngInput.value = lng;
    locationCoords.style.display = 'block';
    coordsText.innerText = `${lat.toFixed(7)}, ${lng.toFixed(7)}`;
    gmapsLink.href = `https://www.google.com/maps?q=${lat.toFixed(7)},${lng.toFixed(7)}&z=20`;
    locationStatus.innerText = '✓ Lokasi berhasil didapat';
    locationStatus.style.color = '#065F46';
    btnSubmit.disabled = false;
  }

  function grabLocation() {
    locationStatus.innerText = 'Mendapatkan lokasi...';
    locationStatus.style.color = '';
    locationCoords.style.display = 'none';
    if (watchId) navigator.geolocation.clearWatch(watchId);

    if (!navigator.geolocation) {
      locationStatus.innerText = 'GPS tidak tersedia.';
      btnSubmit.disabled = false;
      return;
    }

    let bestAcc = Infinity;

    navigator.geolocation.getCurrentPosition(
      (pos) => {
        const { latitude: lat, longitude: lng, accuracy: acc } = pos.coords;
        bestAcc = acc;
        setLocation(lat, lng);

        watchId = navigator.geolocation.watchPosition(
          (p) => {
            if (p.coords.accuracy < bestAcc) {
              bestAcc = p.coords.accuracy;
              setLocation(p.coords.latitude, p.coords.longitude);
            }
            if (bestAcc <= 5) {
              navigator.geolocation.clearWatch(watchId);
              watchId = null;
            }
          },
          () => {},
          { enableHighAccuracy: true, maximumAge: 0, timeout: 15000 }
        );

        setTimeout(() => {
          if (watchId) { navigator.geolocation.clearWatch(watchId); watchId = null; }
        }, 15000);
      },
      (err) => {
        locationStatus.innerText = 'Gagal mendapatkan lokasi. Pastikan GPS aktif.';
        locationStatus.style.color = '#991B1B';
        btnSubmit.disabled = false;
      },
      { enableHighAccuracy: true, maximumAge: 0, timeout: 8000 }
    );
  }

  btnRefreshLoc.addEventListener('click', (e) => { e.preventDefault(); grabLocation(); });

  // ─── Start ────────────────────────────────────────
  startCamera();
  grabLocation();

  window.addEventListener('beforeunload', () => {
    if (watchId) navigator.geolocation.clearWatch(watchId);
    stopCamera();
  });
</script>
@endsection
