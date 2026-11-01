<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Report;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        return view('reports.index', [
            'schools' => School::query()->orderBy('name')->get(['id', 'name']),
            'reports' => $request->user()->reports()->with('school')->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_id' => ['required', 'integer', 'exists:schools,id'],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $request->user()->reports()->create($validated);

        return redirect()
            ->route('reports.index')
            ->with('status', 'Laporan berhasil dikirim.');
    }

    public function inbox(): View
    {
        $filters = request()->validate([
            'school_id' => ['nullable', 'integer', 'exists:schools,id'],
            'status' => ['nullable', Rule::in(['baru', 'diproses', 'selesai'])],
        ]);

        $reportsQuery = Report::with(['school', 'reporter']);

        if (! empty($filters['school_id'])) {
            $reportsQuery->where('school_id', $filters['school_id']);
        }

        if (! empty($filters['status'])) {
            $reportsQuery->where('status', $filters['status']);
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
            'report' => $report->load(['school', 'reporter']),
        ]);
    }

    public function update(Request $request, Report $report): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['baru', 'diproses', 'selesai'])],
        ]);

        $report->update($validated);

        return redirect()
            ->route('reports.show', $report)
            ->with('status', 'Status laporan berhasil diperbarui.');
    }
}
