<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SessionCheck
{
    public function handle($request, Closure $next)
    {
        // 1. Jika user sudah login dan akses halaman login → redirect dashboard
        if ($request->routeIs('login') && Auth::check()) {
            // pastikan ini bukan route dashboard untuk menghindari loop
            return redirect('/');
        }

        // 2. Jika user akses dashboard tetapi belum login → redirect login
        if ($request->is('dashboard/*') && ! Auth::check()) {
            return redirect()->route('user.member');
        }
        // Kalau sudah login dan akses halaman login -> lempar ke dashboard tagihan
        if ($request->routeIs(['login.member', 'login.member.post']) && Auth::guard('customer')->check()) {
            return redirect('https://layanan.jernih.net.id/dashboard/customer/tagihan/home');
        }

        // Kalau akses dashboard customer tapi belum login -> balik ke halaman login
        if ($request->is('dashboard/customer/*') && ! Auth::guard('customer')->check()) {
            return redirect()->route('login.member');
        }


        // 3. Kalau user login dan akses dashboard → biarkan
        return $next($request);
    }
}
