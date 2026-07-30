@extends('layouts.admin-app')
@php $activeMenu = 'antrianPaket'; @endphp

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-text">Proses Peminjaman Buku Paket</h2>
    <a href="{{ route('admin.antrian-paket.index') }}" class="text-gray-600 hover:text-gray-900">&larr; Kembali</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h3 class="font-bold text-lg border-b pb-2 mb-4">Informasi Siswa</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">Nama</dt>
                <dd class="mt-1 text-sm text-text">{{ $antrian->anggota->user->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">NISN</dt>
                <dd class="mt-1 text-sm text-text">{{ $antrian->anggota->nisn ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Kelas</dt>
                <dd class="mt-1 text-sm text-text">{{ $antrian->anggota->kelas->nama_kelas ?? '-' }}</dd>
            </div>
        </dl>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h3 class="font-bold text-lg border-b pb-2 mb-4">Informasi Antrian</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">Nomor Antrian</dt>
                <dd class="mt-1 text-2xl font-bold text-blue-600">{{ $antrian->nomor_antrian }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Tanggal Kunjungan</dt>
                <dd class="mt-1 text-sm text-text">{{ \Carbon\Carbon::parse($antrian->tanggal_kunjungan)->format('d-m-Y') }}</dd>
            </div>
        </dl>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
    <form action="{{ route('admin.antrian-paket.storePeminjaman') }}" method="POST">
        @csrf
        <input type="hidden" name="antrian_id" value="{{ $antrian->id }}">
        
        <h3 class="font-bold text-lg mb-4">Pilih Buku yang Dipinjam (Kelas {{ $tingkatKelas }})</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            @forelse($bukuMapel as $buku)
            <div class="flex items-center space-x-3 p-3 border rounded-md hover:bg-gray-50">
                <input type="checkbox" name="buku_ids[]" value="{{ $buku->id }}" id="buku_{{ $buku->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                <label for="buku_{{ $buku->id }}" class="flex-1 text-sm font-medium text-gray-900 cursor-pointer">
                    {{ $buku->nama_buku }}
                </label>
            </div>
            @empty
            <div class="col-span-full p-4 bg-yellow-50 text-yellow-700 rounded-md">
                Tidak ada data buku paket untuk tingkat kelas ini.
            </div>
            @endforelse
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-700 text-white px-6 py-2 rounded-md hover:bg-blue-800 focus:ring-4 focus:ring-blue-300" {{ $bukuMapel->isEmpty() ? 'disabled' : '' }}>Proses Peminjaman</button>
        </div>
    </form>
</div>
@endsection
