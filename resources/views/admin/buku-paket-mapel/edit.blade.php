@extends('layouts.admin-app')
@php $activeMenu = 'antrianPaket'; @endphp

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-text">Edit Buku Paket Mata Pelajaran</h2>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form action="{{ route('admin.buku-paket-mapel.update', $buku->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Buku</label>
            <input type="text" name="nama_buku" required value="{{ old('nama_buku', $buku->nama_buku) }}" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text border border-gray-300 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
            @error('nama_buku')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat Kelas</label>
            <select name="tingkat_kelas" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                <option value="">-- Pilih Tingkat Kelas --</option>
                <option value="X" {{ old('tingkat_kelas', $buku->tingkat_kelas) == 'X' ? 'selected' : '' }}>Kelas X</option>
                <option value="XI IPA" {{ old('tingkat_kelas', $buku->tingkat_kelas) == 'XI IPA' ? 'selected' : '' }}>Kelas XI IPA</option>
                <option value="XI IPS" {{ old('tingkat_kelas', $buku->tingkat_kelas) == 'XI IPS' ? 'selected' : '' }}>Kelas XI IPS</option>
                <option value="XII IPA" {{ old('tingkat_kelas', $buku->tingkat_kelas) == 'XII IPA' ? 'selected' : '' }}>Kelas XII IPA</option>
                <option value="XII IPS" {{ old('tingkat_kelas', $buku->tingkat_kelas) == 'XII IPS' ? 'selected' : '' }}>Kelas XII IPS</option>
            </select>
            @error('tingkat_kelas')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.buku-paket-mapel.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Batal</a>
            <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-md hover:bg-blue-800 focus:ring-4 focus:ring-blue-300">Simpan</button>
        </div>
    </form>
</div>
@endsection
