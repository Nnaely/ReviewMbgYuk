<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>Dasbor {{ $namaPeran }} | ReviewMbgYuk!</title>
</head>
<body>
    <main>
        <h1>Dasbor {{ $namaPeran }}</h1>
        <p>Selamat datang, {{ auth()->user()->name }}.</p>
        <section aria-labelledby="ringkasan-laporan">
            <h2 id="ringkasan-laporan">Ringkasan {{ auth()->user()->isPelapor() ? 'laporan saya' : 'laporan masuk' }}</h2>
            <dl>
                <dt>Total laporan</dt>
                <dd>{{ $jumlahLaporan }}</dd>
                <dt>Baru</dt>
                <dd>{{ $jumlahPerStatus['baru'] }}</dd>
                <dt>Diproses</dt>
                <dd>{{ $jumlahPerStatus['diproses'] }}</dd>
                <dt>Selesai</dt>
                <dd>{{ $jumlahPerStatus['selesai'] }}</dd>
            </dl>
        </section>
        @if (auth()->user()->isPelapor())
            <p><a href="{{ route('reports.index') }}">Buat laporan</a></p>
            <section aria-labelledby="notifikasi-laporan">
                <h2 id="notifikasi-laporan">Notifikasi tindak lanjut</h2>
                <p>{{ $unreadNotificationCount }} notifikasi belum dibaca</p>
                @forelse ($notifications as $notification)
                    <article>
                        <h3>{{ $notification->data['title'] }}</h3>
                        <p>{{ $notification->data['message'] }}</p>
                        @if (! $notification->read_at)
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                @csrf
                                <button type="submit">Tandai sudah dibaca</button>
                            </form>
                        @endif
                    </article>
                @empty
                    <p>Belum ada notifikasi tindak lanjut.</p>
                @endforelse
            </section>
        @endif
        @if (auth()->user()->isPemantau() || auth()->user()->isKoordinator())
            <p><a href="{{ route('distributions.index') }}">Lihat distribusi makanan</a></p>
            <p><a href="{{ route('reports.inbox') }}">Lihat laporan masuk</a></p>
        @endif
        @if (auth()->user()->isKoordinator())
            <p><a href="{{ route('schools.index') }}">Kelola data sekolah</a></p>
        @endif
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Keluar</button>
        </form>
    </main>
</body>
</html>
