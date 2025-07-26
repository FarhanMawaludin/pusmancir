<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIGMAGANG') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('logo/Logo-Sigmagang.png') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <link rel="icon" href="{{ asset('logo-smancir.png') }}" type="image/png">
    {{-- <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.min.js"></script> --}}

    <!-- Flowbite CSS -->
    {{-- <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" /> --}}

    {{-- Tailwind --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script> --}}

    {{-- Library html5-qrcode --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.min.js"></script>

    <!-- Flowbite JS -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script> --}}

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Trix Editor -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.css">

    {{-- <!-- Vite Assets (Simulated) -->
    <link href="/dist/app.css" rel="stylesheet" />
    <script src="/dist/app.js"></script> --}}
</head>

<body class="text-text font-Inter bg-white">
    {{-- Navigation --}}
    @include('layouts.admin-navigation')

    {{-- Sidebar --}}
    @include('layouts.admin-sidebar')


    {{-- <!-- Content --> --}}
    <div class="p-4 sm:ml-64">
        <div class="p-4 mt-14">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
    <x-alert />

    <!-- Trix Editor JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let isLogoutConfirmed = false;

            // Tambahkan dua state ke history untuk cegah keluar langsung
            history.pushState({
                page: 1
            }, '', '');
            history.pushState({
                page: 2
            }, '', '');

            // Deteksi tombol back
            window.addEventListener('popstate', function() {
                if (!isLogoutConfirmed) {
                    Swal.fire({
                        title: 'Konfirmasi Keluar',
                        text: 'Apakah Anda yakin ingin keluar dari akun?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Keluar',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            isLogoutConfirmed = true;
                            document.getElementById('logout-form').submit();
                        } else {
                            // Tambah lagi state agar tidak langsung keluar
                            history.pushState({
                                page: 2
                            }, '', '');
                        }
                    });
                }
            });

            // Untuk Safari: halaman di-reload dari cache, push state ulang
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    history.pushState({
                        page: 1
                    }, '', '');
                    history.pushState({
                        page: 2
                    }, '', '');
                }
            });

            // Cegah tutup tab/browser kalau belum logout
            window.addEventListener('beforeunload', function(e) {
                if (!isLogoutConfirmed) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        });
    </script>

</body>

</html>
