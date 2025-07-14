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
        <nav class="bg-white fixed w-full z-20 top-0 start-0 lg:px-6">
            <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
                <a href="" class="flex items-center space-x-3 rtl:space-x-reverse">
                    <img src="{{ asset('img/logo-smancir.png') }}" alt="Logo" class="w-10 h-12" />
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
                <div class="items-center hidden w-full md:flex md:w-auto md:order-1" id="navbar-sticky">
                    <ul
                        class="flex flex-col p-4 md:p-0 mt-4 font-medium border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white mx-auto">
                        <li>
                            <a href="#"
                                class="block py-2 px-3 text-white bg-blue-700 rounded-sm md:bg-transparent md:text-blue-700 md:p-0"
                                aria-current="page">Beranda</a>
                        </li>
                        <li>
                            <a href="{{ route('informasi') }}"
                                class="block py-2 px-3 text-base300 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0">Informasi</a>
                        </li>
                        <li>
                            <a href="#"
                                class="block py-2 px-3 text-base300 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0">Berita</a>
                        </li>
                        <li>
                            <a href="#"
                                class="block py-2 px-3 text-base300 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0">Pustakawan</a>
                        </li>
                    </ul>
                </div>

            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="bg-white px-4 py-28 text-center">
        <div class="container mx-auto">
            <h1 class="text-3xl md:text-5xl font-semibold leading-tight">
                Cari <span class="text-blue-700">Buku Favoritmu</span>,<br />
                Kapan Saja & Di Mana Saja!
            </h1>
            <p class="text-base400 mt-4 max-w-2xl mx-auto">
                Temukan & Booking Buku Favoritmu<br>
                Cek statusnya online, pinjam langsung di perpustakaan.
            </p>

            <form method="GET" action="{{ route('welcome') }}">
                <div class="mt-10 max-w-2xl mx-auto flex gap-2 items-center">
                    {{-- Dropdown filter kategori --}}
                    <select name="category"
                        class="h-10 px-4 py-2 border rounded border-base200 bg-white text-sm text-gray-700
                               focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="all">Semua</option>
                        <option value="judul_buku">Judul</option>
                        <option value="pengarang">Pengarang</option>
                        <option value="penerbit">Penerbit</option>
                        <option value="kategori">Kategori</option>
                        <option value="isbn">ISBN</option>
                    </select>

                    {{-- Input pencarian --}}
                    <input type="text" name="search" placeholder="Cari Buku..." value="{{ request('search') }}"
                        class="h-10 w-full px-4 py-2 border rounded border-base200
                               focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
            </form>

            <div class="mt-10 flex flex-wrap justify-center gap-3 text-sm">
                <p class="py-2 text-base400">Pencarian Terpopuler</p>
                <button class="px-4 py-2 border border-base200 rounded-full">Semua Buku</button>
                <button class="px-4 py-2 border border-base200 rounded-full">Fiksi</button>
                <button class="px-4 py-2 border border-base200 rounded-full">Seni & Desain</button>
                <button class="px-4 py-2 border border-base200 rounded-full">Matematika</button>
                <button class="px-4 py-2 border border-base200 rounded-full">IPS & Sejarah</button>
            </div>
        </div>
    </section>


    <!-- Kategori Populer -->
    <section class="px-4 md:px-10 py-10">
        <div class="flex flex-wrap justify-center gap-3 text-sm font-medium text-base300 mb-6 overflow-x-auto">
            {{-- Tombol Semua --}}
            <a
                href="{{ route('welcome', array_filter(['search' => request('search'), 'search_by' => request('search_by')])) }}">
                <button class="px-4 py-2 border rounded-full {{ empty($kategori) ? 'bg-blue-700 text-white' : '' }}">
                    Semua Buku
                </button>
            </a>

            {{-- Kategori Dinamis --}}
            @foreach ($kategoriList as $kat)
                <a
                    href="{{ route('welcome', array_filter(['kategori' => $kat, 'search' => request('search'), 'search_by' => request('search_by')])) }}">
                    <button
                        class="px-4 py-2 border rounded-full {{ $kategori === $kat ? 'bg-blue-700 text-white' : '' }}">
                        {{ $kat }}
                    </button>
                </a>
            @endforeach
        </div>

        {{-- LIST BUKU UTAMA --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 max-w-6xl mx-auto" id="buku-container">
            @foreach ($katalogList->take(6) as $buku)
                @include('partials.buku-item', ['buku' => $buku])
            @endforeach
        </div>

        {{-- LIST BUKU TAMBAHAN --}}
        @if ($katalogList->count() > 6)
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 max-w-6xl mx-auto hidden" id="buku-more">
                @foreach ($katalogList->slice(6)->take(6) as $buku)
                    @include('partials.buku-item', ['buku' => $buku])
                @endforeach
            </div>

            {{-- Tombol Lihat Lebih Banyak --}}
            <div class="text-center mt-10">
                <button id="lihat-lebih"
                    class="font-medium px-4 py-2 border border-primary700 rounded-md text-blue-700 transition duration-200 delay-100 hover:bg-blue-700 hover:text-white">
                    Lihat Lebih Banyak
                </button>
            </div>
        @endif
    </section>



    <!-- Footer -->
    <footer class="bg-gray-100 px-4 py-10 mt-16">
        <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-6 text-sm text-gray-700">
            <div>
                <div class="flex items-center gap-4 mb-2">
                    <img src="{{ asset('img/logo-smancir.png') }}" class="w-8 h-10" />
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
            {{-- <div>
                <form method="GET" action="{{ route('welcome') }}">
                    <h4 class="font-semibold text-gray-800 mb-2" value="{{ request('search') }}">Cari</h4>
                    <input type="text" placeholder="Cari Buku..."
                        class="w-full px-4 py-2 border rounded border-base200 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </form>
                
            </div> --}}
        </div>

        <div class="text-center text-xs text-gray-400 mt-8 border-t pt-4">
            © 2025 · PUSMANCIR &nbsp; | &nbsp; <a href="#" class="underline">Privacy Policy</a> · <a
                href="#" class="underline">Terms</a> · <a href="#" class="underline">Code of Conduct</a>
        </div>
    </footer>
    <script>
        document.getElementById('lihat-lebih')?.addEventListener('click', function() {
            document.getElementById('buku-more')?.classList.remove('hidden');
            this.classList.add('hidden');
        });
    </script>
</body>

</html>
