<footer class="landing-footer app-footer">
    <nav aria-label="Navigasi footer">
        <a href="{{ route('beranda') }}">Beranda</a>
        @auth
            @if (auth()->user()->isPelapor())
                <a href="{{ route('reports.index') }}">Tulis ulasan</a>
            @else
                <a href="{{ route('reports.inbox') }}">Laporan masuk</a>
            @endif
        @else
            <a href="{{ route('login') }}">Masuk</a>
        @endauth
    </nav>
    <div class="landing-footer-bottom">
        <span>© {{ date('Y') }} ReviewMbgYuk!</span>
        <span>Pantau · Laporkan · Tindak lanjuti</span>
    </div>
</footer>
