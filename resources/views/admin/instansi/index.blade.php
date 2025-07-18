@extends('layouts.admin-app')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <!-- Judul -->
        <h1 class="text-2xl font-bold text-text">Data Kode Instansi</h1>

        <!-- Tombol Tambah -->
        <button id="dropdownDividerButton" data-dropdown-toggle="dropdownDivider"
            onclick="location.href='{{ route('admin.instansi.create') }}'" type="button"
            class="flex items-center gap-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded text-sm px-4 py-2.5 text-center dark:bg-blue-700 dark:hover:bg-blue-700 dark:focus:ring-blue-800">

            <!-- Icon: tampil di semua ukuran -->
            <svg class="w-5 h-5 md:hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>

            <!-- Teks: hanya tampil di layar medium ke atas -->
            <span class="hidden md:inline">Tambah</span>
        </button>
    </div>


    <div class="overflow-x-auto relative rounded border border-gray-200">
        <table class="min-w-full text-sm text-left text-text">
            <thead class="text-xs uppercase bg-gray-100 text-text">
                <tr>
                    <th scope="col" class="px-6 py-3 w-43 md:w-12">No</th>
                    <th scope="col" class="px-6 py-3">Nama Instansi</th>
                    <th scope="col" class="px-6 py-3">Kode</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($instansi as $key => $instansiItem)
                    <tr class="bg-white border-b border-gray-200">
                        <td class="px-6 py-4">{{ $instansi->firstItem() + $key }}</td>
                        <td class="px-6 py-4">
                            <div class="min-w-0">
                                <div class="font-medium md:text-base break-words truncate md:whitespace-normal">
                                    {{ $instansiItem->nama_lengkap }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="min-w-0">
                                <div class="font-medium md:text-base break-words truncate md:whitespace-normal">
                                    {{ $instansiItem->kode }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div
                                class="flex flex-row space-x-1 md:flex-row md:space-y-0 md:space-x-1 items-start md:items-center">
                                <!-- Edit -->
                                <a href="{{ route('admin.instansi.edit', $instansiItem->id) }}"
                                    class="inline-flex items-center bg-orange-500 text-white px-3 py-2 rounded hover:bg-orange-700 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 md:mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11.5A1.5 1.5 0 005.5 20H17a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                    <span class="hidden md:inline">Edit</span>
                                </a>

                                <!-- Hapus -->
                                <form action="{{ route('admin.instansi.destroy', $instansiItem->id) }}" method="POST"
                                    class="inline" id="delete-form-{{ $instansiItem->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        class="inline-flex items-center bg-red-500 text-white px-3 py-2 rounded hover:bg-red-700 transition btn-delete"
                                        data-id="{{ $instansiItem->id }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 md:mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span class="hidden md:inline">Hapus</span>
                                    </button>
                                </form>

                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada kode instansi ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="p-4">
            {{ $instansi->links('pagination::tailwind') }}
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
@endsection
