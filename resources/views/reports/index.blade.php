<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan | MBG Komunitas</title>
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

        <form method="POST" action="{{ route('reports.store') }}">
            @csrf
            <label for="distribution_id">Jadwal distribusi</label>
            <select id="distribution_id" name="distribution_id" required>
                <option value="">Pilih jadwal distribusi</option>
                @foreach ($distributions as $distribution)
                    <option value="{{ $distribution->id }}" @selected(old('distribution_id') == $distribution->id)>
                        {{ $distribution->school->name }} — {{ $distribution->distribution_date->format('d-m-Y') }} — {{ $distribution->menu_description }}
                    </option>
                @endforeach
            </select>

            <label for="description">Isi laporan</label>
            <textarea id="description" name="description" rows="5" minlength="10" maxlength="2000" required>{{ old('description') }}</textarea>

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
</body>
</html>
