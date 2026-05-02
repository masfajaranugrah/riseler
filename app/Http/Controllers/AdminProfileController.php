<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('content.apps.Admin.profile.index', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'new_password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
                'confirmed',
            ],
        ], [
            'new_password.min' => 'Password minimal 8 karakter.',
            'new_password.regex' => 'Password harus mengandung huruf besar, angka, dan simbol.',
            'new_password.confirmed' => 'Verifikasi password baru tidak sama.',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diperbarui. Silakan login ulang.',
            'redirect' => route('login'),
        ]);
    }
}
