@extends('layouts.admin-app')

@section('content')
    <form method="POST" action="{{ route('admin.katalog.update', ['id' => $katalog->id, 'page' => request('page'), 'sort' => request('sort'), 'search' => request('search'), 'category' => request('category')]) }}" enctype="multipart/form-data">
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
                        </div>

                        <!-- Tombol Generate ISBN -->
                        <button type="button" id="generate-isbn"
                            class="mt-2 inline-flex items-center px-4 py-2 bg-blue-700 text-white rounded hover:bg-blue-800 text-sm font-semibold">
                            Generate ISBN
                        </button>
                        <button type="button" id="generate-isbn-openrouter"
                            class="mt-2 inline-flex items-center px-4 py-2 bg-purple-700 text-white rounded hover:bg-purple-800 text-sm font-semibold ml-2">
                            Generate ISBN (OpenRouter)
                        </button>

                        <!-- Spinner -->
                        <div id="spinner-isbn" class="mt-3 hidden">
                            <div class="flex items-center text-sm text-gray-700">
                                <svg class="animate-spin h-5 w-5 mr-2 text-blue-600" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16 8 8 0 01-8-8z" />
                                </svg>
                                <span id="spinner-isbn-text">Mencari ISBN di Google Books...</span>
                            </div>
                        </div>

                        <!-- Info Sumber ISBN -->
                        <div id="isbn-source-info" class="mt-2 hidden"></div>

                        @error('isbn')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- Cover Buku --}}
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-text mb-2">Cover Buku</label>

                        <label for="cover_buku"
                            class="flex items-center cursor-pointer rounded-md overflow-hidden border border-gray-300 bg-white mb-2">

                            <span class="bg-gray-800 text-white text-sm font-semibold px-4 py-2">
                                Pilih File
                            </span>

                            <span id="file_name" class="ml-3 text-sm text-gray-500">
                                Tidak ada file dipilih
                            </span>
                        </label>

                        <!-- Tombol Pencarian Cover -->
                        <div class="flex flex-wrap gap-2 mb-2">
                            <button type="button" id="btn-cek-cover"
                                class="inline-flex items-center px-3 py-2 text-white rounded text-xs font-semibold"
                                style="background-color: #16a34a;">
                                Cari Cover di API Google Book
                            </button>
                            <button type="button" id="btn-cari-cover-ai-gemini"
                                class="inline-flex items-center px-3 py-2 text-white rounded text-xs font-semibold"
                                style="background-color: #7e22ce;">
                                Cari Cover di AI Gemini
                            </button>
                            <button type="button" id="btn-cari-cover-ai-openrouter"
                                class="inline-flex items-center px-3 py-2 text-white rounded text-xs font-semibold"
                                style="background-color: #4f46e5;">
                                Cari Cover di OpenRouter
                            </button>
                            <button type="button" id="btn-cari-cover-ai-gramedia"
                                class="inline-flex items-center px-3 py-2 text-white rounded text-xs font-semibold"
                                style="background-color: #be185d;">
                                Cari Cover di database Gramedia (Ai Support)
                            </button>
                        </div>

                        <input type="file" name="cover_buku" id="cover_buku" accept="image/*" class="hidden">

                        <input type="hidden" name="cover_buku_url" id="cover_buku_url">

                        <div class="mt-2 space-y-2" id="cover_buku_feedback">
                            <small id="cover_buku_status" class="text-sm text-gray-600 block"></small>
                            <img id="cover_preview" src="" class="w-24 h-auto rounded border hidden">
                            
                            <!-- Tombol Terima / Tolak Cover -->
                            <div id="cover_action_buttons" class="hidden flex gap-2 mt-2">
                                <button type="button" id="btn-terima-cover"
                                    class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700 text-xs font-semibold">
                                    Terima Cover
                                </button>
                                <button type="button" id="btn-tolak-cover"
                                    class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded hover:bg-red-700 text-xs font-semibold">
                                    Tolak Cover
                                </button>
                            </div>
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
                        <button type="button" id="generate-ringkasan-openrouter"
                            class="mt-2 inline-flex items-center px-4 py-2 bg-purple-700 text-white rounded hover:bg-purple-800 ml-2">
                            Generate Ringkasan (OpenRouter)
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
                        class="mt-2 inline-flex items-center px-4 py-2 bg-blue-700 text-white rounded hover:bg-blue-800">
                        Generate Kode DDC & Nomor Panggil
                    </button>
                    <button type="button" id="generate-ddc-openrouter"
                        class="mt-2 inline-flex items-center px-4 py-2 bg-purple-700 text-white rounded hover:bg-purple-800 ml-2">
                        Generate DDC & Nomor Panggil (OpenRouter)
                    </button>

                    <!-- Spinner (diletakkan setelah tombol) -->
                    <div id="spinner-ddc" class="mt-3 hidden">
                        <div class="flex items-center text-sm text-gray-700">
                            <svg class="animate-spin h-5 w-5 mr-2 text-blue-600" viewBox="0 0 24 24" fill="none">
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
                    <a href="{{ route('admin.katalog.index', ['page' => request('page'), 'sort' => request('sort'), 'search' => request('search'), 'category' => request('category')]) }}"
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
        // --- FEATURE: GENERATE ISBN ---
        async function runGenerateISBN(provider) {
            const judul = document.getElementById('judul_buku_display').value;
            const pengarang = document.getElementById('pengarang_display').value;
            const penerbit = document.getElementById('penerbit_display').value;
            const spinner = document.getElementById('spinner-isbn');
            const spinnerText = document.getElementById('spinner-isbn-text');
            const isbnSourceInfo = document.getElementById('isbn-source-info');

            spinner.classList.remove('hidden');
            if (isbnSourceInfo) isbnSourceInfo.classList.add('hidden');

            const btnIsbn = document.getElementById('generate-isbn');
            const btnIsbnOr = document.getElementById('generate-isbn-openrouter');
            [btnIsbn, btnIsbnOr].forEach(btn => {
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            });

            // Urutan label sesuai urutan pencarian di backend
            const searchSteps = provider === 'openrouter' 
                ? [
                    '🔍 Mencari ISBN di database Gramedia...',
                    '🔍 Mencari ISBN di Google Books...',
                    '🔍 Mencari ISBN di Open Library...',
                    '🤖 Mencari via AI OpenRouter (fallback)...'
                  ]
                : [
                    '🔍 Mencari ISBN di database Gramedia...',
                    '🔍 Mencari ISBN di Google Books...',
                    '🔍 Mencari ISBN di Open Library...',
                    '🤖 Mencari via AI Gemini (fallback)...'
                  ];
            let stepIndex = 0;
            spinnerText.textContent = searchSteps[0];

            const spinnerInterval = setInterval(() => {
                stepIndex++;
                if (stepIndex < searchSteps.length) {
                    spinnerText.textContent = searchSteps[stepIndex];
                }
            }, 3000);

            try {
                const res = await fetch("{{ route('admin.katalog.generate-isbn') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ judul, pengarang, penerbit, provider })
                });

                const data = await res.json();
                if (data.success) {
                    document.getElementById('isbn').value = data.isbn;

                    if (isbnSourceInfo) {
                        const source = data.source || 'Unknown';
                        const confidence = data.confidence || 'low';

                        let badgeColor, badgeIcon, badgeText;
                        if (confidence === 'high') {
                            badgeColor = 'bg-green-100 text-green-800 border-green-300';
                            badgeIcon = `<svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>`;
                            badgeText = `Ditemukan dari <strong>${source}</strong> — ISBN terverifikasi`;
                        } else {
                            badgeColor = 'bg-yellow-100 text-yellow-800 border-yellow-300';
                            badgeIcon = `<svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>`;
                            badgeText = `Dihasilkan oleh <strong>${source}</strong> — <span class="text-red-600 font-semibold">perlu verifikasi manual!</span>`;
                        }

                        isbnSourceInfo.innerHTML = `
                            <div class="inline-flex items-center px-3 py-1.5 rounded-md border text-xs ${badgeColor}">
                                ${badgeIcon} ${badgeText}
                            </div>
                        `;
                        isbnSourceInfo.classList.remove('hidden');
                    }
                } else {
                    alert("Gagal mencari ISBN: " + (data.error || "Unknown error."));
                }
            } catch (error) {
                console.error(error);
                alert("Terjadi kesalahan saat menghubungi server.");
            } finally {
                clearInterval(spinnerInterval);
                spinner.classList.add('hidden');
                [btnIsbn, btnIsbnOr].forEach(btn => {
                    if (btn) {
                        btn.disabled = false;
                        btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                });
            }
        }

        document.getElementById('generate-isbn').addEventListener('click', () => runGenerateISBN('gemini'));
        const btnIsbnOr = document.getElementById('generate-isbn-openrouter');
        if (btnIsbnOr) {
            btnIsbnOr.addEventListener('click', () => runGenerateISBN('openrouter'));
        }

        // --- FEATURE: GENERATE RINGKASAN ---
        async function runGenerateRingkasan(provider) {
            const judul = document.getElementById('judul_buku_display').value;
            const pengarang = document.getElementById('pengarang_display').value;
            const spinner = document.getElementById('spinner-ringkasan');
            
            const btnRingkasan = document.getElementById('generate-ringkasan');
            const btnRingkasanOr = document.getElementById('generate-ringkasan-openrouter');

            spinner.classList.remove('hidden');
            [btnRingkasan, btnRingkasanOr].forEach(btn => {
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            });

            try {
                const res = await fetch("{{ route('admin.katalog.generate-ringkasan') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ judul, pengarang, provider })
                });

                const data = await res.json();
                if (data.success) {
                    document.getElementById('ringkasan_buku').value = data.ringkasan;
                } else {
                    alert("Gagal generate ringkasan: " + (data.error || "Unknown error."));
                }
            } catch (error) {
                console.error(error);
                alert("Terjadi kesalahan saat menghubungi server.");
            } finally {
                spinner.classList.add('hidden');
                [btnRingkasan, btnRingkasanOr].forEach(btn => {
                    if (btn) {
                        btn.disabled = false;
                        btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                });
            }
        }

        document.getElementById('generate-ringkasan').addEventListener('click', () => runGenerateRingkasan('gemini'));
        const btnRingkasanOr = document.getElementById('generate-ringkasan-openrouter');
        if (btnRingkasanOr) {
            btnRingkasanOr.addEventListener('click', () => runGenerateRingkasan('openrouter'));
        }
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
        async function runGenerateDDC(provider) {
            const judul = document.getElementById('judul_buku_display').value.trim();
            const pengarang = document.getElementById('pengarang_display').value.trim();
            const spinner = document.getElementById('spinner-ddc');
            
            const btnDdc = document.getElementById('generate-ddc');
            const btnDdcOr = document.getElementById('generate-ddc-openrouter');

            if (!judul || !pengarang) {
                alert("Judul dan pengarang harus diisi.");
                return;
            }

            // Tampilkan spinner & disable tombol
            spinner.classList.remove('hidden');
            [btnDdc, btnDdcOr].forEach(btn => {
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            });

            try {
                const res = await fetch("{{ route('admin.katalog.generate-ddc') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ judul, pengarang, provider })
                });

                const data = await res.json();
                if (data.success) {
                    document.getElementById('kode_ddc').value = data.kode_ddc;
                    document.getElementById('no_panggil').value = data.no_panggil;
                } else {
                    alert("Gagal generate kode DDC: " + (data.error || "Unknown error."));
                }
            } catch (error) {
                console.error(error);
                alert("Terjadi kesalahan saat menghubungi server.");
            } finally {
                spinner.classList.add('hidden');
                [btnDdc, btnDdcOr].forEach(btn => {
                    if (btn) {
                        btn.disabled = false;
                        btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                });
            }
        }

        document.getElementById('generate-ddc').addEventListener('click', () => runGenerateDDC('gemini'));
        const btnDdcOr = document.getElementById('generate-ddc-openrouter');
        if (btnDdcOr) {
            btnDdcOr.addEventListener('click', () => runGenerateDDC('openrouter'));
        }
    </script>


    <script>
        document.getElementById('btn-cek-cover').addEventListener('click', async function() {
            const isbn = document.getElementById('isbn').value.trim();
            const status = document.getElementById('cover_buku_status');
            const hiddenInput = document.getElementById('cover_buku_url');
            const preview = document.getElementById('cover_preview');
            const actionButtons = document.getElementById('cover_action_buttons');

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
            preview.removeAttribute('data-temp-path');
            actionButtons.classList.add('hidden');

            const judul = document.getElementById('judul_buku_display').value.trim();
            const pengarang = document.getElementById('pengarang_display').value.trim();

            try {
                const res = await fetch(`/admin/katalog/fetch-cover/${isbn}?judul=${encodeURIComponent(judul)}&pengarang=${encodeURIComponent(pengarang)}`);
                const data = await res.json();

                if (data.success) {
                    status.innerHTML = `
                                        <svg class="w-6 h-6 text-green-600 inline-block mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="m8.032 12 1.984 1.984 4.96-4.96m4.55 5.272.893-.893a1.984 1.984 0 0 0 0-2.806l-.893-.893a1.984 1.984 0 0 1-.581-1.403V7.04a1.984 1.984 0 0 0-1.984-1.984h-1.262a1.983 1.983 0 0 1-1.403-.581l-.893-.893a1.984 1.984 0 0 0-2.806 0l-.893.893a1.984 1.984 0 0 1-1.403.581H7.04A1.984 1.984 0 0 0 5.055 7.04v1.262c0 .527-.209 1.031-.581 1.403l-.893.893a1.984 1.984 0 0 0 0 2.806l.893.893c.372.372.581.876.581 1.403v1.262a1.984 1.984 0 0 0 1.984 1.984h1.262c.527 0 1.031.209 1.403.581l.893.893a1.984 1.984 0 0 0 2.806 0l.893-.893a1.985 1.985 0 0 1 1.403-.581h1.262a1.984 1.984 0 0 0 1.984-1.984V15.7c0-.527.209-1.031.581-1.403Z"/>
                                        </svg>
                                        <span class="align-middle">Cover ditemukan di Google Books/Open Library! Apakah Anda ingin menerimanya?</span>
                                        `;

                    preview.dataset.tempPath = data.path;
                    preview.src = data.cover_url;
                    preview.classList.remove('hidden');
                    actionButtons.classList.remove('hidden');
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

    <script>
        async function cariCoverAI(source) {
            const judul = document.getElementById('judul_buku_display').value.trim();
            const pengarang = document.getElementById('pengarang_display').value.trim();
            const status = document.getElementById('cover_buku_status');
            const hiddenInput = document.getElementById('cover_buku_url');
            const preview = document.getElementById('cover_preview');
            const actionButtons = document.getElementById('cover_action_buttons');

            if (!judul || !pengarang) {
                status.textContent = "❌ Judul dan Pengarang tidak boleh kosong untuk mencari via AI.";
                return;
            }

            let labelSource = 'Gemini AI';
            if (source === 'gramedia') {
                labelSource = 'database Gramedia (Ai Support)';
            } else if (source === 'openrouter') {
                labelSource = 'OpenRouter AI';
            }

            status.innerHTML = `
                            <svg class="w-6 h-6 text-blue-700 inline-block animate-spin mr-2"
                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.651 7.65a7.131 7.131 0 0 0-12.68 3.15M18.001 4v4h-4m-7.652 8.35a7.13 7.13 0 0 0 12.68-3.15M6 20v-4h4"/>
                            </svg>
                            <span class="align-middle">Mencari cover menggunakan ${labelSource}…</span>
                            `;

            hiddenInput.value = "";
            preview.classList.add('hidden');
            preview.removeAttribute('data-temp-path');
            actionButtons.classList.add('hidden');

            try {
                const res = await fetch("{{ route('admin.katalog.fetch-cover-ai') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ judul, pengarang, source })
                });
                const data = await res.json();

                if (data.success) {
                    status.innerHTML = `
                                        <svg class="w-6 h-6 text-green-600 inline-block mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="m8.032 12 1.984 1.984 4.96-4.96m4.55 5.272.893-.893a1.984 1.984 0 0 0 0-2.806l-.893-.893a1.984 1.984 0 0 1-.581-1.403V7.04a1.984 1.984 0 0 0-1.984-1.984h-1.262a1.983 1.983 0 0 1-1.403-.581l-.893-.893a1.984 1.984 0 0 0-2.806 0l-.893.893a1.984 1.984 0 0 1-1.403.581H7.04A1.984 1.984 0 0 0 5.055 7.04v1.262c0 .527-.209 1.031-.581 1.403l-.893.893a1.984 1.984 0 0 0 0 2.806l.893.893c.372.372.581.876.581 1.403v1.262a1.984 1.984 0 0 0 1.984 1.984h1.262c.527 0 1.031.209 1.403.581l.893.893a1.984 1.984 0 0 0 2.806 0l.893-.893a1.985 1.985 0 0 1 1.403-.581h1.262a1.984 1.984 0 0 0 1.984-1.984V15.7c0-.527.209-1.031.581-1.403Z"/>
                                        </svg>
                                        <span class="align-middle">Cover ditemukan oleh ${labelSource}! Apakah Anda ingin menerimanya?</span>
                                        `;

                    preview.dataset.tempPath = data.path;
                    preview.src = data.cover_url;
                    preview.classList.remove('hidden');
                    actionButtons.classList.remove('hidden');
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
                    <span class="align-middle">Gagal menghubungi server untuk pencarian AI.</span>
                    `;
            }
        }

        document.getElementById('btn-cari-cover-ai-gemini').addEventListener('click', () => cariCoverAI('gemini'));
        const btnCoverOr = document.getElementById('btn-cari-cover-ai-openrouter');
        if (btnCoverOr) {
            btnCoverOr.addEventListener('click', () => cariCoverAI('openrouter'));
        }
        document.getElementById('btn-cari-cover-ai-gramedia').addEventListener('click', () => cariCoverAI('gramedia'));
    </script>

    <script>
        document.getElementById('btn-terima-cover').addEventListener('click', function() {
            const preview = document.getElementById('cover_preview');
            const hiddenInput = document.getElementById('cover_buku_url');
            const status = document.getElementById('cover_buku_status');
            const actionButtons = document.getElementById('cover_action_buttons');

            if (preview.dataset.tempPath) {
                hiddenInput.value = preview.dataset.tempPath;
                status.innerHTML = `
                                    <svg class="w-6 h-6 text-green-600 inline-block mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="m8.032 12 1.984 1.984 4.96-4.96m4.55 5.272.893-.893a1.984 1.984 0 0 0 0-2.806l-.893-.893a1.984 1.984 0 0 1-.581-1.403V7.04a1.984 1.984 0 0 0-1.984-1.984h-1.262a1.983 1.983 0 0 1-1.403-.581l-.893-.893a1.984 1.984 0 0 0-2.806 0l-.893.893a1.984 1.984 0 0 1-1.403.581H7.04A1.984 1.984 0 0 0 5.055 7.04v1.262c0 .527-.209 1.031-.581 1.403l-.893.893a1.984 1.984 0 0 0 0 2.806l.893.893c.372.372.581.876.581 1.403v1.262a1.984 1.984 0 0 0 1.984 1.984h1.262c.527 0 1.031.209 1.403.581l.893.893a1.984 1.984 0 0 0 2.806 0l.893-.893a1.985 1.985 0 0 1 1.403-.581h1.262a1.984 1.984 0 0 0 1.984-1.984V15.7c0-.527.209-1.031.581-1.403Z"/>
                                    </svg>
                                    <span class="align-middle text-green-600 font-semibold">Cover berhasil diterima dan siap disimpan!</span>
                                    `;
                actionButtons.classList.add('hidden');
            }
        });

        document.getElementById('btn-tolak-cover').addEventListener('click', function() {
            const preview = document.getElementById('cover_preview');
            const hiddenInput = document.getElementById('cover_buku_url');
            const status = document.getElementById('cover_buku_status');
            const actionButtons = document.getElementById('cover_action_buttons');

            // Clear values
            preview.src = "";
            preview.classList.add('hidden');
            preview.removeAttribute('data-temp-path');
            hiddenInput.value = "";

            status.innerHTML = `
                                <svg class="w-5 h-5 text-red-500 inline-block mr-2"
                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18 17.94 6M18 18 6.06 6"/>
                                </svg>
                                <span class="align-middle text-red-500 font-semibold">Cover ditolak. Silakan cari menggunakan sumber lain.</span>
                                `;
            actionButtons.classList.add('hidden');
        });
    </script>
@endsection
