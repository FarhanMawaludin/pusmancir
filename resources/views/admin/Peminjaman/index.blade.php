@extends('layouts.admin-app')

@section('content')
    <div class="flex justify-between items-center min-w-full">
        <!-- Judul -->
        <section class="mb-6">
            <h1 class="text-2xl font-bold text-text mb-4">Data Peminjaman</h1>

            <form action="{{ route('admin.peminjaman.store') }}" method="POST" class="p-4 border rounded bg-white shadow-sm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Input NISN Anggota --}}
                    <div>
                        <label for="anggota_id_input" class="block text-sm font-medium text-gray-700">NISN Anggota</label>
                        <div class="flex gap-2 mt-1">
                            <input type="text" name="anggota_id" id="anggota_id_input" required
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                                placeholder="Masukkan NISN" autocomplete="off" autofocus>
                            <button type="button" onclick="cekAnggota()"
                                class="bg-blue-700 text-white px-2 rounded hover:bg-blue-800">Cek</button>
                        </div>
                        <div id="anggota_info" class="text-sm text-gray-800 mt-1 font-semibold"></div>
                    </div>

                    {{-- Input ID Eksemplar --}}
                    <div>
                        <label for="eksemplar_id_input" class="block text-sm font-medium text-gray-700">Kode Buku</label>
                        <div class="flex gap-2 mt-1">
                            <input type="text" name="eksemplar_id" id="eksemplar_id_input" required
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                                placeholder="Masukkan RFID Eksemplar" autocomplete="off">
                            <button type="button" onclick="cekEksemplar()"
                                class="bg-blue-700 text-white px-2 rounded hover:bg-blue-800">Cek</button>
                        </div>
                        <div id="eksemplar_info" class="text-sm text-gray-800 mt-1 font-semibold"></div>
                    </div>

                    {{-- Input Tanggal Pinjam --}}
                    <div>
                        <label for="tanggal_pinjam" class="block text-sm font-medium text-gray-700">Tanggal Pinjam</label>
                        <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" required
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 mt-1">
                    </div>

                    {{-- Input Tanggal Kembali --}}
                    <div>
                        <label for="tanggal_kembali" class="block text-sm font-medium text-gray-700">Tanggal Kembali</label>
                        <input type="date" name="tanggal_kembali" id="tanggal_kembali" required
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 mt-1">
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-700 text-white rounded hover:bg-blue-800 transition duration-150">
                        Simpan Peminjaman
                    </button>
                </div>
            </form>
        </section>
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
                </tr>
            </thead>
            <tbody>
                @forelse ($peminjaman as $key => $peminjamanItem)
                    <tr class="bg-white border-b border-gray-200">
                        <td class="px-6 py-4">{{ $peminjaman->firstItem() + $key }}</td>
                        <td class="px-6 py-4">
                            <div class="min-w-0">
                                <div class="font-medium md:text-base break-words truncate md:whitespace-normal">
                                    {{ $peminjamanItem->peminjaman->anggota->user->name }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ $peminjamanItem->eksemplar->inventori->judul_buku }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($peminjamanItem->tanggal_pinjam)->format('d-m-Y') }}
                        </td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($peminjamanItem->tanggal_kembali)->format('d-m-Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="
                                px-3 py-1 text-sm rounded-full border 
                                {{ $peminjamanItem->eksemplar->status === 'dipinjam'
                                    ? 'text-white border-orange-600 bg-orange-600 font-semibold'
                                    : 'text-gray-600 border-gray-300 bg-gray-100' }}">
                                {{ ucfirst($peminjamanItem->eksemplar->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada pengguna ditemukan.
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

    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id'); // Ambil ID dari data-id tombol

                // Menampilkan konfirmasi SweetAlert
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: 'Data yang dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Jika ya, submit form untuk menghapus data
                        document.getElementById('delete-form-' + userId).submit();
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
        document.getElementById('tanggal_pinjam').addEventListener('change', function() {
            const tanggalPinjam = new Date(this.value);
            if (isNaN(tanggalPinjam.getTime())) return;

            // Tambah 7 hari
            tanggalPinjam.setDate(tanggalPinjam.getDate() + 7);

            // Format ke YYYY-MM-DD
            const year = tanggalPinjam.getFullYear();
            const month = String(tanggalPinjam.getMonth() + 1).padStart(2, '0');
            const day = String(tanggalPinjam.getDate()).padStart(2, '0');
            const tanggalKembaliFormatted = `${year}-${month}-${day}`;

            document.getElementById('tanggal_kembali').value = tanggalKembaliFormatted;
        });
    </script>
@endsection
