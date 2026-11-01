<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Distribution;
use App\Models\Report;
use App\Notifications\ReportFollowUpUpdated;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

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
            'distributions' => Distribution::with('school')->orderByDesc('distribution_date')->get(),
            'filters' => $filters,
            'reports' => $reportsQuery->with('distribution')->latest()->paginate(10)->withQueryString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'distribution_id' => ['required', 'integer', 'exists:distributions,id'],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $photo = $validated['photo'] ?? null;
        unset($validated['photo']);
        $photoPath = $photo?->store('laporan/bukti', 'local');

        if ($photo && ! $photoPath) {
            return back()->withErrors(['photo' => 'Foto bukti gagal disimpan.'])->withInput();
        }

        try {
            DB::transaction(function () use ($request, $validated, $photoPath): void {
                $distribution = Distribution::query()->findOrFail($validated['distribution_id']);
                $report = $request->user()->reports()->create([
                    'school_id' => $distribution->school_id,
                    'distribution_id' => $distribution->id,
                    'description' => $validated['description'],
                    'photo_path' => $photoPath,
                ]);
                $report->statusHistories()->create([
                    'updated_by' => $request->user()->id,
                    'status' => $report->status,
                    'note' => 'Laporan dibuat oleh pelapor.',
                ]);
            });
        } catch (Throwable $exception) {
            if ($photoPath) {
                Storage::disk('local')->delete($photoPath);
            }

            throw $exception;
        }

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
            'report' => $report->load(['school', 'reporter', 'distribution', 'statusHistories.updater']),
        ]);
    }

    public function photo(Request $request, Report $report): StreamedResponse
    {
        $user = $request->user();
        $canView = $user->isPelapor()
            ? $report->reporter_id === $user->id
            : $user->isPemantau() || $user->isKoordinator();

        abort_unless($canView, 403);
        abort_unless($report->photo_path && Storage::disk('local')->exists($report->photo_path), 404);

        return Storage::disk('local')->response($report->photo_path);
    }

    public function update(Request $request, Report $report): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['baru', 'diproses', 'selesai'])],
            'follow_up_note' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $report, $validated): void {
            $previousStatus = $report->status;
            $previousNote = $report->follow_up_note;
            $hasChanged = $previousStatus !== $validated['status']
                || $previousNote !== $validated['follow_up_note'];

            if (! $hasChanged) {
                return;
            }

            $report->update($validated);
            $report->statusHistories()->create([
                'updated_by' => $request->user()->id,
                'previous_status' => $previousStatus,
                'status' => $report->status,
                'note' => $report->follow_up_note,
            ]);

            $report->loadMissing(['school', 'reporter']);
            $report->reporter->notify(new ReportFollowUpUpdated([
                'title' => 'Tindak lanjut laporan diperbarui',
                'message' => 'Laporan di '.$report->school->name.' diperbarui oleh '
                    .$request->user()->name.'. Status: '.ucfirst($report->status)
                    .'. Catatan: '.$report->follow_up_note,
                'report_id' => $report->id,
            ]));
        });

        return redirect()
            ->route('reports.show', $report)
            ->with('status', 'Status laporan berhasil diperbarui.');
    }
}
