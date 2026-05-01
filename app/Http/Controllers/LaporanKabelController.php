<?php

namespace App\Http\Controllers;

use App\Models\LaporanKabel;
use App\Models\Employee;
use App\Exports\LaporanKabelExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class LaporanKabelController extends Controller
{
    private const PER_PAGE = 40;

    /**
     * Build the base query with shared filters.
     */
    private function applyFilters(Request $request): array
    {
        $filters = $request->validate([
            'date' => 'nullable|date',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2020|max:2099',
            'wilayah' => 'nullable|in:Klaten,Gunung Kidul,Boyolali',
            'search' => 'nullable|string|max:255',
        ]);

        $query = LaporanKabel::with('employee:id,full_name');
        $date = $filters['date'] ?? null;
        $month = $filters['month'] ?? null;
        $year = $filters['year'] ?? null;
        $wilayah = $filters['wilayah'] ?? null;
        $search = trim((string) ($filters['search'] ?? ''));

        // Filter tanggal spesifik (prioritas tertinggi)
        if (filled($date)) {
            $query->whereDate('created_at', Carbon::parse($date)->toDateString());
        } elseif (filled($month) || filled($year)) {
            // Filter per bulan/tahun
            if (filled($month)) {
                $query->whereMonth('created_at', $month);
            }
            if (filled($year)) {
                $query->whereYear('created_at', $year);
            }
        }

        if (filled($wilayah)) {
            $query->where('wilayah', $wilayah);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_pelanggan', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhere('wilayah', 'like', "%{$search}%")
                    ->orWhere('jenis_kabel', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($eq) use ($search) {
                        $eq->where('full_name', 'like', "%{$search}%");
                    });
            });
        }

        return compact('query', 'date', 'month', 'year', 'wilayah', 'search');
    }

    public function index(Request $request)
    {
        $filters = $this->applyFilters($request);

        $laporanKabel = $filters['query']->latest()->paginate(self::PER_PAGE)->appends($request->query());
        $employees = Employee::orderBy('full_name')->get(['id', 'full_name']);

        return view('content.apps.Logistik.laporan-kabel.index', compact('laporanKabel', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'wilayah' => 'required|in:Klaten,Gunung Kidul,Boyolali',
            'employee_id' => 'required|exists:employees,id',
            'alamat' => 'required|string|max:1000',
            'tarikan_meter' => 'required|numeric|min:0',
            'jenis_kabel' => 'required|in:1c,4c,12c',
            'sisa_kabel' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:2000',
        ]);

        LaporanKabel::create([
            'nama_pelanggan' => $validated['nama_pelanggan'],
            'wilayah' => $validated['wilayah'],
            'employee_id' => $validated['employee_id'],
            'alamat' => $validated['alamat'],
            'tarikan_meter' => $validated['tarikan_meter'],
            'jenis_kabel' => $validated['jenis_kabel'],
            // Tetap simpan ke kolom lama agar tidak perlu ubah struktur DB.
            'sisi_core' => $validated['sisa_kabel'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return redirect()->route('logistik.laporan-kabel.index')
            ->with('success', 'Laporan kabel berhasil ditambahkan.');
    }

    public function update(Request $request, LaporanKabel $laporanKabel)
    {
        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'wilayah' => 'required|in:Klaten,Gunung Kidul,Boyolali',
            'employee_id' => 'required|exists:employees,id',
            'alamat' => 'required|string|max:1000',
            'tarikan_meter' => 'required|numeric|min:0',
            'jenis_kabel' => 'required|in:1c,4c,12c',
            'sisa_kabel' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:2000',
        ]);

        $laporanKabel->update([
            'nama_pelanggan' => $validated['nama_pelanggan'],
            'wilayah' => $validated['wilayah'],
            'employee_id' => $validated['employee_id'],
            'alamat' => $validated['alamat'],
            'tarikan_meter' => $validated['tarikan_meter'],
            'jenis_kabel' => $validated['jenis_kabel'],
            'sisi_core' => $validated['sisa_kabel'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return redirect()->route('logistik.laporan-kabel.index');
    }

    public function destroy(LaporanKabel $laporanKabel)
    {
        $laporanKabel->delete();

        return redirect()->route('logistik.laporan-kabel.index');
    }

    /**
     * Export Excel laporan kabel (streaming - tidak kena Cloudflare timeout).
     * Pola sama persis dengan TagihanController::export() yang sukses.
     */
    public function exportExcel(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

        $filters = $this->applyFilters($request);
        extract($filters); // $query, $date, $month, $year, $wilayah, $search

        $filename = 'Laporan-Kabel_'
            . ($date  ? Carbon::parse($date)->format('d-m-Y') . '_' : '')
            . ($month ? 'B' . $month . '_' : '')
            . ($year  ? 'Y' . $year  . '_' : '')
            . now()->format('Ymd-His')
            . '.xlsx';

        try {
            // Jika user memilih bulan tanpa memilih tanggal spesifik -> pecah per sheet
            if (filled($month) && !filled($date)) {
                $exportYear = filled($year) ? (int) $year : (int) date('Y');
                return Excel::download(
                    new \App\Exports\LaporanKabelExportBulan(
                        (int) $month,
                        $exportYear,
                        $wilayah ?? null,
                        $search ?? ''
                    ),
                    $filename
                );
            }

            // Jika export harian (pakai tanggal spesifik) ATAU export semua -> cukup 1 sheet
            return Excel::download(
                new LaporanKabelExport(
                    $date    ?? null,
                    isset($month)   ? (int) $month   : null,
                    isset($year)    ? (int) $year    : null,
                    $wilayah ?? null,
                    $search  ?? ''
                ),
                $filename
            );
        } catch (\Exception $e) {
            Log::error('Excel Export Error: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Gagal export Excel: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export PDF laporan kabel.
     *
     * Strategi: Simpan PDF ke public/temp-exports/ lalu redirect ke URL
     * file statis. Cloudflare TIDAK akan mengganggu download file statis
     * karena tidak melewati PHP.
     */
    public function exportPdf(Request $request)
    {
        try {
            ini_set('memory_limit', '1024M');
            set_time_limit(300);

            // Bersihkan file export lama (> 1 jam)
            $this->cleanOldExports();

            $filters = $this->applyFilters($request);
            extract($filters); // $query, $date, $month, $year, $wilayah, $search

            // Hapus blok validasi wajib isi tanggal/bulan agar admin bisa export SEMUA data (tanpa filter),
            // hanya bulan, hanya tahun, atau kombinasi bebas lainnya.

            $laporanKabel = $query->latest()
                ->select(['id', 'nama_pelanggan', 'employee_id', 'wilayah', 'alamat', 'tarikan_meter', 'jenis_kabel', 'sisi_core', 'keterangan', 'created_at'])
                ->get();

            if ($laporanKabel->isEmpty()) {
                return back()->with('error', 'Tidak ada data untuk di-export pada periode yang dipilih.');
            }

            // Jika filter per bulan → group by tanggal (per halaman)
            $groupByDate = filled($month) && !filled($date);

            if ($groupByDate) {
                $groupedData = $laporanKabel->groupBy(function ($item) {
                    return optional($item->created_at)->format('Y-m-d');
                })->sortKeys();
            } else {
                $groupedData = null;
            }

            // Build period label
            if (filled($date)) {
                $periodeLabel = 'Tanggal: ' . Carbon::parse($date)->translatedFormat('d F Y');
            } elseif (filled($month) || filled($year)) {
                $parts = [];
                if (filled($month)) {
                    $parts[] = Carbon::create()->month($month)->translatedFormat('F');
                }
                if (filled($year)) {
                    $parts[] = $year;
                }
                $periodeLabel = 'Periode: ' . implode(' ', $parts);
            } else {
                $periodeLabel = 'Semua Data';
            }

            $pdf = Pdf::loadView('content.apps.Logistik.laporan-kabel.export-pdf', [
                'laporanKabel' => $laporanKabel,
                'groupedData' => $groupedData,
                'groupByDate' => $groupByDate,
                'periodeLabel' => $periodeLabel,
                'wilayah' => $wilayah,
                'search' => $search,
                'printedAt' => now(),
            ]);

            $pdf->setPaper('a4', 'landscape');
            $pdf->setWarnings(false);

            $options = $pdf->getDomPDF()->getOptions();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', false);
            $options->set('defaultFont', 'Helvetica');
            $pdf->getDomPDF()->setOptions($options);

            // Render PDF
            $output = $pdf->output();

            // Simpan sebagai file statis di folder public
            $filename = 'laporan-kabel-' . now()->format('Ymd-His') . '-' . mt_rand(1000, 9999) . '.pdf';
            $publicPath = public_path('temp-exports/' . $filename);

            file_put_contents($publicPath, $output);

            // Redirect ke URL file statis.
            // Browser akan download langsung dari web server (Apache/Nginx),
            // BUKAN melalui PHP → Cloudflare tidak akan timeout.
            return redirect('/temp-exports/' . $filename);

        } catch (\Exception $e) {
            Log::error('PDF Export Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    /**
     * Hapus file export lama (> 1 jam) dari public/temp-exports/
     */
    private function cleanOldExports(): void
    {
        $dir = public_path('temp-exports');
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
            return;
        }

        foreach (glob($dir . '/*.pdf') as $file) {
            if (filemtime($file) < time() - 3600) {
                @unlink($file);
            }
        }
    }
}

