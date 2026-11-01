<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>Laporan | ReviewMbgYuk!</title>
</head>
<body>
    <main>
        <a href="{{ route('area.pelapor') }}">Kembali ke dasbor</a>
        <h1>Laporan Distribusi</h1>

        @if (session('status'))
            <p role="status">{{ session('status') }}</p>
        @endif

        @if ($errors->any())
            <ul role="alert">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data">
            @csrf
            <label for="school_id">Sekolah</label>
            <select id="school_id" name="school_id" required>
                <option value="">Pilih sekolah</option>
                @foreach ($schools as $school)
                    <option value="{{ $school->id }}" @selected(old('school_id') == $school->id)>{{ $school->name }}</option>
                @endforeach
            </select>

            <label for="distribution_id">Distribusi makanan</label>
            <select id="distribution_id" name="distribution_id" required data-url-template="{{ route('schools.distributions', ['school' => '__SCHOOL_ID__']) }}" data-old-distribution="{{ old('distribution_id') }}">
                <option value="">Pilih sekolah terlebih dahulu</option>
            </select>
            <p id="distribution-status" role="status" aria-live="polite">Pilih sekolah untuk memuat distribusi yang berkaitan.</p>

            <label for="description">Isi laporan</label>
            <textarea id="description" name="description" rows="5" minlength="10" maxlength="2000" required>{{ old('description') }}</textarea>

            <label for="photo">Bukti pendukung (JPG, JPEG, PNG, atau PDF; maksimal 2 MB)</label>
            <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,application/pdf">

            <button type="submit">Kirim laporan</button>
        </form>

        <h2>Laporan Saya</h2>
        <form method="GET" action="{{ route('reports.index') }}">
            <label for="school_filter">Sekolah</label>
            <select id="school_filter" name="school_id">
                <option value="">Semua sekolah</option>
                @foreach ($schools as $school)
                    <option value="{{ $school->id }}" @selected((string) ($filters['school_id'] ?? '') === (string) $school->id)>
                        {{ $school->name }}
                    </option>
                @endforeach
            </select>

            <label for="status_filter">Filter status</label>
            <select id="status_filter" name="status">
                <option value="">Semua status</option>
                @foreach (['baru' => 'Baru', 'diproses' => 'Diproses', 'selesai' => 'Selesai'] as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit">Terapkan filter</button>
            <a href="{{ route('reports.index') }}">Hapus filter</a>
        </form>

        @forelse ($reports as $report)
            <article>
                <h3>{{ $report->school->name }}</h3>
                @if ($report->distribution)
                    <p>Distribusi: {{ $report->distribution->distribution_date->format('d-m-Y') }} — {{ $report->distribution->menu_description }}</p>
                @endif
                @if ($report->photo_path)
                    @if (strtolower(pathinfo($report->photo_path, PATHINFO_EXTENSION)) === 'pdf')
                        <p><a href="{{ route('reports.photo', $report) }}">Lihat bukti PDF</a></p>
                    @else
                        <p><img src="{{ route('reports.photo', $report) }}" alt="Bukti laporan" width="320"></p>
                    @endif
                @endif
                <p>{{ $report->description }}</p>
                <p>Status: {{ ucfirst($report->status) }}</p>
                <p>Catatan tindak lanjut: {{ $report->follow_up_note ?: 'Belum ada catatan tindak lanjut.' }}</p>
                <small>{{ $report->created_at->format('d-m-Y H:i') }}</small>
                <h4>Riwayat tindak lanjut</h4>
                @forelse ($report->statusHistories as $history)
                    <p>{{ $history->previous_status ? ucfirst($history->previous_status) : 'Dibuat' }} → {{ ucfirst($history->status) }}</p>
                    <p>{{ $history->note }}</p>
                    <small>{{ $history->updater->name }} · {{ $history->created_at->format('d-m-Y H:i') }}</small>
                @empty
                    <p>Riwayat belum tersedia.</p>
                @endforelse
            </article>
        @empty
            <p>Belum ada laporan yang dikirim.</p>
        @endforelse

        {{ $reports->links() }}
    </main>
    <script>
        const schoolSelect = document.getElementById('school_id');
        const distributionSelect = document.getElementById('distribution_id');
        const distributionStatus = document.getElementById('distribution-status');

        async function loadDistributions(selectedId = '') {
            const schoolId = schoolSelect.value;
            distributionSelect.replaceChildren(new Option('Memuat distribusi...', ''));

            if (!schoolId) {
                distributionSelect.replaceChildren(new Option('Pilih sekolah terlebih dahulu', ''));
                distributionStatus.textContent = 'Pilih sekolah untuk memuat distribusi yang berkaitan.';
                return;
            }

            try {
                const url = distributionSelect.dataset.urlTemplate.replace('__SCHOOL_ID__', schoolId);
                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                if (!response.ok) throw new Error('Permintaan distribusi gagal.');

                const distributions = await response.json();
                distributionSelect.replaceChildren(new Option('Pilih distribusi', ''));
                for (const distribution of distributions) {
                    distributionSelect.add(new Option(
                        distribution.label,
                        distribution.id,
                        false,
                        String(distribution.id) === String(selectedId),
                    ));
                }

                distributionStatus.textContent = distributions.length
                    ? `${distributions.length} distribusi tersedia.`
                    : 'Belum ada distribusi untuk sekolah ini.';
            } catch {
                distributionSelect.replaceChildren(new Option('Distribusi tidak dapat dimuat', ''));
                distributionStatus.textContent = 'Distribusi gagal dimuat. Coba pilih sekolah lagi.';
            }
        }

        schoolSelect.addEventListener('change', () => loadDistributions());
        if (schoolSelect.value) {
            loadDistributions(distributionSelect.dataset.oldDistribution);
        }
    </script>
</body>
</html>
