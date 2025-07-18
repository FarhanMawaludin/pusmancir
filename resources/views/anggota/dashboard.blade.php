@extends('layouts.anggota-app')

@section('content')
    <div class=" mt-24 text-center">
        <!-- Header -->
        <h1 class="text-2xl sm:text-3xl font-semibold mb-2">
            Selamat Datang,
            <span class="text-blue-700 font-bold hover:underline cursor-pointer">{{ auth()->user()->name }}</span> 👋
        </h1>
        <p class="text-gray-500 mb-10 text-sm sm:text-base">
            Lengkapi profilmu sekarang untuk dan lakukan peminjaman buku.
        </p>

        <!-- Table -->
        <div class="overflow-x-auto relative rounded-lg border border-gray-200">
            <table class="min-w-full text-sm text-left text-gray-700">
                <thead class="text-xs uppercase bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Judul Buku</th>
                        <th class="px-6 py-3">Pengarang</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                
            </table>

            <!-- Pagination -->
            <div class="p-4">
                {{-- {{ $user->links('pagination::tailwind') }} --}}
            </div>
        </div>
    </div>
@endsection
