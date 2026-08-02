@extends('layouts.admin-app')
@php $activeMenu = 'antrianPaket'; @endphp

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-text">Sistem Peminjaman Buku Paket Pelajaran v.2.0</h2>
    <a href="{{ route('admin.antrian-paket.exportPdf', ['tanggal' => $tanggal]) }}" target="_blank" class="bg-blue-700 text-white px-4 py-2 rounded-md hover:bg-blue-800">Print PDF</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.antrian-paket.index') }}" method="GET" id="filter-form">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Antrian</label>
            <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="document.getElementById('filter-form').submit()" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text border border-gray-300 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
        </form>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 flex flex-col justify-center">
        <span class="text-sm font-medium text-gray-500">Total Kuota</span>
        <span class="text-2xl font-bold text-text">{{ $setting ? $setting->kuota : 0 }}</span>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 flex flex-col justify-center">
        <span class="text-sm font-medium text-gray-500">Terisi</span>
        <span class="text-2xl font-bold text-text">{{ $terisi }} / {{ $setting ? $setting->kuota : 0 }}</span>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-text">
            <thead class="text-xs uppercase bg-gray-100 text-text">
                <tr>
                    <th class="px-6 py-4">No Antrian</th>
                    <th class="px-6 py-4">Nama Siswa</th>
                    <th class="px-6 py-4">NISN</th>
                    <th class="px-6 py-4">Kelas</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Buku Dipilih</th>
                </tr>
            </thead>
            <tbody>
                @forelse($antrians as $antrian)
                <tr class="border-b">
                    <td class="px-6 py-4 font-bold">{{ $antrian->nomor_antrian }}</td>
                    <td class="px-6 py-4">{{ $antrian->anggota->user->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $antrian->anggota->nisn ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $antrian->anggota->kelas->nama_kelas ?? '-' }}</td>
                    <td class="px-6 py-4">
                        @if($antrian->status == 'menunggu')
                            <span class="px-3 py-1 text-sm rounded-full bg-orange-100 text-orange-600">Menunggu</span>
                        @elseif($antrian->status == 'batal')
                            <span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-600">Batal</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($antrian->peminjamanBukuPaket && $antrian->peminjamanBukuPaket->detailPeminjamanBukuPaket->count() > 0)
                            <div class="flex flex-wrap gap-1">
                                @foreach($antrian->peminjamanBukuPaket->detailPeminjamanBukuPaket as $detail)
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-800">{{ $detail->bukuPaketMapel->nama_buku ?? '-' }}</span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-gray-400 text-sm italic">Belum memilih</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center">Belum ada antrian untuk tanggal ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4">
        {{ $antrians->links('pagination::tailwind') }}
    </div>
</div>
@endsection
