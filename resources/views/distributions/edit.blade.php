<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>Edit Jadwal Distribusi | MBG Komunitas</title>
</head>
<body>
    <main>
        <a href="{{ route('distributions.index') }}">Kembali ke daftar distribusi</a>
        <h1>Edit Jadwal Distribusi</h1>

        @if ($errors->any())
            <ul role="alert">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ route('distributions.update', $distribution) }}">
            @csrf
            @method('PATCH')

            <p>Sekolah: {{ $distribution->school->name }}</p>

            <label for="distribution_date">Tanggal distribusi</label>
            <input id="distribution_date" name="distribution_date" type="date" value="{{ old('distribution_date', $distribution->distribution_date->format('Y-m-d')) }}" required>

            <label for="menu_description">Keterangan menu</label>
            <textarea id="menu_description" name="menu_description" rows="4" minlength="5" maxlength="2000" required>{{ old('menu_description', $distribution->menu_description) }}</textarea>

            <button type="submit">Simpan perubahan</button>
        </form>
    </main>
</body>
</html>
