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
            <label for="school_id">Sekolah</label>
            <select id="school_id" name="school_id" required>
                <option value="">Pilih sekolah</option>
                @foreach ($schools as $school)
                    <option value="{{ $school->id }}" @selected(old('school_id') == $school->id)>
                        {{ $school->name }}
                    </option>
                @endforeach
            </select>

            <label for="description">Isi laporan</label>
            <textarea id="description" name="description" rows="5" minlength="10" maxlength="2000" required>{{ old('description') }}</textarea>

            <button type="submit">Kirim laporan</button>
        </form>

        <h2>Laporan Saya</h2>
        @forelse ($reports as $report)
            <article>
                <h3>{{ $report->school->name }}</h3>
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
    </main>
</body>
</html>
