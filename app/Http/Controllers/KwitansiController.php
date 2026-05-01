<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class KwitansiController extends Controller
{
    /**
     * Preview kwitansi di browser
     */
    public function preview($tagihanId)
    {
        // Tingkatkan memory & execution time
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        try {
            // 1. Ambil data tagihan
            $tagihan = Tagihan::findOrFail($tagihanId);
            $this->authorizeKwitansiAccess($tagihan);

            // 2. Validasi apakah kwitansi ada
            if (!$tagihan->kwitansi) {
                abort(404, 'Kwitansi belum tersedia untuk tagihan ini');
            }

            [$filePath, $mimeType, $fileSize] = $this->resolveKwitansiFile($tagihan, $tagihanId);

            // 6. Log untuk debugging
            Log::info('Preview kwitansi', [
                'tagihan_id' => $tagihanId,
                'file_size' => $fileSize,
                'memory_usage' => memory_get_usage(true),
                'memory_limit' => ini_get('memory_limit')
            ]);

            // 7. Return dengan streaming response untuk file besar
            return response()->stream(function() use ($filePath) {
                $stream = fopen($filePath, 'rb');

                if ($stream === false) {
                    throw new \Exception('Cannot open file stream');
                }

                // Disable output buffering
                if (ob_get_level()) {
                    ob_end_clean();
                }

                // Stream file menggunakan fpassthru (lebih efisien)
                fpassthru($stream);
                fclose($stream);

            }, 200, [
                'Content-Type' => $mimeType,
                'Content-Length' => $fileSize,
                'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
                'Cache-Control' => 'public, must-revalidate, max-age=0',
                'Pragma' => 'public',
                'Accept-Ranges' => 'bytes',
                'X-Accel-Buffering' => 'no' // Disable nginx buffering
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Tagihan tidak ditemukan');
        } catch (\Exception $e) {
            Log::error('Error preview kwitansi', [
                'tagihan_id' => $tagihanId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Terjadi kesalahan saat memuat kwitansi: ' . $e->getMessage());
        }
    }

    /**
     * Download kwitansi - Optimized untuk Android & iPhone
     */
    public function download($tagihanId)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        try {
            // 1. Ambil data
            $tagihan = Tagihan::with('pelanggan')->findOrFail($tagihanId);
            $this->authorizeKwitansiAccess($tagihan);

            if (!$tagihan->kwitansi) {
                abort(404, 'Kwitansi belum tersedia');
            }

            [$filePath, $mimeType, $fileSize] = $this->resolveKwitansiFile($tagihan, $tagihanId);

            // 4. Nama file
            $customer = preg_replace(
                '/[^A-Za-z0-9\s_-]/',
                '',
                $tagihan->pelanggan->nama_lengkap ?? 'Customer'
            );
            $customer = str_replace(' ', '_', trim($customer));

            $ext = pathinfo($filePath, PATHINFO_EXTENSION);
            $fileName = "Kwitansi_{$tagihan->nomer_id}_{$customer}.{$ext}";

            // 5. Streaming download
            return response()->stream(function () use ($filePath) {
                if (ob_get_level()) {
                    ob_end_clean();
                }

                $stream = fopen($filePath, 'rb');
                fpassthru($stream);
                fclose($stream);
            }, 200, [
                'Content-Type' => $mimeType ?: 'application/octet-stream',
                'Content-Length' => $fileSize,
                'Content-Disposition' => $this->generateContentDisposition($fileName),
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
                'Pragma' => 'no-cache',
                'Accept-Ranges' => 'bytes',
                'X-Accel-Buffering' => 'no',
            ]);
        } catch (\Exception $e) {
            Log::error('Error download kwitansi', [
                'tagihan_id' => $tagihanId,
                'error' => $e->getMessage(),
            ]);

            abort(500, 'Gagal mengunduh kwitansi');
        }
    }

    /**
     * Halaman verifikasi anti-pemalsuan kwitansi.
     * URL valid harus memiliki signature route + kode verifikasi yang cocok.
     */
    public function verify(Request $request, $tagihanId)
    {
        $tagihan = Tagihan::with(['pelanggan', 'paket'])->find($tagihanId);
        $providedCode = strtoupper(trim((string) $request->query('code', '')));
        $hasValidSignature = URL::hasValidSignature($request);

        $expectedCode = $tagihan ? $this->generateVerificationCode($tagihan) : null;

        $isValid = $tagihan
            && $tagihan->kwitansi
            && $hasValidSignature
            && $providedCode !== ''
            && $expectedCode !== null
            && hash_equals($expectedCode, $providedCode);

        return view('content.apps.Customer.tagihan.kwitansi-verify', [
            'tagihan' => $tagihan,
            'isValid' => $isValid,
            'providedCode' => $providedCode,
            'expectedCode' => $expectedCode,
            'hasValidSignature' => $hasValidSignature,
        ]);
    }


    /**
     * Generate Content-Disposition header dengan RFC 6266 (UTF-8 support)
     * untuk kompatibilitas Android & iPhone
     */
    private function generateContentDisposition($fileName)
    {
        // ASCII fallback untuk browser lama
        $fileNameAscii = mb_convert_encoding($fileName, 'ASCII', 'UTF-8');
        $fileNameAscii = preg_replace('/[^\x20-\x7E]/', '_', $fileNameAscii);

        // UTF-8 encoded filename untuk browser modern (RFC 6266/8187)
        $fileNameUtf8 = rawurlencode($fileName);

        // Format: attachment; filename="ascii-fallback.pdf"; filename*=UTF-8''utf8-encoded.pdf
        return sprintf(
            'attachment; filename="%s"; filename*=UTF-8\'\'%s',
            $fileNameAscii,
            $fileNameUtf8
        );
    }

    /**
     * Generate kode verifikasi anti-pemalsuan dari data penting tagihan.
     */
    private function generateVerificationCode(Tagihan $tagihan): string
    {
        $paymentDate = $tagihan->tanggal_pembayaran
            ? Carbon::parse($tagihan->tanggal_pembayaran)->format('Y-m-d H:i:s')
            : '-';

        $payload = implode('|', [
            (string) $tagihan->id,
            (string) ($tagihan->nomer_id ?? ''),
            (string) ($tagihan->pelanggan_id ?? ''),
            $paymentDate,
            (string) ($tagihan->status_pembayaran ?? ''),
            (string) ($tagihan->kwitansi ?? ''),
        ]);

        $secret = (string) config('app.key');
        return strtoupper(substr(hash_hmac('sha256', $payload, $secret), 0, 16));
    }

    /**
     * Pastikan yang akses kwitansi adalah pemilik tagihan atau admin internal.
     */
    private function authorizeKwitansiAccess(Tagihan $tagihan): void
    {
        $webUser = Auth::guard('web')->user();
        if ($webUser && isset($webUser->role) && in_array($webUser->role, ['administrator', 'admin', 'customer_service'], true)) {
            return;
        }

        $customer = Auth::guard('customer')->user();
        if ($customer && (int) $customer->id === (int) $tagihan->pelanggan_id) {
            return;
        }

        abort(403, 'Anda tidak memiliki akses ke kwitansi ini.');
    }

    /**
     * Resolve path, mime dan ukuran file kwitansi.
     */
    private function resolveKwitansiFile(Tagihan $tagihan, $tagihanId): array
    {
        $relativePath = $tagihan->kwitansi;
        if (!str_starts_with($relativePath, 'kwitansi/')) {
            $relativePath = 'kwitansi/' . $relativePath;
        }

        $filePath = storage_path('app/public/' . $relativePath);
        if (!file_exists($filePath)) {
            Log::error('Kwitansi file not found', [
                'tagihan_id' => $tagihanId,
                'kwitansi_field' => $tagihan->kwitansi,
                'expected_path' => $filePath,
            ]);
            abort(404, 'File kwitansi tidak ditemukan di server');
        }

        return [
            $filePath,
            mime_content_type($filePath),
            filesize($filePath),
        ];
    }
}
