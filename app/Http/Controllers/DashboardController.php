<?php

namespace App\Http\Controllers;

use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the welcome dashboard page.
     */
    public function index(Request $request)
    {
        $paket = Paket::all();
        
        // Filter Periode
        $periode = $request->periode;
        $filterDate = function($query) use ($periode) {
            if ($periode) {
                $parts = explode('-', $periode);
                if (count($parts) === 2) {
                    $query->whereYear('tagihans.tanggal_mulai', $parts[0])
                          ->whereMonth('tagihans.tanggal_mulai', $parts[1]);
                }
            }
        };

        // Status filter for main customers - ONLY JMK-GK
        $baseCondition = function($q) {
            $q->where(function($subQ) {
                $subQ->where('progres', Pelanggan::PROGRES_REGISTRASI)
                     ->orWhere('status', 'approve');
            })->where('nomer_id', 'LIKE', '%JMK-GK%');
        };

        // Statistik
        $totalCustomer = Pelanggan::where($baseCondition)->count();
        
        $customerLunas = Tagihan::join('pelanggans', 'tagihans.pelanggan_id', '=', 'pelanggans.id')
            ->where('tagihans.status_pembayaran', 'lunas')
            ->where('pelanggans.nomer_id', 'LIKE', '%JMK-GK%')
            ->where($filterDate)
            ->count();
            
        $belumLunas = Tagihan::join('pelanggans', 'tagihans.pelanggan_id', '=', 'pelanggans.id')
            ->where('tagihans.status_pembayaran', 'belum bayar')
            ->where('pelanggans.nomer_id', 'LIKE', '%JMK-GK%')
            ->where($filterDate)
            ->count();
            
        $totalPaket = $paket->count();

        // Status Active/Inactive
        $activeCustomers = Pelanggan::where($baseCondition)->whereHas('loginStatus', function($q) {
            $q->where('is_active', true);
        })->count();
        
        $inactiveCustomers = Pelanggan::where($baseCondition)->where(function($q) {
            $q->whereHas('loginStatus', function($subQ) {
                $subQ->where('is_active', false);
            })->orWhereDoesntHave('loginStatus');
        })->count();

        return view('content.apps.Dashboard.welcome', [
            'totalCustomer' => $totalCustomer,
            'customerLunas' => $customerLunas,
            'belumLunas' => $belumLunas,
            'totalPaket' => $totalPaket,
            'activeCustomers' => $activeCustomers,
            'inactiveCustomers' => $inactiveCustomers,
        ]);
    }
}
