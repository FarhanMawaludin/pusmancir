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
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

    {{-- Tailwind --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script> --}}

    {{-- Library html5-qrcode --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.min.js"></script>

    <!-- Flowbite JS -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- <!-- Vite Assets (Simulated) -->
    <link href="/dist/app.css" rel="stylesheet" />
    <script src="/dist/app.js"></script> --}}
</head>

<body class="text-text font-Inter bg-white">
    {{-- Navigation --}}
    @include('layouts.anggota-navigation')

    {{-- Sidebar --}}
    @include('layouts.anggota-sidebar')


    {{-- <!-- Content --> --}}
    <div class="p-4 sm:ml-64">
        <div class="p-4 mt-14">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
    <x-alert />

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Push dummy state untuk mendeteksi tombol back
            history.pushState(null, '', location.href);

            window.addEventListener('popstate', function(event) {
                // Saat tombol back ditekan
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
                        // Submit form logout secara manual
                        document.getElementById('logout-form').submit();
                    } else {
                        // Dorong history lagi supaya user tetap di halaman
                        history.pushState(null, '', location.href);
                    }
                });
            });
        });
    </script>

</body>

</html>
