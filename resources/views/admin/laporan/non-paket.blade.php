@extends('layouts.admin-app')

@section('content')
    <div class="flex justify-between items-center w-full">
        <!-- Judul + Form -->
        <section class="mb-2 w-full"> <!-- ✅ DITAMBAHKAN w-full -->

            <!-- Judul -->
            <h1 class="text-2xl font-bold text-text mb-4">Data Laporan Peminjaman Non Paket</h1>

            <div class="mb-4">
                <form method="GET" action="{{ route('admin.laporan.non-paket') }}" class="flex items-center gap-4 mb-4">
                    <label for="tanggal_mulai" class="text-text font-medium">Dari:</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                        class="border border-gray-300 rounded px-3 py-2 text-sm text-text">

                    <label for="tanggal_selesai" class="text-text font-medium">Sampai:</label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                        value="{{ request('tanggal_selesai') }}"
                        class="border border-gray-300 rounded px-3 py-2 text-sm text-text">

                    <button type="submit"
                        class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-primary800 transition text-sm">
                        Tampilkan
                    </button>

                    <a href="{{ route('admin.laporan.exportNonPaket', request()->all()) }}"
                        class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">Export Excel</a>
                </form>
            </div>

        </section>
    </div>

    <div class="overflow-x-auto relative rounded border border-gray-200">
        <table class="min-w-full text-sm text-left text-text">
            <thead class="text-xs uppercase bg-gray-100 text-text">
                <tr>
                    <th scope="col" class="px-6 py-3 w-43 md:w-12">No</th>
                    <th scope="col" class="px-6 py-3">Nama Peminjam</th>
                    <th scope="col" class="px-6 py-3">NISN</th>
                    <th scope="col" class="px-6 py-3">Kelas</th>
                    <th scope="col" class="px-6 py-3">Judul Buku</th>
                    <th scope="col" class="px-6 py-3">Tanggal Pinjam</th>
                    <th scope="col" class="px-6 py-3">Tanggal Kembali</th>
                    <th scope="col" class="px-6 py-3">Status</th>
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
                            {{ $pinjam->anggota->nisn ?? '-' }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $pinjam->anggota->kelas->nama_kelas ?? '-' }}
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
                                  @if ($status === 'berhasil') bg-orange-600 text-white
                                  @else bg-gray-100 text-gray-600 @endif">
                                {{ $status === 'berhasil' ? 'Dipinjam' : 'Selesai' }}
                            </span>
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
