@extends('layouts.admin-app')

@section('content')
    <form method="POST" action="{{ route('admin.surat-keluar.update', $surat->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <h2 class="text-[28px] font-semibold text-text">Formulir Surat Keluar</h2>
            <div class="border-gray-900/10 pb-8 p-4 bg-white border border-gray-200 rounded">
                <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">

                    <!-- Nomor Urut -->
                    <div class="sm:col-span-3">
                        <label for="nomor_urut" class="block text-sm font-medium text-text">Nomor Urut</label>
                        <input type="number" name="nomor_urut" id="nomor_urut"
                            value="{{ old('nomor_urut', $surat->nomor_urut) }}"
                            class="disabled:cursor-not-allowed mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                border border-gray-300 placeholder:text-gray-400
                                focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                        @error('nomor_urut')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Keluar -->
                    <div class="sm:col-span-3">
                        <label for="tanggal_keluar" class="block text-sm font-medium text-text">Tanggal Keluar</label>
                        <input type="date" name="tanggal_keluar" id="tanggal_keluar"
                            value="{{ old('tanggal_keluar', \carbon\Carbon::parse($surat->tanggal_keluar)->format('Y-m-d')) }}"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                border border-gray-300 placeholder:text-gray-400
                                focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                        @error('tanggal_keluar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tujuan Surat -->
                    <div class="sm:col-span-3">
                        <label for="tujuan_surat" class="block text-sm font-medium text-text">Tujuan Surat</label>
                        <input type="text" name="tujuan_surat" id="tujuan_surat"
                            value="{{ old('tujuan_surat', $surat->tujuan_surat) }}"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                border border-gray-300 placeholder:text-gray-400
                                focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                        @error('tujuan_surat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Perihal -->
                    <div class="sm:col-span-3">
                        <label for="perihal" class="block text-sm font-medium text-text">Perihal</label>
                        <input type="text" name="perihal" id="perihal" value="{{ old('perihal', $surat->perihal) }}"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                border border-gray-300 placeholder:text-gray-400
                                focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                        @error('perihal')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kode Jenis Surat -->
                    <div class="sm:col-span-3">
                        <label for="kode_jenis_id" class="block text-sm font-medium text-text">Kode Jenis Surat</label>
                        <select name="kode_jenis_id" id="kode_jenis_id"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                border border-gray-300 placeholder:text-gray-400
                                focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                            <option value="">-- Pilih Kode Jenis Surat --</option>
                            @foreach ($kodeJenisList as $jenis)
                                <option value="{{ $jenis->id }}" @selected(old('kode_jenis_id', $surat->kode_jenis_id) == $jenis->id)>
                                    {{ $jenis->kode }} - {{ $jenis->nama_surat }}
                                </option>
                            @endforeach
                        </select>
                        @error('kode_jenis_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Instansi -->
                    <div class="sm:col-span-3">
                        <label for="instansi_id" class="block text-sm font-medium text-text">Instansi</label>
                        <select name="instansi_id" id="instansi_id"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                border border-gray-300 placeholder:text-gray-400
                                focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                            <option value="">-- Pilih Instansi --</option>
                            @foreach ($instansiList as $instansi)
                                <option value="{{ $instansi->id }}" @selected(old('instansi_id', $surat->instansi_id) == $instansi->id)>
                                    {{ $instansi->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                        @error('instansi_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Isi Surat -->
                    <div class="col-span-full">
                        <label for="isi_surat" class="block text-sm font-medium text-text">Isi Surat</label>
                        <textarea name="isi_surat" id="isi_surat" rows="4"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                border border-gray-300 placeholder:text-gray-400
                                focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">{{ old('isi_surat', $surat->isi_surat) }}</textarea>
                        @error('isi_surat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- File Surat -->
                    <div class="col-span-full">
                        <label for="file_surat" class="block text-sm font-medium text-text mb-2">Upload File Surat</label>
                        <label for="file_surat"
                            class="flex items-center cursor-pointer rounded-md overflow-hidden border border-gray-300 bg-white">

                            <span class="bg-gray-800 text-white text-sm font-semibold px-4 py-2">
                                Pilih File
                            </span>

                            <span id="file_name" class="ml-3 text-sm text-gray-500">
                                Tidak ada file dipilih
                            </span>
                        </label>
                        <input type="file" name="file_surat" id="file_surat" accept="application/pdf" class="hidden">

                        @if ($surat->file_surat)
                            <p class="text-sm mt-1">Saat ini:
                                <a href="{{ asset('storage/' . $surat->file_surat) }}" target="_blank"
                                    class="text-blue-600 underline">Lihat Cover</a>
                            </p>
                        @endif
                        @error('file_surat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Tombol -->
                <div class="mt-6 flex items-center justify-start gap-x-4">
                    <a href="{{ route('admin.surat-keluar.index') }}"
                        class="text-sm font-semibold text-text hover:text-text hover:border border-gray-900 rounded-md px-3 py-2">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-blue-700 rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-800 focus:outline focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </form>

    <script>
        document.getElementById('file_surat').addEventListener('change', function(e) {
            const fileName = e.target.files.length ? e.target.files[0].name : 'Tidak ada file dipilih';
            document.getElementById('file_name').textContent = fileName;

            const preview = document.getElementById('cover_preview');
            const reader = new FileReader();
            reader.onload = function(event) {
                preview.src = event.target.result;
                preview.classList.remove('hidden');
            };
            if (e.target.files[0]) {
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    </script>
@endsection
