<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PUSMANCIR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" href="{{ asset('logo-smancir.png') }}" type="image/png">
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <!-- Flowbite JS -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
</head>

<body class="bg-white text-text">
    <section class="min-h-screen flex">
        <!-- Gambar Kiri -->
        <div class="hidden md:block w-1/2 bg-gray-100 h-screen">
            <img src="img/Buku-Login.jpg" alt="Buku Bertumpuk" class="w-full h-full object-cover" />
        </div>

        <!-- Form Kanan -->
        <div class="w-full md:w-1/2 flex flex-col justify-center items-center px-6 sm:px-12 relative">
            <!-- Tombol kembali -->
            <a href="{{ route('welcome') }}"
                class="absolute top-10 left-6 flex items-center text-sm text-gray-600 hover:text-primary700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>

            <div class="w-full max-w-md space-y-6 mt-8">
                <!-- Judul -->
                <div class="text-center mb-6">
                    <h1 class="text-2xl font-bold text-text mb-1">Selamat Datang di PUSMANCIR</h1>
                    <p class="text-sm text-base400">
                        Akses buku, jurnal, dan koleksi perpustakaan<br> sekolah dengan mudah!
                    </p>
                </div>


                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <!-- Username -->
                    <div class="mb-4">
                        <label for="email"
                            class="block text-[14px] font-semibold text-mirage-950 mb-1">Username</label>
                        <input type="text" name="username" id="username" value="{{ old('username') }}" required
                            autofocus
                            class="form-input block w-full px-4 py-2 text-gray-900 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @error('username')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="block text-[14px] font-semibold text-mirage-950 mb-1">Kata
                            sandi</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                class="form-input block w-full px-4 py-2 text-gray-900 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none pr-4">
                        </div>
                        @error('password')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ingat Saya & Lupa Password -->
                    {{-- <div class="flex items-center justify-between mt-4 mb-6">
                        <label for="remember_me" class="flex items-center">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a class="text-sm text-blue-600 hover:underline" href="{{ route('password.request') }}">
                                Lupa password
                            </a>
                        @endif
                    </div> --}}

                    <!-- Tombol -->
                    <div class="space-y-3">
                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm font-semibold transition duration-200">
                            Masuk
                        </button>

                        {{-- <a href="{{ route('register') }}"
                            class="w-full inline-block text-center text-blue-600 border border-blue-500 py-2 px-4 rounded-md hover:bg-blue-50 transition duration-200 text-sm font-semibold">
                            Daftar
                        </a> --}}
                    </div>
                </form>
            </div>
        </div>
    </section>
</body>

</html>
