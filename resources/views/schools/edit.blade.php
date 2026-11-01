<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>Edit Sekolah | MBG Komunitas</title>
</head>
<body>
    <main>
        <a href="{{ route('schools.index') }}">Kembali ke daftar sekolah</a>
        <h1>Edit Data Sekolah</h1>

        @if ($errors->any())
            <ul role="alert">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ route('schools.update', $school) }}">
            @csrf
            @method('PATCH')

            <label for="name">Nama sekolah</label>
            <input id="name" name="name" type="text" value="{{ old('name', $school->name) }}" minlength="3" maxlength="255" required>

            <label for="address">Alamat sekolah</label>
            <textarea id="address" name="address" rows="3" minlength="5" maxlength="1000" required>{{ old('address', $school->address) }}</textarea>

            <button type="submit">Simpan perubahan</button>
        </form>
    </main>
</body>
</html>
