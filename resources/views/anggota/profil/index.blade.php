@extends('layouts.anggota-app')

@section('content')
    <h2 class="text-2xl font-semibold  mb-6">Profile Saya</h2>

    <!-- Header Profil dengan Foto -->
    <div class="bg-white rounded border border-gray-200 p-6 mb-6 flex items-center gap-6">
        @if (Auth::user()->foto)
            <div class="w-24 h-24 rounded-full overflow-hidden">
                <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="Foto Profil"
                    class="w-full h-full object-cover object-center">
            </div>
        @else
            <div class="w-24 h-24 rounded-full overflow-hidden border border-gray-300">
                <img src="{{ asset('img/Profile.jpg') }}" alt="Foto Default" class="w-full h-full object-cover object-center">
            </div>
        @endif
        <div>
            <p class="text-[22px] font-semibold">{{ $user->name }}</p>
            <p class="text-[18px] text-gray-500">{{ $user->anggota->nisn ?? '-' }}</p>
        </div>
    </div>

    <!-- Informasi Pribadi -->
    <div class="bg-white rounded border border-gray-200 p-6 mb-6">
        <div class="flex justify-between items-center mb-5">
            <span class="text-lg font-semibold text-gray-800">Informasi Pribadi</span>
            <a href="{{ route('anggota.profil.edit', $anggota->id) }}"
                class="inline-flex items-center gap-2 border !border-yellow-500 text-yellow-500 hover:bg-yellow-500 hover:text-white font-semibold px-5 py-2 rounded-full transition text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11.5A1.5 1.5 0 005.5 20H17a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                </svg>
                Edit
            </a>
        </div>

        <!-- Grid Data -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8 text-sm text-gray-700">
            <div>
                <p class="text-[16px] text-gray-500 mb-1">Nama Lengkap</p>
                <p class="text-[18px] font-semibold">{{ $user->name }}</p>
            </div>
            <div>
                <p class="text-[16px] text-gray-500 mb-1">NIM</p>
                <p class="text-[18px] font-semibold">{{ $user->anggota->nisn ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[16px] text-gray-500 mb-1">Email</p>
                <p class="text-[18px] font-semibold">{{ $user->anggota->email ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[16px] text-gray-500 mb-1">No Telepon</p>
                <p class="text-[18px] font-semibold">{{ $user->anggota->no_telp ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[16px] text-gray-500 mb-1">Kelas</p>
                <p class="text-[18px] font-semibold">{{ $user->anggota->kelas->nama_kelas ?? '-' }}</p>
            </div>
        </div>
    </div>
@endsection
