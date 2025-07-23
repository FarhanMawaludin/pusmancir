<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>PERPUSTAKAAN SMA NEGERI 1 CIRUAS</title>

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
        <div class="max-w-screen-xl mx-auto p-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">

                <!-- Row untuk logo dan tombol di mobile -->
                <div class="flex justify-between items-center w-full md:hidden">
                    <!-- Logo -->
                    <a href="{{ route('welcome') }}"
                        class="flex items-center space-x-2 rtl:space-x-reverse max-w-[65%]">
                        <img src="{{ asset('/logo-banten.png') }}" class="h-6 md:h-8" alt="Logo Banten" />
                        <img src="{{ asset('/logo-smancir.png') }}" class="h-6 md:h-8" alt="Logo SMANCIR" />

                        <div class="flex flex-col leading-tight text-text">
                            <span class="text-[12px] font-bold">PUSMANCIR</span>
                            <span style="font-size: 8px" class="whitespace-nowrap">Perpustakaan SMAN 1 Ciruas</span>
                            <span style="font-size: 8px">NPP: <strong>3604091E1000002</strong></span>
                        </div>
                    </a>

                    <!-- Tombol Masuk + Burger (mobile only) -->
                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="location.href='{{ route('login') }}'"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded text-sm px-4 py-2 text-center">
                            Masuk
                        </button>
                        <button data-collapse-toggle="navbar-sticky" type="button"
                            class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
                            aria-controls="navbar-sticky" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14"
                                aria-hidden="true">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M1 1h15M1 7h15M1 13h15" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Kiri (logo + teks - desktop only) -->
                <a href="#" class="hidden md:flex items-center space-x-4 rtl:space-x-reverse w-full md:w-60">
                    <!-- Logo -->
                    <div class="flex space-x-2">
                        <img src="{{ asset('/logo-banten.png') }}" class="h-10" alt="Logo Banten" />
                        <img src="{{ asset('/logo-smancir.png') }}" class="h-10" alt="Logo SMANCIR" />
                    </div>

                    <!-- Teks -->
                    <div class="flex flex-col leading-tight text-sm text-text">
                        <span class="text-md font-bold">PUSMANCIR</span>
                        <span class="text-[10px] whitespace-nowrap">Perpustakaan SMAN 1 Ciruas</span>
                        <span class="text-[10px]">NPP: <strong>3604091E1000002</strong></span>
                    </div>
                </a>

                <!-- Kanan (tombol masuk - desktop only) -->
                <div
                    class="hidden md:flex items-center justify-end w-full md:w-60 md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
                    <button type="button" onclick="location.href='{{ route('login') }}'"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded text-sm px-4 py-2 text-center">
                        Masuk
                    </button>
                </div>

                <!-- Navbar Link (Tengah) -->
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
        </div>
    </nav>


    <div class="max-w-6xl mx-auto px-6 py-20">
        <!-- Main Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <!-- KIRI: Gambar buku -->
            <div class="flex flex-col items-center">
                <img src="{{ asset($buku->cover_image) }}" alt="Book Cover" class="w-60 md:w-80 h-auto rounded-lg shadow-lg border border-gray-300" />
                <h1 class="text-2xl font-semibold text-text leading-tight text-center mt-4 mb-1 border-b-2 border-blue-700 pb-2 w-fit">
                    {{ $buku->judul }}
                </h1>
            </div>
            

            <!-- KANAN: Info detail + tombol aksi -->
            <div class="flex flex-col justify-start h-full space-y-6">

                <!-- Info Bar -->
                <div
                    class="grid grid-cols-2 md:grid-cols-4 text-center text-sm text-gray-600 font-medium border-t border-b py-4 gap-4">
                    <div>
                        <p class="text-gray-400">Kategori</p>
                        <p class="text-text font-semibold">{{ $buku->kategori }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Kelas</p>
                        <p class="text-text font-semibold">{{ $buku->kelas }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Format</p>
                        <p class="text-text font-semibold">E-Book (PDF)</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Kurikulum</p>
                        <p class="text-text font-semibold break-words">{{ $buku->kurikulum }}</p>
                    </div>
                </div>

                <!-- Tombol aksi -->
                <div class="flex flex-col gap-4">
                    <a href="{{ asset('buku-pdf/' . basename($buku->pdf_path)) }}" target="_blank"
                        rel="noopener noreferrer">
                        <button
                            class="w-full text-white bg-blue-700 hover:bg-blue-800 font-semibold rounded-md text-sm px-5 py-3 transition-all duration-300">
                            Baca Buku
                        </button>
                    </a>

                    @if ($buku->pdf_path)
                        <a href="{{ asset('buku-pdf/' . basename($buku->pdf_path)) }}" download>
                            <button
                                class="w-full text-red-600 border border-red-600 hover:bg-red-700 hover:text-white font-semibold rounded-md text-sm px-5 py-3 transition-all duration-300">
                                Download E-Book
                            </button>
                        </a>
                    @else
                        <button disabled
                            class="w-full bg-gray-400 text-white font-semibold rounded-md text-sm px-5 py-3 cursor-not-allowed">
                            E-Book Tidak Tersedia
                        </button>
                    @endif
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
