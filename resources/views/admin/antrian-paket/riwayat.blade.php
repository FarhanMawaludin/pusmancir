@extends('layouts.admin-app')
@php $activeMenu = 'antrianPaket'; @endphp

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-text">Riwayat Peminjaman Buku Paket</h2>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
    <form action="{{ route('admin.antrian-paket.riwayat') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cari (Nama/NISN)</label>
            <input type="text" name="search" value="{{ $search ?? '' }}" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text border border-gray-300 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
            <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai ?? '' }}" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text border border-gray-300 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
            <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai ?? '' }}" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text border border-gray-300 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
        </div>
        <div class="flex space-x-2">
            <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-md hover:bg-blue-800">Filter</button>
            <a href="{{ route('admin.antrian-paket.riwayat') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 text-center">Reset</a>
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
                    <th class="px-6 py-4">Tanggal Pinjam</th>
                    <th class="px-6 py-4">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $key => $item)
                <tr class="border-b">
                    <td class="px-6 py-4">{{ $riwayat->firstItem() + $key }}</td>
                    <td class="px-6 py-4">{{ $item->anggota->user->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $item->anggota->nisn ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $item->anggota->kelas->nama_kelas ?? '-' }}</td>
                    <td class="px-6 py-4">
                        {{ $item->detailPeminjamanBukuPaket->pluck('bukuPaketMapel.nama_buku')->join(', ') ?: '-' }}
                    </td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d-m-Y') }}</td>
                    <td class="px-6 py-4">
                        @if($item->status == 'dipinjam')
                            <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">Dipinjam</span>
                        @elseif($item->status == 'dikembalikan')
                            <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">Dikembalikan</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center">Tidak ada riwayat peminjaman.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4">
        {{ $riwayat->links('pagination::tailwind') }}
    </div>
</div>
@endsection
