<?php

namespace App\Http\Controllers;

use App\Models\KasRegistrasi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class KasRegistrasiController extends Controller
{
    public function index(Request $request)
    {
        $query = KasRegistrasi::query();

        // Filter search
        if ($search = $request->input('search')) {
            $query->where('keterangan', 'LIKE', "%{$search}%");
        }

        // Filter periode (bulan & tahun)
        $filterMonth = $request->input('filter_month', Carbon::now()->month);
        $filterYear  = $request->input('filter_year', Carbon::now()->year);

        $query->whereMonth('tanggal', $filterMonth)
              ->whereYear('tanggal', $filterYear);

        $items = $query->orderBy('tanggal', 'asc')->orderBy('id', 'asc')->get();

        // Hitung running saldo
        $saldoAwal = KasRegistrasi::whereDate('tanggal', '<', Carbon::createFromDate($filterYear, $filterMonth, 1)->startOfMonth())
            ->selectRaw('SUM(pemasukan) - SUM(pengeluaran) as saldo')
            ->value('saldo') ?? 0;

        $runningBalance = $saldoAwal;
        $itemsWithSaldo = $items->map(function ($item) use (&$runningBalance) {
            $runningBalance += ($item->pemasukan - $item->pengeluaran);
            $item->saldo = $runningBalance;
            return $item;
        });

        // Manual Pagination (40 per page)
        $page = $request->input('page', 1);
        $perPage = 40;
        $offset = ($page - 1) * $perPage;
        
        $paginatedItems = new LengthAwarePaginator(
            $itemsWithSaldo->slice($offset, $perPage)->values(),
            $itemsWithSaldo->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $totalPemasukan = $items->sum('pemasukan');
        $totalPengeluaran = $items->sum('pengeluaran');
        $saldoAkhir = $saldoAwal + $totalPemasukan - $totalPengeluaran;

        $monthLabel = Carbon::createFromDate($filterYear, $filterMonth, 1)->locale('id')->isoFormat('MMMM YYYY');

        return view('content.apps.KasRegistrasi.index', compact(
            'paginatedItems',
            'totalPemasukan',
            'totalPengeluaran',
            'saldoAwal',
            'saldoAkhir',
            'filterMonth',
            'filterYear',
            'monthLabel',
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'keterangan'  => 'required|string|max:255',
            'jenis'       => 'required|in:pemasukan,pengeluaran',
            'jumlah'      => 'required|numeric|min:0',
            'tanggal'     => 'required|date',
            'catatan'     => 'nullable|string',
        ]);

        KasRegistrasi::create([
            'keterangan'  => $request->keterangan,
            'pemasukan'   => $request->jenis === 'pemasukan' ? $request->jumlah : 0,
            'pengeluaran' => $request->jenis === 'pengeluaran' ? $request->jumlah : 0,
            'tanggal'     => $request->tanggal,
            'catatan'     => $request->catatan,
        ]);

        return response()->json(['success' => true, 'message' => 'Data berhasil ditambahkan.']);
    }

    public function show($id)
    {
        $item = KasRegistrasi::findOrFail($id);
        $item->jenis = $item->pemasukan > 0 ? 'pemasukan' : 'pengeluaran';
        $item->jumlah = $item->pemasukan > 0 ? $item->pemasukan : $item->pengeluaran;
        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'keterangan'  => 'required|string|max:255',
            'jenis'       => 'required|in:pemasukan,pengeluaran',
            'jumlah'      => 'required|numeric|min:0',
            'tanggal'     => 'required|date',
            'catatan'     => 'nullable|string',
        ]);

        $item = KasRegistrasi::findOrFail($id);
        $item->update([
            'keterangan'  => $request->keterangan,
            'pemasukan'   => $request->jenis === 'pemasukan' ? $request->jumlah : 0,
            'pengeluaran' => $request->jenis === 'pengeluaran' ? $request->jumlah : 0,
            'tanggal'     => $request->tanggal,
            'catatan'     => $request->catatan,
        ]);

        return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        KasRegistrasi::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }
}
