<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use App\Models\School;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class DistributionController extends Controller
{
    public function bySchool(School $school): JsonResponse
    {
        $distributions = $school->distributions()
            ->orderByDesc('distribution_date')
            ->get()
            ->map(fn (Distribution $distribution): array => [
                'id' => $distribution->id,
                'label' => $distribution->distribution_date->format('d-m-Y').' — '.$distribution->menu_description,
            ]);

        return response()->json($distributions);
    }

    public function index(Request $request): View
    {
        $dateToRules = ['nullable', 'date'];
        if ($request->filled('date_from')) {
            $dateToRules[] = 'after_or_equal:date_from';
        }

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'school_id' => ['nullable', 'integer', 'exists:schools,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => $dateToRules,
        ]);

        $distributionsQuery = Distribution::with('school')->withCount('reports');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $distributionsQuery->where(function ($query) use ($search): void {
                $query->where('menu_description', 'like', '%'.$search.'%')
                    ->orWhereHas('school', fn ($schoolQuery) => $schoolQuery->where('name', 'like', '%'.$search.'%'));
            });
        }

        if (! empty($filters['school_id'])) {
            $distributionsQuery->where('school_id', $filters['school_id']);
        }

        if (! empty($filters['date_from'])) {
            $distributionsQuery->whereDate('distribution_date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $distributionsQuery->whereDate('distribution_date', '<=', $filters['date_to']);
        }

        $distributions = $distributionsQuery
            ->orderByDesc('distribution_date')
            ->paginate(15)
            ->withQueryString();
        $schools = School::query()->orderBy('name')->get(['id', 'name']);

        return view('distributions.index', compact('distributions', 'schools', 'filters'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_id' => ['required', 'integer', 'exists:schools,id'],
            'distribution_date' => ['required', 'date'],
            'menu_description' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        Distribution::create($validated);

        return redirect()
            ->route('distributions.index')
            ->with('status', 'Jadwal distribusi berhasil ditambahkan.');
    }

    public function edit(Distribution $distribution): View
    {
        return view('distributions.edit', [
            'distribution' => $distribution->load('school'),
        ]);
    }

    public function update(Request $request, Distribution $distribution): RedirectResponse
    {
        $validated = $request->validate([
            'distribution_date' => ['required', 'date'],
            'menu_description' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $distribution->update($validated);

        return redirect()
            ->route('distributions.index')
            ->with('status', 'Jadwal distribusi berhasil diperbarui.');
    }

    public function destroy(Distribution $distribution): RedirectResponse
    {
        if ($distribution->reports()->exists()) {
            return redirect()
                ->route('distributions.index')
                ->withErrors(['distribution' => 'Jadwal tidak dapat dihapus karena sudah digunakan oleh laporan.']);
        }

        $distribution->delete();

        return redirect()
            ->route('distributions.index')
            ->with('status', 'Jadwal distribusi berhasil dihapus.');
    }
}
