<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>Perbaiki Laporan | ReviewMbgYuk!</title>
</head>
<body>
    <main>
        <a href="{{ route('reports.index') }}">Kembali ke laporan saya</a>
        <h1>Perbaiki Laporan</h1>
        <p>Laporan hanya dapat diperbaiki selama statusnya masih Baru.</p>

        @if ($errors->any())
            <ul role="alert">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ route('reports.correct', $report) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            @include('reports._distribution_fields')

            <label for="description">Isi laporan</label>
            <textarea id="description" name="description" rows="5" minlength="10" maxlength="2000" required>{{ old('description', $report->description) }}</textarea>

            @if ($report->photo_path)
                <p>Bukti saat ini: <a href="{{ route('reports.photo', $report) }}">Lihat bukti</a></p>
            @endif
            <label for="photo">Ganti bukti pendukung (opsional, maksimal 2 MB)</label>
            <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,application/pdf">

            <button type="submit">Simpan perbaikan</button>
        </form>
    </main>
    <script src="{{ asset('js/distribution-select.js') }}" defer></script>
</body>
</html>
