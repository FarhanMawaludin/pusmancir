@extends('layouts.admin-app')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-text">Data Surat Masuk</h1>
    </div>

    <!-- Filter dan Tambah -->
    <div class="flex justify-between items-center mb-4">
        <form class="flex w-full max-w-lg" method="GET" action="{{ route('admin.surat-masuk.index') }}">
            <div class="flex w-full">
                <input type="hidden" name="category" id="selected-category" value="{{ $category }}">

                <!-- Tombol dropdown -->
                <button id="dropdown-button" type="button"
                    class="shrink-0 z-10 inline-flex items-center py-2.5 px-4 text-sm font-medium text-text bg-gray-100 border border-gray-300 rounded-l hover:bg-gray-200">
                    {{ match ($category) {
                        'asal_surat' => 'Asal Surat',
                        'perihal' => 'Perihal',
                        'tanggal_terima' => 'Tanggal Terima',
                        default => 'Filter',
                    } }}
                    <svg class="w-2.5 h-2.5 ms-2.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>

                <!-- Dropdown daftar kategori -->
                <div id="dropdown"
                    class="z-10 hidden absolute mt-12 bg-white divide-y divide-gray-100 rounded shadow-sm w-44">
                    <ul class="py-2 text-sm text-text" aria-labelledby="dropdown-button">
                        <li><button type="button" data-value="asal_surat"
                                class="category-btn w-full text-left px-4 py-2 hover:bg-gray-100">Asal Surat</button></li>
                        <li><button type="button" data-value="perihal"
                                class="category-btn w-full text-left px-4 py-2 hover:bg-gray-100">Perihal</button></li>
                        <li><button type="button" data-value="tanggal_terima"
                                class="category-btn w-full text-left px-4 py-2 hover:bg-gray-100">Tanggal Terima</button></li>
                    </ul>
                </div>

                <!-- Input pencarian -->
                <div class="relative w-full">
                    <input type="search" name="search"
                        class="block p-2.5 w-full text-sm text-text bg-gray-50 rounded-r-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Cari data..." value="{{ $search ?? '' }}" />
                    <button type="submit"
                        class="absolute top-0 end-0 p-2.5 text-sm font-medium h-full text-white bg-blue-700 rounded-r-md hover:bg-blue-800">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </button>
                </div>
            </div>
        </form>

        <!-- Tombol Tambah -->
        <button onclick="location.href='{{ route('admin.surat-masuk.create') }}'"
            class="flex items-center gap-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 font-medium rounded text-sm px-4 py-2.5">
            <svg class="w-5 h-5 md:hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span class="hidden md:inline">Tambah</span>
        </button>
    </div>

    <!-- Tabel -->
    <div class="overflow-x-auto relative rounded border border-gray-200">
        <table class="min-w-full text-sm text-left text-text">
            <thead class="text-xs uppercase bg-gray-100 text-text">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Nomor Surat</th>
                    <th class="px-4 py-3">Tanggal Terima</th>
                    <th class="px-4 py-3">Asal Surat</th>
                    <th class="px-4 py-3">Perihal</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suratMasuk as $key => $item)
                    <tr class="bg-white border-b border-gray-200">
                        <td class="px-4 py-2">{{ $suratMasuk->firstItem() + $key }}</td>
                        <td class="px-4 py-2">{{ $item->nomor_surat }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($item->tanggal_terima)->format('d-m-Y') }}</td>
                        <td class="px-4 py-2">{{ $item->asal_surat }}</td>
                        <td class="px-4 py-2">{{ $item->perihal }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-row space-x-2 items-center">
                                <!-- Detail -->
                                <a href="{{ route('admin.surat-masuk.show', $item->id) }}"
                                    class="inline-flex items-center bg-blue-700 text-white px-3 py-2 rounded hover:bg-blue-800 transition">
                                    <svg class="w-4 h-4 md:mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="hidden md:inline">Detail</span>
                                </a>

                                <!-- Edit -->
                                <a href="{{ route('admin.surat-masuk.edit', $item->id) }}"
                                    class="inline-flex items-center bg-orange-500 text-white px-3 py-2 rounded hover:bg-orange-700 transition">
                                    <svg class="w-4 h-4 md:mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11.5A1.5 1.5 0 005.5 20H17a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                    <span class="hidden md:inline">Edit</span>
                                </a>

                                <!-- Hapus -->
                                <form action="{{ route('admin.surat-masuk.destroy', $item->id) }}" method="POST"
                                    class="inline" id="delete-form-{{ $item->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        class="inline-flex items-center bg-red-500 text-white px-3 py-2 rounded hover:bg-red-700 transition btn-delete"
                                        data-id="{{ $item->id }}">
                                        <svg class="w-4 h-4 md:mr-1" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
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
                        <td colspan="6" class="text-center text-gray-500 py-4">Tidak ada data surat masuk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="p-4">
            {{ $suratMasuk->links('pagination::tailwind') }}
        </div>
    </div>

    <!-- SweetAlert dan Dropdown -->
    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: 'Data yang dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                });
            });
        });

        const dropdownButton = document.getElementById('dropdown-button');
        const dropdownMenu = document.getElementById('dropdown');
        const categoryButtons = document.querySelectorAll('.category-btn');
        const selectedCategoryInput = document.getElementById('selected-category');

        dropdownButton.addEventListener('click', () => {
            dropdownMenu.classList.toggle('hidden');
        });

        categoryButtons.forEach(button => {
            button.addEventListener('click', () => {
                const selectedValue = button.getAttribute('data-value');
                selectedCategoryInput.value = selectedValue;
                dropdownButton.innerHTML = `${button.textContent.trim()}
                <svg class="w-2.5 h-2.5 ms-2.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 4 4 4-4" />
                </svg>`;
                dropdownMenu.classList.add('hidden');
                button.closest('form').submit();
            });
        });

        document.addEventListener('click', (e) => {
            if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });
    </script>
@endsection
