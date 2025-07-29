@extends('layouts.admin-app')

@section('content')
    <form method="POST" action="{{ route('admin.katalog.update', $katalog->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <h2 class="text-[28px] font-semibold text-text">Formulir Katalog</h2>
            <div class="border-gray-900/10 pb-8 p-4 bg-white border border-gray-200 rounded">
                <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">

                    {{-- Judul Buku --}}
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-text mb-2">Judul Buku</label>
                        <input type="text" id="judul_buku_display" value="{{ $katalog->judul_buku }}" disabled
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                        border border-gray-300 placeholder:text-gray-400
                                        focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                    </div>

                    {{-- Pengarang --}}
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-text mb-2">Pengarang</label>
                        <input type="text" id="pengarang_display" value="{{ $katalog->pengarang }}" disabled
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                        border border-gray-300 placeholder:text-gray-400
                                        focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                    </div>

                    {{-- Penerbit --}}
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-text mb-2">Penerbit</label>
                        <input type="text" id="penerbit_display" value="{{ $katalog->penerbit }}" disabled
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                        border border-gray-300 placeholder:text-gray-400
                                        focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                    </div>

                    {{-- Kategori Buku --}}
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-text mb-2">Kategori Buku</label>
                        <input type="text" value="{{ $katalog->kategori_buku }}" disabled
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                        border border-gray-300 placeholder:text-gray-400
                                        focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                    </div>

                    {{-- ISBN --}}
                    <div class="sm:col-span-3">
                        <label for="isbn" class="block text-sm font-medium text-text mb-2">ISBN</label>

                        <div class="flex gap-2 items-stretch mt-2">
                            <input type="text" name="isbn" id="isbn" value="{{ old('isbn', $katalog->isbn) }}"
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                       border border-gray-300 placeholder:text-gray-400
                                       focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">

                            <button type="button" id="btn-cek-cover"
                                class="px-4 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                                Cek
                            </button>
                        </div>

                        <!-- Tombol Generate ISBN -->
                        <button type="button" id="generate-isbn"
                            class="mt-2 inline-flex items-center px-4 py-2 bg-blue-700 text-white rounded hover:bg-green-800 text-sm">
                            Generate ISBN
                        </button>

                        <!-- Spinner -->
                        <div id="spinner-isbn" class="mt-3 hidden">
                            <div class="flex items-center text-sm text-gray-700">
                                <svg class="animate-spin h-5 w-5 mr-2 text-green-600" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16 8 8 0 01-8-8z" />
                                </svg>
                                Sedang menghasilkan ISBN...
                            </div>
                        </div>

                        @error('isbn')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- Cover Buku --}}
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-text mb-2">Cover Buku</label>

                        <label for="cover_buku"
                            class="flex items-center cursor-pointer rounded-md overflow-hidden border border-gray-300 bg-white">

                            <span class="bg-gray-800 text-white text-sm font-semibold px-4 py-2">
                                Pilih File
                            </span>

                            <span id="file_name" class="ml-3 text-sm text-gray-500">
                                Tidak ada file dipilih
                            </span>
                        </label>

                        <input type="file" name="cover_buku" id="cover_buku" accept="image/*" class="hidden">

                        <input type="hidden" name="cover_buku_url" id="cover_buku_url">

                        <div class="mt-2 space-y-2" id="cover_buku_feedback">
                            <small id="cover_buku_status" class="text-sm text-gray-600 block"></small>
                            <img id="cover_preview" src="" class="w-24 h-auto rounded border hidden">
                        </div>

                        {{-- @if ($katalog->cover_buku)
                            <p class="text-sm mt-1">Saat ini:
                                <a href="{{ asset('storage/' . $katalog->cover_buku) }}" target="_blank"
                                    class="text-blue-600 underline">Lihat Cover</a>
                            </p>
                        @endif --}}

                        @if ($katalog->cover_buku)
                            <p class="text-sm mt-1">Saat ini:
                                <a href="{{ asset($katalog->cover_buku) }}" target="_blank" class="text-blue-600 underline">
                                    Lihat Cover
                                </a>
                            </p>
                        @endif

                        @error('cover_buku')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Ringkasan Buku --}}
                    <div class="col-span-full">
                        <label for="ringkasan_buku" class="block text-sm font-medium text-text mb-2">Ringkasan Buku</label>
                        <textarea name="ringkasan_buku" id="ringkasan_buku" rows="4"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                        border border-gray-300 placeholder:text-gray-400
                        focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">{{ old('ringkasan_buku', $katalog->ringkasan_buku) }}</textarea>
                        @error('ringkasan_buku')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror

                        <!-- Tombol Generate -->
                        <button type="button" id="generate-ringkasan"
                            class="mt-2 inline-flex items-center px-4 py-2 bg-blue-700 text-white rounded hover:bg-blue-800">
                            Generate Ringkasan
                        </button>

                        <!-- Spinner -->
                        <div id="spinner-ringkasan" class="mt-3 hidden">
                            <div class="flex items-center text-sm text-gray-700">
                                <svg class="animate-spin h-5 w-5 mr-2 text-blue-600" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16 8 8 0 01-8-8z" />
                                </svg>
                                Sedang menghasilkan ringkasan...
                            </div>
                        </div>
                    </div>

                    {{-- Kode DDC --}}
                    <div class="sm:col-span-3">
                        <label for="kode_ddc" class="block text-sm font-medium text-text mb-2">Kode DDC</label>
                        <input type="text" name="kode_ddc" id="kode_ddc"
                            value="{{ old('kode_ddc', $katalog->kode_ddc) }}"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                        border border-gray-300 placeholder:text-gray-400
                                        focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                        @error('kode_ddc')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nomor Panggil --}}
                    <div class="sm:col-span-3">
                        <label for="no_panggil" class="block text-sm font-medium text-text mb-2">Nomor Panggil</label>
                        <input type="text" name="no_panggil" id="no_panggil"
                            value="{{ old('no_panggil', $katalog->no_panggil) }}"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                        border border-gray-300 placeholder:text-gray-400
                                        focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                        @error('no_panggil')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="sm:col-span-6 mt-4">
                    <button type="button" id="generate-ddc"
                        class="inline-flex items-center px-4 py-2 bg-blue-700 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition">
                        Generate Kode DDC & Nomor Panggil
                    </button>

                    <!-- Spinner (diletakkan setelah tombol) -->
                    <div id="spinner-ddc" class="mt-3 hidden">
                        <div class="flex items-center text-sm text-gray-700">
                            <svg class="animate-spin h-5 w-5 mr-2 text-indigo-600" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16 8 8 0 01-8-8z" />
                            </svg>
                            Sedang menghasilkan kode DDC dan nomor panggil...
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="mt-6 flex items-center justify-start gap-x-4">
                    <a href="{{ route('admin.katalog.index') }}"
                        class="text-sm font-semibold text-text hover:text-text hover:border border-gray-900 rounded-md px-3 py-2 pointer">
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

    <script>
        document.getElementById('generate-isbn').addEventListener('click', async function() {
            const judul = document.getElementById('judul_buku_display').value;
            const pengarang = document.getElementById('pengarang_display').value;
            const spinner = document.getElementById('spinner-isbn');
            const button = this;

            spinner.classList.remove('hidden');
            button.disabled = true;
            button.classList.add('opacity-50', 'cursor-not-allowed');

            try {
                const res = await fetch("{{ route('admin.katalog.generate-isbn') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        judul,
                        pengarang
                    })
                });

                const data = await res.json();
                if (data.success) {
                    document.getElementById('isbn').value = data.isbn;
                } else {
                    alert("Gagal generate ISBN.");
                }
            } catch (error) {
                console.error(error);
                alert("Terjadi kesalahan saat menghubungi server.");
            } finally {
                spinner.classList.add('hidden');
                button.disabled = false;
                button.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        });
    </script>

    <script>
        document.getElementById('generate-ddc').addEventListener('click', async function() {
            const judul = document.getElementById('judul_buku_display').value;
            const pengarang = document.getElementById('pengarang_display').value;
            const spinner = document.getElementById('spinner-ddc');
            const button = this;

            // Tampilkan spinner & disable tombol
            spinner.classList.remove('hidden');
            button.disabled = true;
            button.classList.add('opacity-50', 'cursor-not-allowed');

            try {
                const res = await fetch("{{ route('admin.katalog.generate-ddc') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        judul,
                        pengarang
                    })
                });

                const data = await res.json();
                if (data.success) {
                    document.getElementById('kode_ddc').value = data.kode_ddc;
                    document.getElementById('no_panggil').value = data.no_panggil;
                } else {
                    alert("Gagal generate kode DDC.");
                }
            } catch (error) {
                console.error(error);
                alert("Terjadi kesalahan saat menghubungi server.");
            } finally {
                spinner.classList.add('hidden');
                button.disabled = false;
                button.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        });
    </script>


    <script>
        document.getElementById('cover_buku').addEventListener('change', function(e) {
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

    <script>
        document.getElementById('generate-ringkasan').addEventListener('click', async function() {
            const judul = document.getElementById('judul_buku_display').value;
            const pengarang = document.getElementById('pengarang_display').value;
            const spinner = document.getElementById('spinner-ringkasan');
            const button = this;

            // Tampilkan spinner & disable tombol
            spinner.classList.remove('hidden');
            button.disabled = true;
            button.classList.add('opacity-50', 'cursor-not-allowed');

            try {
                const res = await fetch("{{ route('admin.katalog.generate-ringkasan') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        judul,
                        pengarang
                    })
                });

                const data = await res.json();
                if (data.success) {
                    document.getElementById('ringkasan_buku').value = data.ringkasan;
                } else {
                    alert("Gagal generate ringkasan.");
                }
            } catch (error) {
                console.error(error);
                alert("Terjadi kesalahan saat menghubungi server.");
            } finally {
                // Sembunyikan spinner & aktifkan tombol kembali
                spinner.classList.add('hidden');
                button.disabled = false;
                button.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        });
    </script>

    <script>
        document.getElementById('btn-cek-cover').addEventListener('click', async function() {
            const isbn = document.getElementById('isbn').value.trim();
            const status = document.getElementById('cover_buku_status');
            const hiddenInput = document.getElementById('cover_buku_url');
            const preview = document.getElementById('cover_preview');

            if (!isbn) {
                status.textContent = "❌ ISBN tidak boleh kosong.";
                return;
            }

            status.innerHTML = `
                            <svg class="w-6 h-6 text-blue-700 inline-block animate-spin mr-2"
                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.651 7.65a7.131 7.131 0 0 0-12.68 3.15M18.001 4v4h-4m-7.652 8.35a7.13 7.13 0 0 0 12.68-3.15M6 20v-4h4"/>
                            </svg>
                            <span class="align-middle">Mencari cover dari Google Books…</span>
                            `;

            hiddenInput.value = "";
            preview.classList.add('hidden');

            try {
                const res = await fetch(`/admin/katalog/fetch-cover/${isbn}`);
                const data = await res.json();

                if (data.success) {
                    status.innerHTML = `
                                        <svg class="w-6 h-6 text-green-600 inline-block mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="m8.032 12 1.984 1.984 4.96-4.96m4.55 5.272.893-.893a1.984 1.984 0 0 0 0-2.806l-.893-.893a1.984 1.984 0 0 1-.581-1.403V7.04a1.984 1.984 0 0 0-1.984-1.984h-1.262a1.983 1.983 0 0 1-1.403-.581l-.893-.893a1.984 1.984 0 0 0-2.806 0l-.893.893a1.984 1.984 0 0 1-1.403.581H7.04A1.984 1.984 0 0 0 5.055 7.04v1.262c0 .527-.209 1.031-.581 1.403l-.893.893a1.984 1.984 0 0 0 0 2.806l.893.893c.372.372.581.876.581 1.403v1.262a1.984 1.984 0 0 0 1.984 1.984h1.262c.527 0 1.031.209 1.403.581l.893.893a1.984 1.984 0 0 0 2.806 0l.893-.893a1.985 1.985 0 0 1 1.403-.581h1.262a1.984 1.984 0 0 0 1.984-1.984V15.7c0-.527.209-1.031.581-1.403Z"/>
                                        </svg>
                                        <span class="align-middle">Cover ditemukan dan disimpan.</span>
                                        `;

                    hiddenInput.value = data.path;
                    preview.src = data.cover_url;
                    preview.classList.remove('hidden');
                } else {
                    status.innerHTML = `
                        <svg class="w-5 h-5 text-yellow-500 inline-block mr-2"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a1.5 1.5 0 0 0 1.29 2.25h17.78a1.5 1.5 0 0 0 1.29-2.25L13.71 3.86a1.5 1.5 0 0 0-2.42 0Z"/>
                        </svg>
                        <span class="align-middle">${data.message}</span>
                        `;
                }
            } catch (err) {
                console.error(err);
                status.innerHTML = `
                    <svg class="w-6 h-6 text-red-600 inline-block mr-2"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18 17.94 6M18 18 6.06 6"/>
                    </svg>
                    <span class="align-middle">Gagal mengambil data dari Google Books.</span>
                    `;

            }
        });
    </script>
@endsection
