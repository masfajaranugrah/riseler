<?php

namespace App\Http\Controllers;

use App\Models\SaldoAwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SaldoAwalController extends Controller
{
    /**
     * Get saldo awal by period (month/year)
     */
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        
        $saldoAwal = SaldoAwal::getByPeriod($bulan, $tahun);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $saldoAwal
            ]);
        }
        
        // return view('content.apps.Pembukuan.saldo-awal.index', compact('saldoAwal', 'bulan', 'tahun'));
        return redirect()->route('pembukuan.total', ['bulan' => $bulan, 'tahun' => $tahun]);
    }

    /**
     * Store or update saldo awal
     */
    public function store(Request $request)
    {
        // Clean currency format first before validation
        $rawDedicated = $request->omset_dedicated ?? '0';
        $rawHomenetKotor = $request->omset_homenet_kotor ?? '0';
        $rawHomenetBersih = $request->omset_homenet_bersih ?? '0';
        
        // Pemasukan fields
        $rawPemasukanRegistrasi = $request->pemasukan_registrasi ?? '0';
        $rawPemasukanDedicatedPotongan = $request->pemasukan_dedicated_potongan ?? '0';
        $rawPemasukanHomenetKotor = $request->pemasukan_homenet_kotor ?? '0';
        $rawPemasukanHomenetPotongan = $request->pemasukan_homenet_potongan ?? '0';
        $rawPemasukanHomenetBersih = $request->pemasukan_homenet_bersih ?? '0';
        
        // Piutang fields
        $rawPiutangDedicated = $request->piutang_dedicated ?? '0';
        $rawPiutangHomenet = $request->piutang_homenet ?? '0';
        $rawPiutangBulanSebelumnya = $request->piutang_bulan_sebelumnya ?? '0';
        $rawPiutangPeriodeSebelumnya = $request->piutang_periode_sebelumnya ?? '0';
        $rawPiutangTahunLalu = $request->piutang_tahun_lalu ?? '0';

        // Remove dots and commas (support both 1.000.000 and 1,000,000 formats by just keeping digits)
        $omsetDedicated = preg_replace('/[^\d]/', '', $rawDedicated);
        $omsetHomenetKotor = preg_replace('/[^\d]/', '', $rawHomenetKotor);
        $omsetHomenetBersih = preg_replace('/[^\d]/', '', $rawHomenetBersih);
        
        $pemasukanRegistrasi = preg_replace('/[^\d]/', '', $rawPemasukanRegistrasi);
        $pemasukanDedicatedPotongan = preg_replace('/[^\d]/', '', $rawPemasukanDedicatedPotongan);
        $pemasukanHomenetKotor = preg_replace('/[^\d]/', '', $rawPemasukanHomenetKotor);
        $pemasukanHomenetPotongan = preg_replace('/[^\d]/', '', $rawPemasukanHomenetPotongan);
        $pemasukanHomenetBersih = preg_replace('/[^\d]/', '', $rawPemasukanHomenetBersih);
        
        $piutangDedicated = preg_replace('/[^\d]/', '', $rawPiutangDedicated);
        $piutangHomenet = preg_replace('/[^\d]/', '', $rawPiutangHomenet);
        $piutangBulanSebelumnya = preg_replace('/[^\d]/', '', $rawPiutangBulanSebelumnya);
        $piutangPeriodeSebelumnya = preg_replace('/[^\d]/', '', $rawPiutangPeriodeSebelumnya);
        $piutangTahunLalu = preg_replace('/[^\d]/', '', $rawPiutangTahunLalu);

        // Merge cleaned values back to request for validation
        $request->merge([
            'bulan' => (int) $request->bulan,
            'tahun' => (int) $request->tahun,
            'omset_dedicated' => $omsetDedicated ?: '0',
            'omset_homenet_kotor' => $omsetHomenetKotor ?: '0',
            'omset_homenet_bersih' => $omsetHomenetBersih ?: '0',
            'pemasukan_registrasi' => $pemasukanRegistrasi ?: '0',
            'pemasukan_dedicated_potongan' => $pemasukanDedicatedPotongan ?: '0',
            'pemasukan_homenet_kotor' => $pemasukanHomenetKotor ?: '0',
            'pemasukan_homenet_potongan' => $pemasukanHomenetPotongan ?: '0',
            'pemasukan_homenet_bersih' => $pemasukanHomenetBersih ?: '0',
            'piutang_dedicated' => $piutangDedicated ?: '0',
            'piutang_homenet' => $piutangHomenet ?: '0',
            'piutang_bulan_sebelumnya' => $piutangBulanSebelumnya ?: '0',
            'piutang_periode_sebelumnya' => $piutangPeriodeSebelumnya ?: '0',
            'piutang_tahun_lalu' => $piutangTahunLalu ?: '0',
        ]);

        $validator = Validator::make($request->all(), [
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2100',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Update or create with all fields
            $saldoAwal = SaldoAwal::updateOrCreate(
                [
                    'bulan' => $request->bulan,
                    'tahun' => $request->tahun,
                ],
                [
                    'omset_dedicated' => $request->omset_dedicated,
                    'omset_homenet_kotor' => $request->omset_homenet_kotor,
                    'omset_homenet_bersih' => $request->omset_homenet_bersih,
                    'pemasukan_registrasi' => $request->pemasukan_registrasi,
                    'pemasukan_dedicated_potongan' => $request->pemasukan_dedicated_potongan,
                    'pemasukan_homenet_kotor' => $request->pemasukan_homenet_kotor,
                    'pemasukan_homenet_potongan' => $request->pemasukan_homenet_potongan,
                    'pemasukan_homenet_bersih' => $request->pemasukan_homenet_bersih,
                    'piutang_dedicated' => $request->piutang_dedicated,
                    'piutang_homenet' => $request->piutang_homenet,
                    'piutang_bulan_sebelumnya' => $request->piutang_bulan_sebelumnya,
                    'piutang_periode_sebelumnya' => $request->piutang_periode_sebelumnya,
                    'piutang_tahun_lalu' => $request->piutang_tahun_lalu,
                ]
            );

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil disimpan',
                    'data' => $saldoAwal
                ]);
            }

            return redirect()->route('pembukuan.total', ['bulan' => $request->bulan, 'tahun' => $request->tahun])
                ->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Gagal menyimpan data')->withInput();
        }
    }

    /**
     * Update saldo awal
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'omset_dedicated' => 'required|numeric|min:0',
            'omset_homenet_kotor' => 'required|numeric|min:0',
            'omset_homenet_bersih' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $saldoAwal = SaldoAwal::findOrFail($id);

        // Clean currency format
        $omsetDedicated = str_replace(['.', ','], ['', '.'], $request->omset_dedicated);
        $omsetHomenetKotor = str_replace(['.', ','], ['', '.'], $request->omset_homenet_kotor);
        $omsetHomenetBersih = str_replace(['.', ','], ['', '.'], $request->omset_homenet_bersih);

        $saldoAwal->update([
            'omset_dedicated' => $omsetDedicated,
            'omset_homenet_kotor' => $omsetHomenetKotor,
            'omset_homenet_bersih' => $omsetHomenetBersih,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Saldo Awal berhasil diperbarui',
                'data' => $saldoAwal
            ]);
        }

        return redirect()->route('pembukuan.total')->with('success', 'Saldo Awal berhasil diperbarui');
    }
}
