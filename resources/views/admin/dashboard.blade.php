@extends('layouts.admin-app')

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4 mb-4 w-full">
        <div class=" w-full p-4 bg-white border border-gray-200 rounded  dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-2 rounded-full bg-orange-500 text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5.121 17.804A4 4 0 0 1 8.6 16h6.8a4 4 0 0 1 3.478 1.804M15 10a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white"></h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Anggota</p>
                </div>
            </div>
            <div
                class="mt-4 flex items-center ">
                {{-- @if ($user_increase > 0)
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $user_increase }} mahasiswa bertambah</p>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-1 text-green-600" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Tidak ada penambahan</p>
                @endif --}}
            </div>
        </div>
        <div class=" w-full p-4 bg-white border border-gray-200 rounded dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-2 rounded-full bg-orange-500 text-white">
                    <svg class="w-6 h-6 " fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7H5a2 2 0 0 0-2 2v4m5-6h8M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m0 0h3a2 2 0 0 1 2 2v4m0 0v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-6m18 0s-4 2-9 2-9-2-9-2m9-2h.01" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white"></h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Peminjaman</p>
                </div>
            </div>
            <div
                class="mt-4 flex items-center ">
                {{-- @if ($lowongan_increase > 0)
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $lowongan_increase }} lowongan bertambah</p>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-1 text-green-600" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Tidak ada penambahan</p>
                @endif --}}
            </div>
        </div>
        <div class=" w-full p-4 bg-white border border-gray-200 rounded dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-2 rounded-full bg-orange-500 text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 3v4a1 1 0 0 1-1 1H5m4 6 2 2 4-4m4-8v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white"></h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Buku</p>
                </div>
            </div>
            <div
                class="mt-4 flex items-center ">
                {{-- @if ($pengajuan_increase > 0)
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $pengajuan_increase }} pengajuan bertambah</p>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-1 text-green-600" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Tidak ada penambahan</p>
                @endif --}}
            </div>
        </div>
        <div class="w-full p-4 bg-white border border-gray-200 rounded dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-2 rounded-full bg-orange-500 text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 4h12M6 4v16M6 4H5m13 0v16m0-16h1m-1 16H6m12 0h1M6 20H5M9 7h1v1H9V7Zm5 0h1v1h-1V7Zm-5 4h1v1H9v-1Zm5 0h1v1h-1v-1Zm-3 4h2a1 1 0 0 1 1 1v4h-4v-4a1 1 0 0 1 1-1Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white"></h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Eksempalr</p>
                </div>
            </div>
            <div
                class="mt-4 flex items-center ">
                {{-- @if ($perusahaan_increase > 0)
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $perusahaan_increase }} perusahaan bertambah
                    </p>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-1 text-green-600" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Tidak ada penambahan</p>
                @endif --}}
            </div>
        </div>
    </div>
@endsection
