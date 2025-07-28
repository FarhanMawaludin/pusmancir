<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-white text-text">
    <section class="min-h-screen flex">
        <!-- Gambar Kiri -->
        <div class="hidden md:block w-1/2 bg-gray-100 h-screen">
            <img src="{{ asset('img/Buku-Login.jpg') }}" alt="Buku Bertumpuk" class="w-full h-full object-cover" />
        </div>

        <!-- Form Kanan -->
        <div class="w-full md:w-1/2 flex flex-col justify-center items-center px-6 sm:px-12 relative">

            <!-- Tombol kembali -->
            <a href="{{ route('admin.dashboard.index') }}"
                class="absolute top-10 left-6 flex items-center text-sm text-gray-600 hover:text-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Dashboard
            </a>

            <div class="w-full max-w-md space-y-6 mt-12">

                <!-- Judul -->
                <div class="text-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800 mb-1">Form Buku Tamu</h1>
                    <p class="text-sm text-gray-500">Silakan isi form sesuai status Anda (anggota/non anggota)</p>
                </div>

                <!-- Notifikasi -->
                @if ($errors->any())
                    <div class="p-3 rounded-md bg-red-100 text-red-700 text-sm mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>- {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('admin.buku-tamu.store') }}" class="space-y-4">
                    @csrf

                    <!-- NISN -->
                    <div>
                        <label for="nisn" class="block text-sm font-medium text-gray-700">NISN/NIP</label>
                        <input type="text" name="nisn" id="nisn" value="{{ old('nisn') }}"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                border border-gray-300 placeholder:text-gray-400
                                focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                        <small class="text-gray-500">Wajib diisi oleh semua pengunjung</small>
                    </div>

                    <!-- Keperluan -->
                    <div>
                        <label for="keperluan" class="block text-sm font-medium text-gray-700">Keperluan</label>
                        <input type="text" name="keperluan" id="keperluan" value="{{ old('keperluan') }}"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                border border-gray-300 placeholder:text-gray-400
                                focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                        <small class="text-gray-500">Opsional untuk anggota</small>
                    </div>

                    <!-- Nama (Non Anggota) -->
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700">Nama (non anggota)</label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama') }}"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                border border-gray-300 placeholder:text-gray-400
                                focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                    </div>

                    <!-- Asal Instansi (Non Anggota) -->
                    <div>
                        <label for="asal_instansi" class="block text-sm font-medium text-gray-700">Asal Instansi</label>
                        <input type="text" name="asal_instansi" id="asal_instansi" value="{{ old('asal_instansi') }}"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                border border-gray-300 placeholder:text-gray-400
                                focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                    </div>

                    <!-- Tombol -->
                    <div class="pt-4">
                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm font-semibold transition duration-200">
                            Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <x-alert />
</body>

</html>
