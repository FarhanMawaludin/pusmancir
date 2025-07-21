@extends('layouts.admin-app')

@section('content')
    <div class="flex justify-between paketItems-center w-full">
        <!-- Judul + Form -->
        <section class="mb-2 w-full"> <!-- ✅ DITAMBAHKAN w-full -->

            <!-- Judul -->
            <h1 class="text-2xl font-bold text-text mb-4">Data Pengembalian Paket</h1>

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
        <form id="searchForm" method="GET" action="{{ route('admin.pengembalian-paket.index') }}"
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
                    <th scope="col" class="px-6 py-3">NISN</th>
                    <th scope="col" class="px-6 py-3">Judul Buku</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengembalian as $key => $pengembalianItem)
                    @php
                        $status = $pengembalianItem->peminjamanPaket->status; // 'berhasil' | 'selesai'
                    @endphp
                    <tr class="bg-white border-b border-gray-200">
                        <td class="px-6 py-4">{{ $pengembalian->firstItem() + $key }}</td>
                        <td class="px-6 py-4">
                            <div class="min-w-0">
                                <div class="font-medium md:text-base break-words truncate md:whitespace-normal">
                                    {{ $pengembalianItem->peminjamanPaket->anggota->user->name }}
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            {{ $pengembalianItem->peminjamanPaket->anggota->nisn ?? '-' }}
                        </td>

                        {{-- Judul Paket Buku --}}
                        <td class="px-6 py-4">
                            {{ $pengembalianItem->paketBuku->nama_paket ?? '-' }}
                        </td>


                        <td class="px-6 py-4">
                            <span
                                class="px-3 py-1 text-sm rounded-full
                                  @if ($status === 'berhasil') bg-orange-600 text-white
                                  @else bg-gray-100 text-gray-600 @endif">
                                {{ $status === 'berhasil' ? 'Dipinjam' : 'Selesai' }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            @if ($status === 'berhasil')
                                <form id="terima-form-{{ $pengembalianItem->id }}"
                                    action="{{ route('admin.pengembalian-paket.update', $pengembalianItem->id) }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="button"
                                        class="btn-terima inline-flex items-center bg-green-500 text-white px-3 py-2 rounded hover:bg-green-700 transition"
                                        data-id="{{ $pengembalianItem->id }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 md:mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11.5A1.5 1.5 0 005.5 20H17a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                        <span class="hidden md:inline">Terima</span>
                                    </button>
                                </form>
                            @else
                                <span class="italic text-gray-500">Selesai diperiksa</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada pengguna ditemukan.
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
                    title: 'Konfirmasi PengembalianItem',
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
