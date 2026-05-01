<?php

namespace App\Http\Middleware;

use App\Models\Status;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (Auth::guard('customer')->check()) {
                $pelanggan = Auth::guard('customer')->user();
                if ($pelanggan) {
                    Status::updateOrCreate(
                        ['pelanggan_id' => $pelanggan->id],
                        [
                            'is_active' => true,
                            'logged_in_at' => now(),
                        ]
                    );

                    Log::info('[customer_status] updated', [
                        'nama' => $pelanggan->nama_lengkap ?? 'Unknown',
                        'email' => $pelanggan->email ?? null,
                        'timestamp' => now()->toDateTimeString(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::error('[customer_status] failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return $next($request);
    }
}
