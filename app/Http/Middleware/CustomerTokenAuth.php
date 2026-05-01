<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class CustomerTokenAuth
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Jika sudah login via guard 'customer', lanjut
        if (Auth::guard('customer')->check()) {
            return $next($request);
        }

        // 2. Ambil token dari query (?token=xxx) atau bearer
        $token = $request->query('token') ?: $request->bearerToken();

        // 3. Jika token ada, validasi via Sanctum
        if ($token) {
            $tokenModel = PersonalAccessToken::findToken($token);

            // Pastikan token milik model Pelanggan
            if ($tokenModel && $tokenModel->tokenable_type === \App\Models\Pelanggan::class) {
                $pelanggan = $tokenModel->tokenable;

                // Set user ke guard 'customer' untuk request ini
                Auth::guard('customer')->setUser($pelanggan);

                return $next($request);
            }
        }

        // 4. Jika token tidak ada / invalid -> 401 (tidak redirect)
        $responseBody = [
            'success' => false,
            'message' => 'Unauthorized. Token tidak valid atau tidak ditemukan.',
        ];

        // Kalau permintaan expect JSON (misal dari mobile app)
        if ($request->expectsJson()) {
            return response()->json($responseBody, 401);
        }

        // Untuk request biasa (webview HTML), tetap balas JSON 401
        return response()->json($responseBody, 401);
    }
}
