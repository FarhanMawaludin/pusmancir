@extends('layouts.admin-app')

@section('content')

    <div class="flex justify-between paketItems-center w-full">
        <!-- Judul + Form -->
        <section class="mb-2 w-full"> <!-- ✅ DITAMBAHKAN w-full -->

            <!-- Judul -->
            <h1 class="text-2xl font-bold text-text mb-4">Data Pengembalian</h1>

            {{-- === FORM UTAMA === --}}
            <div class="w-full border border-gray-300 rounded p-4">
                <!-- Grid wrapper -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    <!-- === SCAN BARCODE DENGAN KAMERA === -->
                    <div class="col-span-1 md:col-span-2 md:col-start-2">
                        <label class="block font-medium text-sm mb-2 text-gray-700 text-center">
                            Scan Barcode Anggota (Via Kamera)
                        </label>

                        <!-- Area kamera -->
                        <div class="flex justify-center">
                            <div id="reader" class="mb-3 border border-gray-300 rounded-md shadow"
                                style="width: 100%; max-width: 400px;">
                            </div>
                        </div>

                        <!-- Info Mode -->
                        <p class="text-center text-sm text-gray-500 mb-1">
                            Kamera akan otomatis mengisi kolom pencarian anggota.
                        </p>
                        <small class="text-gray-500 block text-center">
                            Arahkan barcode ke kamera, data akan dicari otomatis.
                        </small>
                    </div>

                </div>
            </div>
        </section>
    </div>

    <div class="flex justify-between paketItems-center">
        <form id="searchForm" method="GET" action="{{ route('admin.pengembalian.index') }}"
            class="flex w-full max-w-lg my-6">
            <div class="relative w-full">
                <input type="search" id="search-dropdown" name="search"
                    class="block w-full rounded-md bg-white px-3 py-2 text-base text-text 
            border border-gray-300 placeholder:text-gray-400
            focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm"
                    placeholder="Cari siswa" value="{{ $search ?? '' }}" />
                <button type="submit"
                    class="absolute top-0 end-0 p-2.5 h-full text-white bg-blue-700 rounded border-blue-700
               hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                </button>
            </div>
        </form>
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
                            ? 'text-white border-red-600 bg-red-600 font-semibold'
                            : ($isDipinjam
                                ? 'text-white border-orange-600 bg-orange-600 font-semibold'
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


    <!-- JavaScript -->
    <script>
        const dropdownButton = document.getElementById('dropdown-button');
        const dropdownMenu = document.getElementById('dropdown');
        const categoryButtons = document.querySelectorAll('.category-btn');
        const selectedCategoryInput = document.getElementById('selected-category');

        // Toggle dropdown
        dropdownButton.addEventListener('click', () => {
            dropdownMenu.classList.toggle('hidden');
        });

        // Handle category selection and submit form
        categoryButtons.forEach(button => {
            button.addEventListener('click', () => {
                const selectedValue = button.getAttribute('data-value');
                const displayText = button.textContent.trim();

                selectedCategoryInput.value = selectedValue;
                dropdownButton.innerHTML = `${displayText}
            <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 1 4 4 4-4" />
            </svg>`;

                dropdownMenu.classList.add('hidden');

                // Automatically submit the form when a category is selected
                button.closest('form').submit();
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });
    </script>

    <script>
        function cekAnggota() {
            const nisn = document.getElementById('anggota_id_input').value.trim();
            const infoBox = document.getElementById('anggota_info');

            if (!nisn) {
                infoBox.innerHTML = `<span class="text-red-500">NISN tidak boleh kosong</span>`;
                return;
            }

            fetch(`{{ url('/api/anggota') }}/${nisn}`)
                .then(res => res.json())
                .then(data => {
                    if (data.nama) {
                        infoBox.innerHTML = `👤 Nama: <strong>${data.nama}</strong>`;
                    } else {
                        infoBox.innerHTML =
                            `<span class="text-red-500">${data.error ?? 'Anggota tidak ditemukan'}</span>`;
                    }
                })
                .catch(() => {
                    infoBox.innerHTML = `<span class="text-red-500">Gagal mengambil data</span>`;
                });
        }

        function cekEksemplar() {
            const rfid = document.getElementById('eksemplar_id_input').value.trim();
            const infoBox = document.getElementById('eksemplar_info');

            if (!rfid) {
                infoBox.innerHTML = `<span class="text-red-500">RFID tidak boleh kosong</span>`;
                return;
            }

            fetch(`{{ url('/api/eksemplar') }}/${rfid}`)
                .then(res => res.json())
                .then(data => {
                    if (data.judul_buku) {
                        infoBox.innerHTML = `📚 Judul: <strong>${data.judul_buku}</strong>`;
                    } else {
                        infoBox.innerHTML =
                            `<span class="text-red-500">${data.error ?? 'Eksemplar tidak ditemukan'}</span>`;
                    }
                })
                .catch(() => {
                    infoBox.innerHTML = `<span class="text-red-500">Gagal mengambil data</span>`;
                });
        }
    </script>

    <script>
        function onScanSuccess(decodedText) {
            const searchBox = document.getElementById('search-dropdown');
            searchBox.value = decodedText;

            // Auto submit jika form tersedia
            const searchForm = document.getElementById('searchForm');
            if (searchForm) {
                searchForm.submit();
            }
        }

        // Inisialisasi scanner untuk mode cari_anggota saja
        const html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", {
                fps: 10,
                qrbox: 250
            },
            false // verbose
        );

        html5QrcodeScanner.render(onScanSuccess);
    </script>
@endsection
