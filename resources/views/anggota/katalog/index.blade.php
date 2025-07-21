@extends('layouts.anggota-app')

@section('content')
    <section class="bg-white py-6 text-center">
        <div class="container mx-auto">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Katalog Buku</h1>
        <p class="text-gray-600 text-sm mb-6">
            Temukan dan booking buku yang kamu butuhkan dengan mudah
        </p>

            <form method="GET" action="{{ route('anggota.katalog.index') }}">
                <div class="mt-2 max-w-2xl mx-auto flex gap-2 items-center">
                    {{-- Dropdown filter kategori --}}
                    <select name="category"
                    class="h-10 px-4` rounded-md bg-white text-base text-text 
                    border border-gray-300 placeholder:text-gray-400
                    focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                        <option value="all">Semua</option>
                        <option value="judul_buku">Judul</option>
                        <option value="pengarang">Pengarang</option>
                        <option value="penerbit">Penerbit</option>
                        <option value="kategori">Kategori</option>
                        <option value="isbn">ISBN</option>
                    </select>

                    {{-- Input pencarian --}}
                    <input type="text" name="search" placeholder="Cari Buku..." value="{{ request('search') }}"
                    class="h-10 w-full rounded-md bg-white px-4 py-2 text-base text-text 
                    border border-gray-300 placeholder:text-gray-400
                    focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm" />
                </div>
            </form>
        </div>
    </section>

    {{-- ======= FILTER KATEGORI ======= --}}
    <section class="px-4 mb-8">
        <div class="flex flex-wrap justify-center gap-2 text-sm font-medium text-gray-700 overflow-x-auto">
            {{-- Tombol Semua --}}
            <a
                href="{{ route(
                    'anggota.katalog.index',
                    array_filter(['search' => request('search'), 'search_by' => request('search_by')]),
                ) }}">
                <button
                    class="px-4 py-2 border rounded-full transition
                           {{ empty($kategori) ? 'bg-blue-700 text-white' : 'border-gray-300 hover:bg-gray-100' }}">
                    Semua
                </button>
            </a>

            {{-- Kategori Dinamis --}}
            @foreach ($kategoriList as $kat)
                <a
                    href="{{ route(
                        'anggota.katalog.index',
                        array_filter(['kategori' => $kat, 'search' => request('search'), 'search_by' => request('search_by')]),
                    ) }}">
                    <button
                        class="px-4 py-2 border rounded-full transition
                               {{ $kategori === $kat ? 'bg-blue-600 text-white' : 'border-gray-300 hover:bg-gray-100' }}">
                        {{ $kat }}
                    </button>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ======= LIST SEMUA BUKU ======= --}}
    <section class="md:px-10 pb-12">
        <div class="grid grid-cols-2 sm:grid-cols-5 lg:grid-cols-6 gap-4 max-w-6xl mx-auto">
            @foreach ($katalogList as $buku)
                @php
                    $available = $buku->inventori?->eksemplar->where('status', '!=', 'dipinjam')->count();
                    $status = $available > 0 ? 'Tersedia' : 'Tidak Tersedia';
                    $statusStyle = $available > 0 ? 'text-green-600' : 'text-red-600';
                @endphp

                <div class="bg-white p-3 text-left">
                    {{-- Sampul --}}
                    <div class="h-44 flex items-center justify-center mb-2">
                        <img src="{{ asset($buku->cover_buku}}"
                            alt="{{ $buku->judul_buku }}" class="max-h-full max-w-full object-contain" />
                    </div>

                    {{-- Info --}}
                    <p class="text-xs text-gray-400 truncate">{{ $buku->pengarang }}</p>
                    <h3 class="text-sm font-semibold text-gray-800 truncate mb-1">
                        {{ $buku->judul_buku }}
                    </h3>
                    <p class="text-xs font-semibold {{ $statusStyle }} mb-3">
                        {{ $status }}
                    </p>

                    {{-- Tombol --}}
                    <a href="{{ route('anggota.katalog.show', $buku->id) }}">
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
