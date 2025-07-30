<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Meta untuk Sosial Media (Open Graph untuk WhatsApp, Facebook, dll) -->
    <meta property="og:title" content="{{ $berita->judul }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($berita->isi), 150) }}">
    <meta property="og:image"
        content="{{ $berita->thumbnail ? asset($berita->thumbnail) : 'https://source.unsplash.com/random/1200x630?news' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="article">
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <!-- Twitter Card (untuk Twitter/X) -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $berita->judul }}">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($berita->isi), 150) }}">
    <meta name="twitter:image"
        content="{{ $berita->thumbnail ? asset($berita->thumbnail) : 'https://source.unsplash.com/random/1200x630?news' }}">

    <title>{{ $berita->judul }} - PERPUSTAKAAN SMA NEGERI 1 CIRUAS</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('logo-smancir.png') }}" type="image/png">

    <!-- CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                        <li>
                            <a href="{{ route('pengaduan') }}"
                                class="block py-2 px-3 text-gray-400 hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0">Pengaduan</a>
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
                    <span
                        class="flex items-center gap-1 border border-gray-300 rounded-full px-2 py-1 bg-blue-700 text-white">
                        <i class="far fa-eye"></i> {{ $berita->views ?? 0 }} kali dilihat
                    </span>

                    {{-- Share Buttons --}}
                    <a href="https://api.whatsapp.com/send?text={{ urlencode(route('berita.show', $berita->id)) }}"
                        target="_blank" class="text-green-500 hover:text-green-600" title="Bagikan ke WhatsApp">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                            fill="currentColor">
                            <path
                                d="M20.5 3.5A11.5 11.5 0 0012 1C6.2 1 1.5 5.8 1.5 11.5c0 2 .6 3.9 1.7 5.6L1 23l6.2-2c1.6 1 3.5 1.6 5.4 1.6h.1c6.1 0 11.1-5 11.1-11.1 0-3-1.2-5.8-3.3-7.9zM12 21c-1.6 0-3.1-.4-4.4-1.2l-.3-.2-3.7 1.2 1.2-3.6-.2-.3c-1-1.3-1.6-2.9-1.6-4.5C3 6.5 7.5 2 12.9 2 17.4 2 21 5.6 21 10.1S17.4 21 12 21zm5.3-6.6c-.3-.2-1.5-.7-1.7-.8-.2-.1-.3-.1-.5.1s-.6.8-.7 1c-.1.2-.3.2-.6.1-1.7-.8-2.9-1.6-4.1-3.7-.3-.6.3-.5.8-1.6.1-.2.1-.4 0-.6s-.5-1.1-.7-1.5c-.2-.5-.4-.4-.6-.4s-.3 0-.5 0c-.2 0-.5.1-.7.4-.2.2-.8.8-.8 2 0 1.1.8 2.2.9 2.4 1.1 1.8 2.6 3.1 4.5 3.9.6.2 1.1.4 1.5.5.6.2 1.1.1 1.5.1.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2z" />
                        </svg>
                    </a>

                    <button onclick="copyToClipboard('{{ route('berita.show', $berita->id) }}')"
                        class="text-blue-500 hover:text-blue-600" title="Salin link">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2M16 8h2a2 2 0 012 2v8a2 2 0 01-2 2h-8a2 2 0 01-2-2v-2" />
                        </svg>
                    </button>
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

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Link berhasil disalin');
            }).catch(err => {
                alert('Gagal menyalin: ' + err);
            });
        }
    </script>
</body>

</html>
