<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!-- Meta untuk Sosial Media -->
    <meta property="og:title" content="{{ $berita->judul }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($berita->isi), 150) }}">
    <meta property="og:image"
        content="{{ $berita->thumbnail ? asset($berita->thumbnail) : 'https://source.unsplash.com/random/300x200?news' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="article">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $berita->judul }}">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($berita->isi), 150) }}">
    <meta name="twitter:image"
        content="{{ $berita->thumbnail ? asset($berita->thumbnail) : 'https://source.unsplash.com/random/300x200?news' }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita - PERPUSTAKAAN SMA NEGERI 1 CIRUAS</title>
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

    <section class="container mx-auto px-6 mt-36">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            {{-- Main content --}}
            <div class="lg:col-span-2">
                {{-- Judul dan Gambar Utama --}}
                <h1 class="text-3xl font-bold mb-4 leading-snug">{{ $berita->judul }}</h1>
                @if ($berita->thumbnail)
                    <img src="{{ asset($berita->thumbnail) }}" alt="{{ $berita->judul }}"
                        class="w-full h-64 md:h-96 object-cover rounded mb-6">
                @endif

                {{-- Meta Info --}}
                <div class="text-sm text-gray-500 flex items-center gap-4 mb-4">
                    <span><i class="far fa-calendar-alt"></i> {{ $berita->created_at->format('d M Y') }}</span>
                    <span><i class="far fa-user"></i> {{ $berita->penulis ?? 'Admin' }}</span>
                </div>

                {{-- Isi Berita --}}
                <div class="prose prose-lg max-w-none mb-12">
                    {!! $berita->isi !!}
                </div>

                {{-- Related Posts --}}
                @if ($related->count())
                    <h2 class="text-xl font-bold text-red-600 mb-4">Postingan Lainnya</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach ($related as $item)
                            <a href="{{ route('berita.show', $item->id) }}"
                                class="bg-white rounded border border-gray-200 hover:shadow-md transition p-3">
                                @if ($item->thumbnail)
                                    <img src="{{ asset($item->thumbnail) }}"
                                        class="rounded mb-2 w-full h-32 object-cover" alt="{{ $item->judul }}">
                                @else
                                    <img src="https://source.unsplash.com/random/300x200?news"
                                        class="rounded mb-2 w-full h-32 object-cover" alt="Default">
                                @endif
                                <h4 class="font-semibold text-sm leading-tight">{{ $item->judul }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $item->created_at->format('d M Y') }}</p>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-8">
                {{-- Top Post --}}
                <div class="bg-white rounded border border-gray-200 p-4">
                    <h3 class="text-md font-bold text-red-600 mb-4">Top Post</h3>
                    @foreach ($related->take(5) as $item)
                        <a href="{{ route('berita.show', $item->id) }}"
                            class="flex items-center gap-3 mb-3 hover:bg-gray-100 p-2 rounded">
                            <div class="w-16 h-16 flex-shrink-0">
                                @if ($item->thumbnail)
                                    <img src="{{ asset($item->thumbnail) }}"
                                        class="w-full h-full object-cover rounded" alt="">
                                @else
                                    <img src="https://source.unsplash.com/random/80x80?news"
                                        class="w-full h-full object-cover rounded" alt="">
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-semibold leading-tight">{{ Str::limit($item->judul, 40) }}</p>
                                <span class="text-xs text-gray-400">{{ $item->created_at->format('d M Y') }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
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
