@extends('layouts.admin-app')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-gray-800">Detail Pengaduan</h1>
    </div>
    <div class="overflow-x-auto relative rounded-lg border border-gray-200 bg-white">

        {{-- Tabel Informasi --}}
        <table class="min-w-full text-sm text-left text-gray-700">
            <thead class="text-xs uppercase bg-gray-100 text-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 w-1/3">Informasi</th>
                    <th scope="col" class="px-6 py-3">Detail</th>
                </tr>
            </thead>
            <tbody>
                <tr class="bg-white border-b border-gray-200">
                    <td class="px-6 py-4 font-medium">Nama</td>
                    <td class="px-6 py-4">{{ $pengaduan->nama ?? '-' }}</td>
                </tr>

                <tr class="bg-white border-b border-gray-200">
                    <td class="px-6 py-4 font-medium">No Telepon</td>
                    <td class="px-6 py-4">{{ $pengaduan->no_telp ?? '-' }}</td>
                </tr>

                <tr class="bg-white border-b border-gray-200">
                    <td class="px-6 py-4 font-medium">Email</td>
                    <td class="px-6 py-4">{{ $pengaduan->email ?? '-' }}</td>
                </tr>

                <tr class="bg-white border-b border-gray-200">
                    <td class="px-6 py-4 font-medium">Pesan</td>
                    <td class="px-6 py-4">{{ $pengaduan->isi ?? '-' }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Tombol Kembali --}}
        <div class="text-center py-6">
            <a href="{{ route('admin.pengaduan.index') }}"
                class="inline-flex items-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm">
                <!-- Ikon Panah Kiri -->
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Daftar Pengaduan
            </a>
        </div>
    </div>
@endsection
