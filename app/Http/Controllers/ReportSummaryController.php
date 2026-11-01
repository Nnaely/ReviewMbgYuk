<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\School;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ReportSummaryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'school_id' => ['nullable', 'integer', 'exists:schools,id'],
        ]);

        $reports = Report::query()->when(
            $filters['school_id'] ?? null,
            fn ($query, $schoolId) => $query->where('school_id', $schoolId)
        );

        $summary = (clone $reports)->selectRaw(
            'COUNT(*) as total, '
                .'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as baru, '
                .'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as diproses, '
                .'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as selesai',
            [Report::STATUS_BARU, Report::STATUS_DIPROSES, Report::STATUS_SELESAI]
        )->first();

        $schoolRows = School::query()
            ->when($filters['school_id'] ?? null, fn ($query, $schoolId) => $query->whereKey($schoolId))
            ->withCount([
                'reports as total_laporan',
                'reports as laporan_baru' => fn ($query) => $query->where('status', Report::STATUS_BARU),
                'reports as laporan_diproses' => fn ($query) => $query->where('status', Report::STATUS_DIPROSES),
                'reports as laporan_selesai' => fn ($query) => $query->where('status', Report::STATUS_SELESAI),
            ])
            ->orderBy('name')
            ->get();

        return view('reports.summary', [
            'schools' => School::query()->orderBy('name')->get(['id', 'name']),
            'filters' => $filters,
            'summary' => $summary,
            'schoolRows' => $schoolRows,
        ]);
    }
}
