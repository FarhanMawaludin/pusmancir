@extends('layouts.admin-app')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-text">Daftar Anggota</h1>
    </div>

    <div class="flex justify-between items-center mb-4">
        <form method="GET" action="{{ route('admin.anggota.index') }}" class="flex w-full max-w-lg">
            <div class="flex w-full relative">
                <!-- Hidden input untuk kategori -->
                <input type="hidden" name="category" id="selected-category" value="{{ $category }}">

                <!-- Tombol Dropdown -->
                <button id="dropdown-button" type="button"
                    class="shrink-0 z-10 inline-flex items-center py-2.5 px-4 text-sm font-medium text-text bg-gray-100 border border-gray-300 rounded-l hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700 dark:text-white dark:border-gray-600">
                    {{ $category === 'all' ? 'Pilih Kategori' : ucfirst(str_replace('_', ' ', $category)) }}
                    <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div id="dropdown"
                    class="z-10 hidden absolute mt-12 bg-white divide-y divide-gray-100 rounded shadow-sm w-44 dark:bg-gray-700">
                    <ul class="py-2 text-sm text-text dark:text-gray-200" aria-labelledby="dropdown-button">
                        <li>
                            <button type="button" data-value="all"
                                class="category-btn w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                Pilih Kategori
                            </button>
                        </li>
                        <li>
                            <button type="button" data-value="name"
                                class="category-btn w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                Nama
                            </button>
                        </li>
                        <li>
                            <button type="button" data-value="nisn"
                                class="category-btn w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                NISN
                            </button>
                        </li>
                        <li>
                            <button type="button" data-value="kelas"
                                class="category-btn w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                Kelas
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Input Pencarian -->
                <div class="relative w-full">
                    <input type="search" id="search-dropdown" name="search"
                        class="block p-2.5 w-full z-20 text-sm text-text bg-gray-50 rounded-r-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:border-blue-500"
                        placeholder="Cari berdasarkan kategori yang dipilih..." value="{{ $search ?? '' }}" />
                    <button type="submit"
                        class="absolute top-0 end-0 p-2.5 text-sm font-medium h-full text-white bg-blue-700 border rounded border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-700 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
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
    </div>

    <form id="form-aktif" action="{{ route('admin.anggota.setAktif') }}" method="POST" class="mb-4">
        @csrf
        <input type="hidden" name="anggota_ids_selected" id="anggota_ids_selected">

        <button type="button" id="btn-aktif"
            class="inline-flex items-center bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-800 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Jadikan Aktif
        </button>

        <div class="overflow-x-auto relative rounded border border-gray-200 mt-2">
            <table class="min-w-full text-sm text-left text-text">
                <thead class="text-xs uppercase bg-gray-100 text-text">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            <input type="checkbox" id="check-all"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        </th>
                        <th scope="col" class="px-6 py-3 w-4 md:w-10">No</th>
                        <th scope="col" class="px-6 py-3">Pengguna</th>
                        <th scope="col" class="px-6 py-3">NISN</th>
                        <th scope="col" class="px-6 py-3">Kelas</th>
                        <th scope="col" class="px-6 py-3">Email</th>
                        <th scope="col" class="px-6 py-3">Nomor Telepon</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $key => $anggotaItem)
                        <tr class="bg-white border-b border-gray-200">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="anggota_ids[]" value="{{ $anggotaItem->anggota->id }}"
                                    class="anggota-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4">{{ $users->firstItem() + $key }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @if ($anggotaItem->foto)
                                        <img src="{{ asset($anggotaItem->foto) }}"
                                            alt="Foto {{ $anggotaItem->name }}"
                                            class="w-10 h-10 rounded-full object-cover shrink-0">
                                    @else
                                        <img src="{{ asset('img/Profile.jpg') }}" alt="Foto Default"
                                            class="w-10 h-10 rounded-full border border-gray-200 object-cover shrink-0">
                                    @endif
                                    <div class="min-w-0">
                                        <div class="font-medium md:text-base break-words truncate md:whitespace-normal">
                                            {{ $anggotaItem->name }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-bold">{{ $anggotaItem->username }}</td>
                            <td class="px-6 py-4">{{ $anggotaItem->anggota->kelas->nama_kelas ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $anggotaItem->anggota->email ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $anggotaItem->anggota->no_telp ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if ($anggotaItem->anggota->status == 'aktif')
                                    <span
                                        class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Aktif</span>
                                @else
                                    <span
                                        class="px-2 py-1 bg-gray-200 text-gray-700 rounded-full text-xs font-semibold">Alumni</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <button
                                    class="inline-flex items-center bg-blue-700 text-white px-3 py-2 rounded hover:bg-blue-800 transition"
                                    onclick="location.href='{{ route('admin.pengguna.show', $anggotaItem->id) }}'">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 md:mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="hidden md:inline">Detail</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada anggota ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <script>
        document.getElementById('btn-aktif').addEventListener('click', function() {
            const selected = Array.from(document.querySelectorAll('.anggota-checkbox:checked'));

            if (selected.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops!',
                    text: 'Pilih minimal satu anggota terlebih dahulu.',
                });
                return;
            }

            Swal.fire({
                title: 'Yakin ingin menjadikan anggota terpilih sebagai anggota aktif?',
                text: 'Aksi ini tidak bisa dibatalkan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Jadikan Aktif',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-aktif').submit();
                }
            });
        });

        // Select All
        document.getElementById('check-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.anggota-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
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
