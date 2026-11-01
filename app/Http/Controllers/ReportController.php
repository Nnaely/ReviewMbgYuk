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
use Illuminate\Support\Facades\Validator;
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
            'filters' => $filters,
            'reports' => $reportsQuery->with('distribution')->latest()->paginate(10)->withQueryString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_id' => ['required', 'integer', 'exists:schools,id'],
            'distribution_id' => [
                'required',
                'integer',
                Rule::exists('distributions', 'id')->where('school_id', $request->input('school_id')),
            ],
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

    public function edit(Request $request, Report $report): View
    {
        $this->ensureReporterCanCorrect($request, $report);

        return view('reports.edit', [
            'report' => $report->load(['school', 'distribution']),
            'schools' => School::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function correct(Request $request, Report $report): RedirectResponse
    {
        $this->ensureReporterCanCorrect($request, $report);

        $validated = $request->validate([
            'school_id' => ['required', 'integer', 'exists:schools,id'],
            'distribution_id' => [
                'required',
                'integer',
                Rule::exists('distributions', 'id')->where('school_id', $request->input('school_id')),
            ],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $photo = $validated['photo'] ?? null;
        unset($validated['photo']);
        $oldPhotoPath = $report->photo_path;
        $newPhotoPath = $photo?->store('laporan/bukti', 'local');

        if ($photo && ! $newPhotoPath) {
            return back()->withErrors(['photo' => 'Bukti gagal disimpan.'])->withInput();
        }

        try {
            DB::transaction(function () use ($request, $report, $validated, $newPhotoPath): void {
                $lockedReport = Report::query()->lockForUpdate()->findOrFail($report->id);
                $this->ensureReporterCanCorrect($request, $lockedReport);
                $distribution = Distribution::query()->findOrFail($validated['distribution_id']);

                $lockedReport->update([
                    'school_id' => $distribution->school_id,
                    'distribution_id' => $distribution->id,
                    'description' => $validated['description'],
                    'photo_path' => $newPhotoPath ?? $lockedReport->photo_path,
                ]);
            });
        } catch (Throwable $exception) {
            if ($newPhotoPath) {
                Storage::disk('local')->delete($newPhotoPath);
            }

            throw $exception;
        }

        if ($newPhotoPath && $oldPhotoPath && $oldPhotoPath !== $newPhotoPath) {
            Storage::disk('local')->delete($oldPhotoPath);
        }

        return redirect()
            ->route('reports.index')
            ->with('status', 'Laporan berhasil diperbaiki.');
    }

    private function ensureReporterCanCorrect(Request $request, Report $report): void
    {
        abort_unless($report->reporter_id === $request->user()->id, 403);
        abort_unless($report->status === 'baru', 403);
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
            'report' => $report->load(['school', 'reporter', 'distribution', 'statusHistories.updater', 'referrer']),
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
        $validated = Validator::make($request->all(), [
            'status' => ['required', Rule::in([Report::STATUS_BARU, Report::STATUS_DIPROSES, Report::STATUS_SELESAI])],
            'follow_up_note' => ['required', 'string', 'min:5', 'max:2000'],
            'needs_referral' => ['nullable', 'boolean'],
        ])->validate();

        DB::transaction(function () use ($request, $report, $validated): void {
            $lockedReport = Report::query()->lockForUpdate()->findOrFail($report->id);
            abort_if($lockedReport->status === Report::STATUS_SELESAI && $validated['status'] !== $lockedReport->status, 422, 'Laporan selesai tidak dapat dibuka kembali.');
            abort_if(
                $validated['status'] !== $lockedReport->status
                    && ! in_array($validated['status'], Report::nextStatuses($lockedReport->status), true),
                422,
                'Status laporan harus diperbarui secara berurutan.'
            );

            $needsReferral = $lockedReport->needs_referral || $request->boolean('needs_referral');

            if ($lockedReport->status === $validated['status']
                && $lockedReport->follow_up_note === $validated['follow_up_note']
                && $lockedReport->needs_referral === $needsReferral) {
                return;
            }

            $lockedReport->update([
                'status' => $validated['status'],
                'follow_up_note' => $validated['follow_up_note'],
                'needs_referral' => $needsReferral,
            ]);

            $lockedReport->loadMissing(['school', 'reporter']);
            $lockedReport->reporter->notify(new ReportFollowUpUpdated([
                'title' => 'Tindak lanjut laporan diperbarui',
                'message' => 'Laporan di '.$lockedReport->school->name.' diperbarui oleh '
                    .$request->user()->name.'. Status: '.ucfirst($lockedReport->status)
                    .'. Catatan: '.$lockedReport->follow_up_note,
                'report_id' => $lockedReport->id,
            ]));
        });

        return redirect()
            ->route('reports.show', $report)
            ->with('status', 'Status laporan berhasil diperbarui.');
    }

    public function forward(Request $request, Report $report): RedirectResponse
    {
        $validated = $request->validate([
            'referral_target' => ['required', Rule::in(['sekolah', 'sppg', 'fasilitas_kesehatan', 'pihak_terkait_lain'])],
            'referral_note' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $report, $validated): void {
            $lockedReport = Report::query()->lockForUpdate()->findOrFail($report->id);
            abort_unless($lockedReport->needs_referral, 422, 'Pemantau belum menandai laporan untuk diteruskan.');
            abort_if($lockedReport->referred_at, 422, 'Laporan ini sudah diteruskan.');

            $lockedReport->update([
                ...$validated,
                'referred_at' => now(),
                'referred_by' => $request->user()->id,
            ]);

            $lockedReport->loadMissing(['school', 'reporter']);
            $lockedReport->reporter->notify(new ReportFollowUpUpdated([
                'title' => 'Laporan diteruskan',
                'message' => 'Laporan di '.$lockedReport->school->name.' diteruskan kepada '
                    .$validated['referral_target'].'. Catatan: '.$validated['referral_note'],
                'report_id' => $lockedReport->id,
            ]));
        });

        return redirect()
            ->route('reports.show', $report)
            ->with('status', 'Penerusan laporan berhasil dicatat.');
    }
}
