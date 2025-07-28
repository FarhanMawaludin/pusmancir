@extends('layouts.anggota-app')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-text">Riwayat Peminjaman</h1>
    </div>

    <div class="overflow-x-auto relative rounded border border-gray-200">
        <table class="min-w-full text-sm text-left text-text">
            <thead class="text-xs uppercase bg-gray-100 text-text">
                <tr>
                    <th scope="col" class="px-6 py-3 w-4 md:w-10">No</th>
                    <th scope="col" class="px-6 py-3">Nama</th>
                    <th scope="col" class="px-6 py-3">NISN</th>
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
                        $pinjam = $peminjamanItem->peminjaman;
                        $status = $pinjam->status;
                        $tanggalKembali = \Carbon\Carbon::parse($pinjam->tanggal_kembali)->startOfDay();
                        $besok = \Carbon\Carbon::tomorrow()->startOfDay();
                    @endphp
                    <tr class="bg-white border-b border-gray-200">
                        <td class="px-6 py-4">{{ $peminjaman->firstItem() + $key }}</td>
                        <td class="px-6 py-4">{{ $pinjam->anggota->user->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $pinjam->anggota->nisn ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $peminjamanItem->eksemplar->inventori->judul_buku ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $pinjam->tanggal_pinjam ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $pinjam->tanggal_kembali ?? '-' }}</td>
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
                        <td class="px-6 py-4">
                            @if ($status === 'berhasil' && $tanggalKembali->equalTo($besok))
                                <form id="perpanjang-form-{{ $pinjam->id }}"
                                    action="{{ route('anggota.peminjaman.perpanjang', $pinjam->id) }}" method="POST">
                                    @csrf
                                    <button type="button"
                                        class="btn-perpanjang bg-indigo-600 hover:bg-indigo-700 text-white text-md font-medium px-4 py-2 rounded"
                                        data-id="{{ $pinjam->id }}">
                                        Perpanjang
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada peminjaman ditemukan.
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
        document.querySelectorAll('.btn-perpanjang').forEach(button => {
            button.addEventListener('click', function() {
                const pinjamId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Perpanjangan hanya bisa dilakukan sekali dan perlu dikonfirmasi.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, perpanjang!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('perpanjang-form-' + pinjamId).submit();
                    }
                });
            });
        });
    </script>


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
@endsection
