@extends('layouts.admin-app')

@section('content')
    <form method="POST" action="{{ route('admin.berita.update', $berita->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <h2 class="text-[28px] font-semibold text-text">Edit Berita</h2>
            <div class="border-gray-900/10 pb-8 p-4 bg-white border border-gray-200 rounded">
                <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">

                    <!-- Judul -->
                    <div class="sm:col-span-6">
                        <label for="judul" class="block text-sm font-medium text-text">Judul</label>
                        <input type="text" name="judul" id="judul" value="{{ old('judul', $berita->judul) }}"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm" />
                        @error('judul')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="sm:col-span-3">
                        <label for="status" class="block text-sm font-medium text-text">Status</label>
                        <select name="status" id="status"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm">
                            <option value="draft" {{ $berita->status === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="publish" {{ $berita->status === 'publish' ? 'selected' : '' }}>Publish</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Penulis -->
                    <div class="sm:col-span-3">
                        <label for="penulis" class="block text-sm font-medium text-text">Penulis</label>
                        <input type="text" name="penulis" id="penulis" value="{{ old('penulis', $berita->penulis) }}"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm" />
                        @error('penulis')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Isi -->
                    <!-- Isi -->
                    <div class="col-span-full">
                        <label for="isi" class="block text-sm font-medium text-text mb-2">Isi Berita</label>

                        <!-- Hidden input untuk menyimpan isi -->
                        <input id="isi" type="hidden" name="isi" value="{{ old('isi', $berita->isi) }}">

                        <!-- Trix editor -->
                        <trix-editor input="isi"
                            class="mt-2 block w-full bg-white border border-gray-300 rounded-md"></trix-editor>

                        @error('isi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gambar Thumbnail -->
                    <div class="col-span-full">
                        <label for="thumbnail" class="block text-sm font-medium text-text mb-2">Thumbnail </label>
                        <label for="thumbnail"
                            class="flex items-center cursor-pointer rounded-md overflow-hidden border border-gray-300 bg-white">

                            <span class="bg-gray-800 text-white text-sm font-semibold px-4 py-2">
                                Pilih File
                            </span>

                            <span id="file_name" class="ml-3 text-sm text-gray-500">
                                Tidak ada file dipilih
                            </span>
                        </label>
                        <input type="file" name="thumbnail" id="thumbnail"
                            accept="image/png, image/jpeg, image/jpg, image/gif, image/webp" class="hidden">

                        {{-- Thumbnail Preview --}}
                        @if ($berita->thumbnail)
                            <p class="text-sm mt-1">Saat ini:
                                <a href="{{ asset($berita->thumbnail) }}" target="_blank"
                                    class="text-blue-600 underline">Lihat Thumbnail</a>
                            </p>
                        @endif
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
                        Update
                    </button>
                </div>
            </div>
        </div>
    </form>

    <script>
        document.getElementById('thumbnail').addEventListener('change', function(e) {
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
