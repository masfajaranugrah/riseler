<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <link rel="manifest" href="/manifest.json">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Install Aplikasi</title>
</head>
<body style="font-family: sans-serif; padding: 20px;">

    <h2>Install Aplikasi</h2>
    <p>Silakan pilih cara pemasangan sesuai browser Anda.</p>

    <!-- Tombol Install Aplikasi (hanya muncul di Chrome/Edge) -->
    <button id="installBtn"
            style="padding: 12px 20px; background: #4f46e5; color: white; 
            border: none; border-radius: 8px; display:none;">
        Install Aplikasi
    </button>

    <!-- Tombol buka Chrome (untuk Vivo/Xiaomi/Oppo) -->
    <button id="openChromeBtn"
            style="padding: 12px 20px; background: #ef4444; color: white;
            border: none; border-radius: 8px; display:none;">
        Buka di Google Chrome
    </button>

    <script>
    let deferredPrompt;

    const ua = navigator.userAgent;

    // DETEKSI CHROME ASLI (Android Chrome resmi)
    const isRealChrome =
        ua.includes("Chrome") &&
        !ua.includes("Edg") &&
        !ua.includes("OPR") &&
        !ua.includes("VivoBrowser") &&
        !ua.includes("MiuiBrowser") &&
        !ua.includes("HeyTapBrowser") &&
        !ua.includes("OPPOBrowser") &&
        !ua.includes("SamsungBrowser") &&
        !ua.includes("HuaweiBrowser");

    const isEdge = ua.includes("Edg");

    // ======== EVENT PWA (hanya Chrome/Edge) ========
    window.addEventListener("beforeinstallprompt", (e) => {
        e.preventDefault();
        deferredPrompt = e;

        if (isRealChrome || isEdge) {
            document.getElementById("installBtn").style.display = "block";
        }
    });

    // ======== TOMBOL INSTALL ========
    document.getElementById("installBtn").addEventListener("click", async () => {
        if (!deferredPrompt) return;
        deferredPrompt.prompt();
        await deferredPrompt.userChoice;
        deferredPrompt = null;
        document.getElementById("installBtn").style.display = "none";
    });

    // ======== TOMBOL BUKA DI GOOGLE CHROME ========
    document.getElementById("openChromeBtn").addEventListener("click", () => {
        const url = "layanan.jernih.net.id/install";
        const intentURL = 
            "intent://" + url + 
            "#Intent;scheme=https;package=com.android.chrome;end";
        window.location.href = intentURL;
    });

    // ======== TAMPILKAN TOMBOL SESUAI BROWSER ========
    setTimeout(() => {
        if (isRealChrome || isEdge) {
            // Sudah di Chrome ? hide tombol buka Chrome
            document.getElementById("openChromeBtn").style.display = "none";
        } else {
            // Browser bawaan ? tampilkan tombol buka Chrome
            document.getElementById("openChromeBtn").style.display = "block";
        }
    }, 500);
</script>

</body>
</html>
