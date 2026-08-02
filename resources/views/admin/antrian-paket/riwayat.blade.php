@extends('layouts.admin-app')
@php $activeMenu = 'antrianPaket'; @endphp

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-text">Rekap Data Peminjaman Buku Paket</h2>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <span class="text-sm font-medium text-gray-500">Total Peminjam</span>
        <span class="text-2xl font-bold text-blue-600 block mt-1">{{ $totalPeminjam }}</span>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <span class="text-sm font-medium text-gray-500">Total Buku Dipinjam</span>
        <span class="text-2xl font-bold text-green-600 block mt-1">{{ $totalBukuDipinjam }}</span>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <span class="text-sm font-medium text-gray-500">Buku Terpopuler</span>
        <span class="text-2xl font-bold text-purple-600 block mt-1">{{ $bukuPopuler->first()->bukuPaketMapel->nama_buku ?? '-' }}</span>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
    <form action="{{ route('admin.antrian-paket.riwayat') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Cari (Nama/NISN)</label>
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Nama / NISN..." class="block w-full rounded-md bg-white px-3 py-2 text-sm text-text border border-gray-300 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-600">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Filter Kelas</label>
            <select name="kelas" class="block w-full rounded-md bg-white px-3 py-2 text-sm text-text border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600">
                <option value="">-- Semua Kelas --</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k->id }}" {{ ($kelas == $k->id || $kelas == $k->nama_kelas) ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tampilkan</label>
            <select name="per_page" class="block w-full rounded-md bg-white px-3 py-2 text-sm text-text border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600">
                <option value="10" {{ ($perPage == 10) ? 'selected' : '' }}>10 Data</option>
                <option value="50" {{ ($perPage == 50) ? 'selected' : '' }}>50 Data</option>
                <option value="100" {{ ($perPage == 100) ? 'selected' : '' }}>100 Data</option>
                <option value="semua" {{ ($perPage == 'semua' || $perPage == 'all') ? 'selected' : '' }}>Semua Data</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Dari Tanggal</label>
            <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai ?? '' }}" class="block w-full rounded-md bg-white px-3 py-2 text-sm text-text border border-gray-300 focus:ring-2 focus:ring-blue-600">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Sampai Tanggal</label>
            <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai ?? '' }}" class="block w-full rounded-md bg-white px-3 py-2 text-sm text-text border border-gray-300 focus:ring-2 focus:ring-blue-600">
        </div>
        <div class="flex space-x-2">
            <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-md hover:bg-blue-800 text-sm font-medium w-full transition">Filter</button>
            <a href="{{ route('admin.antrian-paket.riwayat') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 text-center text-sm font-medium w-full transition">Reset</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-text">
            <thead class="text-xs uppercase bg-gray-100 text-text">
                <tr>
                    <th class="px-6 py-4">No</th>
                    <th class="px-6 py-4">Nama</th>
                    <th class="px-6 py-4">NISN</th>
                    <th class="px-6 py-4">Kelas</th>
                    <th class="px-6 py-4">Buku Dipinjam</th>
                    <th class="px-6 py-4 text-center">Jumlah</th>
                    <th class="px-6 py-4">Tgl Pinjam</th>
                    <th class="px-6 py-4">Tgl Kembali</th>
                    <th class="px-6 py-4 text-center">Status / Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $key => $item)
                <tr class="border-b hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4 font-medium">{{ $riwayat->firstItem() + $key }}</td>
                    <td class="px-6 py-4 font-bold text-gray-800">{{ $item->anggota->user->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $item->anggota->nisn ?? '-' }}</td>
                    <td class="px-6 py-4 font-semibold text-blue-700">{{ $item->anggota->kelas->nama_kelas ?? '-' }}</td>
                    <td class="px-6 py-4 max-w-xs">
                        {{ $item->detailPeminjamanBukuPaket->pluck('bukuPaketMapel.nama_buku')->join(', ') ?: '-' }}
                    </td>
                    <td class="px-6 py-4 text-center font-bold">
                        {{ $item->detailPeminjamanBukuPaket->count() }}
                    </td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d-m-Y') }}</td>
                    <td class="px-6 py-4">
                        @if($item->tanggal_kembali)
                            <span class="text-green-700 font-semibold">{{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d-m-Y') }}</span>
                        @else
                            <span class="text-gray-400 italic">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($item->status == 'dipinjam')
                            <div class="flex items-center justify-center space-x-2">
                                <span class="px-2.5 py-1 text-xs rounded-full bg-blue-100 text-blue-800 font-medium">Dipinjam</span>
                                <form id="form-kembali-{{ $item->id }}" action="{{ route('admin.antrian-paket.kembali', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="button" onclick="confirmKembali({{ $item->id }}, '{{ addslashes($item->anggota->user->name ?? '') }}')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1 rounded text-xs font-semibold shadow-sm transition inline-flex items-center gap-1" style="background-color: #059669; color: #ffffff;">
                                        Kembali
                                    </button>
                                </form>
                            </div>
                        @elseif($item->status == 'dikembalikan')
                            <div class="text-center">
                                <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold inline-block">
                                    Dikembalikan
                                </span>
                                @if($item->tanggal_kembali)
                                    <div class="text-[11px] text-green-700 mt-1">pada {{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d-m-Y') }}</div>
                                @endif
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">Tidak ada riwayat peminjaman.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($riwayat->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $riwayat->links('pagination::tailwind') }}
    </div>
    @endif
</div>

<script>
function confirmKembali(id, namaSiswa) {
    Swal.fire({
        title: 'Konfirmasi Pengembalian Buku',
        text: `Tandai buku paket atas nama "${namaSiswa}" telah dikembalikan hari ini?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Sudah Dikembalikan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-kembali-' + id).submit();
        }
    });
}
</script>
@endsection

