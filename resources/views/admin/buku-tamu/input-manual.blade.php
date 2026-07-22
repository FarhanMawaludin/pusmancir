@extends('layouts.admin-app')

@section('content')
    <div class="space-y-4 max-w-3xl">
        <div class="flex justify-between items-center mb-2">
            <div>
                <h1 class="text-2xl font-bold text-text">Input Buku Tamu Manual</h1>
                <p class="text-sm text-gray-500 mt-1">Gunakan formulir ini jika terjadi mati lampu atau pemadaman listrik sehingga pengunjung mengisi di buku manual fisik.</p>
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded shadow-sm">
            <form method="POST" action="{{ route('admin.buku-tamu.store-manual') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="tanggal" class="block text-sm font-medium text-text mb-1">
                        Tanggal Kunjungan <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal" id="tanggal" 
                        value="{{ old('tanggal', date('Y-m-d')) }}" required
                        class="block w-full max-w-xs rounded-md bg-white px-3 py-2 text-sm text-text 
                                border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    @error('tanggal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="jumlah_pengunjung" class="block text-sm font-medium text-text mb-1">
                        Jumlah Total Pengunjung (Angka) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="jumlah_pengunjung" id="jumlah_pengunjung" 
                        value="{{ old('jumlah_pengunjung') }}" min="1" placeholder="Contoh: 15" required
                        class="block w-full max-w-xs rounded-md bg-white px-3 py-2 text-sm text-text 
                                border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    <p class="mt-1 text-xs text-gray-500">Masukkan total angka pengunjung dari buku pencatatan manual.</p>
                    @error('jumlah_pengunjung')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="asal_instansi" class="block text-sm font-medium text-text mb-1">
                        Kategori / Asal Instansi (Opsional)
                    </label>
                    <input type="text" name="asal_instansi" id="asal_instansi" 
                        value="{{ old('asal_instansi', 'Siswa / Internal (Manual)') }}"
                        class="block w-full rounded-md bg-white px-3 py-2 text-sm text-text 
                                border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    @error('asal_instansi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="keperluan" class="block text-sm font-medium text-text mb-1">
                        Keperluan / Catatan (Opsional)
                    </label>
                    <input type="text" name="keperluan" id="keperluan" 
                        value="{{ old('keperluan', 'Kunjungan Buku Tamu Manual (Mati Lampu / Offline)') }}"
                        class="block w-full rounded-md bg-white px-3 py-2 text-sm text-text 
                                border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    @error('keperluan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" 
                        class="bg-blue-700 hover:bg-blue-800 text-white font-medium px-5 py-2.5 rounded text-sm transition">
                        Simpan Data Rekap Buku Tamu
                    </button>
                    <a href="{{ route('admin.buku-tamu.log-tamu') }}" 
                        class="px-4 py-2.5 border border-gray-300 rounded text-sm text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
