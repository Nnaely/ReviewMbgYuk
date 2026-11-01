<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>ReviewMbgYuk!</title>
</head>
<body>
    <main>
        <p>PEMANTAUAN DISTRIBUSI MBG</p>
        <h1>ReviewMbgYuk!</h1>
        <p>Ruang bersama untuk melaporkan dan menindaklanjuti distribusi makanan di sekolah.</p>

        @auth
            @php
                $dashboardRoute = auth()->user()->isPelapor()
                    ? 'area.pelapor'
                    : (auth()->user()->isPemantau() ? 'area.pemantau' : 'area.koordinator');
            @endphp
            <p><a href="{{ route($dashboardRoute) }}">Buka dasbor</a></p>
        @else
            <p><a href="{{ route('login') }}">Masuk ke aplikasi</a></p>
        @endauth
    </main>
</body>
</html>
