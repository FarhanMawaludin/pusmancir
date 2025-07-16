@extends('layouts.anggota-app')

@section('content')
    <section class="bg-white py-6 text-center">
        <div class="container mx-auto">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Katalog Buku paket</h1>
            <p class="text-gray-600 text-sm mb-6">
                Temukan dan booking buku yang kamu butuhkan dengan mudah
            </p>
        </div>
    </section>


    {{-- ======= LIST SEMUA BUKU ======= --}}
    <section class="md:px-10 pb-12">
        <div class="grid grid-cols-2 sm:grid-cols-5 lg:grid-cols-6 gap-4 max-w-6xl mx-auto">
            @foreach ($katalogList as $buku)
                @php
                    $available = (int) ($buku->stok_tersedia ?? 0);
                    $isAvailable = $available > 0;

                    $status = $isAvailable ? 'Tersedia' : 'Tidak Tersedia';
                    $statusStyle = $isAvailable ? 'text-green-600' : 'text-red-600';
                @endphp

                <div class="bg-white p-3 text-left">
                    {{-- Sampul --}}
                    <div
                        class="h-44 flex items-center justify-center mb-2 relative border border-gray-300 rounded-lg bg-white shadow-inner overflow-hidden">
                        <div
                            class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                            <span class="text-center px-2 text-sm font-semibold text-gray-700">
                                {{ $buku->nama_paket ?? 'Tanpa Nama Paket' }}
                            </span>
                        </div>
                    </div>


                    {{-- Info --}}
                    <p class="text-xs text-gray-400 truncate">{{ $buku->pengarang }}</p>
                    <h3 class="text-sm font-semibold text-gray-800 truncate mb-1">
                        {{ $buku->nama_paket }}
                    </h3>
                    <p class="text-xs font-semibold {{ $statusStyle }} mb-3">
                        {{ $status }}
                    </p>

                    {{-- Tombol --}}
                    <a href="{{ route('anggota.katalog-paket.show', $buku->id) }}">
                        <button type="button"
                            class="w-full text-white bg-blue-600 hover:bg-blue-700 font-semibold rounded-md
                                   text-sm px-4 py-2 transition">
                            Lihat Buku
                        </button>
                    </a>
                </div>
            @endforeach
        </div>
    </section>
@endsection
