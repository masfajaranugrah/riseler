<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Employee;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class KaryawanHomeController extends Controller
{
    public function index()
    {
        Carbon::setLocale('id');

        $user = Auth::user();
        $employee = Employee::where('full_name', $user?->name)->first();
        $today = now()->setTimezone('Asia/Jakarta')->toDateString();
        $todayAttendance = Absensi::where('user_id', $user?->id)
            ->whereDate('date', $today)
            ->first();

        $jabatan = $employee?->jabatan ?: $this->formatRoleLabel($user?->role);

        return view('content.apps.Karyawan.home.index', [
            'userName' => $user?->name ?? 'Staff',
            'jabatan' => $jabatan,
            'hariIni' => now()->translatedFormat('l'),
            'tanggalHariIni' => now()->translatedFormat('j F Y'),
            'jamKerja' => '07.45 - 16.00 WIB',
            'timeIn' => $todayAttendance?->time_in?->setTimezone('Asia/Jakarta')->format('H:i:s') ?? '--:--:--',
            'timeOut' => $todayAttendance?->time_out?->setTimezone('Asia/Jakarta')->format('H:i:s') ?? '--:--:--',
        ]);
    }

    private function formatRoleLabel(?string $role): string
    {
        if (!$role) {
            return 'Karyawan';
        }

        return ucwords(str_replace('_', ' ', strtolower($role)));
    }
}
