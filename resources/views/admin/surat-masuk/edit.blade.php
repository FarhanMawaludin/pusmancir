@extends('layouts.admin-app')

@section('content')
    <form method="POST" action="{{ route('admin.surat-masuk.update', $suratMasuk->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <h2 class="text-[28px] font-semibold text-text">Edit Surat Masuk</h2>
            <div class="border-gray-900/10 pb-8 p-4 bg-white border border-gray-200 rounded">
                <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">

                    <!-- Nomor Surat -->
                    <div class="sm:col-span-3">
                        <label for="nomor_surat" class="block text-sm font-medium text-text">Nomor Surat</label>
                        <input type="text" name="nomor_surat" id="nomor_surat"
                            value="{{ old('nomor_surat', $suratMasuk->nomor_surat) }}"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                        @error('nomor_surat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Terima -->
                    <div class="sm:col-span-3">
                        <label for="tanggal_terima" class="block text-sm font-medium text-text">Tanggal Terima</label>
                        <input type="date" name="tanggal_terima" id="tanggal_terima"
                            value="{{ old('tanggal_terima', \Carbon\Carbon::parse($suratMasuk->tanggal_terima)->format('Y-m-d')) }}"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                        @error('tanggal_terima')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Asal Surat -->
                    <div class="sm:col-span-3">
                        <label for="asal_surat" class="block text-sm font-medium text-text">Asal Surat</label>
                        <input type="text" name="asal_surat" id="asal_surat"
                            value="{{ old('asal_surat', $suratMasuk->asal_surat) }}"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                        @error('asal_surat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Perihal -->
                    <div class="sm:col-span-3">
                        <label for="perihal" class="block text-sm font-medium text-text">Perihal</label>
                        <input type="text" name="perihal" id="perihal"
                            value="{{ old('perihal', $suratMasuk->perihal) }}"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                        @error('perihal')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Lampiran -->
                    <div class="sm:col-span-3">
                        <label for="lampiran" class="block text-sm font-medium text-text">Lampiran</label>
                        <input type="number" name="lampiran" id="lampiran"
                            value="{{ old('lampiran', $suratMasuk->lampiran) }}"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                        @error('lampiran')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-3">
                        <label for="status" class="block text-sm font-medium text-text">Status</label>
                        <select name="status" id="status"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                            @foreach (['Diterima', 'Didisposisikan', 'Selesai'] as $option)
                                <option value="{{ $option }}" {{ old('status') === $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- File Surat -->
                    <div class="col-span-full">
                        <label for="file_surat" class="block text-sm font-medium text-text">Upload Ulang File (jika ingin
                            mengganti)</label>
                        <input type="file" name="file_surat" id="file_surat"
                            class="mt-2 block w-full text-sm text-text border border-gray-300 rounded cursor-pointer bg-gray-50 focus:outline-none" />
                        @error('file_surat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if ($suratMasuk->file_surat)
                            <p class="text-sm mt-2">File saat ini:
                                <a href="{{ asset('storage/' . $suratMasuk->file_surat) }}" target="_blank"
                                    class="text-blue-600 underline">
                                    Lihat file
                                </a>
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Tombol -->
                <div class="mt-6 flex items-center justify-start gap-x-4">
                    <a href="{{ route('admin.surat-masuk.index') }}"
                        class="text-sm font-semibold text-text hover:text-text hover:border border-gray-900 rounded-md px-3 py-2">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-blue-700 rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-800 focus:outline focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
