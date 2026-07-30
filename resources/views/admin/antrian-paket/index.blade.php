@extends('layouts.admin-app')
@php $activeMenu = 'antrianPaket'; @endphp

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-text">Daftar Antrian Buku Paket</h2>
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
                    <th class="px-6 py-4">Aksi</th>
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
                        @elseif($antrian->status == 'hadir')
                            <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-600">Hadir</span>
                        @elseif($antrian->status == 'batal')
                            <span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-600">Batal</span>
                        @elseif($antrian->status == 'tidak_hadir')
                            <span class="px-3 py-1 text-sm rounded-full bg-red-100 text-red-600">Tidak Hadir</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($antrian->status == 'menunggu')
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.antrian-paket.proses', $antrian->id) }}" class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700">Proses</a>
                                <form action="{{ route('admin.antrian-paket.updateStatus', $antrian->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="tidak_hadir">
                                    <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600" onclick="return confirm('Tandai siswa sebagai tidak hadir?')">Tidak Hadir</button>
                                </form>
                            </div>
                        @elseif($antrian->status == 'hadir')
                            <span class="text-sm text-gray-500 italic">Sudah Diproses</span>
                        @else
                            -
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
