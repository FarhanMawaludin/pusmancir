@extends('layouts.admin-app')

@section('content')
    <div class="space-y-6">
        <h2 class="text-xl font-semibold text-text">Detail Inventori</h2>

        <div class="bg-white border rounded border-base200 p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                📘 Informasi Buku
            </h3>

            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm text-gray-700">
                <div>
                    <dt class="font-medium text-gray-600">Judul</dt>
                    <dd>{{ $inventori->judul_buku }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-600">Pengarang</dt>
                    <dd>{{ $inventori->pengarang }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-600">Kategori</dt>
                    <dd>{{ $inventori->kategoriBuku->nama_kategori ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-600">Jenis Media</dt>
                    <dd>{{ $inventori->jenisMedia->nama_jenis_media ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-600">Jenis Sumber</dt>
                    <dd>{{ $inventori->jenisSumber->nama_sumber ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-600">Sumber</dt>
                    <dd>{{ $inventori->sumber->nama ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-600">Penerbit</dt>
                    <dd>{{ $inventori->penerbit->nama_penerbit ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-600">Sekolah</dt>
                    <dd>{{ $inventori->sekolah->nama_sekolah ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-600">Tanggal Pembelian</dt>
                    <dd>{{ \Carbon\Carbon::parse($inventori->tanggal_pembelian)->format('d-m-Y') }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-600">Jumlah Eksemplar</dt>
                    <dd>{{ $inventori->jumlah_eksemplar }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-600">Harga Satuan</dt>
                    <dd>Rp {{ number_format($inventori->harga_satuan, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-600">Total Harga</dt>
                    <dd>Rp {{ number_format($inventori->total_harga, 0, ',', '.') }}</dd>
                </div>
            </dl>
        </div>


        <div class="bg-white border rounded p-4 border-base200 mt-4">
            <h3 class="font-medium text-lg mb-3">Daftar Eksemplar</h3>
            <table class="w-full table-auto border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">No</th>
                        <th class="px-4 py-2 border">No Induk</th>
                        <th class="px-4 py-2 border">No Inventori</th>
                        <th class="px-4 py-2 border">RFID</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inventori->eksemplar as $index => $eks)
                        <tr>
                            <td class="px-4 py-2 border text-center">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 border">{{ $eks->no_induk }}</td>
                            <td class="px-4 py-2 border">{{ $eks->no_inventori }}</td>
                            <td class="px-4 py-2 border">{{ $eks->no_rfid }}</td>
                            <td class="px-4 py-2 border">{{ ucfirst($eks->status) }}</td>
                            <td class="px-4 py-2 border text-center">
                                <a href="{{ route('admin.eksemplar.edit', $eks->id) }}"
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                                    Ubah Status
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">Tidak ada eksemplar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <a href="{{ route('admin.inventori.index') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                Kembali
            </a>
        </div>
    </div>
@endsection
