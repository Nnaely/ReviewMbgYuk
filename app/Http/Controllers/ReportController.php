<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Report;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(['baru', 'diproses', 'selesai'])],
            'school_id' => ['nullable', 'integer', 'exists:schools,id'],
        ]);

        $reportsQuery = $request->user()->reports()
            ->with(['school', 'statusHistories.updater']);

        if (! empty($filters['status'])) {
            $reportsQuery->where('status', $filters['status']);
        }

        if (! empty($filters['school_id'])) {
            $reportsQuery->where('school_id', $filters['school_id']);
        }

        return view('reports.index', [
            'schools' => School::query()->orderBy('name')->get(['id', 'name']),
            'filters' => $filters,
            'reports' => $reportsQuery->latest()->paginate(10)->withQueryString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_id' => ['required', 'integer', 'exists:schools,id'],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $validated): void {
            $report = $request->user()->reports()->create($validated);
            $report->statusHistories()->create([
                'updated_by' => $request->user()->id,
                'status' => $report->status,
                'note' => 'Laporan dibuat oleh pelapor.',
            ]);
        });

        return redirect()
            ->route('reports.index')
            ->with('status', 'Laporan berhasil dikirim.');
    }

    public function inbox(Request $request): View
    {
        $dateToRules = ['nullable', 'date'];
        if ($request->filled('date_from')) {
            $dateToRules[] = 'after_or_equal:date_from';
        }

        $filters = $request->validate([
            'school_id' => ['nullable', 'integer', 'exists:schools,id'],
            'status' => ['nullable', Rule::in(['baru', 'diproses', 'selesai'])],
            'date_from' => ['nullable', 'date'],
            'date_to' => $dateToRules,
        ]);

        $reportsQuery = Report::with(['school', 'reporter']);

        if (! empty($filters['school_id'])) {
            $reportsQuery->where('school_id', $filters['school_id']);
        }

        if (! empty($filters['status'])) {
            $reportsQuery->where('status', $filters['status']);
        }

        if (! empty($filters['date_from'])) {
            $reportsQuery->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $reportsQuery->whereDate('created_at', '<=', $filters['date_to']);
        }

        return view('reports.inbox', [
            'schools' => School::query()->orderBy('name')->get(['id', 'name']),
            'filters' => $filters,
            'reports' => $reportsQuery
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function show(Report $report): View
    {
        return view('reports.show', [
            'report' => $report->load(['school', 'reporter', 'statusHistories.updater']),
        ]);
    }

    public function update(Request $request, Report $report): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['baru', 'diproses', 'selesai'])],
            'follow_up_note' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $report, $validated): void {
            $previousStatus = $report->status;
            $report->update($validated);
            $report->statusHistories()->create([
                'updated_by' => $request->user()->id,
                'previous_status' => $previousStatus,
                'status' => $report->status,
                'note' => $report->follow_up_note,
            ]);
        });

        return redirect()
            ->route('reports.show', $report)
            ->with('status', 'Status laporan berhasil diperbarui.');
    }
}
