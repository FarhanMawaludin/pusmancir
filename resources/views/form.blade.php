<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- Library html5-qrcode --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
</head>

<body class="bg-white text-text">
    <section class="min-h-screen flex">
        <!-- Gambar Kiri -->
        <!-- Scanner Kamera -->
        <div class="hidden md:flex w-1/2 bg-gray-50 h-screen flex-col justify-center items-center p-6 overflow-auto">
            <label class="block font-medium text-sm mb-4 text-gray-700 text-center">
                Scan Barcode Anggota (Via Kamera)
            </label>

            <!-- Area kamera -->
            <div id="reader" class="mb-4 border border-gray-300 rounded-md shadow"
                style="width: 100%; max-width: 400px;">
            </div>

            <!-- Info -->
            <p class="text-center text-sm text-gray-500 mb-1">
                Kamera akan otomatis mengisi kolom NISN/NIP.
            </p>
            <small class="text-gray-500 block text-center">
                Arahkan barcode ke kamera, data akan dimasukkan otomatis.
            </small>
        </div>

        <!-- Form Kanan -->
        <div class="w-full md:w-1/2 flex flex-col justify-center items-center px-6 sm:px-12 relative">

            <!-- Tombol kembali -->
            {{-- <a href="{{ route('admin.dashboard.index') }}"
                class="absolute top-10 left-6 flex items-center text-sm text-gray-600 hover:text-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Dashboard
            </a> --}}

            <div class="w-full max-w-md space-y-6 mt-8">

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
                <form method="POST" action="{{ route('store') }}" class="space-y-4">
                    @csrf

                    <!-- NISN -->
                    <div>
                        <label for="nisn" class="block text-sm font-medium text-gray-700">NISN/NIP</label>
                        <input type="text" name="nisn" id="nisn"
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
                        <input type="text" name="nama" id="nama"
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

    <script>
        function onScanSuccess(decodedText) {
            const nisnInput = document.getElementById('nisn');
            nisnInput.value = decodedText;

            // Langsung submit form
            const form = document.querySelector('form'); // atau pakai ID kalau form-nya banyak
            if (form) {
                form.submit();
            }
        }

        const html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", {
                fps: 10,
                qrbox: 250
            },
            false
        );

        html5QrcodeScanner.render(onScanSuccess);
    </script>


    @if (session('success') || session('error') || session('info') || session('warning'))
        @php
            $alertType = session('success')
                ? 'success'
                : (session('error')
                    ? 'error'
                    : (session('info')
                        ? 'info'
                        : 'warning'));

            $alertTitle = session('success')
                ? 'Berhasil!'
                : (session('error')
                    ? 'Gagal!'
                    : (session('info')
                        ? 'Info'
                        : 'Peringatan'));

            $alertText = session('success') ?? (session('error') ?? (session('info') ?? session('warning')));
        @endphp

        <script>
            Swal.fire({
                icon: '{{ $alertType }}',
                title: '{{ $alertTitle }}',
                text: '{{ $alertText }}',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                },
                timer: 1500, // tampil 1 detik
                timerProgressBar: true,
                showConfirmButton: false
            });
        </script>
    @endif
</body>

</html>
