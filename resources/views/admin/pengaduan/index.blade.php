@extends('layouts.admin-app')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <!-- Judul -->
        <h1 class="text-2xl font-bold text-text">Data Pengaduan</h1>

        <!-- Tombol Tambah (Jika Ada Fitur Tambah) -->
        {{-- <button onclick="location.href='{{ route('admin.pengaduan.create') }}'" type="button"
            class="flex items-center gap-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded text-sm px-4 py-2.5 text-center dark:bg-blue-700 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            <svg class="w-5 h-5 md:hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span class="hidden md:inline">Tambah</span>
        </button> --}}
    </div>

    <div class="overflow-x-auto relative rounded border border-gray-200">
        <table class="min-w-full text-sm text-left text-text">
            <thead class="text-xs uppercase bg-gray-100 text-text">
                <tr>
                    <th scope="col" class="px-6 py-3">No</th>
                    <th scope="col" class="px-6 py-3">Nama</th>
                    <th scope="col" class="px-6 py-3">No Telp</th>
                    <th scope="col" class="px-6 py-3">Email</th>
                    <th scope="col" class="px-6 py-3">Pesan</th>
                    <th scope="col" class="px-6 py-3">Tanggal</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengaduans as $index => $item)
                    <tr class="bg-white border-b border-gray-200">
                        <td class="px-6 py-4">{{ $pengaduans->firstItem() + $index }}</td>
                        <td class="px-6 py-4">{{ $item->nama ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $item->no_telp ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $item->email ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $item->isi ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $item->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.pengaduan.show', $item->id) }}"
                                class="inline-flex items-center bg-blue-700 text-white px-3 py-2 rounded hover:bg-blue-800 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 md:mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="hidden md:inline">Detail</span>
                            </a>
                        </td>
                        {{-- <td class="px-6 py-4">
                            <form action="{{ route('admin.pengaduan.destroy', $item->id) }}" method="POST"
                                id="delete-form-{{ $item->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                    class="inline-flex items-center bg-red-500 text-white px-3 py-2 rounded hover:bg-red-700 transition btn-delete"
                                    data-id="{{ $item->id }}">
                                    <!-- icon & teks hapus -->
                                </button>
                            </form>
                        </td> --}}
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data pengaduan ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="p-4">
            {{ $pengaduans->links('pagination::tailwind') }}
        </div>
    </div>

    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: 'Data ini akan dihapus secara permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                });
            });
        });
    </script>
@endsection
