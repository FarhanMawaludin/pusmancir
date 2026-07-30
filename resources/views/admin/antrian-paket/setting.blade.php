@extends('layouts.admin-app')
@php $activeMenu = 'antrianPaket'; @endphp

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-text">Pengaturan Kuota Antrian Buku Paket</h2>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-8">
    <form action="{{ route('admin.antrian-paket.setting.store') }}" method="POST" class="space-y-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="date" name="tanggal" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text border border-gray-300 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir (Opsional)</label>
                <input type="date" name="tanggal_akhir" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text border border-gray-300 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kuota</label>
                <input type="number" name="kuota" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text border border-gray-300 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan (Opsional)</label>
                <input type="text" name="keterangan" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text border border-gray-300 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
            </div>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-md hover:bg-blue-800 focus:ring-4 focus:ring-blue-300">Simpan</button>
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-text">
            <thead class="text-xs uppercase bg-gray-100 text-text">
                <tr>
                    <th class="px-6 py-4">No</th>
                    <th class="px-6 py-4">Tanggal</th>
                    <th class="px-6 py-4">Kuota</th>
                    <th class="px-6 py-4">Terisi</th>
                    <th class="px-6 py-4">Sisa</th>
                    <th class="px-6 py-4">Keterangan</th>
                    <th class="px-6 py-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($settings as $key => $setting)
                <tr class="border-b">
                    <td class="px-6 py-4">{{ $key + 1 }}</td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($setting->tanggal)->format('d-m-Y') }}</td>
                    <td class="px-6 py-4">{{ $setting->kuota }}</td>
                    <td class="px-6 py-4">{{ $setting->terisi }}</td>
                    <td class="px-6 py-4">{{ $setting->kuota - $setting->terisi }}</td>
                    <td class="px-6 py-4">{{ $setting->keterangan ?? '-' }}</td>
                    <td class="px-6 py-4 flex space-x-2">
                        <button onclick="editKuota({{ $setting->id }}, {{ $setting->kuota }})" class="bg-orange-500 text-white px-3 py-1 rounded text-xs hover:bg-orange-600">Edit Kuota</button>
                        <form id="delete-form-{{ $setting->id }}" action="{{ route('admin.antrian-paket.setting.destroy', $setting->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDelete({{ $setting->id }})" class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center">Belum ada pengaturan kuota.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<form id="edit-kuota-form" method="POST" class="hidden">
    @csrf
    @method('PUT')
    <input type="hidden" name="kuota" id="edit-kuota-input">
</form>

<script>
function editKuota(id, currentKuota) {
    Swal.fire({
        title: 'Edit Kuota',
        input: 'number',
        inputValue: currentKuota,
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Batal',
        inputValidator: (value) => {
            if (!value || value <= 0) {
                return 'Kuota harus lebih dari 0!'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('edit-kuota-form');
            form.action = `/admin/antrian-paket/setting/${id}`;
            document.getElementById('edit-kuota-input').value = result.value;
            form.submit();
        }
    });
}

function confirmDelete(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Pengaturan kuota ini akan dihapus!",
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
}
</script>
@endsection
