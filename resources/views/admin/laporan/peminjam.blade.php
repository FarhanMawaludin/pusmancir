@extends('layouts.admin-app')

@section('content')
    @php
        $f = fn($n) => number_format($n, 0, ',', '.');
        $tgl = fn($t) => $t ? \Carbon\Carbon::parse($t)->format('d-m-Y') : '-';
        $filter = request()->only(['tanggal_mulai', 'tanggal_selesai', 'status', 'jenis', 'kelas_id', 'search']);
        $badge = [
            'sedang' => ['Sedang Dipinjam', '#dbeafe', '#1e40af'],
            'terlambat' => ['⚠ Terlambat', '#fee2e2', '#991b1b'],
            'selesai' => ['✓ Selesai', '#dcfce7', '#166534'],
        ];
        $inputClass = 'border border-gray-300 rounded px-3 py-2 text-sm bg-white';
    @endphp

    <div class="mb-4">
        <h1 class="text-2xl font-bold text-text">Laporan Data Peminjam Buku</h1>
        <p class="text-sm text-gray-500">Nama peminjam beserta buku yang sedang maupun sudah selesai dipinjam (koleksi non paket)</p>
    </div>

    <form method="GET" action="{{ route('admin.laporan.peminjam') }}" class="p-4 bg-white border border-gray-200 rounded mb-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1" for="tanggal_mulai">Tgl pinjam dari</label>
                <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="{{ $inputClass }} w-full">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1" for="tanggal_selesai">Sampai</label>
                <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="{{ $inputClass }} w-full">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1" for="status">Status</label>
                <select id="status" name="status" class="{{ $inputClass }} w-full">
                    <option value="">Sedang &amp; Selesai</option>
                    <option value="sedang" {{ request('status') === 'sedang' ? 'selected' : '' }}>Sedang Dipinjam</option>
                    <option value="terlambat" {{ request('status') === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai Dipinjam</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1" for="jenis">Peminjam</label>
                <select id="jenis" name="jenis" class="{{ $inputClass }} w-full">
                    <option value="">Siswa &amp; Guru</option>
                    <option value="siswa" {{ request('jenis') === 'siswa' ? 'selected' : '' }}>Siswa</option>
                    <option value="guru" {{ request('jenis') === 'guru' ? 'selected' : '' }}>Guru</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1" for="kelas_id">Kelas</label>
                <select id="kelas_id" name="kelas_id" class="{{ $inputClass }} w-full">
                    <option value="">Semua Kelas</option>
                    @foreach ($listKelas as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1" for="search">Cari</label>
                <input type="search" id="search" name="search" value="{{ request('search') }}" placeholder="Nama / NISN / NIP / judul" class="{{ $inputClass }} w-full">
            </div>
        </div>
        <div class="flex flex-wrap gap-2 mt-3">
            <button type="submit" class="px-4 py-2 rounded text-sm text-white" style="background-color:#1d4ed8">Tampilkan</button>
            <a href="{{ route('admin.laporan.peminjam') }}" class="px-4 py-2 rounded text-sm bg-gray-200 text-gray-700">Reset</a>
            <div class="flex gap-2 ml-auto">
                <a href="{{ route('admin.laporan.peminjam.pdf', $filter) }}" target="_blank"
                    class="px-4 py-2 rounded text-sm text-white" style="background-color:#dc2626">Cetak PDF (A4)</a>
                <a href="{{ route('admin.laporan.peminjam.pdf', $filter + ['download' => 1]) }}"
                    class="px-4 py-2 rounded text-sm text-white" style="background-color:#991b1b">Unduh PDF</a>
            </div>
        </div>
    </form>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
        @foreach ([
            ['Peminjam', $ringkasan['peminjam']],
            ['Judul', $ringkasan['judul']],
            ['Eksemplar', $ringkasan['eksemplar']],
            ['Sedang dipinjam', $ringkasan['sedang']],
            ['Terlambat', $ringkasan['terlambat']],
            ['Selesai dipinjam', $ringkasan['selesai']],
        ] as [$label, $nilai])
            <div class="p-4 bg-white border border-gray-200 rounded">
                <div class="text-2xl font-bold text-gray-900">{{ $f($nilai) }}</div>
                <div class="text-sm text-gray-600">{{ $label }}</div>
            </div>
        @endforeach
    </div>

    <div class="overflow-x-auto relative rounded border border-gray-200 bg-white">
        <table class="min-w-full text-sm text-left text-text">
            <thead class="text-xs uppercase bg-gray-100 text-text">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Nama Peminjam</th>
                    <th class="px-4 py-3">Kelas / Identitas</th>
                    <th class="px-4 py-3">Judul Buku</th>
                    <th class="px-4 py-3">No. Induk</th>
                    <th class="px-4 py-3">Tgl Pinjam</th>
                    <th class="px-4 py-3">Batas Kembali</th>
                    <th class="px-4 py-3">Tgl Kembali</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($details as $i => $d)
                    @php
                        $a = $d->peminjaman->anggota;
                        $st = \App\Http\Controllers\LaporanController::statusDetail($d);
                    @endphp
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-3">{{ $details->firstItem() + $i }}</td>
                        <td class="px-4 py-3 font-medium">{{ $a->user->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if ($a && $a->isGuru())
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#ede9fe;color:#6d28d9">Guru</span>
                            @else
                                {{ $a->kelas->nama_kelas ?? '-' }}
                            @endif
                            <div class="text-xs text-gray-400">{{ $a->label_identitas ?? 'NISN' }}: {{ $a->nisn ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            {{ $d->eksemplar->inventori->judul_buku ?? '-' }}
                            <div class="text-xs text-gray-400">{{ $d->eksemplar->inventori->pengarang ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $d->eksemplar->no_induk ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $tgl($d->peminjaman->tanggal_pinjam) }}</td>
                        <td class="px-4 py-3">{{ $tgl($d->peminjaman->tanggal_kembali) }}</td>
                        <td class="px-4 py-3">{{ $tgl($d->tanggal_kembali_asli) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold whitespace-nowrap"
                                style="background:{{ $badge[$st][1] }};color:{{ $badge[$st][2] }}">{{ $badge[$st][0] }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="px-4 py-6 text-center text-gray-500">Tidak ada data peminjaman ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $details->links('pagination::tailwind') }}
        </div>
    </div>
@endsection
