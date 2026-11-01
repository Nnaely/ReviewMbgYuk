<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>Masuk | ReviewMbgYuk!</title>
    <style>
        body { margin: 0; background: #f7f7f2; color: #252b26; font-family: Arial, sans-serif; }
        main { width: min(400px, calc(100% - 40px)); margin: 12vh auto; }
        h1 { margin-bottom: 8px; font-size: 28px; }
        p { color: #5a625b; }
        label { display: block; margin: 18px 0 6px; font-size: 14px; }
        input { box-sizing: border-box; width: 100%; padding: 12px; border: 1px solid #c6ccc4; border-radius: 5px; font: inherit; }
        button { width: 100%; margin-top: 22px; padding: 12px; border: 0; border-radius: 5px; background: #244b3e; color: white; font: inherit; cursor: pointer; }
        a { color: #244b3e; }
        .error { color: #a12727; font-size: 14px; }
    </style>
</head>
<body>
    <main>
        <a href="{{ route('beranda') }}">ReviewMbgYuk!</a>
        <h1>Masuk</h1>
        <p>Gunakan akun demo untuk masuk ke aplikasi.</p>

        @if ($errors->any())
            <p class="error" role="alert">{{ $errors->first() }}</p>
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
</body>
</html>
