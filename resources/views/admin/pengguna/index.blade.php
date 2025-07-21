@extends('layouts.admin-app')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-text">Data Pengguna</h1>
    </div>

    <div class="flex justify-between items-center mb-4">
        <form method="GET" action="{{ route('admin.pengguna.index') }}" class="flex w-full max-w-lg">
            <div class="flex w-full relative">
                <!-- Hidden input untuk kategori -->
                <input type="hidden" name="category" id="selected-category" value="{{ $category }}">

                <!-- Tombol Dropdown -->
                <button id="dropdown-button" type="button"
                    class="shrink-0 z-10 inline-flex items-center py-2.5 px-4 text-sm font-medium text-text bg-gray-100 border border-gray-300 rounded-l hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 ">
                    {{ $category === 'all' ? 'Semua Posisi' : ucfirst(str_replace('_', ' ', $category)) }}
                    <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div id="dropdown"
                    class="z-10 hidden absolute mt-12 bg-white divide-y divide-gray-100 rounded shadow-sm w-44">
                    <ul class="py-2 text-sm text-text" aria-labelledby="dropdown-button">
                        <li>
                            <button type="button" data-value="all"
                                class="category-btn w-full text-left px-4 py-2 hover:bg-gray-100">
                                Semua Posisi
                            </button>
                        </li>
                        <li>
                            <button type="button" data-value="admin"
                                class="category-btn w-full text-left px-4 py-2 hover:bg-gray-100">
                                Admin
                            </button>
                        </li>
                        <li>
                            <button type="button" data-value="pustakawan"
                                class="category-btn w-full text-left px-4 py-2 hover:bg-gray-100">
                                Pustakawan
                            </button>
                        </li>
                        <li>
                            <button type="button" data-value="anggota"
                                class="category-btn w-full text-left px-4 py-2 hover:bg-gray-100">
                                Anggota
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Input Pencarian -->
                <div class="relative w-full">
                    <input type="search" id="search-dropdown" name="search"
                        class="block p-2.5 w-full z-20 text-sm text-text bg-gray-50 rounded-r-md border-s-gray-50 border-s-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500 "
                        placeholder="Cari pengguna berdasarkan nama atau NISN..." value="{{ $search ?? '' }}" />
                    <button type="submit"
                        class="absolute top-0 end-0 p-2.5 text-sm font-medium h-full text-white bg-blue-700 border rounded border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                        <span class="sr-only">Search</span>
                    </button>
                </div>
            </div>
        </form>

        <div class="flex flex-wrap gap-2">
            <!-- Button Import Excel -->
            <button id="importExcelButton"
                class="flex items-center gap-2 text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded text-sm px-4 py-2.5 text-center "
                type="button">

                <svg class="w-5 h-5 md:hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v16h16V8l-6-4H4z" />
                </svg>

                <span class="hidden md:inline">Import Excel</span>
            </button>

            <!-- Modal Import Excel -->
            <div id="import-excel-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="bg-white rounded shadow-xl w-full max-w-md relative">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-4 border-b">
                            <h3 class="text-lg font-semibold">Import Pengguna via Excel</h3>
                            <button onclick="closeImportModal()"
                                class="text-gray-500 hover:text-gray-700 text-2xl font-bold">
                                &times;
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="p-4 space-y-4">
                            <!-- Download Template -->
                            <div class="flex items-center justify-between bg-gray-100 p-3 rounded">
                                <span class="text-sm font-medium text-gray-700">Download Template Excel:</span>
                                <a href="{{ asset('template_pengguna.xlsx') }}"
                                    class="flex items-center gap-2 px-3 py-1.5 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                                    download>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 16v-8m0 8l-3-3m3 3l3-3m6 8H6a2 2 0 01-2-2V6a2 2 0 012-2h6.586a2 2 0 011.414.586l5.414 5.414A2 2 0 0120 11.414V20a2 2 0 01-2 2z" />
                                    </svg>
                                    Download
                                </a>
                            </div>

                            <!-- Upload Form -->
                            <form action="{{ route('admin.pengguna.import') }}" method="POST" enctype="multipart/form-data"
                                class="space-y-4">
                                @csrf
                                <div>
                                    <label for="excel_file" class="block mb-1 text-sm font-medium text-gray-900">Upload File
                                        Excel:</label>
                                    <input
                                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded cursor-pointer bg-gray-50 focus:outline-none focus:ring focus:border-blue-300"
                                        type="file" id="excel_file" name="file_pengguna" accept=".xlsx,.xls"
                                        required>
                                </div>

                                <!-- Footer -->
                                <div class="flex justify-end gap-2 pt-4 border-t">
                                    <button type="button" onclick="closeImportModal()"
                                        class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 text-sm">
                                        Batal
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white text-sm">
                                        Upload
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Button Tambah -->
            <button id="dropdownDividerButton" data-dropdown-toggle="dropdownDivider"
                class="flex items-center gap-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded text-sm px-4 py-2.5 text-center"
                type="button" onclick="location.href='{{ route('admin.pengguna.create') }}'">

                <svg class="w-5 h-5 md:hidden" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>

                <span class="hidden md:inline">Tambah</span>
            </button>
        </div>
    </div>

    <div class="overflow-x-auto relative rounded border border-gray-200">
        <table class="min-w-full text-sm text-left text-text">
            <thead class="text-xs uppercase bg-gray-100 text-text">
                <tr>
                    <th scope="col" class="px-6 py-3 w-43 md:w-12">No</th>
                    <th scope="col" class="px-6 py-3">Pengguna</th>
                    <th scope="col" class="px-6 py-3">Posisi</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($user as $key => $userItem)
                    <tr class="bg-white border-b border-gray-200">
                        <td class="px-6 py-4">{{ $user->firstItem() + $key }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                @if ($userItem->foto)
                                    <img src="{{ asset('storage/' . $userItem->foto) }}" alt="Foto {{ $userItem->name }}"
                                        class="w-10 h-10 rounded-full object-cover shrink-0">
                                @else
                                    <img src="{{ asset('img/Profile.jpg') }}" alt="Foto Default"
                                        class="w-10 h-10 rounded-full border border-gray-200 object-cover shrink-0">
                                @endif
                                <div class="min-w-0">
                                    <div class="font-medium md:text-base break-words truncate md:whitespace-normal">
                                        {{ $userItem->name }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ ucwords(str_replace('_', ' ', $userItem->role)) }}</td>
                        <td class="px-6 py-4">
                            <div
                                class="flex flex-row space-x-2 md:flex-row md:space-y-0 md:space-x-1 items-start md:items-center">

                                <!-- Detail -->
                                <button
                                    class="inline-flex items-center bg-blue-700 text-white px-3 py-2 rounded hover:bg-blue-800 transition"
                                    onclick="location.href='{{ route('admin.pengguna.show', $userItem->id) }}'">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 md:mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="hidden md:inline">Detail</span>
                                </button>

                                <!-- Edit -->
                                <a href="{{ route('admin.pengguna.edit', $userItem->id) }}"
                                    class="inline-flex items-center bg-orange-500 text-white px-3 py-2 rounded hover:bg-orange-700 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 md:mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11.5A1.5 1.5 0 005.5 20H17a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                    <span class="hidden md:inline">Edit</span>
                                </a>

                                <!-- Hapus -->
                                <form action="{{ route('admin.pengguna.destroy', $userItem->id) }}" method="POST"
                                    class="inline" id="delete-form-{{ $userItem->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        class="inline-flex items-center bg-red-500 text-white px-3 py-2 rounded hover:bg-red-700 transition btn-delete"
                                        data-id="{{ $userItem->id }}">
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
                            Tidak ada pengguna ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <!-- Pagination -->
        <div class="p-4 bg-white rounded border border-gray-200 mt-4">
            {{ $user->links('pagination::tailwind') }}
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
