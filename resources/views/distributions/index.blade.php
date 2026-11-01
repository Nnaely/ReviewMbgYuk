<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Distribusi Makanan | MBG Komunitas</title>
</head>
<body>
    <main>
        <h1>Daftar Distribusi Makanan</h1>
        <p>Jadwal distribusi makanan untuk sekolah.</p>

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

        <h2>Cari dan Filter Distribusi</h2>
        <form method="GET" action="{{ route('distributions.index') }}">
            <label for="search">Cari sekolah atau menu</label>
            <input id="search" name="search" type="search" value="{{ $filters['search'] ?? '' }}" maxlength="100">

            <label for="school_filter">Sekolah</label>
            <select id="school_filter" name="school_id">
                <option value="">Semua sekolah</option>
                @foreach ($schools as $school)
                    <option value="{{ $school->id }}" @selected((string) ($filters['school_id'] ?? '') === (string) $school->id)>
                        {{ $school->name }}
                    </option>
                @endforeach
            </select>

            <label for="date_from">Dari tanggal</label>
            <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}">

            <label for="date_to">Sampai tanggal</label>
            <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}">

            <button type="submit">Terapkan filter</button>
            <a href="{{ route('distributions.index') }}">Hapus filter</a>
        </form>

        @if (auth()->user()->isKoordinator())
            <h2>Tambah Jadwal Distribusi</h2>
            <form method="POST" action="{{ route('distributions.store') }}">
                @csrf
                <label for="school_id">Sekolah</label>
                <select id="school_id" name="school_id" required>
                    <option value="">Pilih sekolah</option>
                    @foreach ($schools as $school)
                        <option value="{{ $school->id }}" @selected(old('school_id') == $school->id)>
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>

                <label for="distribution_date">Tanggal distribusi</label>
                <input id="distribution_date" name="distribution_date" type="date" value="{{ old('distribution_date') }}" required>

                <label for="menu_description">Keterangan menu</label>
                <textarea id="menu_description" name="menu_description" rows="4" minlength="5" maxlength="2000" required>{{ old('menu_description') }}</textarea>

                <button type="submit">Tambah distribusi</button>
            </form>
        @endif

        <table>
            <thead>
                <tr>
                    <th>Sekolah</th>
                    <th>Tanggal Distribusi</th>
                    <th>Keterangan Menu</th>
                    @if (auth()->user()->isKoordinator())
                        <th>Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($distributions as $item)
                    <tr>
                        <td>{{ $item->school->name }}</td>
                        <td>{{ $item->distribution_date->format('d-m-Y') }}</td>
                        <td>{{ $item->menu_description }}</td>
                        @if (auth()->user()->isKoordinator())
                            <td>
                                <a href="{{ route('distributions.edit', $item) }}">Edit jadwal</a>
                                @if ((int) $item->reports_count === 0)
                                    <form method="POST" action="{{ route('distributions.destroy', $item) }}" onsubmit="return confirm('Hapus jadwal distribusi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">Hapus</button>
                                    </form>
                                @else
                                    <p>Dipakai {{ $item->reports_count }} laporan</p>
                                @endif
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isKoordinator() ? 4 : 3 }}">Belum ada data distribusi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $distributions->links() }}
    </main>
</body>
</html>
