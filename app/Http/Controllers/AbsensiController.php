<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    protected $standardHours = 8.0;

      public function getAll(Request $request)
{
    $absensi = Absensi::with('user')
        ->orderBy('date', 'desc')
        ->orderBy('time_in', 'asc')
        ->get();

    return view('content.apps.Absensi.absensi-list', compact('absensi'));
}

    public function lemburIndex()
    {
        $user = Auth::user();

        $selectedMonth = (int) request('month', now()->month);
        $selectedYear = (int) request('year', now()->year);

        $attendances = Absensi::where('user_id', $user->id)
            ->whereYear('date', $selectedYear)
            ->whereMonth('date', $selectedMonth)
            ->whereNotNull('lembur_in')
            ->orderBy('date', 'desc')
            ->get();

        return view('content.apps.Absensi.lembur', compact('attendances', 'selectedMonth', 'selectedYear'));
    }

    public function index()
    {
        $user = Auth::user();

        $selectedMonth = (int) request('month', now()->month);
        $selectedYear = (int) request('year', now()->year);

        $attendances = Absensi::where('user_id', $user->id)
            ->whereYear('date', $selectedYear)
            ->whereMonth('date', $selectedMonth)
            ->orderBy('date', 'desc')
            ->get();

        return view('content.apps.Absensi.absensi', compact('attendances', 'selectedMonth', 'selectedYear'));
    }

    public function capture(Request $request)
    {
        $action = $request->query('action', 'checkin');
        $user = Auth::user();
        
        $title = 'Catat Jam Masuk';
        if ($action === 'checkout') $title = 'Catat Jam Pulang';
        if ($action === 'lembur_in') $title = 'Mulai Lembur';
        if ($action === 'lembur_out') $title = 'Selesai Lembur';

        return view('content.apps.Absensi.absensi-capture', compact('action', 'title', 'user'));
    }

    public function submit(Request $request)
    {
        $user = Auth::user();
        // gunakan WIB
        // $today = now()->setTimezone('Asia/Jakarta')->addDay()->toDateString();

        $today = now()->setTimezone('Asia/Jakarta')->toDateString();

        $request->validate([
            'action' => 'required|in:checkin,checkout,lembur_in,lembur_out',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $attendance = Absensi::firstOrNew([
            'user_id' => $user->id,
            'date' => $today,
        ]);

        // fungsi untuk menyimpan foto jika ada
        $savePhoto = function ($field) use ($request, $user) {
            if ($request->hasFile('photo')) {
                return $request->file('photo')->store("absensi/{$user->id}", 'public');
            }

            return null;
        };

        switch ($request->action) {
            case 'checkin':
                if ($attendance->time_in) {
                    return back()->with('error', 'Anda sudah melakukan check-in hari ini.');
                }
                $attendance->time_in = now()->setTimezone('Asia/Jakarta');
                $attendance->lat_in = $request->latitude;
                $attendance->lng_in = $request->longitude;
                $attendance->photo_in = $savePhoto('photo_in') ?? $attendance->photo_in;
                $attendance->save();

                return $this->attendanceResponse($request, 'Check-in berhasil.');

            case 'checkout':
                if (! $attendance->time_in) {
                    return back()->with('error', 'Belum melakukan check-in hari ini.');
                }
                if ($attendance->time_out) {
                    return back()->with('error', 'Anda sudah melakukan check-out hari ini.');
                }
                $attendance->time_out = now()->setTimezone('Asia/Jakarta');
                $attendance->lat_out = $request->latitude;
                $attendance->lng_out = $request->longitude;
                $attendance->photo_out = $savePhoto('photo_out') ?? $attendance->photo_out;

                [$total, $overtime] = $this->calculateHours($attendance->time_in, $attendance->time_out);
                $attendance->total_hours = $total;
                $attendance->overtime_hours = $overtime;
                $attendance->save();

                return $this->attendanceResponse($request, 'Check-out berhasil.');

            case 'lembur_in':
                if ($attendance->lembur_in) {
                    return back()->with('error', 'Anda sudah mulai lembur hari ini.');
                }
                $attendance->lembur_in = now()->setTimezone('Asia/Jakarta');
                $attendance->lat_lembur_in = $request->latitude;
                $attendance->lng_lembur_in = $request->longitude;
                $attendance->photo_lembur_in = $savePhoto('photo_lembur_in') ?? $attendance->photo_lembur_in;
                if ($request->has('note')) {
                    $attendance->note = $request->note;
                }
                $attendance->save();

                return $this->attendanceResponse($request, 'Lembur dimulai.');

            case 'lembur_out':
                if (! $attendance->lembur_in) {
                    return back()->with('error', 'Belum mulai lembur hari ini.');
                }
                if ($attendance->lembur_out) {
                    return back()->with('error', 'Anda sudah selesai lembur hari ini.');
                }
                $attendance->lembur_out = now()->setTimezone('Asia/Jakarta');
                $attendance->lat_lembur_out = $request->latitude;
                $attendance->lng_lembur_out = $request->longitude;
                $attendance->photo_lembur_out = $savePhoto('photo_lembur_out') ?? $attendance->photo_lembur_out;

                if ($request->has('note')) {
                    // append note or set it if empty
                    $attendance->note = $attendance->note ? $attendance->note . ' | ' . $request->note : $request->note;
                }

                [$total, $overtime] = $this->calculateHours($attendance->lembur_in, $attendance->lembur_out);
                $attendance->overtime_hours = $overtime;
                $attendance->save();

                return $this->attendanceResponse($request, 'Lembur selesai.');

            default:
                return back()->with('error', 'Aksi tidak valid.');
        }
    }

    protected function calculateHours($timeIn, $timeOut)
    {
        $in = Carbon::parse($timeIn);
        $out = Carbon::parse($timeOut);

        // Jika keluar lebih awal dari masuk (lewat tengah malam)
        if ($out->lt($in)) {
            $out->addDay();
        }

        $totalMinutes = $out->diffInMinutes($in);
        $totalHours = round($totalMinutes / 60, 2);

        // lembur = total - 8 jam
        $overtimeMinutes = max(0, $totalMinutes - ($this->standardHours * 60));

        // Kembalikan total jam dan lembur dalam menit
        return [$totalMinutes, $overtimeMinutes];
    }

    protected function attendanceResponse(Request $request, string $message)
    {
        if ($request->boolean('redirect_to_home')) {
            return redirect()->route('karyawan.home')->with('success', $message);
        }

        return back()->with('success', $message);
    }

    public function destroy(Absensi $absensi)
    {
        $user = Auth::user();
        if (!$user || $absensi->user_id !== $user->id) {
            abort(403);
        }

        $photos = array_filter([
            $absensi->photo_in,
            $absensi->photo_out,
            $absensi->photo_lembur_in,
            $absensi->photo_lembur_out,
        ]);

        foreach ($photos as $photoPath) {
            Storage::disk('public')->delete($photoPath);
        }

        $absensi->delete();

        return back()->with('success', 'Data absensi dan semua fotonya berhasil dihapus.');
    }
}
