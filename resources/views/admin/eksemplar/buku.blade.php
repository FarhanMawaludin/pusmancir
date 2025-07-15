@extends('layouts.admin-app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-text">Data Buku</h1>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4 mb-4 w-full">
        <div class=" w-full p-4 bg-white border border-gray-200 rounded  dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-2 rounded-full bg-orange-500 text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5.121 17.804A4 4 0 0 1 8.6 16h6.8a4 4 0 0 1 3.478 1.804M15 10a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{$tersediaCount}}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Tersedia</p>
                </div>
            </div>
            <div class="mt-4 flex items-center ">
                {{-- @if ($user_increase > 0)
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $user_increase }} mahasiswa bertambah</p>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-1 text-green-600" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Tidak ada penambahan</p>
                @endif --}}
            </div>
        </div>
        <div class=" w-full p-4 bg-white border border-gray-200 rounded dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-2 rounded-full bg-orange-500 text-white">
                    <svg class="w-6 h-6 " fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7H5a2 2 0 0 0-2 2v4m5-6h8M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m0 0h3a2 2 0 0 1 2 2v4m0 0v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-6m18 0s-4 2-9 2-9-2-9-2m9-2h.01" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{$dipinjamCount}}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Dipinjam</p>
                </div>
            </div>
            <div class="mt-4 flex items-center ">
                {{-- @if ($lowongan_increase > 0)
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $lowongan_increase }} lowongan bertambah</p>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-1 text-green-600" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Tidak ada penambahan</p>
                @endif --}}
            </div>
        </div>
        <div class=" w-full p-4 bg-white border border-gray-200 rounded dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-2 rounded-full bg-orange-500 text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 3v4a1 1 0 0 1-1 1H5m4 6 2 2 4-4m4-8v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{$rusakCount}}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Rusak</p>
                </div>
            </div>
            <div class="mt-4 flex items-center ">
                {{-- @if ($pengajuan_increase > 0)
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $pengajuan_increase }} pengajuan bertambah</p>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-1 text-green-600" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Tidak ada penambahan</p>
                @endif --}}
            </div>
        </div>
        <div class="w-full p-4 bg-white border border-gray-200 rounded dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-2 rounded-full bg-orange-500 text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 4h12M6 4v16M6 4H5m13 0v16m0-16h1m-1 16H6m12 0h1M6 20H5M9 7h1v1H9V7Zm5 0h1v1h-1V7Zm-5 4h1v1H9v-1Zm5 0h1v1h-1v-1Zm-3 4h2a1 1 0 0 1 1 1v4h-4v-4a1 1 0 0 1 1-1Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{$hilangCount}}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Hilang</p>
                </div>
            </div>
            <div class="mt-4 flex items-center ">
                {{-- @if ($perusahaan_increase > 0)
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $perusahaan_increase }} perusahaan bertambah
                    </p>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-1 text-green-600" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Tidak ada penambahan</p>
                @endif --}}
            </div>
        </div>
    </div>

    <div class="flex justify-between items-center">
        <form method="GET" action="{{ route('admin.eksemplar.buku') }}"
            class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 mb-6 max-w-xl">

            {{-- Dropdown Status --}}
            <div class="relative w-full sm:w-auto">
                <input type="hidden" name="status" id="selected-status" value="{{ $status }}">

                <button id="dropdown-button" type="button"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium bg-gray-100 text-gray-800 border border-gray-300 rounded hover:bg-gray-200 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                    {{ ucfirst($status !== 'all' ? $status : 'Pilih Status') }}
                    <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 1l4 4 4-4" />
                    </svg>
                </button>

                {{-- Dropdown Menu --}}
                <div id="dropdown-menu"
                    class="hidden absolute z-10 mt-1 bg-white border border-gray-200 rounded shadow w-44 dark:bg-gray-700">
                    <ul class="text-sm text-gray-700 dark:text-gray-200">
                        @foreach (['all' => 'Semua Status', 'tersedia' => 'Tersedia', 'dipinjam' => 'Dipinjam', 'rusak' => 'Rusak', 'hilang' => 'Hilang'] as $key => $label)
                            <li>
                                <button type="button" data-value="{{ $key }}"
                                    class="status-option w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                    {{ $label }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto relative rounded border border-gray-200">
        <table class="min-w-full text-sm text-left text-text">
            <thead class="text-xs uppercase bg-gray-100 text-text">
                <tr>
                    <th scope="col" class="px-6 py-3 w-43 md:w-12">No</th>
                    <th scope="col" class="px-6 py-3">Judul</th>
                    <th scope="col" class="px-6 py-3">Pengarang</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($eksemplar as $key => $item)
                    <tr class="bg-white border-b border-gray-200">
                        <td class="px-6 py-4">{{ $eksemplar->firstItem() + $key }}</td>

                        <td class="px-6 py-4">
                            <div class="min-w-0">
                                <div class="font-medium md:text-base break-words truncate md:whitespace-normal">
                                    {{ $item->inventori->judul_buku }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ $item->inventori->pengarang }}</td>
                        <td class="px-6 py-4">{{ $item->status }}</td>
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

        <!-- Pagination -->
        <div class="p-4">
            {{ $eksemplar->links('pagination::tailwind') }}
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

    {{-- Script Dropdown --}}
    <script>
        const dropdownButton = document.getElementById('dropdown-button');
        const dropdownMenu = document.getElementById('dropdown-menu');
        const statusInput = document.getElementById('selected-status');

        // Toggle dropdown menu
        dropdownButton.addEventListener('click', () => {
            dropdownMenu.classList.toggle('hidden');
        });

        // Pilih status dan submit form otomatis
        document.querySelectorAll('.status-option').forEach(button => {
            button.addEventListener('click', function() {
                const selectedValue = this.getAttribute('data-value');
                statusInput.value = selectedValue;
                dropdownButton.innerHTML = this.innerText + `
                <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 1l4 4 4-4" />
                </svg>`;
                dropdownMenu.classList.add('hidden');

                // Submit form
                this.closest('form').submit();
            });
        });

        // Klik di luar dropdown akan menutupnya
        document.addEventListener('click', function(event) {
            if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.classList.add('hidden');
            }
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
