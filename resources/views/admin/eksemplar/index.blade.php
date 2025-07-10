@extends('layouts.admin-app')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-text">Data eksemplar</h1>
    </div>

    <div class="overflow-x-auto relative rounded border border-gray-200">
        <form action="{{ route('admin.eksemplar.cetak-batch') }}" method="POST" target="_blank">
            @csrf

            <table class="min-w-full text-sm text-left text-text">
                <thead class="text-xs uppercase bg-gray-100 text-text">
                    <tr>
                        <th class="px-6 py-3">
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th scope="col" class="px-6 py-3 w-43 md:w-12">No</th>
                        <th scope="col" class="px-6 py-3">Judul</th>
                        <th scope="col" class="px-6 py-3">No Induk</th>
                        <th scope="col" class="px-6 py-3">Kode RFID</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($eksemplar as $key => $item)
                        <tr class="bg-white border-b border-gray-200">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="selected[]" value="{{ $item->id }}">
                            </td>
                            <td class="px-6 py-4">{{ $eksemplar->firstItem() + $key }}</td>

                            <td class="px-6 py-4">
                                <div class="min-w-0">
                                    <div class="font-medium md:text-base break-words truncate md:whitespace-normal">
                                        {{ $item->inventori->judul_buku }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">{{ $item->no_induk }}</td>
                            <td class="px-6 py-4">{{ $item->no_rfid }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-row space-x-2 items-center">
                                    <button onclick="location.href='{{ route('admin.eksemplar.cetakBarcode', $item->id) }}'"
                                        type="button"
                                        class="inline-flex items-center bg-primary700 text-white px-3 py-2 rounded hover:bg-blue-800 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 md:mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                                                d="M16.444 18H19a1 1 0 0 0 1-1v-5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h2.556M17 11V5a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v6h10ZM7 15h10v4a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1v-4Z" />
                                        </svg>

                                        <span class="hidden md:inline">Cetak</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada eksemplar ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Tombol Cetak & Input Rentang -->
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <!-- Kosong Awal -->
                    <div>
                        <label for="kosong_awal" class="block text-sm font-medium text-gray-700">
                            Jumlah label kosong di awal
                        </label>
                        <input type="number" name="kosong_awal" id="kosong_awal" value="0" min="0"
                            max="20"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 mt-2">
                    </div>

                    <!-- Dari No. Induk -->
                    <div>
                        <label for="start_induk" class="block text-sm font-medium text-gray-700">
                            Dari No. Induk
                        </label>
                        <input type="number" name="start_induk" id="start_induk"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 mt-2">
                    </div>

                    <!-- Sampai No. Induk -->
                    <div>
                        <label for="end_induk" class="block text-sm font-medium text-gray-700">
                            Sampai No. Induk
                        </label>
                        <input type="number" name="end_induk" id="end_induk"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 mt-2">
                    </div>

                    <!-- Tombol Submit -->
                    <div class="flex">
                        <button type="submit"
                            class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-medium w-full py-2 px-4 rounded h-[40px] md:mt-auto">
                            Cetak Barcode
                        </button>
                    </div>
                </div>
            </div>

        </form>

        <!-- Pagination -->
        <div class="p-4">
            {{ $eksemplar->links('pagination::tailwind') }}
        </div>
    </div>

    <script>
        document.getElementById('selectAll').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('input[name="selected[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
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

    <script>
        // Show modal
        document.getElementById('importExcelButton').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('import-excel-modal').classList.remove('hidden');
        });

        // Hide modal
        function closeImportModal() {
            document.getElementById('import-excel-modal').classList.add('hidden');
        }
    </script>
@endsection
