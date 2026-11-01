<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>Data Sekolah | ReviewMbgYuk!</title>
</head>
<body>
    <main>
        <a href="{{ route('area.koordinator') }}">Kembali ke dasbor</a>
        <h1>Pengelolaan Data Sekolah</h1>

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

        <h2>Tambah Sekolah</h2>
        <form method="POST" action="{{ route('schools.store') }}">
            @csrf
            <label for="name">Nama sekolah</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" minlength="3" maxlength="255" required>

            <label for="address">Alamat sekolah</label>
            <textarea id="address" name="address" rows="3" minlength="5" maxlength="1000" required>{{ old('address') }}</textarea>

            <button type="submit">Tambah sekolah</button>
        </form>

        <h2>Daftar Sekolah</h2>
        @forelse ($schools as $school)
            <article>
                <h3>{{ $school->name }}</h3>
                <p>{{ $school->address }}</p>
                <a href="{{ route('schools.edit', $school) }}">Edit sekolah</a>
            </article>
        @empty
            <p>Belum ada data sekolah.</p>
        @endforelse
    </main>
</body>
</html>
