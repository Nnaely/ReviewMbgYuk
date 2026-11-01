<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SchoolController extends Controller
{
    public function index(): View
    {
        return view('schools.index', [
            'schools' => School::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255', 'unique:schools,name'],
            'address' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        School::create($validated);

        return redirect()
            ->route('schools.index')
            ->with('status', 'Data sekolah berhasil ditambahkan.');
    }

    public function edit(School $school): View
    {
        return view('schools.edit', compact('school'));
    }

    public function update(Request $request, School $school): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255', Rule::unique('schools', 'name')->ignore($school->id)],
            'address' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $school->update($validated);

        return redirect()
            ->route('schools.index')
            ->with('status', 'Data sekolah berhasil diperbarui.');
    }
}
