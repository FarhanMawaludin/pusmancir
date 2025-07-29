@extends('layouts.admin-app')

@section('content')
    <form method="POST" action="{{ route('admin.inventori.update', $inventori->id) }}"
        class="space-y-8 divide-y divide-gray-900/10') }}">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <h2 class="text-[28px] font-semibold text-text">Formulir Inventori</h2>
            <div class="border-gray-900/10 pb-8 p-4 bg-white border border-gray-200 rounded">
                <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">

                    {{-- Jenis Media --}}
                    <div class="sm:col-span-3">
                        <label for="id_jenis_media" class="block text-sm font-medium text-text mb-2">Jenis Media</label>
                        <select name="id_jenis_media" id="id_jenis_media"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                        border border-gray-300 placeholder:text-gray-400
                                        focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                            @foreach ($jenisMedia as $media)
                                <option value="{{ $media->id }}"
                                    {{ old('id_jenis_media', $inventori->id_jenis_media) == $media->id ? 'selected' : '' }}>
                                    {{ $media->nama_jenis_media }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_jenis_media')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jumlah Eksemplar --}}
                    <div class="sm:col-span-3">
                        <label for="jumlah_eksemplar" class="block text-sm font-medium text-text mb-2">Jumlah
                            Eksemplar</label>
                        <input type="number" name="jumlah_eksemplar" id="jumlah_eksemplar"
                            value="{{ old('jumlah_eksemplar', $inventori->jumlah_eksemplar) }}"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                        border border-gray-300 placeholder:text-gray-400
                                        focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm"
                            required>
                        @error('jumlah_eksemplar')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Pembelian --}}
                    <div class="sm:col-span-3">
                        <label for="tanggal_pembelian" class="block text-sm font-medium text-text mb-2">Tanggal
                            Pembelian</label>
                        <input type="date" name="tanggal_pembelian" id="tanggal_pembelian"
                            value="{{ old('tanggal_pembelian', $inventori->tanggal_pembelian) }}"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                        border border-gray-300 placeholder:text-gray-400
                                        focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm"
                            required>
                        @error('tanggal_pembelian')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jenis Sumber --}}
                    <div class="sm:col-span-3">
                        <label for="id_jenis_sumber" class="block text-sm font-medium text-text mb-2">Jenis Sumber</label>
                        <select name="id_jenis_sumber" id="id_jenis_sumber"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                        border border-gray-300 placeholder:text-gray-400
                                        focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                            @foreach ($jenisSumber as $jenis)
                                <option value="{{ $jenis->id }}"
                                    {{ old('id_jenis_sumber', $inventori->id_jenis_sumber) == $jenis->id ? 'selected' : '' }}>
                                    {{ $jenis->nama_sumber }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_jenis_sumber')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- Sumber --}}
                    <div class="sm:col-span-3">
                        <label for="id_sumber" class="block text-sm font-medium text-text mb-2">Sumber</label>
                        <select name="id_sumber" id="id_sumber"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                        border border-gray-300 placeholder:text-gray-400
                                        focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                            @foreach ($daftarSumber as $item)
                                <option value="{{ $item->id }}"
                                    {{ old('id_sumber', $inventori->id_sumber) == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_sumber')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kategori Buku --}}
                    <div class="sm:col-span-3">
                        <label for="id_kategori_buku" class="block text-sm font-medium text-text mb-2">Kategori Buku</label>
                        <select name="id_kategori_buku" id="id_kategori_buku"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                        border border-gray-300 placeholder:text-gray-400
                                        focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                            @foreach ($kategoriBuku as $kategori)
                                <option value="{{ $kategori->id }}"
                                    {{ old('id_kategori_buku', $inventori->id_kategori_buku) == $kategori->id ? 'selected' : '' }}>
                                    {{ $kategori->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_kategori_buku')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Judul Buku --}}
                    <div class="col-span-full">
                        <label for="judul_buku" class="block text-sm font-medium text-text mb-2">Judul Buku</label>
                        <input type="text" name="judul_buku" id="judul_buku"
                            value="{{ old('judul_buku', $inventori->judul_buku) }}"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                        border border-gray-300 placeholder:text-gray-400
                                        focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm"
                            required>
                        @error('judul_buku')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Pengarang --}}
                    <div class="col-span-full">
                        <label for="pengarang" class="block text-sm font-medium text-text mb-2">Pengarang</label>
                        <input type="text" name="pengarang" id="pengarang"
                            value="{{ old('pengarang', $inventori->pengarang) }}"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                        border border-gray-300 placeholder:text-gray-400
                                        focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                        @error('pengarang')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Sekolah --}}
                    <div class="sm:col-span-3">
                        <label for="id_sekolah" class="block text-sm font-medium text-text mb-2">Sekolah</label>
                        <select name="id_sekolah" id="id_sekolah"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                        border border-gray-300 placeholder:text-gray-400
                                        focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                            @foreach ($sekolah as $skl)
                                <option value="{{ $skl->id }}"
                                    {{ old('id_sekolah', $inventori->id_sekolah) == $skl->id ? 'selected' : '' }}>
                                    {{ $skl->nama_sekolah }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_sekolah')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Penerbit --}}
                    <div class="sm:col-span-3">
                        <label for="id_penerbit" class="block text-sm font-medium text-text mb-2">Penerbit</label>
                        <select name="id_penerbit" id="id_penerbit"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                        border border-gray-300 placeholder:text-gray-400
                                        focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                            <option value="" {{ old('id_penerbit') ? '' : 'selected' }}>-- Pilih Penerbit --</option>
                            @foreach ($penerbit as $pnb)
                                <option value="{{ $pnb->id }}"
                                    {{ old('id_penerbit') == $pnb->id ? 'selected' : '' }}>
                                    {{ $pnb->nama_penerbit }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_penerbit')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Harga Satuan --}}
                    <div class="sm:col-span-3">
                        <label for="harga_satuan" class="block text-sm font-medium text-text mb-2">Harga Satuan</label>
                        <input type="text" name="harga_satuan" id="harga_satuan"
                            value="{{ old('harga_satuan', $inventori->harga_satuan) }}"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text
             border border-gray-300 placeholder:text-gray-400
             focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm"
                            required autocomplete="off">
                        @error('harga_satuan')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Total Harga --}}
                    <div class="sm:col-span-3">
                        <label for="total_harga" class="block text-sm font-medium text-text mb-2">Total Harga</label>
                        <input type="text" name="total_harga" id="total_harga"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text
             border border-gray-300 placeholder:text-gray-400
             focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm cursor-not-allowed"
                            value="{{ old('total_harga', $inventori->total_harga) }}" readonly>
                    </div>
                </div>

            </div>

            <div class="mt-6 flex items-center justify-start gap-x-4">
                <button type="button" onclick="location.href='{{ route('admin.inventori.index') }}'"
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const jumlah = document.getElementById('jumlah_eksemplar');
            const harga = document.getElementById('harga_satuan');
            const total = document.getElementById('total_harga');

            // Format angka ke format Rupiah ID (titik ribuan, koma desimal)
            function formatRupiah(value) {
                if (isNaN(value) || value === 0) return '0';
                let val = Number(value).toFixed(2);
                let parts = val.split('.');
                let intPart = parts[0];
                let decPart = parts[1];
                intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                if (decPart === '00') return intPart;
                return intPart + ',' + decPart;
            }

            // Parse string format rupiah ke angka float
            function parseRupiah(rupiahString) {
                if (!rupiahString) return 0;
                let numberString = rupiahString.replace(/\./g, '').replace(',', '.');
                return parseFloat(numberString) || 0;
            }

            // Format input harga_satuan saat ketik
            function formatInputRupiah(e) {
                let cursorPos = e.target.selectionStart;
                let originalLength = e.target.value.length;

                let value = e.target.value.replace(/[^0-9,]/g, '');
                let parts = value.split(',');
                if (parts.length > 2) {
                    value = parts[0] + ',' + parts[1];
                }
                let intPart = parts[0];
                let decPart = parts[1] || '';
                if (decPart.length > 2) decPart = decPart.slice(0, 2);
                intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                e.target.value = decPart ? intPart + ',' + decPart : intPart;

                let newLength = e.target.value.length;
                cursorPos = cursorPos + (newLength - originalLength);
                e.target.setSelectionRange(cursorPos, cursorPos);
            }

            // Hitung total harga
            function hitungTotal() {
                let jml = parseFloat(jumlah.value) || 0;
                let hrg = parseRupiah(harga.value);
                let totalHarga = jml * hrg;
                total.value = formatRupiah(totalHarga);
            }

            harga.addEventListener('input', function(e) {
                formatInputRupiah(e);
                hitungTotal();
            });

            jumlah.addEventListener('input', hitungTotal);

            // Hitung total di awal
            hitungTotal();
        });
    </script>
@endsection
