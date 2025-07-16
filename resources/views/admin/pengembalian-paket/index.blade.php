@extends('layouts.admin-app')

@section('content')
    <div class="flex justify-between items-center ">
        <!-- Judul -->
        <section class="mb-2">
            <h1 class="text-2xl font-bold text-text mb-8">Data pengembalian</h1>
            <!-- Form Pencarian -->
            <form method="GET" action="{{ route('admin.pengembalian-paket.index') }}" class="mb-4 flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full rounded-md bg-white px-2 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                    placeholder="Cari NISN anggota...">
                <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800 transition">
                    Cari
                </button>
                @if (request('search'))
                    <a href="{{ route('admin.pengembalian-paket.index') }}"
                        class="text-sm text-gray-600 underline hover:text-blue-600 ml-2">Reset</a>
                @endif
            </form>
        </section>
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
@endsection
