<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita - PUSMANCIR</title>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white text-text">
    <!-- Navbar -->
    <header class="flex justify-between items-center py-4 px-10 shadow-sm bg-white md:flex-row lg:flex-row xl:flex-row">
        <nav class="bg-white fixed w-full z-20 top-0 start-0 lg:px-6">
            <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
                <a href="" class="flex items-center space-x-3 rtl:space-x-reverse">
                    <img src="{{ asset('img/logo-smancir.png') }}" alt="Logo" class="w-10 h-12" />
                    <span class="text-xl font-bold text-text hidden md:block">PUSMANCIR</span>
                </a>
                <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
                    <button type="button"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center "
                        onclick="location.href='{{ route('login') }}'">Masuk</button>
                    <button data-collapse-toggle="navbar-sticky" type="button"
                        class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
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
                            <a href="{{ route('welcome') }}"
                                class="block py-2 px-3 !text-base300 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0"
                                aria-current="page">Beranda</a>
                        </li>
                        <li>
                            <a href="{{ route('informasi') }}"
                                class="block py-2 px-3 text-base300 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0">Informasi</a>
                        </li>
                        <li>
                            <a href="{{ route('berita.index') }}"
                                class="block py-2 px-3 text-white bg-blue-700 rounded-sm md:bg-transparent md:text-blue-700 md:p-0">Berita</a>
                        </li>
                        <li>
                            <a href="{{ route('peringkat.index') }}"
                                class="block py-2 px-3 text-base300 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0">Peringkat</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero slider -->
    <section class="container mx-auto px-6 mt-20">
        <div class="relative rounded-lg overflow-hidden shadow-lg">
            <img src="https://source.unsplash.com/random/1024x480?technology" alt="Hero"
                class="w-full h-64 object-cover">
            <div class="absolute inset-0 bg-black/40 flex flex-col justify-end p-6">
                <h2 class="text-white text-lg md:text-2xl font-semibold">Why I Stopped Using Multiple Monitor</h2>
                <p class="text-white text-xs mt-1">A Single Monitor Manifesto – Many Developers Believe Multiple
                    Monitors Improve Productivity...</p>
            </div>
        </div>
    </section>

    <!-- Popular Posts -->
    <section class="container mx-auto px-6 mt-10">
        <h3 class="text-lg font-semibold mb-4 text-red-600">Popular Posts</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-3">
                <img src="https://source.unsplash.com/random/300x200?sea" class="rounded mb-2" alt="">
                <h4 class="font-semibold text-sm">Opening Day of Boating Season, Seattle WA</h4>
                <p class="text-xs text-gray-500 mt-1">James - Aug 18, 2022</p>
            </div>
            <div class="bg-white rounded-lg shadow p-3">
                <img src="https://source.unsplash.com/random/300x200?laptop" class="rounded mb-2" alt="">
                <h4 class="font-semibold text-sm">How To Choose The Right Laptop For Programming</h4>
                <p class="text-xs text-gray-500 mt-1">Louis Hoebregts - July 25, 2022</p>
            </div>
            <div class="bg-white rounded-lg shadow p-3">
                <img src="https://source.unsplash.com/random/300x200?car" class="rounded mb-2" alt="">
                <h4 class="font-semibold text-sm">How We Built The First Real Self-Driving Car</h4>
                <p class="text-xs text-gray-500 mt-1">Mary - July 14, 2022</p>
            </div>
            <div class="bg-white rounded-lg shadow p-3">
                <img src="https://source.unsplash.com/random/300x200?food" class="rounded mb-2" alt="">
                <h4 class="font-semibold text-sm">How To Persuade Your Parents To Buy Fast Food</h4>
                <p class="text-xs text-gray-500 mt-1">Jon Kantner - May 10, 2022</p>
            </div>
        </div>
    </section>

    <!-- Latest Videos -->
    <section class="container mx-auto px-6 mt-12">
        <h3 class="text-lg font-semibold mb-4 text-red-600">Latest Videos</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="relative">
                <img src="https://source.unsplash.com/random/600x400?music" class="rounded-lg" alt="">
                <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                    <svg class="w-14 h-14 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M6.5 5.5l7 4.5-7 4.5v-9z"></path>
                    </svg>
                </div>
                <div class="mt-3">
                    <h4 class="text-sm font-semibold">How Music Affects Your Brain (Plus 11 Artists To Listen To At
                        Work)</h4>
                    <p class="text-xs text-gray-500">You've Read All Your Free Member-Only Stories...</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="flex flex-col gap-2">
                    <img src="https://source.unsplash.com/random/200x150?boxing" class="rounded" alt="">
                    <h4 class="text-xs font-semibold">5 Reasons Why You Should Wrap Your Hands Before Boxing</h4>
                </div>
                <div class="flex flex-col gap-2">
                    <img src="https://source.unsplash.com/random/200x150?food2" class="rounded" alt="">
                    <h4 class="text-xs font-semibold">Food for the Mind and Body</h4>
                </div>
                <div class="flex flex-col gap-2">
                    <img src="https://source.unsplash.com/random/200x150?genre" class="rounded" alt="">
                    <h4 class="text-xs font-semibold">Music Genre Classification With AI</h4>
                </div>
                <div class="flex flex-col gap-2">
                    <img src="https://source.unsplash.com/random/200x150?neon" class="rounded" alt="">
                    <h4 class="text-xs font-semibold">The Neon Room Setup Tour</h4>
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
</body>

</html>
