@extends('layouts.admin-app')

@section('content')
    <form method="POST" action="{{ route('admin.katalog.update', $katalog->id) }}" enctype="multipart/form-data" >
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <h2 class="text-[28px] font-semibold text-text">Formulir Katalog</h2>
            <div class="border-gray-900/10 pb-8 p-4 bg-white border border-gray-200 rounded">
                <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">

                    {{-- Judul Buku --}}
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-text mb-2">Judul Buku</label>
                        <input type="text" value="{{ $katalog->judul_buku }}" 
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" disabled>
                    </div>

                    {{-- Pengarang --}}
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-text mb-2">Pengarang</label>
                        <input type="text" value="{{ $katalog->pengarang }}" 
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" disabled>
                    </div>

                    {{-- Penerbit --}}
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-text mb-2">Penerbit</label>
                        <input type="text" value="{{ $katalog->penerbit }}" 
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" disabled>
                    </div>

                    {{-- Kategori Buku --}}
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-text mb-2">Kategori Buku</label>
                        <input type="text" value="{{ $katalog->kategori_buku }}" 
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" disabled>
                    </div>

                    {{-- ISBN --}}
                    <div class="sm:col-span-3">
                        <label for="isbn" class="block text-sm font-medium text-text mb-2">ISBN</label>
                        <input type="text" name="isbn" id="isbn" value="{{ old('isbn', $katalog->isbn) }}" 
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 mt-2">
                        @error('isbn')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Cover Buku --}}
                    <div class="sm:col-span-3">
                        <label for="cover_buku" class="block text-sm font-medium text-text mb-2">Cover Buku</label>
                        <input type="file" name="cover_buku" id="cover_buku"
                            class="mt-2 block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer bg-white">
                        @if($katalog->cover_buku)
                            <p class="text-sm mt-1">Saat ini: <a href="{{ asset('storage/' . $katalog->cover_buku) }}" target="_blank" class="text-blue-600 underline">Lihat Cover</a></p>
                        @endif
                        @error('cover_buku')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Ringkasan Buku --}}
                    <div class="col-span-full">
                        <label for="ringkasan_buku" class="block text-sm font-medium text-text mb-2">Ringkasan Buku</label>
                        <textarea name="ringkasan_buku" id="ringkasan_buku" rows="4"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 mt-2">{{ old('ringkasan_buku', $katalog->ringkasan_buku) }}</textarea>
                        @error('ringkasan_buku')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kode DDC --}}
                    <div class="sm:col-span-3">
                        <label for="kode_ddc" class="block text-sm font-medium text-text mb-2">Kode DDC</label>
                        <input type="text" name="kode_ddc" id="kode_ddc" value="{{ old('kode_ddc', $katalog->kode_ddc) }}"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 mt-2">
                        @error('kode_ddc')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nomor Panggil --}}
                    <div class="sm:col-span-3">
                        <label for="no_panggil" class="block text-sm font-medium text-text mb-2">Nomor Panggil</label>
                        <input type="text" name="no_panggil" id="no_panggil" value="{{ old('no_panggil', $katalog->no_panggil) }}"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 mt-2">
                        @error('no_panggil')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <div class="mt-6 flex items-center justify-start gap-x-4">
                    <a href="{{ route('admin.katalog.index') }}"
                        class="text-sm/6 font-semibold text-text hover:text-text hover:border border-gray-900 rounded-md px-3 py-2 pointer">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-blue-700 rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
