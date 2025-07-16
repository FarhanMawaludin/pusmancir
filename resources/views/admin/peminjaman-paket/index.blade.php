@extends('layouts.admin-app')

@section('content')
    <div class="flex justify-between paketItems-center w-full">
        <!-- Judul + Form -->
        <section class="mb-2 w-full"> <!-- ✅ DITAMBAHKAN w-full -->

            <!-- Judul -->
            <h1 class="text-2xl font-bold text-text mb-4">Data paket</h1>

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
        <form id="searchForm" method="GET" action="{{ route('admin.peminjaman-paket.index') }}"
            class="flex w-full max-w-lg my-6">
            <div class="relative w-full">
                <input type="search" id="search-dropdown" name="search"
                    class="block p-2.5 w-full z-20 text-sm text-text bg-gray-50 rounded-md border border-gray-300
                   focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari siswa" value="{{ $search ?? '' }}" />
                <button type="submit"
                    class="absolute top-0 end-0 p-2.5 h-full text-white bg-primary700 rounded border-primary700
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
                    <th scope="col" class="px-6 py-3">Nama Peminjam</th>
                    <th scope="col" class="px-6 py-3">NISN</th>
                    <th scope="col" class="px-6 py-3">Judul Buku</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($peminjamanPaket as $key => $paketItem)
                    @php
                        // objek peminjaman induk
                        $pinjam = $paketItem->peminjamanPaket; // instance PeminjamanPaket
                        $status = $pinjam->status ?? '-'; // menunggu | berhasil | tolak
                    @endphp

                    <tr class="bg-white border-b border-gray-200">
                        {{-- No --}}
                        <td class="px-6 py-4">
                            {{ $peminjamanPaket->firstItem() + $key }}
                        </td>

                        {{-- Nama peminjam --}}
                        <td class="px-6 py-4">
                            <div class="font-medium md:text-base truncate md:whitespace-normal">
                                {{ $pinjam->anggota->user->name ?? '-' }}
                            </div>
                        </td>

                        {{-- NISN --}}
                        <td class="px-6 py-4">
                            {{ $pinjam->anggota->nisn ?? '-' }}
                        </td>

                        {{-- Judul Paket Buku --}}
                        <td class="px-6 py-4">
                            {{ $paketItem->paketBuku->nama_paket ?? '-' }}
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4">
                            <span
                                class="px-3 py-1 text-sm rounded-full
                                @if ($status === 'berhasil') bg-green-600
                                @elseif ($status === 'tolak') bg-red-600
                                @else bg-orange-600 @endif text-white">
                                {{ ucfirst($status) }}
                            </span>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4">
                            {{-- APPROVE --}}
                            <button type="button"
                                class="btn-approve p-2 bg-green-600 text-white rounded hover:bg-green-700 transition"
                                data-id="{{ $paketItem->id }}" title="Approve">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </button>

                            <form id="approve-form-{{ $paketItem->id }}" class="hidden"
                                action="{{ route('admin.peminjaman-paket.updateStatus', $paketItem->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="aksi" value="berhasil">
                            </form>

                            {{-- TOLAK --}}
                            <button type="button"
                                class="btn-tolak p-2 bg-red-600 text-white rounded hover:bg-red-700 transition ml-2"
                                data-id="{{ $paketItem->id }}" title="Tolak">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <form id="tolak-form-{{ $paketItem->id }}" class="hidden"
                                action="{{ route('admin.peminjaman-paket.updateStatus', $paketItem->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="aksi" value="tolak">
                            </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data paket ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>


        <!-- Pagination -->
        <div class="p-4">
            {{ $peminjamanPaket->links('pagination::tailwind') }}
        </div>
    </div>


    <!-- JavaScript -->

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // ------------ APPROVE ---------------
            document.querySelectorAll('.btn-approve').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    const form = document.getElementById('approve-form-' + id);

                    Swal.fire({
                        title: 'Approve paket?',
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
                        title: 'Tolak paket?',
                        text: 'paket akan ditolak.',
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
                            `<span class="text-red-500">${data.error ?? 'Anggota tidak dpaketItemukan'}</span>`;
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
                            `<span class="text-red-500">${data.error ?? 'Eksemplar tidak dpaketItemukan'}</span>`;
                    }
                })
                .catch(() => {
                    infoBox.innerHTML = `<span class="text-red-500">Gagal mengambil data</span>`;
                });
        }
    </script>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const tanggalPinjamInput = document.getElementById('tanggal_pinjam');
            const tanggalKembaliInput = document.getElementById('tanggal_kembali');

            // Auto set tanggal pinjam hari ini
            const today = new Date();
            const todayFormatted = today.toISOString().split('T')[0];
            tanggalPinjamInput.value = todayFormatted;

            // Auto set tanggal kembali +7 hari
            const kembaliDate = new Date(today);
            kembaliDate.setDate(kembaliDate.getDate() + 7);
            tanggalKembaliInput.value = kembaliDate.toISOString().split('T')[0];

            // Update tanggal kembali jika tanggal pinjam diganti manual
            tanggalPinjamInput.addEventListener('change', function() {
                const tanggalPinjam = new Date(this.value);
                if (isNaN(tanggalPinjam.getTime())) return;

                tanggalPinjam.setDate(tanggalPinjam.getDate() + 7);
                tanggalKembaliInput.value = tanggalPinjam.toISOString().split('T')[0];
            });
        });
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
