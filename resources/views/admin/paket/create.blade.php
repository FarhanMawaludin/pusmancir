@extends('layouts.admin-app')

@section('content')
    <form method="POST" action="{{ route('admin.paket.store') }}">
        @csrf
        <div class="space-y-4">
            <h2 class="text-[28px] font-semibold text-text">Formulir Buku Paket</h2>
            <div class="border-gray-900/10 pb-8 p-4 bg-white border border-gray-200 rounded">
                <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">

                    {{-- Jumlah Eksemplar --}}
                    <div class="sm:col-span-3">
                        <label for="nama_paket" class="block text-sm font-medium text-text mb-2">Nama Buku</label>
                        <input type="text" name="nama_paket" id="nama_paket" value="{{ old('nama_paket') }}"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                            required>
                        @error('nama_paket')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Pembelian --}}
                    <div class="sm:col-span-3">
                        <label for="stok_total" class="block text-sm font-medium text-text mb-2">Total Buku Paket</label>
                        <input type="number" name="stok_total" id="stok_total" value="{{ old('stok_total') }}"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                            required>
                        @error('stok_total')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-full">
                        <label for="deskripsi" class="block text-sm font-medium text-text mb-2">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="4"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 mt-2"></textarea>
                        @error('deskripsi')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <div class="mt-6 flex items-center justify-start gap-x-4">
                    <button type="button" onclick="location.href='{{ route('admin.paket.index') }}'"
                        class="text-sm/6 font-semibold text-text hover:text-text hover:border border-gray-900 rounded-md px-3 py-2 pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="bg-blue-700 rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
