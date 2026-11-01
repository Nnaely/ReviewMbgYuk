<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>Masuk | ReviewMbgYuk!</title>
</head>
<body class="app-page">
    @include('partials.app-header')
    <main class="app-shell app-shell--compact">
        <p class="app-eyebrow">Akses Pelapor · Pemantau · Koordinator</p>
        <h1>Masuk</h1>
        <p>Masuk untuk menulis ulasan, meninjau laporan, atau membuka rekap.</p>

        @if ($errors->any())
            <p class="app-error" role="alert">{{ $errors->first() }}</p>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>

            <label for="password">Sandi</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>

            <button type="submit">Masuk</button>
        </form>
    </main>
    @include('partials.app-footer')
</body>
</html>
