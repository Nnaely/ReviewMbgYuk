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
                <p class="landing-description">Ruang bersama untuk melaporkan, memeriksa, dan menindaklanjuti distribusi makanan bergizi di sekolah.</p>
                @if (auth()->guest() || auth()->user()->isPelapor())
                    <a class="landing-cta" href="{{ route('reports.index') }}">{{ auth()->guest() ? 'Masuk untuk menulis ulasan' : 'Tulis ulasan' }} <span aria-hidden="true">↗</span></a>
                    <p class="landing-cta-note">Ulasan dikirim sebagai laporan dan ditinjau oleh Pemantau.</p>
                @endif
            </div>
            <a class="landing-photo-credit" href="https://www.bgn.go.id/news/foto/mbg-prasmanan" target="_blank" rel="noopener noreferrer">Foto: Badan Gizi Nasional · Lihat sumber</a>
        </section>

        <section class="landing-numbers" aria-labelledby="angka-mbg">
            <div class="landing-numbers-heading">
                <p class="landing-eyebrow">Sorotan program</p>
                <h2 id="angka-mbg">MBG dalam angka</h2>
                <p class="landing-numbers-date">Data BGN per 20 Oktober 2026</p>
            </div>
            <div class="landing-stats">
                <article class="landing-stat">
                    <strong>57.491.334</strong>
                    <span>Penerima manfaat</span>
                </article>
                <article class="landing-stat">
                    <strong>1.027.998</strong>
                    <span>Tenaga kerja</span>
                </article>
                <article class="landing-stat">
                    <strong>26.385</strong>
                    <span>SPPG beroperasi</span>
                </article>
            </div>
            <a class="landing-data-source" href="https://www.bgn.go.id/news/berita/mbg-20-program-berskala-besar-dengan-standar-lebih-benar" target="_blank" rel="noopener noreferrer">Sumber: Badan Gizi Nasional <span aria-hidden="true">↗</span></a>
        </section>

        <section class="landing-stories" id="cerita" aria-label="Kegiatan Makan Bergizi Gratis">
            <div class="landing-stories-heading">
                <p class="landing-eyebrow">Di balik setiap porsi</p>
                <h2>Dari dapur hingga ke sekolah.</h2>
            </div>
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

        <aside class="landing-fact">
            <div class="landing-fact-marker" aria-hidden="true">!</div>
            <div>
                <p class="landing-eyebrow">Tahukah kamu?</p>
                <h2>Sebelum dibagikan, makanan diperiksa di sekolah.</h2>
                <p>PIC sekolah memeriksa sampel dengan melihat, mencium, dan mencicipi makanan sebelum disajikan.</p>
            </div>
            <a href="https://www.bgn.go.id/news/foto/bgn-perketat-pengawasan-keamanan-pangan-program-mbg-di-sekolah" target="_blank" rel="noopener noreferrer">Baca sumber BGN <span aria-hidden="true">↗</span></a>
        </aside>

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
