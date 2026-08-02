@extends('layouts.admin-app')
@php $activeMenu = 'antrianPaket'; @endphp

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-text">Rekap Per Buku Pelajaran</h2>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
    <form action="{{ route('admin.antrian-paket.rekap') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat Kelas</label>
            <select name="tingkat_kelas" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                <option value="">Semua Tingkat</option>
                <option value="X" {{ $tingkatKelas == 'X' ? 'selected' : '' }}>X</option>
                <option value="XI IPA" {{ $tingkatKelas == 'XI IPA' ? 'selected' : '' }}>XI IPA</option>
                <option value="XI IPS" {{ $tingkatKelas == 'XI IPS' ? 'selected' : '' }}>XI IPS</option>
                <option value="XII IPA" {{ $tingkatKelas == 'XII IPA' ? 'selected' : '' }}>XII IPA</option>
                <option value="XII IPS" {{ $tingkatKelas == 'XII IPS' ? 'selected' : '' }}>XII IPS</option>
            </select>
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
            <a href="{{ route('admin.antrian-paket.rekap') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 text-center">Reset</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
    <span class="text-sm font-medium text-gray-500">Total Seluruh Buku Dipinjam</span>
    <span class="text-2xl font-bold text-blue-600 block mt-1">{{ $totalSeluruhBuku }}</span>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-text">
            <thead class="text-xs uppercase bg-gray-100 text-text">
                <tr>
                    <th class="px-6 py-4">No</th>
                    <th class="px-6 py-4">Nama Buku</th>
                    <th class="px-6 py-4">Tingkat Kelas</th>
                    <th class="px-6 py-4">Jumlah Dipinjam</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekapBuku as $key => $item)
                <tr class="border-b">
                    <td class="px-6 py-4">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4">{{ $item->bukuPaketMapel->nama_buku ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $item->bukuPaketMapel->tingkat_kelas ?? '-' }}</td>
                    <td class="px-6 py-4 font-bold">{{ $item->total_dipinjam }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center">Tidak ada data rekap.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
