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
        let isLogoutClicked = false;

        document.addEventListener('DOMContentLoaded', () => {
            const logoutBtn = document.getElementById('logout-button');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', () => {
                    isLogoutClicked = true;
                });
            }

            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'hidden' && !isLogoutClicked) {
                    // Mungkin tampilkan notifikasi atau kirim log
                    console.log('User mungkin keluar tanpa logout.');
                }
            });

            if (window.location.pathname === '/dashboard') {
                history.pushState(null, null, location.href);
                window.addEventListener('popstate', function() {
                    Swal.fire({
                        title: 'Keluar Aplikasi?',
                        text: 'Apakah Anda yakin ingin keluar dari akun?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Keluar',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#d33'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('logout-form').submit();
                        } else {
                            history.pushState(null, null, location.href);
                        }
                    });
                });
            }
        });
    </script>

</body>

</html>
