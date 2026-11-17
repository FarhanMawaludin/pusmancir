@extends('layouts.admin-app')

@section('content')
    @php
        $f = fn($n) => number_format($n, 0, ',', '.');
        $filter = request()->only(['tahun', 'induk_dari', 'induk_sampai', 'asal', 'search']);
        $inputClass = 'border border-gray-300 rounded px-3 py-2 text-sm bg-white';
        $bahasaLabel = ['ind' => 'Ind', 'ing' => 'Ingg', 'lain' => 'Lain'];
    @endphp

    <div class="flex flex-wrap justify-between items-start gap-3 mb-4">
        <div>
            <h1 class="text-2xl font-bold text-text">Buku Induk / Inventaris</h1>
            <p class="text-sm text-gray-500">Dibuat otomatis dari koleksi non paket (inventori &amp; eksemplar) sesuai format BUKU-INDUK sekolah.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" id="btnImportInduk" class="px-4 py-2 rounded text-sm text-white" style="background-color:#16a34a">Import Buku Induk</button>
            <a href="{{ route('admin.laporan.buku-induk.excel', $filter) }}" class="px-4 py-2 rounded text-sm text-white" style="background-color:#0f766e">Export Excel</a>
            @if (count($daftarBagian) <= 1)
                <a href="{{ route('admin.laporan.buku-induk.pdf', $filter) }}" target="_blank" class="px-4 py-2 rounded text-sm text-white" style="background-color:#dc2626">Cetak PDF (A4)</a>
                <a href="{{ route('admin.laporan.buku-induk.pdf', $filter + ['download' => 1]) }}" class="px-4 py-2 rounded text-sm text-white" style="background-color:#991b1b">Unduh PDF</a>
            @endif
        </div>
    </div>

    @if (count($daftarBagian) > 1)
        <div class="p-4 mb-5 rounded border" style="background-color:#fef2f2; border-color:#fecaca;">
            <div class="font-semibold text-gray-800 mb-1">Cetak / Unduh PDF Buku Induk (A4 landscape)</div>
            <p class="text-xs text-gray-600 mb-3">
                Agar ringan diproses, PDF dibagi per {{ \App\Services\BukuInduk::PDF_BARIS_PER_BAGIAN }} eksemplar (±20 halaman).
                Baris <strong>JUMLAH pindahan</strong> tetap bersambung antar bagian, jadi seluruh bagian dapat dicetak berurutan menjadi satu buku induk.
                Gunakan filter di bawah untuk mencetak rentang tertentu.
            </p>
            <div class="flex flex-wrap gap-2">
                @foreach ($daftarBagian as $b)
                    <div class="flex items-center rounded border border-gray-300 bg-white text-sm overflow-hidden">
                        <span class="px-3 py-1.5 text-gray-700">Bagian {{ $b['bagian'] }} <span class="text-gray-400">(No. {{ $b['dari'] }}–{{ $b['sampai'] }})</span></span>
                        <a href="{{ route('admin.laporan.buku-induk.pdf', $filter + ['bagian' => $b['bagian']]) }}" target="_blank"
                            class="px-2 py-1.5 text-white" style="background-color:#dc2626" title="Cetak">Cetak</a>
                        <a href="{{ route('admin.laporan.buku-induk.pdf', $filter + ['bagian' => $b['bagian'], 'download' => 1]) }}"
                            class="px-2 py-1.5 text-white" style="background-color:#991b1b" title="Unduh">Unduh</a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <form method="GET" action="{{ route('admin.laporan.buku-induk') }}" class="p-4 bg-white border border-gray-200 rounded mb-5 flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1" for="tahun">Tahun pengadaan</label>
            <select id="tahun" name="tahun" class="{{ $inputClass }}">
                <option value="">Semua</option>
                @foreach ($daftarTahun as $t)
                    <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1" for="induk_dari">No. induk dari</label>
            <input type="number" min="1" id="induk_dari" name="induk_dari" value="{{ request('induk_dari') }}" class="{{ $inputClass }} w-28">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1" for="induk_sampai">sampai</label>
            <input type="number" min="1" id="induk_sampai" name="induk_sampai" value="{{ request('induk_sampai') }}" class="{{ $inputClass }} w-28">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1" for="asal">Asal</label>
            <select id="asal" name="asal" class="{{ $inputClass }}">
                <option value="">Semua</option>
                <option value="beli" {{ request('asal') === 'beli' ? 'selected' : '' }}>Pembelian</option>
                <option value="hadiah" {{ request('asal') === 'hadiah' ? 'selected' : '' }}>Hadiah / Hibah</option>
            </select>
        </div>
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1" for="search">Cari</label>
            <input type="search" id="search" name="search" value="{{ request('search') }}" placeholder="Judul, pengarang, no. induk / inventaris" class="{{ $inputClass }} w-full">
        </div>
        <button type="submit" class="px-4 py-2 rounded text-sm text-white" style="background-color:#1d4ed8">Tampilkan</button>
        <a href="{{ route('admin.laporan.buku-induk') }}" class="px-4 py-2 rounded text-sm bg-gray-200 text-gray-700">Reset</a>
    </form>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
        @foreach ([
            ['Judul', $ringkasan['jdl']],
            ['Eksemplar', $ringkasan['eks']],
            ['Judul dibeli', $ringkasan['beli_jdl']],
            ['Eksemplar dibeli', $ringkasan['beli_eks']],
            ['Judul hadiah', $ringkasan['hadiah_jdl']],
            ['Eksemplar hadiah', $ringkasan['hadiah_eks']],
        ] as [$label, $nilai])
            <div class="p-4 bg-white border border-gray-200 rounded">
                <div class="text-2xl font-bold text-gray-900">{{ $f($nilai) }}</div>
                <div class="text-sm text-gray-600">{{ $label }}</div>
            </div>
        @endforeach
    </div>
    <p class="text-sm text-gray-600 mb-3">Nilai koleksi (total harga): <strong>Rp {{ $f($ringkasan['harga']) }}</strong></p>

    <div class="overflow-x-auto rounded border border-gray-200 bg-white">
        <table class="min-w-full text-xs text-left text-text">
            <thead class="uppercase bg-gray-100 text-text text-center">
                <tr>
                    <th rowspan="2" class="px-2 py-2 border-r border-gray-200">No.</th>
                    <th rowspan="2" class="px-2 py-2 border-r border-gray-200">Tgl</th>
                    <th rowspan="2" class="px-2 py-2 border-r border-gray-200">No Inventaris</th>
                    <th rowspan="2" class="px-2 py-2 border-r border-gray-200 text-left">Pengarang</th>
                    <th rowspan="2" class="px-2 py-2 border-r border-gray-200 text-left">Judul</th>
                    <th rowspan="2" class="px-2 py-2 border-r border-gray-200 text-left">Penerbit</th>
                    <th colspan="2" class="px-2 py-1 border-r border-gray-200">Jumlah</th>
                    <th colspan="2" class="px-2 py-1 border-r border-gray-200">Asal</th>
                    <th rowspan="2" class="px-2 py-2 border-r border-gray-200">Klas.</th>
                    <th rowspan="2" class="px-2 py-2 border-r border-gray-200">Call Number</th>
                    <th rowspan="2" class="px-2 py-2 border-r border-gray-200">Bahasa</th>
                    <th rowspan="2" class="px-2 py-2 border-r border-gray-200">Harga Rp.</th>
                    <th rowspan="2" class="px-2 py-2">Ket</th>
                </tr>
                <tr>
                    <th class="px-2 py-1 border-r border-gray-200">Jdl</th>
                    <th class="px-2 py-1 border-r border-gray-200">Eks</th>
                    <th class="px-2 py-1 border-r border-gray-200">Beli</th>
                    <th class="px-2 py-1 border-r border-gray-200">Hadiah</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-2 py-2 text-center">{{ $row->no }}</td>
                        <td class="px-2 py-2 text-center whitespace-nowrap">{{ $row->tgl }}</td>
                        <td class="px-2 py-2 text-center whitespace-nowrap">{{ $row->no_inventori }}</td>
                        <td class="px-2 py-2">{{ $row->pengarang }}</td>
                        <td class="px-2 py-2 font-medium">{{ $row->judul }}</td>
                        <td class="px-2 py-2">{{ $row->penerbit }}</td>
                        <td class="px-2 py-2 text-center">{{ $row->jdl ?: '' }}</td>
                        <td class="px-2 py-2 text-center">{{ $row->eks }}</td>
                        <td class="px-2 py-2 text-center">{{ $row->beli_eks ? '✓' : '' }}</td>
                        <td class="px-2 py-2 text-center">{{ $row->hadiah_eks ? '✓' : '' }}</td>
                        <td class="px-2 py-2 text-center">{{ $row->klas !== null ? $row->klas . '00' : '-' }}</td>
                        <td class="px-2 py-2 whitespace-nowrap">{{ $row->call_number ?: '-' }}</td>
                        <td class="px-2 py-2 text-center">{{ $bahasaLabel[$row->bahasa] }}</td>
                        <td class="px-2 py-2 text-right whitespace-nowrap">{{ \App\Services\BukuInduk::formatHarga($row->harga) }}</td>
                        <td class="px-2 py-2">{{ $row->ket }}</td>
                    </tr>
                @empty
                    <tr><td colspan="15" class="px-4 py-6 text-center text-gray-500">Belum ada data koleksi non paket. Tambahkan melalui menu Inventori atau Import Buku Induk.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $paginator->links('pagination::tailwind') }}
        </div>
    </div>

    <p class="text-xs text-gray-400 mt-3">
        Catatan: kolom Klasifikasi diambil dari Kode DDC / No. Panggil di menu Katalog. Jika No. Panggil kosong, Call Number dibentuk otomatis
        dari kode DDC + 3 huruf nama pengarang + 1 huruf judul. Bahasa diambil dari data inventori (default Indonesia).
    </p>

    {{-- Modal Import --}}
    <div id="modalImportInduk" class="fixed inset-0 z-50 hidden overflow-y-auto" style="background-color: rgba(0,0,0,.5);">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded shadow-xl w-full max-w-lg">
                <div class="flex justify-between items-center p-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Import Buku Induk (Excel)</h3>
                    <button type="button" onclick="tutupImportInduk()" class="text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>
                </div>
                <form action="{{ route('admin.laporan.buku-induk.import') }}" method="POST" enctype="multipart/form-data" class="p-4 space-y-4" id="formImportInduk">
                    @csrf
                    <p class="text-xs text-gray-600">
                        Upload file dengan format <strong>BUKU-INDUK</strong> (.xls / .xlsx) &mdash; kolom No, TGL, No Inventaris, Pengarang,
                        Judul, Penerbit, Jumlah, Asal (Beli/Hadiah), Klasifikasi 0&ndash;9, Call Number, Bahasa, Harga, Ket.
                        Baris kop, header berulang dan baris JUMLAH dilewati otomatis. Setiap baris menjadi 1 eksemplar; eksemplar dengan judul,
                        pengarang dan tanggal yang sama digabung menjadi 1 judul (inventori + katalog).
                    </p>
                    <div class="flex items-center justify-between bg-gray-100 p-3 rounded">
                        <span class="text-sm font-medium text-gray-700">Template format Buku Induk:</span>
                        <a href="{{ route('admin.laporan.buku-induk.template') }}" class="px-3 py-1.5 text-sm text-white rounded" style="background-color:#2563eb">Download</a>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-900" for="file_buku_induk">File Excel</label>
                        <input type="file" name="file_buku_induk" id="file_buku_induk" accept=".xls,.xlsx" required
                            class="block w-full text-sm border border-gray-300 rounded p-2">
                    </div>
                    <div>
                        <span class="block mb-1 text-sm font-medium text-gray-900">Nomor induk</span>
                        <label class="flex items-start gap-2 text-sm mb-1">
                            <input type="radio" name="mode_nomor" value="asli" checked class="mt-1">
                            <span>Gunakan nomor dari file <span class="text-gray-500">(baris dengan No. Induk / No. Inventaris yang sudah terdaftar akan dilewati)</span></span>
                        </label>
                        <label class="flex items-start gap-2 text-sm">
                            <input type="radio" name="mode_nomor" value="lanjut" class="mt-1">
                            <span>Buat nomor induk baru melanjutkan nomor terakhir <span class="text-gray-500">(No. Inventaris tetap dari file)</span></span>
                        </label>
                    </div>
                    <div class="flex justify-end gap-2 pt-4 border-t">
                        <button type="button" onclick="tutupImportInduk()" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 text-sm">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded text-white text-sm" style="background-color:#16a34a" id="btnSubmitImport">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('btnImportInduk').addEventListener('click', () => {
            document.getElementById('modalImportInduk').classList.remove('hidden');
        });

        function tutupImportInduk() {
            document.getElementById('modalImportInduk').classList.add('hidden');
        }

        document.getElementById('formImportInduk').addEventListener('submit', () => {
            const btn = document.getElementById('btnSubmitImport');
            btn.disabled = true;
            btn.textContent = 'Memproses... mohon tunggu';
        });
    </script>
@endsection
