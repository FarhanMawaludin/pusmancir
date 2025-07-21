@extends('layouts.anggota-app')

@section('content')
    <div class="flex justify-center items-center min-h-screen bg-gray-100">
        <div class="w-full max-w-sm bg-white rounded shadow-lg overflow-hidden my-auto mx-4">

            {{-- Header --}}
            <div class="flex items-center justify-between p-4 bg-blue-600 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 shrink-0">
                        <img src="{{ asset('img/logo-smancir.png') }}" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <h1 class="text-lg font-bold tracking-wide">PUSMANCIR</h1>
                </div>
                <div class="text-xs text-right leading-tight">
                    <p class="font-semibold">PERPUSTAKAAN</p>
                    <p>SMAN 1 CIRUAS</p>
                </div>
            </div>

            {{-- Foto & Identitas --}}
            <div class="flex flex-col items-center p-5 space-y-3">
                <div class="w-28 aspect-[3/4] overflow-hidden rounded-md border shadow-md bg-gray-50">
                    <img src="{{ $user->foto ? asset($user->foto) : asset('img/Profile.jpg') }}"
                        alt="Foto Profil" class="w-full h-full object-cover">
                </div>

                <div class="text-center">
                    <h2 class="text-lg font-semibold text-gray-800">{{ $user->name }}</h2>
                    <p class="text-gray-600 text-sm">NISN: {{ $anggota->nisn }}</p>
                </div>
            </div>

            {{-- Garis Pemisah --}}
            <div class="border-t mx-4"></div>

            {{-- QR Code --}}
            <div class="flex justify-center items-center p-6 sm:p-10">
                <div class="w-40 h-40 flex justify-center items-center">
                    <div class="inline-block">
                        {!! $qrCode !!}
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
