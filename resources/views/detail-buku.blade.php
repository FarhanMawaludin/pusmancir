<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>PUSMANCIR</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" href="{{ asset('logo-smancir.png') }}" type="image/png">

    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <!-- Flowbite JS -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script> --}}
</head>

<body class="text-text font-Inter bg-white">
    <!-- Header -->
    <nav class="bg-white fixed w-full z-20 top-0 left-0">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="#" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="{{ asset('/logo-banten.png') }}" class="h-10" alt="Logo" />
                <img src="{{ asset('img/logo-smancir.png') }}" class="h-10" alt="Logo" />
                <span class="self-center text-xl font-bold whitespace-nowrap text-text hidden md:block">PUSMANCIR</span>
            </a>
            <div class="flex items-center md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
                <button type="button" onclick="location.href='{{ route('login') }}'"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded text-sm px-4 py-2 text-center">
                    Masuk
                </button>
                <button data-collapse-toggle="navbar-sticky" type="button"
                    class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
                    aria-controls="navbar-sticky" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14"
                        aria-hidden="true">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 1h15M1 7h15M1 13h15" />
                    </svg>
                </button>
            </div>
            <div class="hidden items-center justify-between w-full md:flex md:w-auto md:order-1" id="navbar-sticky">
                <ul
                    class="flex flex-col p-4 mt-4 font-medium border border-gray-100 rounded-lg bg-gray-50 md:flex-row md:space-x-8 md:mt-0 md:border-0 md:bg-white">
                    <li>
                        <a href="{{ route('welcome') }}"
                            class="block py-2 px-3 text-gray-400 hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0"
                            aria-current="page">Beranda</a>
                    </li>
                    <li>
                        <a href="{{ route('informasi') }}"
                            class="block py-2 px-3 text-gray-400 hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0">Informasi</a>
                    </li>
                    <li>
                        <a href="{{ route('berita.index') }}"
                            class="block py-2 px-3 text-gray-400 hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0">Berita</a>
                    </li>
                    <li>
                        <a href="{{ route('peringkat.index') }}"
                            class="block py-2 px-3 text-gray-400 hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0">Peringkat</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-6 py-20">
        <!-- Main Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <!-- KIRI: Gambar dan info buku -->
            @php
                $available = $buku->inventori?->eksemplar->where('status', '!=', 'dipinjam')->count();

                $status = $available > 0 ? 'Tersedia' : 'dipinjam';
                $statusColor = $available > 0 ? 'text-green-600' : 'text-red-600';
                $style = $status === 'Tersedia' ? 'text-green-700 ' : 'text-red-700 ';
            @endphp
            <div>
                <!-- Gambar -->
                <div class="flex justify-center mb-2">
                    <img src="{{ asset('storage/' . ($buku->cover_buku ?? 'img/book1.png')) }}" alt="Book Cover"
                        class="w-60 h-auto" />
                </div>

                <!-- Judul dan Penulis -->
                <h1 class="text-2xl font-semibold text-text leading-tight text-center mb-1">
                    {{ $buku->judul_buku }}
                </h1>
                <p class="text-md text-center text-gray-500 mb-6">Penulis: {{ $buku->pengarang }}</p>

                <!-- Info Bar -->
                <div
                    class="grid grid-cols-4 gap-4 text-center text-sm text-gray-600 font-medium border-t border-b py-4">
                    <div>
                        <p class="text-gray-400">Kategori</p>
                        <p class="text-text font-semibold">{{ $buku->kategori_buku }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Penerbit</p>
                        <p class="text-text font-semibold">{{ $buku->penerbit }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">status</p>
                        <p class="font-semibold {{ $style }}">{{ $status }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">ISBN</p>
                        <p class="text-text font-semibold">
                            {{ $buku->isbn }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- KANAN: Tabs dan Konten -->
            <div class="flex flex-col justify-start h-full">
                <!-- Judul Halaman -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 text-left">Detail Buku</h2>
                </div>

                <!-- Tabs -->
                <div class="flex gap-6 border-b mb-4 text-sm font-medium text-gray-500">
                    <button class="pb-2 border-b-2 border-blue-600 text-blue-600">Sinopsis</button>
                </div>

                <!-- Konten Tab Aktif -->
                <div class="flex-1">
                    <p class="text-sm text-gray-700 leading-relaxed text-justify mb-8">
                        {{ $buku->ringkasan_buku }}
                    </p>
                    {{-- <div>
                        <button
                            class="w-full bg-black text-white py-3 rounded-md font-semibold text-sm hover:bg-gray-900 transition duration-200">
                            Start Reading
                        </button>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
    </div>




    <!-- Footer -->
    <footer class="bg-gray-100 px-4 py-10 mt-16">
        <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-6 text-sm text-gray-700">
            <div>
                <div class="flex items-center gap-4 mb-2">
                    <img src="{{ asset('img/logo-smancir.png') }}" class="w-8 h-10" />
                    <span class="font-bold">PUSMANCIR</span>
                </div>
                <ul class="space-y-1 text-gray-500">
                    <li>
                        <a href="{{ route('informasi') }}">Informasi</a>
                    </li>
                    <li>
                        <a href="{{ route('berita.index') }}">Berita</a>
                    </li>
                    <li>
                        <a href="{{ route('peringkat.index') }}">Peringkat</a>
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Tentang Kami</h4>
                <p class="text-gray-500">
                    Perpustakaan SMAN 1 Harapan Bangsa adalah sarana belajar yang menyediakan berbagai koleksi buku
                    pelajaran, fiksi, referensi, dan digital untuk menunjang kegiatan akademik dan pengembangan literasi
                    siswa. Berdiri sejak 2008, perpustakaan ini berkomitmen menjadi pusat ilmu dan budaya baca di
                    lingkungan
                    sekolah.
                </p>
            </div>
        </div>

        <div class="text-center text-xs text-gray-400 mt-8 border-t pt-4">
            © 2025 · PUSMANCIR &nbsp; | &nbsp; <a href="#" class="underline">Privacy Policy</a> · <a
                href="#" class="underline">Terms</a> · <a href="#" class="underline">Code of Conduct</a>
        </div>
    </footer>

</body>

</html>
