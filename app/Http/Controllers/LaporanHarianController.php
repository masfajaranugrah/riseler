<?php

namespace App\Http\Controllers;

use App\Exports\LaporanHarianExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanHarianController extends Controller
{
    public function index()
    {
        $month = now()->month;
        $year  = now()->year;
        return view('content.apps.Laporan.laporan-harian', compact('month', 'year'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'filter_month' => 'required|integer|min:1|max:12',
            'filter_year'  => 'required|integer|min:2020|max:2099',
        ]);

        $month = (int) $request->filter_month;
        $year  = (int) $request->filter_year;

        $monthLabel = Carbon::createFromDate($year, $month, 1)->locale('id')->isoFormat('MMMM_YYYY');
        $filename   = 'Laporan_Harian_' . $monthLabel . '.xlsx';

        return Excel::download(new LaporanHarianExport($month, $year), $filename);
    }
}
