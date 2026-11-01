<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Ruang bersama untuk memantau dan menindaklanjuti distribusi Makan Bergizi Gratis di sekolah.">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>ReviewMbgYuk! — Pemantauan MBG</title>
</head>
<body class="landing-page">
    @php
        $dashboardRoute = auth()->check()
            ? (auth()->user()->isPelapor()
                ? 'area.pelapor'
                : (auth()->user()->isPemantau() ? 'area.pemantau' : 'area.koordinator'))
            : 'login';
        $accessLabel = auth()->check() ? 'Dasbor' : 'Masuk';
    @endphp

    <main class="landing-shell">
        <header class="landing-header">
            <details class="landing-menu">
                <summary aria-label="Buka menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </summary>
                <nav aria-label="Navigasi utama">
                    <a href="#tentang">Tentang</a>
                    <a href="#cerita">Cara kerja</a>
                    <a href="{{ route($dashboardRoute) }}">{{ $accessLabel }}</a>
                </nav>
            </details>

            <a class="landing-wordmark" href="{{ url('/') }}" aria-label="ReviewMbgYuk, beranda">
                <span class="landing-mark" aria-hidden="true"><i></i><i></i><i></i></span>
                <span>ReviewMbgYuk!</span>
            </a>

            <a class="landing-access" href="{{ route($dashboardRoute) }}">{{ $accessLabel }} <span aria-hidden="true">↗</span></a>
        </header>

        <section class="landing-intro" id="tentang">
            <img class="landing-hero-image" src="{{ asset('images/mbg-prasmanan-bgn.jpg') }}" alt="Siswa menikmati hidangan Makan Bergizi Gratis di sekolah" width="3118" height="1754" fetchpriority="high">
            <div class="landing-intro-copy">
                <p class="landing-eyebrow">Makan Bergizi Gratis · Pemantauan Bersama</p>
                <h1>Setiap porsi, layak dipantau.</h1>
                <p>Ruang bersama untuk melaporkan, memeriksa, dan menindaklanjuti distribusi makanan bergizi di sekolah.</p>
            </div>
            <a class="landing-photo-credit" href="https://www.bgn.go.id/news/foto/mbg-prasmanan" target="_blank" rel="noopener noreferrer">Foto: Badan Gizi Nasional · Lihat sumber</a>
        </section>

        <section class="landing-stories" id="cerita" aria-label="Kegiatan Makan Bergizi Gratis">
            <article class="landing-story">
                <a class="landing-story-image" href="https://www.bgn.go.id/news/foto/mbg-perdana-di-batang" target="_blank" rel="noopener noreferrer" aria-label="Lihat sumber foto persiapan SPPG">
                    <img src="{{ asset('images/pemeriksaan-sppg-bgn.jpg') }}" alt="Kegiatan pemantauan di dapur SPPG" width="1280" height="720" loading="lazy">
                </a>
                <h2>Dari dapur SPPG</h2>
                <p>Pemantauan dimulai dari proses penyiapan makanan.</p>
                <a class="landing-story-credit" href="https://www.bgn.go.id/news/foto/kepala-bgn-kunjungi-sppg-kebayoran-lama" target="_blank" rel="noopener noreferrer">Foto: Badan Gizi Nasional</a>
            </article>

            <article class="landing-story">
                <a class="landing-story-image" href="https://www.bgn.go.id/news/foto/mbg-di-kabupaten-bogor" target="_blank" rel="noopener noreferrer" aria-label="Lihat sumber foto siswa menerima MBG">
                    <img src="{{ asset('images/persiapan-sppg-bgn.jpg') }}" alt="Kegiatan penyiapan dan distribusi makanan bergizi" width="3916" height="2203" loading="lazy">
                </a>
                <h2>Persiapan MBG</h2>
                <p>Petugas SPPG menyiapkan makanan untuk sekolah.</p>
                <a class="landing-story-credit" href="https://www.bgn.go.id/news/foto/mbg-perdana-di-batang" target="_blank" rel="noopener noreferrer">Foto: Badan Gizi Nasional</a>
            </article>

            <article class="landing-story">
                <a class="landing-story-image" href="https://www.bgn.go.id/news/foto/kepala-bgn-kunjungi-sppg-kebayoran-lama" target="_blank" rel="noopener noreferrer" aria-label="Lihat sumber foto pemeriksaan dapur SPPG">
                    <img src="{{ asset('images/siswa-menerima-mbg-bgn.jpg') }}" alt="Seorang siswa menikmati makanan bergizi di sekolah" width="3672" height="2424" loading="lazy">
                </a>
                <h2>Diterima di sekolah</h2>
                <p>Pengalaman siswa menjadi bagian dari pemantauan.</p>
                <a class="landing-story-credit" href="https://www.bgn.go.id/news/foto/mbg-di-kabupaten-bogor" target="_blank" rel="noopener noreferrer">Foto: Badan Gizi Nasional</a>
            </article>
        </section>

        <section class="landing-signoff" aria-label="ReviewMbgYuk">
            <span class="landing-signoff-mark" aria-hidden="true"><i></i><i></i><i></i></span>
            <p class="landing-eyebrow">Pantau · Laporkan · Tindak lanjuti</p>
            <h2>ReviewMbgYuk!</h2>
            <p>Untuk distribusi MBG yang lebih terbuka dan terpantau.</p>
        </section>

        <footer class="landing-footer">
            <nav aria-label="Navigasi footer">
                <a href="#tentang">Tentang</a>
                <a href="#cerita">Kegiatan MBG</a>
                <a href="{{ route($dashboardRoute) }}">{{ $accessLabel }}</a>
            </nav>
            <div class="landing-footer-bottom">
                <span>© {{ date('Y') }} ReviewMbgYuk!</span>
                <a href="https://www.bgn.go.id/" target="_blank" rel="noopener noreferrer">Sumber foto: Badan Gizi Nasional</a>
            </div>
        </footer>
    </main>
</body>
</html>
