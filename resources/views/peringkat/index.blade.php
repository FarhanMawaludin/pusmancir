<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita - PERPUSTAKAAN SMA NEGERI 1 CIRUAS</title>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" href="{{ asset('logo-smancir.png') }}" type="image/png">
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <!-- Flowbite JS -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script> --}}
</head>

<body class="bg-white text-text">
    <!-- Navbar -->
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
                                class="block py-2 px-3 text-white bg-blue-700 rounded md:bg-transparent md:text-blue-700 md:p-0">Peringkat</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero slider -->
    <section class="container mx-auto px-6 mt-40">
        <div class="bg-white rounded-lg border border-gray-200 p-12 min-w-full mb-6">
            <h2 class="text-xl font-bold text-center text-gray-700 mb-8">Top 3 Pengunjung Teraktif</h2>
            <div
                class="flex flex-col sm:flex-row items-center sm:items-end justify-center gap-10 sm:gap-20 text-center mt-6">
                @foreach ($top3_kunjungan as $i => $data)
                    @php
                        $mt_card = $i === 0 ? 'mt-0' : 'mt-12';
                        $crowns = ['🥇', '🥈', '🥉'];
                        $crown = $crowns[$i] ?? '';
                        $orderClasses = ['sm:order-1', 'sm:order-0', 'sm:order-2'];
                        $orderClass = $orderClasses[$i] ?? '';
                        $style = $i === 0 ? 'transform: translateY(-20px);' : '';
                    @endphp

                    <div class="flex flex-col items-center gap-2 {{ $mt_card }} {{ $orderClass }}"
                        style="{{ $style }}">
                        <div class="text-5xl">{{ $crown }}</div>
                        <div class="font-bold text-lg text-gray-800">{{ $data->nama }}</div>
                        <div class="text-sm text-gray-500">Total Kunjungan</div>
                        <div class="bg-gray-200 rounded-full px-4 py-2 mt-1 font-semibold text-blue-800">
                            {{ $data->total_kunjungan }} kali
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="container mx-auto px-6 mt-8">
        <div class="bg-white rounded-lg border border-gray-200 p-12 min-w-full mb-6">
            <h2 class="text-xl font-bold text-center text-gray-700 mb-8">Top 3 Peminjam Buku Teraktif</h2>
            <div
                class="flex flex-col sm:flex-row items-center sm:items-end justify-center gap-10 sm:gap-20 text-center mt-6">
                @foreach ($top3_peminjaman as $i => $data)
                    @php
                        $mt_card = $i === 0 ? 'mt-0' : 'mt-12';
                        $crowns = ['🥇', '🥈', '🥉'];
                        $crown = $crowns[$i] ?? '';
                        $orderClasses = ['sm:order-1', 'sm:order-0', 'sm:order-2'];
                        $orderClass = $orderClasses[$i] ?? '';
                        $style = $i === 0 ? 'transform: translateY(-20px);' : '';
                        $nama = $data->anggota->user->name ?? 'Tanpa Nama';
                    @endphp

                    <div class="flex flex-col items-center gap-2 {{ $mt_card }} {{ $orderClass }}"
                        style="{{ $style }}">
                        <div class="text-5xl">{{ $crown }}</div>
                        <div class="font-bold text-lg text-gray-800">{{ $nama }}</div>
                        <div class="text-sm text-gray-500">Total Peminjaman</div>
                        <div class="bg-gray-200 rounded-full px-4 py-2 mt-1 font-semibold text-blue-800">
                            {{ $data->total_peminjaman }} kali
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="container mx-auto px-6 mt-12">
        <div class="bg-white rounded-lg border border-gray-200 p-12 min-w-full mb-6">
            <h2 class="text-xl font-bold text-center text-gray-700 mb-8">Top 3 Buku yang Paling Sering Dipinjam</h2>
            <div
                class="flex flex-col sm:flex-row items-center sm:items-end justify-center gap-10 sm:gap-20 text-center mt-6">
                @foreach ($top3_buku as $i => $data)
                    @php
                        $mt_card = $i === 0 ? 'mt-0' : 'mt-12';
                        $crowns = ['🥇', '🥈', '🥉'];
                        $crown = $crowns[$i] ?? '';
                        $orderClasses = ['sm:order-1', 'sm:order-0', 'sm:order-2'];
                        $orderClass = $orderClasses[$i] ?? '';
                        $style = $i === 0 ? 'transform: translateY(-20px);' : '';
                    @endphp

                    <div class="flex flex-col items-center gap-2 {{ $mt_card }} {{ $orderClass }}"
                        style="{{ $style }}">
                        <div class="text-5xl">{{ $crown }}</div>
                        <div class="font-bold text-lg text-gray-800 text-center">{{ $data->judul_buku }}</div>
                        <div class="text-sm text-gray-500">Total Dipinjam</div>
                        <div class="bg-gray-200 rounded-full px-4 py-2 mt-1 font-semibold text-blue-800">
                            {{ $data->total_dipinjam }} kali
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
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
                    Perpustakaan SMAN 1 Ciruas merupakan fasilitas penunjang pendidikan yang menyediakan berbagai
                    koleksi buku pelajaran, fiksi, referensi, dan sumber digital guna mendukung kegiatan belajar
                    mengajar serta meningkatkan minat baca siswa. Berdiri sejak tahun 2008, perpustakaan ini berkomitmen
                    untuk menjadi pusat informasi, literasi, dan budaya baca di lingkungan SMA Negeri 1 Ciruas,
                    Kabupaten Serang, Banten.
                </p>
            </div>
        </div>

        <div class="text-center text-xs text-gray-600 mt-8 border-t pt-4">
            Hanware &nbsp; · &nbsp; © 2025 · PUSMANCIR &nbsp;
        </div>
    </footer>
</body>

</html>
