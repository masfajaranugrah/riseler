<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Log;

class WebviewKwitansiController extends Controller
{
    /**
     * List semua invoice (yang lunas) untuk pelanggan yang login via token WebView.
     */
    public function index(Request $request)
    {
        $pelanggan = $this->resolvePelangganFromToken($request);

        $tagihans = Tagihan::with(['pelanggan', 'paket', 'rekening'])
            ->where('pelanggan_id', $pelanggan->id)
            ->where('status_pembayaran', 'lunas')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view('content.apps.Mobile.kwitansi.index', compact('tagihans'));
    }

    /**
     * Preview kwitansi di browser WebView.
     */
    public function preview(Request $request, $tagihanId)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        $pelanggan = $this->resolvePelangganFromToken($request);

        try {
            // Pastikan tagihan milik pelanggan yang sama
            $tagihan = Tagihan::where('pelanggan_id', $pelanggan->id)
                ->findOrFail($tagihanId);

            if (! $tagihan->kwitansi) {
                abort(404, 'Kwitansi belum tersedia untuk tagihan ini');
            }

            $relativePath = $tagihan->kwitansi;
            if (! str_starts_with($relativePath, 'kwitansi/')) {
                $relativePath = 'kwitansi/'.$relativePath;
            }

            $filePath = storage_path('app/public/'.$relativePath);

            if (! file_exists($filePath)) {
                Log::error('Kwitansi file not found', [
                    'tagihan_id'     => $tagihanId,
                    'kwitansi_field' => $tagihan->kwitansi,
                    'expected_path'  => $filePath,
                ]);
                abort(404, 'File kwitansi tidak ditemukan di server');
            }

            $mimeType = mime_content_type($filePath);
            $fileSize = filesize($filePath);

            Log::info('Preview kwitansi webview', [
                'pelanggan_id' => $pelanggan->id,
                'tagihan_id'   => $tagihanId,
                'file_size'    => $fileSize,
            ]);

            return response()->stream(function () use ($filePath) {
                if (ob_get_level()) {
                    ob_end_clean();
                }

                $stream = fopen($filePath, 'rb');
                if ($stream === false) {
                    throw new \Exception('Cannot open file stream');
                }

                fpassthru($stream);
                fclose($stream);
            }, 200, [
                'Content-Type'        => $mimeType,
                'Content-Length'      => $fileSize,
                'Content-Disposition' => 'inline; filename="'.basename($filePath).'"',
                'Cache-Control'       => 'public, must-revalidate, max-age=0',
                'Pragma'              => 'public',
                'Accept-Ranges'       => 'bytes',
                'X-Accel-Buffering'   => 'no',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Tagihan tidak ditemukan');
        } catch (\Exception $e) {
            Log::error('Error preview kwitansi webview', [
                'pelanggan_id' => $pelanggan ? $pelanggan->id : null,
                'tagihan_id'   => $tagihanId,
                'error'        => $e->getMessage(),
            ]);
            abort(500, 'Terjadi kesalahan saat memuat kwitansi');
        }
    }

    /**
     * Download kwitansi (aman untuk Flutter WebView).
     */
    public function download(Request $request, $tagihanId)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        $pelanggan = $this->resolvePelangganFromToken($request);

        try {
            $tagihan = Tagihan::with('pelanggan')
                ->where('pelanggan_id', $pelanggan->id)
                ->findOrFail($tagihanId);

            if (! $tagihan->kwitansi) {
                abort(404, 'Kwitansi belum tersedia');
            }

            $relativePath = $tagihan->kwitansi;
            if (! str_starts_with($relativePath, 'kwitansi/')) {
                $relativePath = 'kwitansi/'.$relativePath;
            }

            $filePath = storage_path('app/public/'.$relativePath);

            if (! file_exists($filePath)) {
                Log::error('File kwitansi tidak ditemukan', [
                    'pelanggan_id' => $pelanggan->id,
                    'tagihan_id'   => $tagihanId,
                    'path'         => $filePath,
                ]);
                abort(404, 'File kwitansi tidak ditemukan');
            }

            $customer = preg_replace(
                '/[^A-Za-z0-9\s_-]/',
                '',
                $tagihan->pelanggan->nama_lengkap ?? 'Customer'
            );
            $customer = str_replace(' ', '_', trim($customer));

            $ext      = pathinfo($filePath, PATHINFO_EXTENSION);
            $fileName = "Kwitansi_{$tagihan->nomer_id}_{$customer}.{$ext}";
            $fileSize = filesize($filePath);

            return response()->stream(function () use ($filePath) {
                if (ob_get_level()) {
                    ob_end_clean();
                }

                $stream = fopen($filePath, 'rb');
                fpassthru($stream);
                fclose($stream);
            }, 200, [
                'Content-Type'        => 'application/octet-stream',
                'Content-Length'      => $fileSize,
                'Content-Disposition' => $this->generateContentDisposition($fileName),
                'Cache-Control'       => 'no-store, no-cache, must-revalidate',
                'Pragma'              => 'no-cache',
                'Accept-Ranges'       => 'bytes',
                'X-Accel-Buffering'   => 'no',
            ]);

        } catch (\Exception $e) {
            Log::error('Error download kwitansi webview', [
                'pelanggan_id' => $pelanggan ? $pelanggan->id : null,
                'tagihan_id'   => $tagihanId,
                'error'        => $e->getMessage(),
            ]);

            abort(500, 'Gagal mengunduh kwitansi');
        }
    }

    /**
     * Resolve pelanggan dari guard customer atau dari query/header token.
     */
    private function resolvePelangganFromToken(Request $request)
    {
        // Kalau sudah ada user di guard customer (dari middleware), pakai itu
        $pelanggan = Auth::guard('customer')->user();
        if ($pelanggan) {
            return $pelanggan;
        }

        // Fallback: ambil token dari query ?token=... atau bearer
        $token = $request->query('token') ?: $request->bearerToken();

        if (! $token) {
            abort(401, 'Unauthorized. Token tidak ditemukan.');
        }

        $tokenModel = PersonalAccessToken::findToken($token);

        if (! $tokenModel || $tokenModel->tokenable_type !== 'App\Models\Pelanggan') {
            abort(401, 'Unauthorized. Token tidak valid.');
        }

        $pelanggan = $tokenModel->tokenable;

        // Set juga ke guard customer untuk konsistensi
        Auth::guard('customer')->setUser($pelanggan);

        return $pelanggan;
    }

    /**
     * RFC 6266 Content-Disposition (UTF-8 friendly).
     */
    private function generateContentDisposition($fileName)
    {
        $fileNameAscii = mb_convert_encoding($fileName, 'ASCII', 'UTF-8');
        $fileNameAscii = preg_replace('/[^\x20-\x7E]/', '_', $fileNameAscii);

        $fileNameUtf8 = rawurlencode($fileName);

        return sprintf(
            'attachment; filename="%s"; filename*=UTF-8\'\'%s',
            $fileNameAscii,
            $fileNameUtf8
        );
    }
}
