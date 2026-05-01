<?php

namespace App\Http\Controllers;

use App\Exports\TagihanExport;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function exportExcel(Request $request)
    {
        $status = $request->input('status');
        $kabupaten = $request->input('kabupaten');
        $kecamatan = $request->input('kecamatan');
        $search = $request->input('search');

        $query = $this->buildTagihanQuery($status, $kabupaten, $kecamatan, $search);

        if (!$query->exists()) {
            return back()->with('error', 'Data tagihan tidak ditemukan untuk filter yang dipilih.');
        }

        return (new TagihanExport($status, $kabupaten, $kecamatan, $search))
            ->download('laporan_tagihan.xlsx');
    }

    public function tagihan(Request $request)
    {
        $status = $request->input('status');
        $kabupaten = $request->input('kabupaten');
        $kecamatan = $request->input('kecamatan');
        $search = $request->input('search');

        $tagihans = $this->buildTagihanQuery($status, $kabupaten, $kecamatan, $search)
            ->paginate(40)
            ->appends($request->query());

        $kabupatens = Pelanggan::distinct()->pluck('kabupaten');
        $kecamatans = Pelanggan::distinct()->pluck('kecamatan');

        return view('content.apps.Laporan.tagihan', compact(
            'tagihans',
            'kabupatens',
            'kecamatans'
        ));
    }

    private function buildTagihanQuery($status, $kabupaten, $kecamatan, $search = null)
    {
        return Tagihan::with(['pelanggan', 'paket', 'rekening'])
            ->when($search, function ($query, $search) {
                $query->whereHas('pelanggan', function ($q) use ($search) {
                    $q->where('nama_lengkap', 'like', '%' . $search . '%');
                });
            })
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

    public function pembayaran()
    {
        return view('content.apps.Laporan.tagihan');
    }
}
