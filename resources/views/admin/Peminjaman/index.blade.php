@extends('layouts.admin-app')

@section('content')
    <div class="flex justify-between items-center w-full">
        <!-- Judul + Form -->
        <section class="mb-2 w-full"> <!-- ✅ DITAMBAHKAN w-full -->

            <!-- Judul -->
            <h1 class="text-2xl font-bold text-text mb-4">Data Peminjaman</h1>

            <form action="{{ route('admin.peminjaman.store') }}" method="POST" class="p-4 border rounded bg-white shadow-sm">
                @csrf

                {{-- === FORM UTAMA === --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 w-full"> <!-- ✅ Tambah w-full agar grid penuh -->

                    {{-- NISN Anggota --}}
                    <div>
                        <label for="anggota_id_input" class="block text-sm font-medium text-gray-700">NISN Anggota</label>
                        <div class="flex gap-2 mt-1">
                            <input type="text" name="anggota_id" id="anggota_id_input" required
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text >
                                border border-gray-300 placeholder:text-gray-400>
                                focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm
                                placeholder="Masukkan
                                NISN" autocomplete="off" autofocus>
                            <button type="button" onclick="cekAnggota()"
                                class="bg-blue-700 text-white px-2 rounded hover:bg-blue-800 text-sm">Cek</button>
                        </div>
                        <div id="anggota_info" class="text-sm text-gray-800 mt-1 font-semibold"></div>
                    </div>

                    {{-- Kode RFID Eksemplar --}}
                    <div>
                        <label for="eksemplar_id_input" class="block text-sm font-medium text-gray-700">
                            Kode Buku (RFID/Barcode)
                        </label>
                        <div class="flex gap-2 mt-1">
                            <input type="text" name="eksemplar_id" id="eksemplar_id_input" required
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                border border-gray-300 placeholder:text-gray-400
                                focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm"
                                placeholder="Masukkan RFID Eksemplar" autocomplete="off">
                            <button type="button" onclick="cekEksemplar()"
                                class="bg-blue-700 text-white px-2 rounded hover:bg-blue-800 text-sm">Cek</button>
                        </div>
                        <div id="eksemplar_info" class="text-sm text-gray-800 mt-1 font-semibold"></div>
                        @error('eksemplar_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Pinjam --}}
                    <div>
                        <label for="tanggal_pinjam" class="block text-sm font-medium text-gray-700">Tanggal Pinjam</label>
                        <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" required
                            class="mt-1 lock w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                            border border-gray-300 placeholder:text-gray-400
                            focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                    </div>

                    {{-- Tanggal Kembali --}}
                    <div>
                        <label for="tanggal_kembali" class="block text-sm font-medium text-gray-700">Tanggal Kembali</label>
                        <input type="date" name="tanggal_kembali" id="tanggal_kembali" required
                            class="mt-1 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text
                            border border-gray-300 placeholder:text-gray-400
                            focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                    </div>
                </div>

                {{-- === SCAN BARCODE DENGAN KAMERA === --}}
                <div class="mt-6">
                    <label class="block font-medium text-sm mb-2 text-gray-700">Scan Barcode (Via Kamera)</label>

                    {{-- Area kamera --}}
                    <div id="reader" class="mx-auto mb-3 border border-gray-300 rounded-md shadow" style="width: 400px;">
                    </div>

                    {{-- Tombol Pilih Mode Scan --}}
                    <div class="flex gap-2 justify-center mb-2">
                        <button type="button" onclick="setScanMode('anggota')"
                            class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700 transition">
                            Scan Anggota
                        </button>
                        <button type="button" onclick="setScanMode('buku')"
                            class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700 transition">
                            Scan Buku
                        </button>
                        <button type="button" onclick="setScanMode('cari_anggota')"
                            class="bg-orange-600 text-white px-3 py-1 rounded text-xs hover:bg-indigo-700 transition">
                            Cari Anggota
                        </button>
                    </div>

                    {{-- Info Mode --}}
                    <p id="scan_mode_info" class="text-center text-sm text-gray-500 mb-1">
                        Mode Scan: Buku
                    </p>
                    <small class="text-gray-500 block text-center">
                        Barcode otomatis dimasukkan sesuai mode pilihan.
                    </small>
                </div>

                {{-- Tombol Simpan --}}
                <div class="mt-6">
                    <button type="submit"
                        class="w-full md:w-auto px-4 py-2 bg-blue-700 text-white rounded hover:bg-blue-800 transition duration-150">
                        Simpan Peminjaman
                    </button>
                </div>
            </form>

        </section>
    </div>


    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 my-6">
        <form id="searchForm" method="GET" action="{{ route('admin.peminjaman.index') }}"
            class="flex flex-wrap items-center gap-4 w-full">
            <div class="relative w-full max-w-lg">
                <input type="search" id="search-dropdown" name="search"
                    class="block p-2.5 w-full z-20 text-sm text-text bg-gray-50 rounded-md border border-gray-300
                   focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari berdasarkan nama atau NISN..." value="{{ $search ?? '' }}" />
                <button type="submit"
                    class="absolute top-0 end-0 p-2.5 h-full text-white bg-blue-700 rounded border-blue-700
                   hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                </button>
            </div>

            <!-- Filter Limit Tampilan -->
            <div class="flex items-center gap-2 shrink-0">
                <label for="limit" class="text-sm font-medium text-text">Lihat Data:</label>
                <select name="limit" id="limit" onchange="this.form.submit()"
                    class="bg-gray-50 border border-gray-300 text-text text-sm rounded focus:ring-blue-500 focus:border-blue-500 block p-2">
                    <option value="10" {{ ($limit ?? 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="50" {{ ($limit ?? 10) == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ ($limit ?? 10) == 100 ? 'selected' : '' }}>100</option>
                    <option value="all" {{ ($limit ?? 10) === 'all' ? 'selected' : '' }}>Semua</option>
                </select>
            </div>
        </form>
    </div>

    {{-- Tombol Bulk Actions --}}
    <div class="flex flex-wrap items-center gap-2 mb-4">
        <button type="button" id="btn-bulk-approve"
            class="inline-flex items-center bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Setujui Terpilih
        </button>

        <button type="button" id="btn-bulk-tolak"
            class="inline-flex items-center bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Tolak Terpilih
        </button>
    </div>

    {{-- Form tersembunyi Bulk Action --}}
    <form id="form-bulk-action" action="{{ route('admin.peminjaman.bulkUpdateStatus') }}" method="POST" class="hidden">
        @csrf
        @method('PATCH')
        <input type="hidden" name="aksi" id="bulk-aksi">
        <input type="hidden" name="keterangan" id="bulk-keterangan">
        <div id="bulk-ids-container"></div>
    </form>

    <div class="overflow-x-auto relative rounded border border-gray-200">
        <table class="min-w-full text-sm text-left text-text">
            <thead class="text-xs uppercase bg-gray-100 text-text">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        <input type="checkbox" id="check-all"
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                    </th>
                    <th scope="col" class="px-6 py-3 w-43 md:w-12">No</th>
                    <th scope="col" class="px-6 py-3">Nama Peminjam</th>
                    <th scope="col" class="px-6 py-3">Kelas</th>
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
                        {{-- Checkbox --}}
                        <td class="px-6 py-4">
                            <input type="checkbox" name="peminjaman_ids[]" value="{{ $peminjamanItem->id }}"
                                class="peminjaman-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        </td>

                        <td class="px-6 py-4">{{ $peminjaman->firstItem() + $key }}</td>

                        <td class="px-6 py-4">
                            <div class="font-medium md:text-base truncate md:whitespace-normal">
                                {{ $pinjam->anggota->user->name ?? '-' }}
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            {{ $peminjamanItem->anggota->kelas->nama_kelas ?? '-' }}
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
                        <td class="px-6 py-4">
                            <span
                                class="px-3 py-1 text-sm rounded-full
                                  @if ($status === 'menunggu') bg-orange-600 text-white
                                  @elseif ($status === 'berhasil') bg-green-600 text-white
                                  @elseif ($status === 'tolak') bg-red-600 text-white
                                  @else bg-gray-100 text-gray-600 @endif">
                                @if ($status === 'menunggu')
                                    Menunggu
                                @elseif ($status === 'berhasil')
                                    Dipinjam
                                @elseif ($status === 'tolak')
                                    Ditolak
                                @else
                                    Selesai
                                @endif
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
                                    <input type="hidden" name="keterangan" id="keterangan-{{ $pinjam->id }}">
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


    <!-- JavaScript -->

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
                    const keteranganInput = document.getElementById('keterangan-' + id);

                    Swal.fire({
                        title: 'Tolak Peminjaman Buku',
                        input: 'textarea',
                        inputLabel: 'Keterangan / Alasan Penolakan (Wajib):',
                        inputPlaceholder: 'Tuliskan alasan penolakan di sini...',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Tolak Peminjaman',
                        confirmButtonColor: '#dc2626',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        inputValidator: (value) => {
                            if (!value || !value.trim()) {
                                return 'Keterangan penolakan wajib diisi!';
                            }
                        }
                    }).then(result => {
                        if (result.isConfirmed) {
                            keteranganInput.value = result.value.trim();
                            form.submit();
                        }
                    });
                });
            });

            // ------------ SELECT ALL & BULK ACTIONS ---------------
            const checkAll = document.getElementById('check-all');
            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.peminjaman-checkbox');
                    checkboxes.forEach(cb => cb.checked = this.checked);
                });
            }

            function getSelectedIds() {
                return Array.from(document.querySelectorAll('.peminjaman-checkbox:checked')).map(cb => cb.value);
            }

            function fillBulkForm(ids, aksi, keterangan = '') {
                document.getElementById('bulk-aksi').value = aksi;
                document.getElementById('bulk-keterangan').value = keterangan;
                const container = document.getElementById('bulk-ids-container');
                container.innerHTML = '';
                ids.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    container.appendChild(input);
                });
            }

            // ------------ BULK APPROVE ---------------
            const btnBulkApprove = document.getElementById('btn-bulk-approve');
            if (btnBulkApprove) {
                btnBulkApprove.addEventListener('click', () => {
                    const ids = getSelectedIds();
                    if (ids.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Oops!',
                            text: 'Pilih minimal satu data peminjaman terlebih dahulu.'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Setujui ' + ids.length + ' Peminjaman Terpilih?',
                        text: 'Semua peminjaman yang dipilih akan disetujui.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Setujui Semua',
                        confirmButtonColor: '#16a34a',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then(result => {
                        if (result.isConfirmed) {
                            fillBulkForm(ids, 'berhasil');
                            document.getElementById('form-bulk-action').submit();
                        }
                    });
                });
            }

            // ------------ BULK TOLAK ---------------
            const btnBulkTolak = document.getElementById('btn-bulk-tolak');
            if (btnBulkTolak) {
                btnBulkTolak.addEventListener('click', () => {
                    const ids = getSelectedIds();
                    if (ids.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Oops!',
                            text: 'Pilih minimal satu data peminjaman terlebih dahulu.'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Tolak ' + ids.length + ' Peminjaman Terpilih',
                        input: 'textarea',
                        inputLabel: 'Keterangan / Alasan Penolakan untuk Semua Data Terpilih (Wajib):',
                        inputPlaceholder: 'Tuliskan alasan penolakan di sini...',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Tolak Semua Terpilih',
                        confirmButtonColor: '#dc2626',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        inputValidator: (value) => {
                            if (!value || !value.trim()) {
                                return 'Keterangan penolakan wajib diisi untuk semua data terpilih!';
                            }
                        }
                    }).then(result => {
                        if (result.isConfirmed) {
                            fillBulkForm(ids, 'tolak', result.value.trim());
                            document.getElementById('form-bulk-action').submit();
                        }
                    });
                });
            }
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
                            `<span class="text-red-500">${data.error ?? 'Anggota tidak ditemukan'}</span>`;
                    }
                })
                .catch(() => {
                    infoBox.innerHTML = `<span class="text-red-500">Gagal mengambil data</span>`;
                });
        }

        function cekEksemplar() {
            const input = document.getElementById('eksemplar_id_input').value.trim();
            const infoBox = document.getElementById('eksemplar_info');

            if (!input) {
                infoBox.innerHTML = `<span class="text-red-500">Input tidak boleh kosong</span>`;
                return;
            }

            fetch(`{{ url('/api/eksemplar') }}/${input}`)
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
        let scanMode = 'buku'; // default

        function setScanMode(mode) {
            scanMode = mode;
            const label = (mode === 'anggota') ?
                'Anggota' :
                (mode === 'cari_anggota' ? 'Cari Anggota' : 'Buku');
            document.getElementById('scan_mode_info').innerText =
                'Mode Scan: ' + label;
        }

        function onScanSuccess(decodedText) {

            if (scanMode === 'anggota') {
                document.getElementById('anggota_id_input').value = decodedText;
                cekAnggota(); // fungsi milikmu
            } else if (scanMode === 'buku') {
                document.getElementById('eksemplar_id_input').value = decodedText;
                cekEksemplar(); // fungsi milikmu
            } else if (scanMode === 'cari_anggota') {
                // isi kolom pencarian & (opsional) auto‑submit
                const searchBox = document.getElementById('search-dropdown');
                searchBox.value = decodedText;

                // auto‑submit (jika diinginkan)
                document.getElementById('searchForm').submit();
            }

            /* Jangan clear scanner supaya kamera tetap aktif
               html5QrcodeScanner.clear();
            */
        }

        // Inisialisasi scanner
        const html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", {
                fps: 10,
                qrbox: 250
            },
            /* verbose=*/
            false
        );
        html5QrcodeScanner.render(onScanSuccess);
    </script>
@endsection
