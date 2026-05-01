<?php
// app/Http/Controllers/IklanController.php

namespace App\Http\Controllers;

use App\Models\Iklan;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Jobs\SendIklanJob;

class IklanController extends Controller
{
    public function index()
    {
        $iklans = Iklan::with('creator')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('content.apps.Iklan.iklan', compact('iklans'));
    }

    public function create()
    {
        return view('content.apps.Iklan.add-iklan');
    }

public function store(Request $request)
{
    try {
        Log::info('Iklan store request', [
            'user_id' => Auth::id(),
            'data' => $request->except('image')
        ]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:1000',
            'type' => 'required|in:informasi,maintenance,iklan',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            Log::info('Uploading image');
            $imagePath = $request->file('image')->store('iklan', 'public');
            Log::info('Image uploaded', ['path' => $imagePath]);
        }

        $iklanData = [
            'id' => (string) Str::uuid(),
            'title' => $validated['title'],
            'message' => $validated['message'],
            'type' => $validated['type'],
            'image' => $imagePath,
            'status' => 'active',
            'total_sent' => 0,
            'created_by' => Auth::id()
        ];

        Log::info('Creating iklan', $iklanData);
        $iklan = Iklan::create($iklanData);
        Log::info('Iklan created successfully', ['iklan_id' => $iklan->id]);

        // ? Kirim push notification setelah iklan dibuat (via Queue / OneSignal)
        SendIklanJob::dispatch($iklan->id);

        return redirect()->route('iklan.index')
            ->with('success', 'Iklan berhasil dibuat dan notifikasi sedang dikirim!');

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation error', ['errors' => $e->errors()]);
        return redirect()->back()->withErrors($e->errors())->withInput();

    } catch (\Exception $e) {
        Log::error('Error creating iklan', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
        return redirect()->back()
            ->with('error', 'Gagal membuat iklan: ' . $e->getMessage())
            ->withInput();
    }
}

// ? Method untuk kirim push notification via WebPushr
private function sendPushNotification($iklan)
{
    try {
        $pelanggans = Pelanggan::whereNotNull('webpushr_sid')
            ->where('webpushr_sid', '!=', '')
            ->get();

        if ($pelanggans->isEmpty()) {
            Log::info('Tidak ada pelanggan dengan webpushr_sid');
            return;
        }

        $sentCount = 0;
        $failedCount = 0;

        foreach ($pelanggans as $pelanggan) {
            try {
                $result = $this->sendWebpushrNotification([
                    'title' => $iklan->title,
                    'message' => $iklan->message,
                    'target_url' => url('https://layanan.jernih.net.id/dashboard/customer/tagihan/home'),
                    'sid' => $pelanggan->webpushr_sid,
                ]);

                if ($result['success']) {
                    $sentCount++;
                } else {
                    $failedCount++;
                }

            } catch (\Exception $e) {
                $failedCount++;
                Log::error('Error sending to ' . $pelanggan->nama_lengkap, ['error' => $e->getMessage()]);
                continue;
            }
        }

        // Update total sent
        $iklan->update(['total_sent' => $sentCount]);

        Log::info('Push notification summary', [
            'sent' => $sentCount,
            'failed' => $failedCount,
            'total' => $pelanggans->count()
        ]);

    } catch (\Exception $e) {
        Log::error('Push notification error: ' . $e->getMessage());
    }
}

// ? Copy method dari PushNotificationController
private function sendWebpushrNotification($data)
{
    try {
        $ch = curl_init('https://api.webpushr.com/v1/notification/send/sid');

        $payload = [
            'title' => $data['title'] ?? 'Notifikasi',
            'message' => $data['message'] ?? '',
            'target_url' => $data['target_url'] ?? url('/'),
            'sid' => $data['sid'],
        ];

        $headers = [
            'Content-Type: application/json',
            'webpushrKey: ' . env('WEBPUSHR_KEY', '2ee12b373a17d9ba5f44683cb42d4279'),
            'webpushrAuthToken: ' . env('WEBPUSHR_TOKEN', '116294'),
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($httpCode == 200 && !empty($response)) {
            return ['success' => true, 'response' => $responseData];
        } else {
            return ['success' => false, 'error' => $curlError ?: 'HTTP Code: ' . $httpCode];
        }

    } catch (\Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}


public function update(Request $request, $id)
{
    try {
        $iklan = Iklan::findOrFail($id);

        // ? Validasi dengan type
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:informasi,maintenance,iklan', // ? Tambah validasi type
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            if ($iklan->image && Storage::disk('public')->exists($iklan->image)) {
                Storage::disk('public')->delete($iklan->image);
            }
            $validated['image'] = $request->file('image')->store('iklan', 'public');
        }

        $iklan->update($validated);

        return redirect()->route('iklan.index')
            ->with('success', 'Iklan berhasil diupdate!');

    } catch (\Exception $e) {
        Log::error('Error updating iklan', ['message' => $e->getMessage()]);

        return redirect()->back()
            ->with('error', 'Gagal update iklan: ' . $e->getMessage())
            ->withInput();
    }
}
    public function send($id)
    {
        try {
            $iklan = Iklan::find($id);

            if (!$iklan) {
                return response()->json([
                    'success' => false,
                    'queued' => false,
                    'message' => 'Iklan tidak ditemukan'
                ], 404);
            }

            // Tandai status sebagai queued (opsional, abaikan jika kolom tidak ada)
            $iklan->update(['status' => 'queued']);

            // Dorong ke queue agar berjalan di background
            SendIklanJob::dispatch($iklan->id);

            return response()->json([
                'success' => true,
                'queued' => true,
                'message' => 'Iklan sedang dikirim di background melalui queue'
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending iklan', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'queued' => false,
                'message' => 'Gagal mengirim iklan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $iklan = Iklan::findOrFail($id);
            $iklan->delete();

            return redirect()->route('iklan.index')
                ->with('success', 'Iklan berhasil dihapus!');

        } catch (\Exception $e) {
            Log::error('Error deleting iklan', ['message' => $e->getMessage()]);

            return redirect()->back()
                ->with('error', 'Gagal menghapus iklan: ' . $e->getMessage());
        }
    }
}
