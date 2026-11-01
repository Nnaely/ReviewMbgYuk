@php
    $currentUser = auth()->user();
    $dashboardRoute = $currentUser?->isPelapor()
        ? 'area.pelapor'
        : ($currentUser?->isPemantau() ? 'area.pemantau' : 'area.koordinator');
@endphp

<header class="landing-header app-header">
    <details class="landing-menu">
        <summary aria-label="Buka menu">
            <span></span>
            <span></span>
            <span></span>
        </summary>
        <nav aria-label="Navigasi utama">
            <a href="{{ route('beranda') }}">Beranda</a>
            @auth
                <a href="{{ route($dashboardRoute) }}">Dasbor</a>
                @if ($currentUser->isPelapor())
                    <a href="{{ route('reports.index') }}">Tulis ulasan</a>
                @else
                    <a href="{{ route('reports.inbox') }}">Laporan masuk</a>
                    <a href="{{ route('reports.summary') }}">Rekap laporan</a>
                    <a href="{{ route('distributions.index') }}">Distribusi</a>
                @endif
                @if ($currentUser->isKoordinator())
                    <a href="{{ route('schools.index') }}">Data sekolah</a>
                @endif
            @else
                <a href="{{ route('login') }}">Masuk</a>
            @endauth
        </nav>
    </details>

    <a class="landing-wordmark" href="{{ route('beranda') }}" aria-label="ReviewMbgYuk, beranda">
        <span class="landing-mark" aria-hidden="true"><i></i><i></i><i></i></span>
        <span>ReviewMbgYuk!</span>
    </a>

    @auth
        <a class="landing-access" href="{{ route($dashboardRoute) }}">Dasbor <span aria-hidden="true">↗</span></a>
    @else
        <a class="landing-access" href="{{ route('login') }}">Masuk <span aria-hidden="true">↗</span></a>
    @endauth
</header>
