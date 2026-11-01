<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>Detail Laporan | ReviewMbgYuk!</title>
</head>
<body>
    <main>
        <a href="{{ route('reports.inbox') }}">Kembali ke laporan masuk</a>
        <h1>Detail Laporan</h1>

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

        <dl>
            <dt>Sekolah</dt>
            <dd>{{ $report->school->name }}</dd>

            @if ($report->distribution)
                <dt>Jadwal distribusi</dt>
                <dd>{{ $report->distribution->distribution_date->format('d-m-Y') }} — {{ $report->distribution->menu_description }}</dd>
            @endif

            <dt>Pelapor</dt>
            <dd>{{ $report->reporter->name }}</dd>

            <dt>Waktu laporan</dt>
            <dd>{{ $report->created_at->format('d-m-Y H:i') }}</dd>

            <dt>Status</dt>
            <dd>{{ ucfirst($report->status) }}</dd>

            <dt>Catatan tindak lanjut</dt>
            <dd>{{ $report->follow_up_note ?: 'Belum ada catatan tindak lanjut.' }}</dd>

            <dt>Status penerusan</dt>
            <dd>{{ $report->needs_referral ? 'Perlu diteruskan' : 'Tidak perlu diteruskan' }}</dd>
            @if ($report->referred_at)
                <dt>Tujuan penerusan</dt>
                <dd>{{ str_replace('_', ' ', ucfirst($report->referral_target)) }}</dd>
                <dt>Catatan penerusan</dt>
                <dd>{{ $report->referral_note }}</dd>
                <dt>Dicatat oleh</dt>
                <dd>{{ $report->referrer?->name ?? 'Koordinator' }} · {{ $report->referred_at->format('d-m-Y H:i') }}</dd>
            @endif

            <dt>Isi laporan</dt>
            <dd>{{ $report->description }}</dd>

            @if ($report->photo_path)
                <dt>Foto bukti</dt>
                <dd>
                    @if (strtolower(pathinfo($report->photo_path, PATHINFO_EXTENSION)) === 'pdf')
                        <a href="{{ route('reports.photo', $report) }}">Lihat bukti PDF</a>
                    @else
                        <img src="{{ route('reports.photo', $report) }}" alt="Bukti laporan" width="480">
                    @endif
                </dd>
            @endif
        </dl>

        <h2>Riwayat tindak lanjut</h2>
        <ol>
            @forelse ($report->statusHistories as $history)
                <li>
                    <p>{{ $history->previous_status ? ucfirst($history->previous_status) : 'Dibuat' }} → {{ ucfirst($history->status) }}</p>
                    <p>{{ $history->note }}</p>
                    <small>{{ $history->updater->name }} · {{ $history->created_at->format('d-m-Y H:i') }}</small>
                </li>
            @empty
                <li>Riwayat belum tersedia.</li>
            @endforelse
        </ol>

        @if (auth()->user()->isPemantau())
            <h2>Pembaruan pemantau</h2>
            <form method="POST" action="{{ route('reports.update', $report) }}">
                @csrf
                @method('PATCH')
                <label for="status">Status berikutnya</label>
                <select id="status" name="status" required>
                    @php($statusOptions = [$report->status => ucfirst($report->status)])
                    @foreach (\App\Models\Report::nextStatuses($report->status) as $nextStatus)
                        @php($statusOptions[$nextStatus] = ucfirst($nextStatus))
                    @endforeach
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $report->status) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <label for="follow_up_note">Catatan tindak lanjut</label>
                <textarea id="follow_up_note" name="follow_up_note" rows="4" minlength="5" maxlength="2000" required>{{ old('follow_up_note', $report->follow_up_note) }}</textarea>
                @if (! $report->needs_referral)
                    <label>
                        <input type="checkbox" name="needs_referral" value="1" @checked(old('needs_referral'))>
                        Tandai untuk diteruskan
                    </label>
                @endif
                <button type="submit">Simpan pembaruan</button>
            </form>
        @elseif (auth()->user()->isKoordinator() && $report->needs_referral && ! $report->referred_at)
            <h2>Penerusan laporan</h2>
            <form method="POST" action="{{ route('reports.forward', $report) }}">
                @csrf
                @method('PATCH')
                <label for="referral_target">Tujuan penerusan</label>
                <select id="referral_target" name="referral_target" required>
                    @foreach (['sekolah' => 'Sekolah', 'sppg' => 'SPPG', 'fasilitas_kesehatan' => 'Fasilitas kesehatan', 'pihak_terkait_lain' => 'Pihak terkait lain'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('referral_target') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <label for="referral_note">Catatan penerusan</label>
                <textarea id="referral_note" name="referral_note" rows="4" minlength="5" maxlength="2000" required>{{ old('referral_note') }}</textarea>
                <button type="submit">Catat penerusan</button>
            </form>
        @endif
    </main>
</body>
</html>
