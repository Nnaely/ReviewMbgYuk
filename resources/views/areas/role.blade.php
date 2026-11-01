<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>Dasbor {{ $namaPeran }} | ReviewMbgYuk!</title>
</head>
<body class="app-page">
    @include('partials.app-header')
    <main class="app-shell">
        <p class="app-eyebrow">ReviewMbgYuk! · {{ $namaPeran }}</p>
        <h1>Selamat datang, {{ auth()->user()->name }}.</h1>
        <p>Dasbor {{ strtolower($namaPeran) }}.</p>
        <section aria-labelledby="ringkasan-laporan">
            <h2 id="ringkasan-laporan">Ringkasan {{ auth()->user()->isPelapor() ? 'laporan saya' : 'laporan masuk' }}</h2>
            <div class="app-stats">
                <div><span>Total laporan</span><strong>{{ $jumlahLaporan }}</strong></div>
                <div><span>Baru</span><strong>{{ $jumlahPerStatus['baru'] }}</strong></div>
                <div><span>Diproses</span><strong>{{ $jumlahPerStatus['diproses'] }}</strong></div>
                <div><span>Selesai</span><strong>{{ $jumlahPerStatus['selesai'] }}</strong></div>
            </div>
        </section>
        @if (auth()->user()->isPelapor())
            <p class="app-primary-action"><a href="{{ route('reports.index') }}">Tulis ulasan <span aria-hidden="true">↗</span></a></p>
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
            <section aria-labelledby="ruang-kerja">
                <h2 id="ruang-kerja">Ruang kerja</h2>
                <div class="app-action-grid">
                    <a href="{{ route('reports.inbox') }}"><strong>Laporan masuk</strong><span>Tinjau isi dan catatan laporan <b aria-hidden="true">↗</b></span></a>
                    <a href="{{ route('distributions.index') }}"><strong>Distribusi makanan</strong><span>Lihat jadwal sekolah <b aria-hidden="true">↗</b></span></a>
                    <a href="{{ route('reports.summary') }}"><strong>Rekap laporan</strong><span>Ringkasan berdasarkan sekolah <b aria-hidden="true">↗</b></span></a>
                    @if (auth()->user()->isKoordinator())
                        <a href="{{ route('schools.index') }}"><strong>Data sekolah</strong><span>Tambah atau perbarui data <b aria-hidden="true">↗</b></span></a>
                    @endif
                </div>
            </section>
        @endif
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Keluar</button>
        </form>
    </main>
    @include('partials.app-footer')
</body>
</html>
