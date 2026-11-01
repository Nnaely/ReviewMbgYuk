<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>Rekap Laporan | ReviewMbgYuk!</title>
</head>
<body class="app-page">
    @include('partials.app-header')
    <main class="app-shell">
        <a href="{{ auth()->user()->isPemantau() ? route('area.pemantau') : route('area.koordinator') }}">Kembali ke dasbor</a>
        <p class="app-eyebrow">Pemantau · Koordinator</p>
        <h1>Rekap Laporan</h1>
        <p>Ringkasan laporan tersimpan menurut sekolah dan status.</p>

        <form method="GET" action="{{ route('reports.summary') }}">
            <label for="school_id">Filter sekolah</label>
            <select id="school_id" name="school_id">
                <option value="">Semua sekolah</option>
                @foreach ($schools as $school)
                    <option value="{{ $school->id }}" @selected((string) ($filters['school_id'] ?? '') === (string) $school->id)>
                        {{ $school->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit">Terapkan filter</button>
            <a href="{{ route('reports.summary') }}">Hapus filter</a>
        </form>

        <section aria-labelledby="ringkasan-status">
            <h2 id="ringkasan-status">Ringkasan status</h2>
            <dl>
                <dt>Total laporan</dt>
                <dd>{{ $summary->total ?? 0 }}</dd>
                <dt>Baru</dt>
                <dd>{{ $summary->baru ?? 0 }}</dd>
                <dt>Diproses</dt>
                <dd>{{ $summary->diproses ?? 0 }}</dd>
                <dt>Selesai</dt>
                <dd>{{ $summary->selesai ?? 0 }}</dd>
            </dl>
        </section>

        <h2>Jumlah laporan per sekolah</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Sekolah</th>
                        <th scope="col">Total</th>
                        <th scope="col">Baru</th>
                        <th scope="col">Diproses</th>
                        <th scope="col">Selesai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($schoolRows as $school)
                        <tr>
                            <th scope="row">{{ $school->name }}</th>
                            <td>{{ $school->total_laporan }}</td>
                            <td>{{ $school->laporan_baru }}</td>
                            <td>{{ $school->laporan_diproses }}</td>
                            <td>{{ $school->laporan_selesai }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5">Belum ada data sekolah.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
    @include('partials.app-footer')
</body>
</html>
