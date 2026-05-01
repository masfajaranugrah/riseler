<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Install Aplikasi</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            padding: 20px;
            max-width: 500px;
            margin: 0 auto;
        }
        .install-button {
            padding: 12px 20px;
            background: #4f46e5;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }
        .ios-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-top: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
        }
        .ios-banner h3 {
            margin: 0 0 15px 0;
            font-size: 20px;
        }
        .ios-banner p {
            margin: 10px 0;
            font-size: 16px;
            line-height: 1.6;
        }
        .ios-banner strong {
            background: rgba(255,255,255,0.2);
            padding: 2px 8px;
            border-radius: 4px;
        }
        .share-icon-big {
            font-size: 48px;
            margin: 15px 0;
            display: block;
        }
        .ios-instructions {
            background: #f3f4f6;
            padding: 20px;
            border-radius: 8px;
            margin-top: 15px;
        }
        .ios-instructions ol {
            margin: 10px 0;
            padding-left: 20px;
        }
        .ios-instructions li {
            margin: 8px 0;
        }
        .hidden {
            display: none;
        }
        .success {
            background: #d1fae5;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #10b981;
        }
        .step-by-step {
            background: white;
            border: 2px solid #667eea;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        .step {
            display: flex;
            align-items: center;
            margin: 12px 0;
            padding: 10px;
            background: #f9fafb;
            border-radius: 6px;
        }
        .step-number {
            background: #667eea;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 12px;
            flex-shrink: 0;
        }
        .step-text {
            flex: 1;
        }
    </style>
</head>
<body>
    <h2>Install Aplikasi</h2>
    <p>Pasang aplikasi ini di perangkat Anda untuk akses lebih cepat.</p>

    <!-- Tombol untuk Android Chrome/Edge -->
    <button id="installBtn" class="install-button hidden">
        ?? Install Aplikasi
    </button>

    <!-- Banner Peringatan untuk iPhone -->
    <div id="iphoneBanner" class="ios-banner hidden">
        <h3>?? Pengguna iPhone/iPad</h3>
        <p>Untuk memasang aplikasi ini:</p>
        <span class="share-icon-big">??</span>
        <p>Klik tombol <strong>Share</strong> di browser Anda,<br>lalu pilih <strong>"Add to Home Screen"</strong></p>
    </div>

    <!-- Instruksi Detail untuk iOS Safari -->
    <div id="iosSafariInstructions" class="step-by-step hidden">
        <h3>?? Cara Install di Safari:</h3>
        <div class="step">
            <div class="step-number">1</div>
            <div class="step-text">Tap tombol <strong>Share</strong> ?? di <strong>bawah layar</strong></div>
        </div>
        <div class="step">
            <div class="step-number">2</div>
            <div class="step-text">Scroll dan pilih <strong>"Add to Home Screen"</strong></div>
        </div>
        <div class="step">
            <div class="step-number">3</div>
            <div class="step-text">Tap <strong>"Add"</strong> di pojok kanan atas</div>
        </div>
        <p style="text-align: center; margin-top: 15px;">
            <small>?? Aplikasi akan muncul di home screen Anda</small>
        </p>
    </div>

    <!-- Instruksi Detail untuk iOS Chrome -->
    <div id="iosChromeInstructions" class="step-by-step hidden">
        <h3>?? Cara Install di Chrome:</h3>
        <div class="step">
            <div class="step-number">1</div>
            <div class="step-text">Tap tombol <strong>Share</strong> ?? di <strong>kanan atas</strong> (sebelah address bar)</div>
        </div>
        <div class="step">
            <div class="step-number">2</div>
            <div class="step-text">Pilih <strong>"Add to Home Screen"</strong></div>
        </div>
        <div class="step">
            <div class="step-number">3</div>
            <div class="step-text">Tap <strong>"Add"</strong> untuk konfirmasi</div>
        </div>
        <p style="text-align: center; margin-top: 15px;">
            <small>?? Aplikasi akan muncul di home screen Anda</small>
        </p>
    </div>

    <!-- Status sudah terinstall -->
    <div id="alreadyInstalled" class="success hidden">
        <p><strong>? Aplikasi sudah terinstall!</strong></p>
        <p>Anda sedang menggunakan aplikasi dalam mode standalone.</p>
    </div>

    <script>
        let deferredPrompt;

        // Deteksi platform dan browser
        function detectPlatform() {
            const ua = navigator.userAgent;
            
            // Deteksi iOS
            const isIOS = /iPad|iPhone|iPod/.test(ua) && !window.MSStream;
            
            // Deteksi Chrome di iOS (user agent mengandung "CriOS")
            const isChromeIOS = isIOS && /CriOS/.test(ua);
            
            // Deteksi Safari di iOS
            const isSafariIOS = isIOS && !isChromeIOS;
            
            // Deteksi Android
            const isAndroid = /Android/.test(ua);
            
            // Deteksi Chrome di Android
            const isChromeAndroid = isAndroid && /Chrome/.test(ua) && !/Edge/.test(ua);
            
            // Cek apakah sudah dalam mode standalone (sudah terinstall)
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches 
                || window.navigator.standalone 
                || document.referrer.includes('android-app://');

            return { 
                isIOS, 
                isChromeIOS, 
                isSafariIOS,
                isAndroid, 
                isChromeAndroid,
                isStandalone 
            };
        }

        // Inisialisasi
        function init() {
            const platform = detectPlatform();
            const installBtn = document.getElementById('installBtn');
            const iphoneBanner = document.getElementById('iphoneBanner');
            const iosSafariInstructions = document.getElementById('iosSafariInstructions');
            const iosChromeInstructions = document.getElementById('iosChromeInstructions');
            const alreadyInstalled = document.getElementById('alreadyInstalled');

            // Cek apakah sudah terinstall
            if (platform.isStandalone) {
                alreadyInstalled.classList.remove('hidden');
                return;
            }

            // iOS - tampilkan banner peringatan dan instruksi
            if (platform.isIOS) {
                // Tampilkan banner peringatan
                iphoneBanner.classList.remove('hidden');
                
                // Tampilkan instruksi detail sesuai browser
                if (platform.isChromeIOS) {
                    iosChromeInstructions.classList.remove('hidden');
                } else {
                    iosSafariInstructions.classList.remove('hidden');
                }
                return;
            }

            // Android - tunggu event beforeinstallprompt (hanya Chrome/Edge)
            // Tombol akan muncul otomatis saat event dipicu
        }

        // Event beforeinstallprompt (hanya Android Chrome/Edge)
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            
            const installBtn = document.getElementById('installBtn');
            installBtn.classList.remove('hidden');
        });

        // Klik tombol install (Android)
        document.getElementById('installBtn').addEventListener('click', async () => {
            if (!deferredPrompt) return;
            
            try {
                // Tampilkan prompt install
                deferredPrompt.prompt();
                
                // Tunggu respon user
                const { outcome } = await deferredPrompt.userChoice;
                
                console.log(`User ${outcome === 'accepted' ? 'menerima' : 'menolak'} install`);
                
                // Reset
                deferredPrompt = null;
                document.getElementById('installBtn').classList.add('hidden');
            } catch (error) {
                console.error('Error saat install:', error);
            }
        });

        // Event setelah berhasil install
        window.addEventListener('appinstalled', () => {
            console.log('PWA berhasil diinstall');
            document.getElementById('alreadyInstalled').classList.remove('hidden');
            document.getElementById('installBtn').classList.add('hidden');
        });

        // Jalankan saat halaman dimuat
        init();
    </script>
</body>
</html>
