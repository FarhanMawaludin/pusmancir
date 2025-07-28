<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Informasi - PERPUSTAKAAN SMA NEGERI 1 CIRUAS</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" href="{{ asset('logo-smancir.png') }}" type="image/png">
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <!-- Flowbite JS -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <!-- Icon -->
    <script src="https://cdn.jsdelivr.net/npm/iconsax-icons@1.0.0/dist/iconsax.js"></script> --}}



</head>

<body class="bg-white text-gray-800 font-sans">
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
                                class="block py-2 px-3 text-white bg-blue-700 rounded md:bg-transparent md:text-blue-700 md:p-0">Informasi</a>
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
    <section class="flex flex-col-reverse md:flex-row gap-8 px-10 py-28 items-center">
        <!-- Text -->
        <div class="md:w-1/2">
            <h1 class="text-3xl font-bold mb-4">PUSMANCIR</h1>
            <p class="text-gray-700 leading-relaxed">
                Perpustakaan SMAN 1 Ciruas merupakan fasilitas penunjang pendidikan yang menyediakan berbagai koleksi
                buku pelajaran, fiksi, referensi, dan sumber digital guna mendukung kegiatan belajar mengajar serta
                meningkatkan minat baca siswa. Berdiri sejak tahun 2008, perpustakaan ini berkomitmen untuk menjadi
                pusat informasi, literasi, dan budaya baca di lingkungan SMA Negeri 1 Ciruas, Kabupaten Serang, Banten.
            </p>
        </div>

        <!-- Image -->
        <div class="md:w-1/2">
            <img src="{{ asset('img/perpus-foto.jpg') }}" alt="Perpustakaan"
                class="rounded w-full h-auto object-cover">
        </div>
    </section>



    <section class="px-10 py-12 max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-[300px_1fr] gap-6 items-center justify-center">
            <!-- Jam Operasional -->
            <div class="bg-white border border-base200 rounded p-5 h-full flex flex-col">
                <h2 class="text-xl font-bold text-center mb-6 text-[#233647]">Jam Operasional</h2>
                <ul class="space-y-3 text-sm text-text break-words">
                    <li class="flex items-start gap-2">
                        <span class="text-lg"><svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <strong>Buka</strong><br />
                            07.00 - 15.00 WIB
                        </div>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-lg"><svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <strong>Istirahat</strong><br />
                            11.00 - 13.00 WIB
                        </div>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-lg"><svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <strong>Tutup</strong><br />
                            Sabtu, Minggu, & Hari Libur
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Alur Peminjaman -->
            <div class="bg-white border border-base200 rounded p-5 h-full flex flex-col">
                <h2 class="text-xl font-bold text-center mb-6 text-[#233647]">Alur Peminjaman</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5 mb-6">
                    <!-- Step 1–4 -->
                    <div>
                        <div class="flex items-center gap-1.5">
                            <div
                                class="w-5 h-5 rounded-full border border-[#233647] text-[#233647] flex items-center justify-center text-[10px] font-semibold">
                                1</div>
                            <h3 class="font-semibold text-[#233647] text-sm">Login</h3>
                            <div class="flex-1 border-b-2 border-[#233647] ml-1"></div>
                        </div>
                        <p class="mt-2 text-sm text-text break-words">Siswa login menggunakan akun yang telah
                            diberikan (NIS / email sekolah)</p>
                    </div>

                    <div>
                        <div class="flex items-center gap-1.5">
                            <div
                                class="w-5 h-5 rounded-full border border-[#233647] text-[#233647] flex items-center justify-center text-[10px] font-semibold">
                                2</div>
                            <h3 class="font-semibold text-[#233647] text-sm">Cari Buku</h3>
                            <div class="flex-1 border-b-2 border-[#233647] ml-1"></div>
                        </div>
                        <p class="mt-2 text-sm text-text">Gunakan fitur pencarian atau telusuri katalog.</p>
                    </div>

                    <div>
                        <div class="flex items-center gap-1.5">
                            <div
                                class="w-5 h-5 rounded-full border border-[#233647] text-[#233647] flex items-center justify-center text-[10px] font-semibold">
                                3</div>
                            <h3 class="font-semibold text-[#233647] text-sm">Ajukan Peminjaman</h3>
                            <div class="flex-1 border-b-2 border-[#233647] ml-1"></div>
                        </div>
                        <p class="mt-2 text-sm text-text">Klik tombol "Pinjam" jika buku tersedia.</p>
                    </div>

                    <div>
                        <div class="flex items-center gap-1.5">
                            <div
                                class="w-5 h-5 rounded-full border border-[#233647] text-[#233647] flex items-center justify-center text-[10px] font-semibold">
                                4</div>
                            <h3 class="font-semibold text-[#233647] text-sm">Ambil Buku</h3>
                            <div class="flex-1 border-b-2 border-[#233647] ml-1"></div>
                        </div>
                        <p class="mt-2 text-sm text-text">Tunjukkan bukti peminjaman (kode QR atau ID transaksi).
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                    <!-- Step 5–7 -->
                    <div>
                        <div class="flex items-center gap-1.5">
                            <div
                                class="w-5 h-5 rounded-full border border-[#233647] text-[#233647] flex items-center justify-center text-[10px] font-semibold">
                                5</div>
                            <h3 class="font-semibold text-[#233647] text-sm">Gunakan Sesuai Waktu</h3>
                            <div class="flex-1 border-b-2 border-[#233647] ml-1"></div>
                        </div>
                        <p class="mt-2 text-sm text-text">Lama pinjam 7 hari setelah peminjaman.</p>
                    </div>

                    <div>
                        <div class="flex items-center gap-1.5">
                            <div
                                class="w-5 h-5 rounded-full border border-[#233647] text-[#233647] flex items-center justify-center text-[10px] font-semibold">
                                6</div>
                            <h3 class="font-semibold text-[#233647] text-sm">Kembalikan Buku</h3>
                            <div class="flex-1 border-b-2 border-[#233647] ml-1"></div>
                        </div>
                        <p class="mt-2 text-sm text-text">Datang ke perpustakaan, serahkan buku ke petugas.</p>
                    </div>

                    <div>
                        <div class="flex items-center gap-1.5">
                            <div
                                class="w-5 h-5 rounded-full border border-[#233647] text-[#233647] flex items-center justify-center text-[10px] font-semibold">
                                7</div>
                            <h3 class="font-semibold text-[#233647] text-sm">Selesai</h3>
                            <div class="flex-1 border-b-2 border-[#233647] ml-1"></div>
                        </div>
                        <p class="mt-2 text-sm text-text">Silahkan Anda bisa melakukan peminjaman kembali.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Fasilitas -->
    <section class="px-10 py-12 max-w-7xl mx-auto mt-10">
        <h2 class="text-2xl font-bold text-center text-[#233647] mb-10">Fasilitas</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Ruang Baca -->
            <div class="bg-[#F1F8FB] p-6 rounded flex gap-4">
                <div class="bg-white rounded-md p-2 h-fit">
                    <!-- Icon Book -->
                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                            d="M12.1429 11v9m0-9c-2.50543-.7107-3.19099-1.39543-6.13657-1.34968-.48057.00746-.86348.38718-.86348.84968v7.2884c0 .4824.41455.8682.91584.8617 2.77491-.0362 3.45995.6561 6.08421 1.3499m0-9c2.5053-.7107 3.1067-1.39542 6.0523-1.34968.4806.00746.9477.38718.9477.84968v7.2884c0 .4824-.4988.8682-1 .8617-2.775-.0362-3.3758.6561-6 1.3499m2-14c0 1.10457-.8955 2-2 2-1.1046 0-2-.89543-2-2s.8954-2 2-2c1.1045 0 2 .89543 2 2Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-[#233647] mb-1">Ruang Baca</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Ruang baca dilengkapi meja, kursi, dan pencahayaan
                        yang baik untuk mendukung kegiatan belajar dan membaca dengan tenang.</p>
                </div>
            </div>

            <!-- Koleksi Digital -->
            <div class="bg-[#F1F8FB] p-6 rounded flex gap-4">
                <div class="bg-white rounded-md p-2 h-fit">
                    <!-- Icon Layers -->
                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5.005 11.19V12l6.998 4.042L19 12v-.81M5 16.15v.81L11.997 21l6.998-4.042v-.81M12.003 3 5.005 7.042l6.998 4.042L19 7.042 12.003 3Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-[#233647] mb-1">Koleksi Digital</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Perpustakaan menyediakan koleksi buku digital
                        (PDF/ePub) yang bisa diakses melalui perangkat siswa, baik di perpustakaan maupun dari rumah.
                    </p>
                </div>
            </div>

            <!-- Ruang Diskusi -->
            <div class="bg-[#F1F8FB] p-6 rounded flex gap-4">
                <div class="bg-white rounded-md p-2 h-fit">
                    <!-- Icon Users Group -->
                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                            d="M4.5 17H4a1 1 0 0 1-1-1 3 3 0 0 1 3-3h1m0-3.05A2.5 2.5 0 1 1 9 5.5M19.5 17h.5a1 1 0 0 0 1-1 3 3 0 0 0-3-3h-1m0-3.05a2.5 2.5 0 1 0-2-4.45m.5 13.5h-7a1 1 0 0 1-1-1 3 3 0 0 1 3-3h3a3 3 0 0 1 3 3 1 1 0 0 1-1 1Zm-1-9.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-[#233647] mb-1">Ruang Diskusi</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Tersedia ruang kecil untuk kegiatan belajar
                        kelompok atau diskusi antar siswa, dilengkapi meja bundar dan papan tulis.</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Akses Wi-Fi -->
            <div class="bg-[#F1F8FB] p-6 rounded flex gap-4">
                <div class="bg-white rounded-md p-2 h-fit">
                    <!-- Icon Wi-Fi -->
                    <svg class="w-6 h-6 text-[#233647]" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5.05 9a10 10 0 0113.9 0M8.53 12.47a5 5 0 016.94 0M12 16h.01" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-[#233647] mb-1">Akses Wi-Fi Gratis</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Wi-Fi disediakan untuk menunjang kegiatan belajar
                        berbasis digital, seperti mengakses e-book, jurnal, atau tugas daring.</p>
                </div>
            </div>

            <!-- Loker Penyimpanan -->
            <div class="bg-[#F1F8FB] p-6 rounded flex gap-4">
                <div class="bg-white rounded-md p-2 h-fit">
                    <!-- Icon Lock -->
                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 14v3m-3-6V7a3 3 0 1 1 6 0v4m-8 0h10a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-7a1 1 0 0 1 1-1Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-[#233647] mb-1">Loker Penyimpanan</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Siswa dapat menyimpan tas dan barang bawaan di
                        loker yang aman sebelum memasuki ruang baca, menjaga kenyamanan dan keamanan.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- Footer -->
    <!-- Footer -->
    <footer class="bg-gray-100 px-4 py-10 mt-16">
        <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-6 text-sm text-gray-700">
            <div>
                <div class="flex items-center gap-4 mb-2">
                    <img src="img/logo-smancir.png" class="w-8 h-10" />
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

        <div class="text-center text-xs text-gray-600 mt-8 border-t pt-4">
            Hanware &nbsp; · &nbsp; © 2025 · PUSMANCIR &nbsp;
        </div>
    </footer>

    <script>
        < script src = "https://cdn.jsdelivr.net/npm/iconsax-icons@1.0.0/dist/iconsax.js" >
    </script>

    </script>
</body>

</html>
