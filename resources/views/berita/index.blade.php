<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita - PUSMANCIR</title>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" href="{{ asset('logo-smancir.png') }}" type="image/png">
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
</head>

<body class="bg-white text-text">
    <!-- Navbar -->
    <nav class="bg-white fixed w-full z-20 top-0 left-0">
        <div class="max-w-screen-xl mx-auto p-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">

                <!-- Row untuk logo dan tombol di mobile -->
                <div class="flex justify-between items-center w-full md:hidden">
                    <!-- Logo -->
                    <a href="#" class="flex items-center space-x-3 rtl:space-x-reverse">
                        <img src="{{ asset('/logo-banten.png') }}" class="h-10" alt="Logo" />
                        <img src="{{ asset('/logo-smancir.png') }}" class="h-10" alt="Logo" />
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
                <a href="#" class="hidden md:flex items-center space-x-3 rtl:space-x-reverse w-full md:w-60">
                    <img src="{{ asset('/logo-banten.png') }}" class="h-10" alt="Logo" />
                    <img src="{{ asset('/logo-smancir.png') }}" class="h-10" alt="Logo" />
                    <span class="self-center text-xl font-bold whitespace-nowrap text-text">PUSMANCIR</span>
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
                                class="block py-2 px-3 text-white bg-blue-700 rounded md:bg-transparent md:text-blue-700 md:p-0">Berita</a>
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

    <!-- Hero slider -->
    <section class="container mx-auto px-6 mt-40">
        <a href="{{ route('berita.show', $hero->id) }}">
            <div class="relative rounded-lg overflow-hidden shadow-lg">
                @if ($hero && $hero->thumbnail)
                    <img src="{{ asset($hero->thumbnail) }}" alt="{{ $hero->judul }}"
                        class="w-full h-64 object-cover">
                @else
                    <img src="https://source.unsplash.com/random/1024x480?technology" alt="Hero"
                        class="w-full h-64 object-cover">
                @endif
                <div class="absolute inset-0 bg-black/40 flex flex-col justify-end p-6">
                    <h2 class="text-white text-lg md:text-2xl font-semibold">
                        {{ $hero->judul ?? 'Tidak ada berita terbaru' }}</h2>
                    <p class="text-white text-xs mt-1">{{ Str::limit(strip_tags($hero->isi ?? '...'), 100) }}</p>
                </div>
            </div>
        </a>
    </section>

    <!-- Popular Posts -->
    <section class="max-w-screen-xl mx-auto px-4 py-8 lg:py-16">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold">Berita Populer</h2>
            <p class="text-gray-600 mt-2">Berita yang paling banyak dilihat</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="popular-posts">
            @foreach ($popular as $index => $item)
                <a href="{{ route('berita.show', $item->id) }}"
                    class="post-item block bg-white rounded border border-gray-200 p-3 hover:shadow-lg transition"
                    data-index="{{ $index }}" @if ($index >= 4) hidden @endif>
                    <img src="{{ asset($item->thumbnail) }}" alt="{{ $item->judul }}"
                        class="w-full h-40 object-cover rounded mb-3">

                    <h3 class="text-lg font-semibold text-gray-800 leading-snug">{{ $item->judul }}</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ \Illuminate\Support\Str::limit(strip_tags($item->isi), 100) }}</p>
                    <p class="text-xs text-gray-500 mt-2">Dilihat {{ $item->views }} kali</p>
                </a>
            @endforeach
        </div>

        @if (count($popular) > 4)
            <div class="text-center mt-6">
                <button id="loadMoreBtn" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
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
        </div>

        <div class="text-center text-xs text-gray-400 mt-8 border-t pt-4">
            © 2025 · PUSMANCIR &nbsp; | &nbsp; <a href="#" class="underline">Privacy Policy</a> · <a
                href="#" class="underline">Terms</a> · <a href="#" class="underline">Code of Conduct</a>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const posts = document.querySelectorAll('.post-item');
            const loadMoreBtn = document.getElementById('loadMoreBtn');

            loadMoreBtn?.addEventListener('click', () => {
                let shownCount = 0;
                posts.forEach((post, index) => {
                    if (post.hasAttribute('hidden') && shownCount < 4) {
                        post.removeAttribute('hidden');
                        shownCount++;
                    }
                });

                const remainingHidden = Array.from(posts).filter(post => post.hasAttribute('hidden'));
                if (remainingHidden.length === 0) {
                    loadMoreBtn.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>
