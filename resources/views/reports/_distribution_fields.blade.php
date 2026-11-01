@php
    $selectedSchoolId = old('school_id', isset($report) ? $report->school_id : '');
    $selectedDistributionId = old('distribution_id', isset($report) ? $report->distribution_id : '');
@endphp

<label for="school_id">Sekolah</label>
<select id="school_id" name="school_id" required>
    <option value="">Pilih sekolah</option>
    @foreach ($schools as $school)
        <option value="{{ $school->id }}" @selected((string) $selectedSchoolId === (string) $school->id)>{{ $school->name }}</option>
    @endforeach
</select>

<label for="distribution_id">Distribusi makanan</label>
<select id="distribution_id" name="distribution_id" required data-url-template="{{ route('schools.distributions', ['school' => '__SCHOOL_ID__']) }}" data-old-distribution="{{ $selectedDistributionId }}">
    <option value="">Pilih sekolah terlebih dahulu</option>
</select>
<p id="distribution-status" role="status" aria-live="polite">Pilih sekolah untuk memuat distribusi yang berkaitan.</p>
