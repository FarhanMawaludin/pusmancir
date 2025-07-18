@extends('layouts.admin-app')

@section('content')
    <form method="POST" action="{{ route('admin.berita.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="space-y-4">
            <h2 class="text-[28px] font-semibold text-text">Formulir Berita</h2>
            <div class="border-gray-900/10 pb-8 p-4 bg-white border border-gray-200 rounded">
                <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">

                    <!-- Judul -->
                    <div class="sm:col-span-6">
                        <label for="judul" class="block text-sm font-medium text-text">Judul</label>
                        <input type="text" name="judul" id="judul" value="{{ old('judul') }}"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text border border-gray-300 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm" />
                        @error('judul')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="sm:col-span-3">
                        <label for="status" class="block text-sm font-medium text-text">Status</label>
                        <select name="status" id="status"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm">
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="publish" {{ old('status') === 'publish' ? 'selected' : '' }}>Publish</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Penulis -->
                    <div class="sm:col-span-3">
                        <label for="penulis" class="block text-sm font-medium text-text">Penulis</label>
                        <input type="text" name="penulis" id="penulis" value="{{ old('penulis') }}"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text border border-gray-300 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm" />
                        @error('penulis')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Isi -->
                    <div class="col-span-full">
                        <label for="isi" class="block text-sm font-medium text-text">Isi Berita</label>
                        <textarea name="isi" id="isi" rows="6"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text border border-gray-300 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm">{{ old('isi') }}</textarea>
                        @error('isi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gambar Thumbnail -->
                    <div class="col-span-full">
                        <label for="thumbnail" class="block text-sm font-medium text-text">Thumbnail (opsional)</label>
                        <input type="file" name="thumbnail" id="thumbnail"
                            accept="image/png, image/jpeg, image/jpg, image/gif, image/webp"
                            class="mt-2 block w-full text-sm text-text border border-gray-300 rounded cursor-pointer bg-gray-50 focus:outline-none" />
                        @error('thumbnail')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Tombol -->
                <div class="mt-6 flex items-center justify-start gap-x-4">
                    <a href="{{ route('admin.berita.index') }}"
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
@endsection
