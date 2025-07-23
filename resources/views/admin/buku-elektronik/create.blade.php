@extends('layouts.admin-app')

@section('content')
    <form method="POST" action="{{ route('admin.buku-elektronik.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="space-y-4">
            <h2 class="text-[28px] font-semibold text-text">Formulir Buku Elektronik</h2>
            <div class="border-gray-900/10 pb-8 p-4 bg-white border border-gray-200 rounded">

                {{-- Judul --}}
                <div class="mt-4">
                    <label for="judul" class="block text-sm font-medium text-text">Judul</label>
                    <input type="text" name="judul" id="judul" value="{{ old('judul') }}"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-text shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        required>
                    @error('judul')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Penulis --}}
                <div class="mt-4">
                    <label for="penulis" class="block text-sm font-medium text-text">Penulis</label>
                    <input type="text" name="penulis" id="penulis" value="{{ old('penulis') }}"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-text shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('penulis')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kategori --}}
                <div class="mt-4">
                    <label for="kategori" class="block text-sm font-medium text-text">Kategori</label>
                    <input type="text" name="kategori" id="kategori" value="{{ old('kategori') }}"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-text shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        required>
                    @error('kategori')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kelas --}}
                <div class="mt-4">
                    <label for="kelas" class="block text-sm font-medium text-text">Kelas (contoh: 10, 11, 12)</label>
                    <input type="text" name="kelas" id="kelas" value="{{ old('kelas') }}"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-text shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        required>
                    @error('kelas')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kurikulum --}}
                <div class="mt-4">
                    <label for="kurikulum" class="block text-sm font-medium text-text">Kurikulum</label>
                    <input type="text" name="kurikulum" id="kurikulum" value="{{ old('kurikulum') }}"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-text shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('kurikulum')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Upload PDF --}}
                <div class="mt-4">
                    <label for="pdf_path" class="block text-sm font-medium text-text mb-2">Upload File PDF</label>
                    <label for="pdf_path"
                        class="flex items-center cursor-pointer rounded-md overflow-hidden border border-gray-300 bg-white">

                        <span class="bg-gray-800 text-white text-sm font-semibold px-4 py-2">
                            Pilih File
                        </span>

                        <span id="file_pdf" class="ml-3 text-sm text-gray-500">
                            Tidak ada file dipilih
                        </span>
                    </label>
                    <input type="file" name="pdf_path" id="pdf_path" accept="application/pdf" class="hidden">
                    {{-- <input type="file" name="pdf_path" id="pdf_path" accept=".pdf" class="mt-1 block w-full text-text"> --}}
                    @error('pdf_path')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Upload Cover Image --}}
                <div class="mt-4">
                    <label for="cover_image" class="block text-sm font-medium text-text mb-2">Upload Cover Gambar</label>
                    <label for="cover_image"
                        class="flex items-center cursor-pointer rounded-md overflow-hidden border border-gray-300 bg-white">

                        <span class="bg-gray-800 text-white text-sm font-semibold px-4 py-2">
                            Pilih File
                        </span>

                        <span id="file_name" class="ml-3 text-sm text-gray-500">
                            Tidak ada file dipilih
                        </span>
                    </label>
                    <input type="file" name="cover_image" id="cover_image" accept="image/*" class="hidden">
                    {{-- <input type="file" name="cover_image" id="cover_image" accept="image/*"
                        class="mt-1 block w-full text-text"> --}}
                    @error('cover_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6 flex items-center justify-start gap-x-4">
                    <button type="button"
                        class="text-sm font-semibold text-text hover:text-text hover:border border-gray-900 rounded-md px-3 py-2"
                        onclick="location.href='{{ route('admin.buku-elektronik.index') }}'">Batal</button>
                    <button type="submit"
                        class="bg-blue-700 rounded-md px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-blue-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Simpan</button>
                </div>
            </div>
        </div>
    </form>

    <script>
        document.getElementById('pdf_path').addEventListener('change', function(e) {
            const fileName = e.target.files.length ? e.target.files[0].name : 'Tidak ada file dipilih';
            document.getElementById('file_pdf').textContent = fileName;

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

    <script>
        document.getElementById('cover_image').addEventListener('change', function(e) {
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
