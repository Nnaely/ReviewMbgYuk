<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function pelapor(Request $request): View
    {
        return $this->dashboard($request, 'Pelapor');
    }

    public function pemantau(Request $request): View
    {
        return $this->dashboard($request, 'Pemantau');
    }

    public function koordinator(Request $request): View
    {
        return $this->dashboard($request, 'Koordinator');
    }

    private function dashboard(Request $request, string $namaPeran): View
    {
        $reports = Report::query();

        if ($request->user()->isPelapor()) {
            $reports->where('reporter_id', $request->user()->id);
        }

        $counts = $reports
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $jumlahPerStatus = [
            'baru' => (int) ($counts['baru'] ?? 0),
            'diproses' => (int) ($counts['diproses'] ?? 0),
            'selesai' => (int) ($counts['selesai'] ?? 0),
        ];

        return view('areas.role', [
            'namaPeran' => $namaPeran,
            'jumlahPerStatus' => $jumlahPerStatus,
            'jumlahLaporan' => array_sum($jumlahPerStatus),
            'notifications' => $request->user()->isPelapor()
                ? $request->user()->notifications()->latest()->limit(5)->get()
                : collect(),
            'unreadNotificationCount' => $request->user()->unreadNotifications()->count(),
        ]);
    }
}
