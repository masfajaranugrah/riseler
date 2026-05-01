<?php

namespace App\Http\Controllers;

use App\Exports\KwitansiExport;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TagihanKwitansiController extends Controller
{
    /**
     * Tampilkan halaman daftar tagihan untuk export Excel
     */
    public function index()
    {
        $status = request('status');
        $kabupaten = request('kabupaten');
        $kecamatan = request('kecamatan');

        $tagihans = $this->buildKwitansiQuery($status, $kabupaten, $kecamatan)
            ->paginate(10)
            ->appends(request()->query());

        $kabupatens = Pelanggan::distinct()->pluck('kabupaten');
        $kecamatans = Pelanggan::distinct()->pluck('kecamatan');

        return view('content.apps.Laporan.kwitansi', compact(
            'tagihans',
            'kabupatens',
            'kecamatans'
        ));
    }

    /**
     * Export Excel tagihan beserta gambar kwitansi
     */
    public function exportExcel(Request $request)
    {
        $status = $request->input('status');
        $kabupaten = $request->input('kabupaten');
        $kecamatan = $request->input('kecamatan');

        $query = $this->buildKwitansiQuery($status, $kabupaten, $kecamatan);

        if (!$query->exists()) {
            return back()->with('error', 'Data kwitansi tidak ditemukan untuk filter yang dipilih.');
        }

        return Excel::download(
            new KwitansiExport($status, $kabupaten, $kecamatan),
            'laporan_kwitansi.xlsx'
        );
    }

    private function buildKwitansiQuery($status, $kabupaten, $kecamatan)
    {
        return Tagihan::with(['pelanggan', 'paket'])
            ->when($status, function ($query, $status) {
                $query->where('status_pembayaran', $status);
            })
            ->when($kabupaten, function ($query, $kabupaten) {
                $query->whereHas('pelanggan', function ($q) use ($kabupaten) {
                    $q->where('kabupaten', $kabupaten);
                });
            })
            ->when($kecamatan, function ($query, $kecamatan) {
                $query->whereHas('pelanggan', function ($q) use ($kecamatan) {
                    $q->where('kecamatan', $kecamatan);
                });
            })
            ->orderBy('tanggal_mulai', 'desc');
    }
}
