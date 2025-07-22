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
                    <a href="#" class="flex items-center space-x-2 rtl:space-x-reverse">
                        <!-- Logo Banten -->
                        <img src="{{ asset('/logo-banten.png') }}" class="h-6 md:h-10" alt="Logo Banten" />

                        <!-- Logo SMANCIR -->
                        <img src="{{ asset('/logo-smancir.png') }}" class="h-6 md:h-10" alt="Logo SMANCIR" />

                        <!-- Teks -->
                        <div class="flex flex-col leading-tight text-text">
                            <span class="text-sm md:text-lg font-bold">PUSMANCIR</span>
                            <span class="text-[6px] md:text-[9px] whitespace-nowrap">Perpustakaan SMAN 1 Ciruas</span>
                            <span class="text-[6px] md:text-[9px]">NPP: <strong>3604091E1000002</strong></span>
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
                        <span class="text-xl font-bold">PUSMANCIR</span>
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
                                class="block py-2 px-3 text-white bg-blue-700 rounded md:bg-transparent md:text-blue-700 md:p-0"
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




    <!-- Hero Section -->
    <section class="bg-white px-4 py-40 text-center">
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
                        class="h-10 px-4 py-2 border rounded border-gray-300 bg-white text-sm text-text
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
                        class="h-10 w-full px-4 py-2 border rounded border-gray-300 bg-white text-sm text-text
                        placeholder-gray-300
                        focus:outline-none focus:ring-2 focus:ring-blue-500" />

                </div>
            </form>
        </div>
    </section>


    <!-- Kategori Populer -->
    <section class="px-4 md:px-10 py-10">
        <div class="relative mb-6">
            {{-- Panah kiri (disembunyikan di desktop) --}}
            <button id="btn-left"
                class="absolute left-0 top-1/2 -translate-y-1/2 bg-blue-600 text-white rounded-full p-2 shadow-md z-10 md:hidden"
                aria-label="Scroll Left">
                <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            {{-- Container tombol kategori --}}
            <div id="kategori-container"
                class="flex md:flex-wrap md:justify-center overflow-x-auto md:overflow-visible gap-3 px-10 scrollbar-hide">
                {{-- Tombol Semua --}}
                <a
                    href="{{ route('welcome', array_filter(['search' => request('search'), 'search_by' => request('search_by')])) }}">
                    <button
                        class="px-4 py-2 border rounded-full {{ empty($kategori) ? 'bg-blue-700 text-white' : '' }} min-w-max whitespace-nowrap">
                        Semua Buku
                    </button>
                </a>

                {{-- Kategori Dinamis --}}
                @foreach ($kategoriList as $kat)
                    <a
                        href="{{ route('welcome', array_filter(['kategori' => $kat, 'search' => request('search'), 'search_by' => request('search_by')])) }}">
                        <button
                            class="px-4 py-2 border rounded-full {{ $kategori === $kat ? 'bg-blue-700 text-white' : '' }} min-w-max whitespace-nowrap">
                            {{ $kat }}
                        </button>
                    </a>
                @endforeach
            </div>

            {{-- Panah kanan (disembunyikan di desktop) --}}
            <button id="btn-right"
                class="absolute right-0 top-1/2 -translate-y-1/2 bg-blue-600 text-white rounded-full p-2 shadow-md z-10 md:hidden"
                aria-label="Scroll Right">
                <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M9 5l7 7-7 7" />
                </svg>
            </button>
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
                    class="font-medium px-4 py-2 border border-primary700 rounded-md border-blue-700 text-blue-700 transition duration-200 delay-100 hover:bg-blue-700 hover:text-white">
                    Lihat Lebih Banyak
                </button>
            </div>
        @endif
    </section>



    <!-- Footer -->
    <footer class="bg-gray-100 px-4 py-10 mt-16">
        <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-6 text-sm text-gray-400">
            <div>
                <div class="flex items-center gap-4 mb-2">
                    <img src="{{ asset('img/logo-smancir.png') }}" class="w-8 h-10" />
                    <span class="text-text font-bold">PUSMANCIR</span>
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
            {{-- <div>
                <form method="GET" action="{{ route('welcome') }}">
                    <h4 class="font-semibold text-gray-800 mb-2" value="{{ request('search') }}">Cari</h4>
                    <input type="text" placeholder="Cari Buku..."
                        class="w-full px-4 py-2 border rounded border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" />
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('kategori-container');
            const btnLeft = document.getElementById('btn-left');
            const btnRight = document.getElementById('btn-right');

            const scrollAmount = 120; // scroll per klik

            function updateButtons() {
                if (container.scrollLeft <= 0) {
                    btnLeft.style.display = 'none'; // sembunyikan tombol kiri
                } else {
                    btnLeft.style.display = 'block';
                }

                if (container.scrollLeft + container.clientWidth >= container.scrollWidth - 1) {
                    // -1 untuk toleransi floating point
                    btnRight.style.display = 'none'; // sembunyikan tombol kanan
                } else {
                    btnRight.style.display = 'block';
                }
            }

            btnLeft.addEventListener('click', () => {
                container.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
                setTimeout(updateButtons, 300);
            });

            btnRight.addEventListener('click', () => {
                container.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
                setTimeout(updateButtons, 300);
            });

            // update status tombol saat scroll manual juga (misal swipe)
            container.addEventListener('scroll', updateButtons);

            // inisialisasi tombol
            updateButtons();
        });
    </script>
</body>

</html>
