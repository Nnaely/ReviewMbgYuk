<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>Laporan Masuk | ReviewMbgYuk!</title>
</head>
<body>
    <main>
        <a href="{{ auth()->user()->isPemantau() ? route('area.pemantau') : route('area.koordinator') }}">Kembali ke dasbor</a>
        <h1>Laporan Masuk</h1>

        @if ($errors->any())
            <ul role="alert">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="GET" action="{{ route('reports.inbox') }}">
            <label for="school_id">Sekolah</label>
            <select id="school_id" name="school_id">
                <option value="">Semua sekolah</option>
                @foreach ($schools as $school)
                    <option value="{{ $school->id }}" @selected((string) ($filters['school_id'] ?? '') === (string) $school->id)>
                        {{ $school->name }}
                    </option>
                @endforeach
            </select>

            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="">Semua status</option>
                @foreach (['baru' => 'Baru', 'diproses' => 'Diproses', 'selesai' => 'Selesai'] as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <label for="date_from">Dari tanggal</label>
            <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}">

            <label for="date_to">Sampai tanggal</label>
            <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}">

            <button type="submit">Terapkan filter</button>
            <a href="{{ route('reports.inbox') }}">Hapus filter</a>
        </form>

        @forelse ($reports as $report)
            <article>
                <h2>{{ $report->school->name }}</h2>
                <p>Pelapor: {{ $report->reporter->name }}</p>
                <p>Status: {{ ucfirst($report->status) }}</p>
                <small>{{ $report->created_at->format('d-m-Y H:i') }}</small>
                <p><a href="{{ route('reports.show', $report) }}">Lihat detail laporan</a></p>
            </article>
        @empty
            <p>Belum ada laporan masuk.</p>
        @endforelse

        {{ $reports->links() }}
    </main>
</body>
</html>
