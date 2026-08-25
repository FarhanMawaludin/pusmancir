@extends('layouts.admin-app')
@php $activeMenu = 'antrianPaket'; @endphp

@section('content')
<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <h2 class="text-2xl font-bold text-text">Kelola Buku Paket Mata Pelajaran</h2>
    <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
        @if(!empty($tingkatKelas))
            <form action="{{ route('admin.buku-paket-mapel.copy') }}" method="POST" class="flex items-center space-x-2 bg-gray-100 p-1 px-2 rounded-md border border-gray-200">
                @csrf
                <input type="hidden" name="to_class" value="{{ $tingkatKelas }}">
                <span class="text-xs font-semibold text-gray-600">Salin dari:</span>
                <select name="from_class" class="text-xs rounded border-gray-300 py-1 px-2 bg-white">
                    @foreach(['X', 'XI IPA', 'XI IPS', 'XII IPA', 'XII IPS'] as $c)
                        @if($c !== $tingkatKelas)
                            <option value="{{ $c }}">Kelas {{ $c }}</option>
                        @endif
                    @endforeach
                </select>
                <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded-md text-xs hover:bg-green-700 font-semibold transition">Salin</button>
            </form>
        @endif
        <a href="{{ route('admin.buku-paket-mapel.create') }}" class="bg-blue-700 text-white px-4 py-2 rounded-md hover:bg-blue-800">Tambah</a>
    </div>
</div>

<div class="mb-6 overflow-x-auto">
    <div class="flex space-x-2 border-b">
        <a href="{{ route('admin.buku-paket-mapel.index') }}" class="px-4 py-2 {{ empty($tingkatKelas) ? 'border-b-2 border-blue-600 text-blue-600 font-bold' : 'text-gray-500 hover:text-gray-700' }}">Semua</a>
        <a href="{{ route('admin.buku-paket-mapel.index', ['tingkat_kelas' => 'X']) }}" class="px-4 py-2 {{ $tingkatKelas == 'X' ? 'border-b-2 border-blue-600 text-blue-600 font-bold' : 'text-gray-500 hover:text-gray-700' }}">Kelas X</a>
        <a href="{{ route('admin.buku-paket-mapel.index', ['tingkat_kelas' => 'XI IPA']) }}" class="px-4 py-2 {{ $tingkatKelas == 'XI IPA' ? 'border-b-2 border-blue-600 text-blue-600 font-bold' : 'text-gray-500 hover:text-gray-700' }}">Kelas XI IPA</a>
        <a href="{{ route('admin.buku-paket-mapel.index', ['tingkat_kelas' => 'XI IPS']) }}" class="px-4 py-2 {{ $tingkatKelas == 'XI IPS' ? 'border-b-2 border-blue-600 text-blue-600 font-bold' : 'text-gray-500 hover:text-gray-700' }}">Kelas XI IPS</a>
        <a href="{{ route('admin.buku-paket-mapel.index', ['tingkat_kelas' => 'XII IPA']) }}" class="px-4 py-2 {{ $tingkatKelas == 'XII IPA' ? 'border-b-2 border-blue-600 text-blue-600 font-bold' : 'text-gray-500 hover:text-gray-700' }}">Kelas XII IPA</a>
        <a href="{{ route('admin.buku-paket-mapel.index', ['tingkat_kelas' => 'XII IPS']) }}" class="px-4 py-2 {{ $tingkatKelas == 'XII IPS' ? 'border-b-2 border-blue-600 text-blue-600 font-bold' : 'text-gray-500 hover:text-gray-700' }}">Kelas XII IPS</a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-text">
            <thead class="text-xs uppercase bg-gray-100 text-text">
                <tr>
                    <th class="px-6 py-4">No</th>
                    <th class="px-6 py-4">Nama Buku</th>
                    <th class="px-6 py-4">Tingkat Kelas</th>
                    <th class="px-6 py-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bukuList as $key => $buku)
                <tr class="border-b">
                    <td class="px-6 py-4">{{ $bukuList->firstItem() + $key }}</td>
                    <td class="px-6 py-4 font-medium">{{ $buku->nama_buku }}</td>
                    <td class="px-6 py-4">{{ $buku->tingkat_kelas }}</td>
                    <td class="px-6 py-4 flex space-x-2">
                        <a href="{{ route('admin.buku-paket-mapel.edit', $buku->id) }}" class="bg-orange-500 text-white px-3 py-1 rounded text-xs hover:bg-orange-600">Edit</a>
                        <form id="delete-form-{{ $buku->id }}" action="{{ route('admin.buku-paket-mapel.destroy', $buku->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDelete({{ $buku->id }})" class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center">Tidak ada data buku paket.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4">
        {{ $bukuList->appends(request()->query())->links('pagination::tailwind') }}
    </div>
</div>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data buku ini akan dihapus permanen!",
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
