<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>PUSMANCIR</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="text-text font-Inter bg-white">
    <!-- Header -->
    <header class="flex justify-between items-center py-4 px-10 shadow-sm bg-white md:flex-row lg:flex-row xl:flex-row">
        <nav class="bg-white dark:bg-gray-900 fixed w-full z-20 top-0 start-0 lg:px-6">
            <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
                <a href="" class="flex items-center space-x-3 rtl:space-x-reverse">
                    <img src="img/logo-smancir.png" alt="Logo" class="w-10 h-12" />
                    <span class="text-xl font-bold text-text hidden md:block">PUSMANCIR</span>
                </a>
                <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
                    <button type="button"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded text-sm px-4 py-2 text-center dark:bg-blue-700 dark:hover:bg-blue-800 dark:focus:ring-blue-800"
                        onclick="location.href='{{ route('login') }}'">Masuk</button>
                    <button data-collapse-toggle="navbar-sticky" type="button"
                        class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                        aria-controls="navbar-sticky" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 17 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M1 1h15M1 7h15M1 13h15" />
                        </svg>
                    </button>
                </div>
                <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-sticky">
                    <ul
                        class="flex flex-col p-4 md:p-0 mt-4 font-medium border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                        <li>
                            <a href="#"
                                class="block py-2 px-3 text-white bg-blue-700 rounded-sm md:bg-transparent md:text-blue-700 md:p-0 md:dark:text-blue-500"
                                aria-current="page">Beranda</a>
                        </li>
                        <li>
                            <a href="{{ route('informasi') }}"
                                class="block py-2 px-3 text-base300 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 md:dark:hover:text-blue-500 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Informasi</a>
                        </li>
                        <li>
                            <a href="#"
                                class="block py-2 px-3 text-base300 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 md:dark:hover:text-blue-500 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Berita</a>
                        </li>
                        <li>
                            <a href="#"
                                class="block py-2 px-3 text-base300 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 md:dark:hover:text-blue-500 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Pustakawan</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="text-center px-4 py-28 bg-white">
        <h1 class="text-3xl md:text-5xl font-semibold leading-tight">
            Cari <span class="text-blue-700">Buku Favoritmu</span>,<br />
            Kapan Saja & Di Mana Saja!
        </h1>
        <p class="text-base400 mt-4 max-w-2xl mx-auto">
            Pinjam, baca, dan cek status buku tanpa harus datang ke perpustakaan.<br> Semua jadi lebih cepat, gampang,
            dan
            seru!
        </p>

        <div class="mt-10 max-w-md mx-auto">
            <input type="text" placeholder="Cari Buku..."
                class="w-full px-4 py-2 border rounded border-base200 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div class="mt-10 flex flex-wrap justify-center gap-3 text-sm">
            <p class="py-2 text-base400">Pencarian Terpopuler</p>
            <button class="px-4 py-2 border border-base200 rounded-full">Semua Buku</button>
            <button class="px-4 py-2 border border-base200 rounded-full">Fiksi</button>
            <button class="px-4 py-2 border border-base200 rounded-full">Seni & Desain</button>
            <button class="px-4 py-2 border border-base200 rounded-full">Matematika</button>
            <button class="px-4 py-2 border border-base200 rounded-full">IPS & Sejarah</button>
        </div>
    </section>

    <!-- Kategori Populer -->
    <section class="px-4 md:px-10 py-10">
        <div class="flex flex-wrap justify-center gap-3 text-sm font-medium text-base300 mb-6 overflow-x-auto">
            <button class="px-4 py-2 border bg-blue-700 text-white rounded-full">Semua Buku</button>
            <button class="px-4 py-2">Fiksi</button>
            <button class="px-4 py-2">Seni & Desain</button>
            <button class="px-4 py-2">Matematika</button>
            <button class="px-4 py-2">IPS & Sejarah</button>
            <button class="px-4 py-2">Fiksi</button>
            <button class="px-4 py-2">Seni & Desain</button>
        </div>

        <!-- Buku List -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 max-w-6xl mx-auto">
            @foreach ($katalogList as $buku)
                @php
                    $jumlah = $buku->inventori->jumlah_eksemplar ?? 0;
                    $status = $jumlah > 0 ? 'Tersedia' : 'Kosong';
                    $statusColor = $jumlah > 0 ? 'text-green-600' : 'text-red-600';
                @endphp

                <div class="max-w-[160px] w-full rounded-xl overflow-hidden mx-auto">
                    <div class="p-3">
                        <div class="w-full bg-white rounded-xxl overflow-hidden">
                            <!-- Wrapper untuk gambar dan isi -->
                            <div class="flex flex-col items-center">
                                <!-- Gambar -->
                                <div class="w-full h-48 flex items-center justify-center bg-white mb-1">
                                    <img src="{{ asset('storage/' . ($buku->cover_buku ?? 'img/book1.png')) }}"
                                        alt="{{ $buku->judul_buku }}"
                                        class="max-h-full max-w-full object-contain shadow-lg" />
                                </div>

                    
                                <!-- Konten isi, pastikan align kiri -->
                                <div class="w-full px-2"> <!-- padding kiri/kanan -->
                                    <!-- Pengarang -->
                                    <p class="text-[14px] font-medium text-gray-400 truncate">{{ $buku->pengarang }}</p>
                    
                                    <!-- Judul -->
                                    <h3 class="text-[16px] font-semibold text-gray-900 leading-tight mb-1 truncate">
                                        {{ $buku->judul_buku }}
                                    </h3>
                    
                                    <!-- Status -->
                                    <p class="text-xs font-semibold {{ $statusColor }} mb-3">
                                        {{ $status }}
                                    </p>
                                </div>
                    
                                <!-- Tombol -->
                                <a href="{{ route('detail-buku', $buku->id) }}" class="w-full px-2 pb-4">
                                    <button type="button"
                                        class="w-full text-white bg-blue-600 hover:bg-blue-800 font-semibold rounded-md text-sm px-5 py-2 text-center transition-all duration-300 ease-in-out cursor-pointer">
                                        Lihat Buku
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                </div>
            @endforeach
        </div>



        <div class="text-center mt-10">
            <button
                class="font-medium px-4 py-2 border border-primary700 rounded-md text-blue-700 transition duration-200 delay-100 hover:bg-blue-700 hover:text-white">
                Lihat Semua Buku
            </button>
        </div>
    </section>



    <!-- Footer -->
    <footer class="bg-gray-100 px-4 py-10 mt-16">
        <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-6 text-sm text-gray-700">
            <div>
                <div class="flex items-center gap-4 mb-2">
                    <img src="img/logo-smancir.png" class="w-8 h-10" />
                    <span class="font-bold">PUSMANCIR</span>
                </div>
                <ul class="space-y-1 text-gray-500">
                    <li>Informasi</li>
                    <li>Berita</li>
                    <li>Pustakawan</li>
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
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Cari</h4>
                <input type="text" placeholder="Cari Buku..."
                    class="w-full px-4 py-2 border rounded border-base200 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
        </div>

        <div class="text-center text-xs text-gray-400 mt-8 border-t pt-4">
            © 2025 · PUSMANCIR &nbsp; | &nbsp; <a href="#" class="underline">Privacy Policy</a> · <a
                href="#" class="underline">Terms</a> · <a href="#" class="underline">Code of Conduct</a>
        </div>
    </footer>

</body>

</html>
