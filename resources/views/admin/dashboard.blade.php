@extends('layouts.admin-app')

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4 mb-10 w-full">
        <div class="w-full p-4 bg-white border border-gray-200 rounded dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-start space-x-4">
                <div class="p-2 rounded-full bg-orange-500 text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5.121 17.804A4 4 0 0 1 8.6 16h6.8a4 4 0 0 1 3.478 1.804M15 10a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalAnggota }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Anggota</p>
                </div>
            </div>
        </div>

        <div class="w-full p-4 bg-white border border-gray-200 rounded dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-start space-x-4">
                <div class="p-2 rounded-full bg-orange-500 text-white">
                    <svg class="w-6 h-6 text-white " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 3v4a1 1 0 0 1-1 1H5m4 6 2 2 4-4m4-8v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalPeminjamanMenunggu }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Menunggu Peminjaman</p>
                </div>
            </div>
        </div>

        <div class="w-full p-4 bg-white border border-gray-200 rounded dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-start space-x-4">
                <div class="p-2 rounded-full bg-orange-500 text-white">
                    <svg class="w-6 h-6text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 6c0 1.657-3.134 3-7 3S5 7.657 5 6m14 0c0-1.657-3.134-3-7-3S5 4.343 5 6m14 0v6M5 6v6m0 0c0 1.657 3.134 3 7 3s7-1.343 7-3M5 12v6c0 1.657 3.134 3 7 3s7-1.343 7-3v-6" />
                    </svg>

                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalJudulBuku }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Judul Buku</p>
                </div>
            </div>
        </div>

        <div class="w-full p-4 bg-white border border-gray-200 rounded dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-start space-x-4">
                <div class="p-2 rounded-full bg-orange-500 text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 4h12M6 4v16M6 4H5m13 0v16m0-16h1m-1 16H6m12 0h1M6 20H5M9 7h1v1H9V7Zm5 0h1v1h-1V7Zm-5 4h1v1H9v-1Zm5 0h1v1h-1v-1Zm-3 4h2a1 1 0 0 1 1 1v4h-4v-4a1 1 0 0 1 1-1Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalEksemplar }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Eksemplar</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-text">Data Peminjaman</h1>

        <button id="dropdownDividerButton" data-dropdown-toggle="dropdownDivider"
            class="flex items-center gap-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded text-sm px-4 py-2.5 text-center dark:bg-blue-700 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            type="button" onclick="location.href='{{ route('admin.peminjaman.index') }}'">

            <!-- Icon tampil di semua ukuran -->
            <svg class="w-5 h-5 md:hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>

            <!-- Teks hanya di desktop -->
            <span class="hidden md:inline">Lihat Semua</span>
        </button>
    </div>


    <div class="overflow-x-auto relative rounded border border-gray-200 mb-10">
        <table class="min-w-full text-sm text-left text-text">
            <thead class="text-xs uppercase bg-gray-100 text-text">
                <tr>
                    <th scope="col" class="px-6 py-3 w-43 md:w-12">No</th>
                    <th scope="col" class="px-6 py-3">Nama Peminjam</th>
                    <th scope="col" class="px-6 py-3">Judul Buku</th>
                    <th scope="col" class="px-6 py-3">Tanggal Pinjam</th>
                    <th scope="col" class="px-6 py-3">Tanggal Kembali</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($peminjaman as $key => $peminjamanItem)
                    @php
                        $pinjam = $peminjamanItem->peminjaman; // relasi Peminjaman
                        $status = $pinjam->status; // menunggu | berhasil | tolak
                    @endphp

                    <tr class="bg-white border-b border-gray-200">
                        <td class="px-6 py-4">{{ $peminjaman->firstItem() + $key }}</td>

                        <td class="px-6 py-4">
                            <div class="font-medium md:text-base truncate md:whitespace-normal">
                                {{ $pinjam->anggota->user->name ?? '-' }}
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            {{ $peminjamanItem->eksemplar->inventori->judul_buku ?? '-' }}
                        </td>

                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->format('d-m-Y') }}
                        </td>

                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($pinjam->tanggal_kembali)->format('d-m-Y') }}
                        </td>

                        {{-- Status (tanpa border, hanya warna bg) --}}
                        <td class="px-6 py-4 font-medium">
                            <span
                                class="px-3 py-1 text-sm rounded-full
                                @if ($status === 'berhasil') bg-green-600 text-white
                                @elseif ($status === 'tolak') bg-red-600 text-white
                                @else bg-orange-600 text-white @endif">
                                {{ ucfirst($status) }}
                            </span>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4">
                            @if ($status === 'menunggu')
                                {{-- ===============  BUTTON APPROVE  =============== --}}
                                <button type="button"
                                    class="btn-approve p-2 bg-green-600 text-white rounded hover:bg-green-700 transition"
                                    data-id="{{ $pinjam->id }}" title="Approve">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>

                                <form id="approve-form-{{ $pinjam->id }}" class="hidden"
                                    action="{{ route('admin.peminjaman.updateStatus', $pinjam->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="aksi" value="berhasil">
                                </form>

                                {{-- ===============  BUTTON TOLAK  =============== --}}
                                <button type="button"
                                    class="btn-tolak p-2 bg-red-600 text-white rounded hover:bg-red-700 transition ml-2"
                                    data-id="{{ $pinjam->id }}" title="Tolak">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                <form id="tolak-form-{{ $pinjam->id }}" class="hidden"
                                    action="{{ route('admin.peminjaman.updateStatus', $pinjam->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="aksi" value="tolak">
                                </form>
                            @else
                                <span class="italic text-gray-500">Selesai diperiksa</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data peminjaman ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>


        <!-- Pagination -->
        <div class="p-4">
            {{ $peminjaman->links('pagination::tailwind') }}
        </div>
    </div>

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-text">Data Pengembalian</h1>

        <button id="dropdownDividerButton" data-dropdown-toggle="dropdownDivider"
            class="flex items-center gap-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded text-sm px-4 py-2.5 text-center dark:bg-blue-700 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            type="button" onclick="location.href='{{ route('admin.pengembalian.index') }}'">

            <!-- Icon tampil di semua ukuran -->
            <svg class="w-5 h-5 md:hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>

            <!-- Teks hanya di desktop -->
            <span class="hidden md:inline">Lihat Semua</span>
        </button>
    </div>

    <div class="overflow-x-auto relative rounded border border-gray-200">
        <table class="min-w-full text-sm text-left text-text">
            <thead class="text-xs uppercase bg-gray-100 text-text">
                <tr>
                    <th scope="col" class="px-6 py-3 w-43 md:w-12">No</th>
                    <th scope="col" class="px-6 py-3">Nama peminjam</th>
                    <th scope="col" class="px-6 py-3">Judul Buku</th>
                    <th scope="col" class="px-6 py-3">Tanggal Pinjam</th>
                    <th scope="col" class="px-6 py-3">Tanggal Kembali</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengembalian as $key => $pengembalianItem)
                    @php
                        $peminjaman = $pengembalianItem->peminjaman;
                        $status = $peminjaman->status; // 'berhasil' atau 'selesai'
                        $isDipinjam = $status === 'berhasil';
                        $isSelesai = $status === 'selesai';
                        $isTerlambat = $isDipinjam && now()->gt($peminjaman->tanggal_kembali);

                        $badgeText = $isTerlambat ? 'Terlambat' : ($isDipinjam ? 'Dipinjam' : 'Selesai');
                        $badgeClass = $isTerlambat
                            ? 'text-white border-red-600 bg-red-600 font-medium'
                            : ($isDipinjam
                                ? 'text-white border-orange-600 bg-orange-600 font-medium'
                                : 'text-gray-600 border-gray-300 bg-gray-100');
                    @endphp

                    <tr class="bg-white border-b border-gray-200">
                        <td class="px-6 py-4">{{ $pengembalian->firstItem() + $key }}</td>

                        <td class="px-6 py-4">
                            <div class="min-w-0">
                                <div class="font-medium md:text-base break-words truncate md:whitespace-normal">
                                    {{ $peminjaman->anggota->user->name }}
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            {{ $pengembalianItem->eksemplar->inventori->judul_buku }}
                        </td>

                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d-m-Y') }}
                        </td>

                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d-m-Y') }}
                        </td>

                        {{-- BADGE STATUS --}}
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-sm rounded-full border {{ $badgeClass }}">
                                {{ $badgeText }}
                            </span>
                        </td>

                        {{-- TOMBOL / BADGE AKSI --}}
                        <td class="px-6 py-4">
                            <div class="flex flex-col items-left justify-center space-y-2">
                                @if ($isDipinjam)
                                    {{-- Tombol Terima --}}
                                    <form id="terima-form-{{ $pengembalianItem->id }}"
                                        action="{{ route('admin.pengembalian.update', $pengembalianItem->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="button" {{-- ubah dari submit ke button --}}
                                            class="btn-terima w-32 flex items-center justify-center bg-blue-700 text-white px-3 py-2 rounded hover:bg-green-700 transition text-sm"
                                            data-id="{{ $pengembalianItem->id }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11.5A1.5 1.5 0 005.5 20H17a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                            Kembali
                                        </button>
                                    </form>


                                    @if ($isTerlambat)
                                        {{-- Tombol Export PDF --}}
                                        <a href="{{ route('admin.pengembalian.export-surat-terlambat', $pengembalianItem->id) }}"
                                            target="_blank"
                                            class="w-32 flex items-center justify-center bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700 transition text-sm">
                                            <svg class="w-5 h-5 text-white mr-2" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M5 17v-5h1.5a1.5 1.5 0 1 1 0 3H5m12 2v-5h2m-2 3h2M5 10V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v6M5 19v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1M10 3v4a1 1 0 0 1-1 1H5m6 4v5h1.375A1.627 1.627 0 0 0 14 15.375v-1.75A1.627 1.627 0 0 0 12.375 12H11Z" />
                                            </svg>

                                            Export Surat
                                        </a>

                                        {{-- Tombol Kirim WA --}}
                                        <form action="{{ route('admin.pengembalian.kirim_wa', $pengembalianItem->id) }}"
                                            method="POST" target="_blank">
                                            @csrf
                                            <button type="submit"
                                                class="w-32 flex items-center justify-center bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700 transition text-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2"
                                                    fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M16.988 13.838c-.253-.127-1.494-.738-1.726-.823-.232-.084-.401-.127-.569.127-.168.253-.653.823-.801.992-.147.168-.295.19-.548.063-.253-.127-1.068-.393-2.034-1.252-.751-.669-1.257-1.496-1.404-1.748-.147-.253-.016-.39.111-.516.114-.113.253-.295.379-.442.127-.147.168-.253.253-.422.084-.168.042-.316-.021-.442-.063-.127-.569-1.369-.779-1.882-.205-.492-.413-.425-.569-.433l-.484-.009c-.168 0-.442.063-.674.316-.232.253-.883.863-.883 2.103 0 1.24.905 2.438 1.032 2.607.127.168 1.783 2.722 4.326 3.818.605.26 1.077.415 1.444.532.607.193 1.16.165 1.596.1.487-.073 1.494-.61 1.707-1.2.21-.584.21-1.084.147-1.2-.063-.116-.232-.184-.484-.31m-4.988 6.162c-1.654 0-3.214-.48-4.539-1.361l-3.162.99.99-3.075c-.905-1.27-1.438-2.787-1.438-4.416 0-4.278 3.486-7.75 7.75-7.75 2.074 0 4.02.81 5.487 2.276a7.706 7.706 0 012.263 5.474c-.002 4.277-3.487 7.762-7.751 7.762z" />
                                                </svg>
                                                Kirim WA
                                            </button>
                                        </form>
                                    @endif
                                @elseif ($isSelesai)
                                    <span class="italic text-gray-500 text-left">Selesai diperiksa</span>
                                @endif
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data pengembalian ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

        <!-- Pagination -->
        <div class="p-4">
            {{ $pengembalian->links('pagination::tailwind') }}
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // ------------ APPROVE ---------------
            document.querySelectorAll('.btn-approve').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    const form = document.getElementById('approve-form-' + id);

                    Swal.fire({
                        title: 'Approve peminjaman?',
                        text: 'Setelah disetujui, buku akan dianggap dipinjam.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, approve',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then(first => {
                        if (first.isConfirmed) {
                            Swal.fire({
                                title: 'Konfirmasi akhir!',
                                text: 'Tindakan ini tidak dapat dibatalkan.',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Approve Sekarang',
                                cancelButtonText: 'Batal',
                                reverseButtons: true
                            }).then(second => {
                                if (second.isConfirmed) form.submit();
                            });
                        }
                    });
                });
            });

            // ------------ TOLAK ---------------
            document.querySelectorAll('.btn-tolak').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    const form = document.getElementById('tolak-form-' + id);

                    Swal.fire({
                        title: 'Tolak peminjaman?',
                        text: 'Peminjaman akan ditolak.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, tolak',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then(first => {
                        if (first.isConfirmed) {
                            Swal.fire({
                                title: 'Konfirmasi terakhir!',
                                text: 'Tindakan ini tidak dapat dibatalkan.',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Tolak Sekarang',
                                cancelButtonText: 'Batal',
                                reverseButtons: true
                            }).then(second => {
                                if (second.isConfirmed) form.submit();
                            });
                        }
                    });
                });
            });
        });
    </script>


    <script>
        document.querySelectorAll('.btn-terima').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Konfirmasi Pengembalian',
                    text: 'Apakah Anda yakin ingin mengubah status menjadi tersedia?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Terima',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('terima-form-' + itemId).submit();
                    }
                });
            });
        });
    </script>


@endsection
