<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class DirecturMarketingController extends Controller
{
    private const PER_PAGE = 40;

    private const PROGRES_MENU = [
        'all' => 'Semua Progress',
        'belum-progres' => Pelanggan::PROGRES_BELUM_DIPROSES,
        'tarik-kabel' => Pelanggan::PROGRES_TARIK_KABEL,
        'aktivasi' => Pelanggan::PROGRES_AKTIVASI,
        'registrasi' => Pelanggan::PROGRES_REGISTRASI,
        'approve' => 'Approve',
    ];

    public function index(Request $request)
    {
        return $this->renderMonitoring($request, 'all');
    }

    public function progres(Request $request)
    {
        return redirect()->route('directur.progres.belum-progres');
    }

    public function progresBelum(Request $request)
    {
        return $this->renderMonitoring($request, 'belum-progres');
    }

    public function progresTarikKabel(Request $request)
    {
        return $this->renderMonitoring($request, 'tarik-kabel');
    }

    public function progresAktivasi(Request $request)
    {
        return $this->renderMonitoring($request, 'aktivasi');
    }

    public function progresRegistrasi(Request $request)
    {
        return $this->renderMonitoring($request, 'registrasi');
    }

    public function approve(Request $request)
    {
        return $this->renderMonitoring($request, 'approve');
    }

    private function renderMonitoring(Request $request, string $stageKey)
    {
        $page = $this->resolvePage($request);
        $query = Pelanggan::with(['paket', 'user'])
            ->whereHas('user', function ($q) {
                $q->where('role', 'marketing');
            });

        $stageLabel = self::PROGRES_MENU[$stageKey] ?? self::PROGRES_MENU['all'];

        if ($stageKey === 'belum-progres') {
            $query->where(function ($q) {
                $q->where('progres', Pelanggan::PROGRES_BELUM_DIPROSES)
                    ->orWhereNull('progres')
                    ->orWhere('progres', '');
            });
        }

        if (isset(self::PROGRES_MENU[$stageKey]) && in_array($stageKey, ['tarik-kabel', 'aktivasi', 'registrasi'], true)) {
            $query->where('progres', self::PROGRES_MENU[$stageKey]);
        }

        if ($stageKey === 'approve') {
            $query->where('status', Pelanggan::STATUS_APPROVE);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nomer_id', 'like', "%{$search}%")
                    ->orWhere('no_whatsapp', 'like', "%{$search}%")
                    ->orWhere('progress_note', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $statsBaseQuery = clone $query;
        $totalCount = (clone $statsBaseQuery)->count();
        $notedCount = (clone $statsBaseQuery)
            ->whereNotNull('progress_note')
            ->where('progress_note', '!=', '')
            ->count();
        $marketingCount = (clone $statsBaseQuery)->distinct('user_id')->count('user_id');

        $pelanggan = $query
            ->latest()
            ->paginate(self::PER_PAGE, ['*'], 'page', $page)
            ->appends($request->query());

        $stageDescriptions = [
            'all' => 'Pantau seluruh progress pelanggan yang diinput oleh tim marketing.',
            'belum-progres' => 'Pantau pelanggan marketing yang belum masuk tahap progres.',
            'tarik-kabel' => 'Pantau pelanggan marketing yang masih di tahap tarik kabel.',
            'aktivasi' => 'Pantau pelanggan marketing yang sedang tahap aktivasi.',
            'registrasi' => 'Pantau pelanggan marketing yang sudah masuk tahap registrasi.',
            'approve' => 'Pantau pelanggan marketing yang sudah berstatus approve.',
        ];

        return view('content.apps.Directur.monitoring', [
            'pelanggan' => $pelanggan,
            'selectedStageKey' => $stageKey,
            'selectedStageLabel' => $stageLabel,
            'progressMenu' => self::PROGRES_MENU,
            'pageTitle' => $stageKey === 'all' ? 'Dashboard Directur' : 'Monitoring ' . $stageLabel,
            'pageDescription' => $stageDescriptions[$stageKey] ?? 'Pantau progress pelanggan dari tim marketing.',
            'totalCount' => $totalCount,
            'notedCount' => $notedCount,
            'marketingCount' => $marketingCount,
        ]);
    }

    private function resolvePage(Request $request): int
    {
        return max((int) $request->query('page', 1), 1);
    }
}
