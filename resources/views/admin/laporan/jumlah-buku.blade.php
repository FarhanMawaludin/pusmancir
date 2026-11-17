@extends('layouts.admin-app')

@section('content')
    @php
        $f = fn($n) => number_format($n, 0, ',', '.');
        $query = array_filter(['tahun' => $tahun ?? 'semua', 'bulan' => $bulan]);
    @endphp

    <div class="mb-4">
        <h1 class="text-2xl font-bold text-text">Laporan Jumlah Buku Dipinjam</h1>
        <p class="text-sm text-gray-500">Jumlah judul &amp; eksemplar koleksi non paket yang dipinjam &mdash; periode <strong>{{ $periode }}</strong></p>
    </div>

    <form method="GET" action="{{ route('admin.laporan.jumlah-buku') }}" class="flex flex-wrap items-end gap-3 mb-5 p-4 bg-white border border-gray-200 rounded">
        <div>
            <label for="tahun" class="block text-xs font-medium text-gray-600 mb-1">Tahun</label>
            <select name="tahun" id="tahun" class="border border-gray-300 rounded px-3 py-2 text-sm bg-white" onchange="document.getElementById('bulan').disabled = this.value === 'semua'">
                <option value="semua" {{ !$tahun ? 'selected' : '' }}>Semua Tahun</option>
                @foreach ($daftarTahun as $t)
                    <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="bulan" class="block text-xs font-medium text-gray-600 mb-1">Bulan</label>
            <select name="bulan" id="bulan" class="border border-gray-300 rounded px-3 py-2 text-sm bg-white" {{ !$tahun ? 'disabled' : '' }}>
                <option value="">Semua Bulan</option>
                @foreach ($daftarBulan as $num => $nama)
                    <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 rounded text-sm text-white" style="background-color:#1d4ed8">Tampilkan</button>
        <div class="flex gap-2 ml-auto">
            <a href="{{ route('admin.laporan.jumlah-buku.pdf', $query) }}" target="_blank"
                class="px-4 py-2 rounded text-sm text-white" style="background-color:#dc2626">Cetak PDF (A4)</a>
            <a href="{{ route('admin.laporan.jumlah-buku.pdf', $query + ['download' => 1]) }}"
                class="px-4 py-2 rounded text-sm text-white" style="background-color:#991b1b">Unduh PDF</a>
        </div>
    </form>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        @foreach ([
            ['Judul dipinjam', $ringkasan['judul_total'], $f($ringkasan['eks_total']) . ' eksemplar'],
            ['Judul sedang dipinjam', $ringkasan['judul_aktif'], $f($ringkasan['eks_aktif']) . ' eksemplar'],
            ['Judul selesai dipinjam', $ringkasan['judul_selesai'], $f($ringkasan['eks_selesai']) . ' eksemplar'],
            ['Eksemplar terlambat', $ringkasan['eks_terlambat'], 'per hari ini'],
            ['Transaksi', $ringkasan['transaksi'], 'peminjaman'],
            ['Peminjam', $ringkasan['peminjam'], 'anggota berbeda'],
        ] as [$label, $nilai, $sub])
            <div class="p-4 bg-white border border-gray-200 rounded">
                <div class="text-2xl font-bold text-gray-900">{{ $f($nilai) }}</div>
                <div class="text-sm text-gray-600">{{ $label }}</div>
                <div class="text-xs text-gray-400 mt-1">{{ $sub }}</div>
            </div>
        @endforeach
    </div>

    {{-- Rekap per periode --}}
    <div class="bg-white border border-gray-200 rounded mb-6 overflow-x-auto">
        <div class="px-4 pt-4 pb-2 font-semibold text-gray-700">Rekap per {{ $granularitas }}</div>
        <table class="min-w-full text-sm text-left text-text">
            <thead class="text-xs uppercase bg-gray-100">
                <tr>
                    <th rowspan="2" class="px-4 py-2">{{ $granularitas }}</th>
                    <th colspan="3" class="px-4 py-2 text-center border-l border-gray-200">Judul</th>
                    <th colspan="3" class="px-4 py-2 text-center border-l border-gray-200">Eksemplar</th>
                </tr>
                <tr>
                    <th class="px-4 py-2 text-right border-l border-gray-200">Dipinjam</th>
                    <th class="px-4 py-2 text-right">Sedang</th>
                    <th class="px-4 py-2 text-right">Selesai</th>
                    <th class="px-4 py-2 text-right border-l border-gray-200">Dipinjam</th>
                    <th class="px-4 py-2 text-right">Sedang</th>
                    <th class="px-4 py-2 text-right">Selesai</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rekap as $r)
                    <tr class="border-b border-gray-100 {{ $r['eks_total'] === 0 ? 'text-gray-400' : '' }}">
                        <td class="px-4 py-2">{{ $r['label'] }}</td>
                        <td class="px-4 py-2 text-right border-l border-gray-100 font-semibold">{{ $f($r['judul_total']) }}</td>
                        <td class="px-4 py-2 text-right">{{ $f($r['judul_aktif']) }}</td>
                        <td class="px-4 py-2 text-right">{{ $f($r['judul_selesai']) }}</td>
                        <td class="px-4 py-2 text-right border-l border-gray-100 font-semibold">{{ $f($r['eks_total']) }}</td>
                        <td class="px-4 py-2 text-right">{{ $f($r['eks_aktif']) }}</td>
                        <td class="px-4 py-2 text-right">{{ $f($r['eks_selesai']) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Tidak ada data peminjaman pada periode ini.</td></tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 font-bold">
                <tr>
                    <td class="px-4 py-2">Jumlah</td>
                    <td class="px-4 py-2 text-right border-l border-gray-200">{{ $f($ringkasan['judul_total']) }}</td>
                    <td class="px-4 py-2 text-right">{{ $f($ringkasan['judul_aktif']) }}</td>
                    <td class="px-4 py-2 text-right">{{ $f($ringkasan['judul_selesai']) }}</td>
                    <td class="px-4 py-2 text-right border-l border-gray-200">{{ $f($ringkasan['eks_total']) }}</td>
                    <td class="px-4 py-2 text-right">{{ $f($ringkasan['eks_aktif']) }}</td>
                    <td class="px-4 py-2 text-right">{{ $f($ringkasan['eks_selesai']) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <div class="bg-white border border-gray-200 rounded overflow-x-auto">
            <div class="px-4 pt-4 pb-2 font-semibold text-gray-700">Per Kategori Buku</div>
            <table class="min-w-full text-sm text-left text-text">
                <thead class="text-xs uppercase bg-gray-100">
                    <tr><th class="px-4 py-2">Kategori</th><th class="px-4 py-2 text-right">Judul</th><th class="px-4 py-2 text-right">Eksemplar</th></tr>
                </thead>
                <tbody>
                    @forelse ($perKategori as $k)
                        <tr class="border-b border-gray-100"><td class="px-4 py-2">{{ $k->kategori }}</td><td class="px-4 py-2 text-right">{{ $f($k->judul) }}</td><td class="px-4 py-2 text-right">{{ $f($k->eksemplar) }}</td></tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-4 text-center text-gray-400">-</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bg-white border border-gray-200 rounded overflow-x-auto">
            <div class="px-4 pt-4 pb-2 font-semibold text-gray-700">Per Kelas / Guru</div>
            <table class="min-w-full text-sm text-left text-text">
                <thead class="text-xs uppercase bg-gray-100">
                    <tr><th class="px-4 py-2">Kelompok</th><th class="px-4 py-2 text-right">Peminjam</th><th class="px-4 py-2 text-right">Judul</th><th class="px-4 py-2 text-right">Eksemplar</th></tr>
                </thead>
                <tbody>
                    @forelse ($perKelompok as $k)
                        <tr class="border-b border-gray-100"><td class="px-4 py-2">{{ $k->kelompok }}</td><td class="px-4 py-2 text-right">{{ $f($k->peminjam) }}</td><td class="px-4 py-2 text-right">{{ $f($k->judul) }}</td><td class="px-4 py-2 text-right">{{ $f($k->eksemplar) }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-4 text-center text-gray-400">-</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded overflow-x-auto">
        <div class="px-4 pt-4 pb-2 font-semibold text-gray-700">Daftar Judul Buku yang Dipinjam ({{ $f($perJudul->count()) }} judul)</div>
        <table class="min-w-full text-sm text-left text-text">
            <thead class="text-xs uppercase bg-gray-100">
                <tr>
                    <th class="px-4 py-2">No</th>
                    <th class="px-4 py-2">Judul Buku</th>
                    <th class="px-4 py-2">Pengarang</th>
                    <th class="px-4 py-2">Kategori</th>
                    <th class="px-4 py-2 text-right">Dipinjam (eks)</th>
                    <th class="px-4 py-2 text-right">Sedang</th>
                    <th class="px-4 py-2 text-right">Selesai</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($perJudul as $i => $j)
                    <tr class="border-b border-gray-100">
                        <td class="px-4 py-2">{{ $i + 1 }}</td>
                        <td class="px-4 py-2 font-medium">{{ $j->judul_buku }}</td>
                        <td class="px-4 py-2">{{ $j->pengarang }}</td>
                        <td class="px-4 py-2">{{ $j->kategori }}</td>
                        <td class="px-4 py-2 text-right">{{ $f($j->kali_dipinjam) }}</td>
                        <td class="px-4 py-2 text-right">{{ $f($j->sedang_dipinjam) }}</td>
                        <td class="px-4 py-2 text-right">{{ $f($j->sudah_kembali) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Tidak ada data peminjaman pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
