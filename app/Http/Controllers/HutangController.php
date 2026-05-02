<?php

namespace App\Http\Controllers;

use App\Models\Hutang;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HutangController extends Controller
{
    public function index(Request $request)
    {
        $query = Hutang::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', '%' . $search . '%')
                  ->orWhere('catatan', 'like', '%' . $search . '%');
            });
        }

        $hutangs = $query->orderByDesc('tanggal')->paginate(40)->withQueryString();

        return view('content.apps.Laba.hutang.hutang', compact('hutangs'));
    }

    public function create()
    {
        return view('content.apps.Laba.hutang.add-hutang');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
            'tanggal' => 'required|date',
        ]);

        $jumlahBersih = (int) str_replace('.', '', (string) $validated['jumlah']);

        Hutang::create([
            'nama_barang' => $validated['nama_barang'],
            'jumlah' => $jumlahBersih,
            'catatan' => $validated['catatan'] ?? null,
            'tanggal' => Carbon::parse($validated['tanggal']),
        ]);

        return redirect()->route('hutang.index')->with('success', 'Data hutang berhasil ditambahkan.');
    }
}
