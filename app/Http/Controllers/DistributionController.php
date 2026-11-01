<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use App\Models\School;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DistributionController extends Controller
{
    public function index(): View
    {
        $distributions = Distribution::with('school')
            ->withCount('reports')
            ->orderByDesc('distribution_date')
            ->get();
        $schools = School::query()->orderBy('name')->get(['id', 'name']);

        return view('distributions.index', compact('distributions', 'schools'));
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
