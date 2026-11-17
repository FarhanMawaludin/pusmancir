<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Induk / Inventaris Perpustakaan</title>
    <style>
        @page { margin: 0.9cm 0.8cm 1.2cm 0.8cm; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 7pt; color: #111; margin: 0; }
        .halaman { page-break-after: always; }
        .halaman.akhir { page-break-after: auto; }
        .judul { text-align: center; font-size: 11pt; font-weight: bold; letter-spacing: 1px; margin: 2px 0 1px; font-family: 'Times New Roman', Times, serif; }
        .sub { text-align: center; font-size: 7.5pt; color: #444; margin-bottom: 5px; }
        .lampiran { font-size: 8pt; margin: 0 0 2px; font-family: 'Times New Roman', Times, serif; }
        .lanjutan { font-size: 7.5pt; color: #444; margin: 0 0 3px; }
        table.induk { width: 100%; border-collapse: collapse; }
        table.induk th, table.induk td { border: 0.6px solid #222; padding: 1.5px 1.5px; }
        table.induk th { background: #e5e7eb; font-size: 6pt; font-weight: bold; text-align: center; vertical-align: middle; }
        table.induk td { font-size: 6.6pt; vertical-align: top; line-height: 1.12; }
        table.induk tr { page-break-inside: avoid; }
        table.induk tr.jumlah td { font-weight: bold; background: #f3f4f6; font-size: 6.8pt; }
        .c { text-align: center; }
        .r { text-align: right; }
        .ck { font-family: 'DejaVu Sans', sans-serif; text-align: center; font-size: 6.2pt; }
        .footer { position: fixed; bottom: -0.75cm; left: 0; right: 0; font-size: 6.5pt; color: #777; }
        .footer .kanan { float: right; }
        .pagenum:after { content: counter(page) " / " counter(pages); }
    </style>
</head>
<body>
    @php
        $f = fn($n) => $n ? number_format($n, 0, ',', '.') : '';
        $klasKolom = range(0, 9);
        $bahasaKolom = ['ind', 'ing', 'lain'];
        $jumlahHalaman = count($halaman);
        // Lebar isi 28 kolom (mm). Total isi ±239mm + padding/border ±30mm = ±269mm,
        // muat di A4 landscape (281mm setelah margin). dompdf mengabaikan <colgroup>,
        // jadi lebar dipasang langsung di setiap sel.
        $L = array_merge(
            [8, 14, 20, 26, 40, 22],          // 0-5  No, Tgl, No Inventaris, Pengarang, Judul, Penerbit
            array_fill(0, 6, 5.5),            // 6-11 Jumlah Jdl/Eks, Beli Jdl/Eks, Hadiah Jdl/Eks
            array_fill(0, 10, 2.8),           // 12-21 Klasifikasi 0-9
            [16],                             // 22   Call Number
            array_fill(0, 3, 3.5),            // 23-25 Bahasa Ind/Ingg/Lain
            [13, 8]                           // 26-27 Harga, Ket
        );
        $w = fn($i) => 'width:' . $L[$i] . 'mm;';
        $awalLanjutan = $awalLanjutan ?? false; // true jika file ini adalah bagian lanjutan
    @endphp

    <div class="footer">
        Buku Induk Perpustakaan UPT SMA Negeri 1 Ciruas &middot; {{ $keterangan }} &middot; Dicetak {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}
        <span class="kanan">Halaman <span class="pagenum"></span></span>
    </div>

    @foreach ($halaman as $idx => $hal)
        <div class="halaman {{ $idx === $jumlahHalaman - 1 ? 'akhir' : '' }}">
            @if ($idx === 0 && !$awalLanjutan)
                @include('admin.laporan.pdf._kop')
                <div class="lampiran">Lampiran</div>
                <div class="judul">BUKU INVENTARIS/INDUK</div>
                <div class="sub">{{ $keterangan }} &mdash; {{ number_format($totalRows, 0, ',', '.') }} eksemplar</div>
            @elseif ($idx === 0)
                <div class="lanjutan"><strong>BUKU INVENTARIS/INDUK</strong> (lanjutan) &mdash; {{ $keterangan }}</div>
            @endif

            <table class="induk">
                <thead>
                    <tr>
                        <th rowspan="3" style="{{ $w(0) }}">No.</th>
                        <th rowspan="3" style="{{ $w(1) }}">TGL</th>
                        <th rowspan="3" style="{{ $w(2) }}">No Inventaris</th>
                        <th rowspan="3" style="{{ $w(3) }}">Pengarang</th>
                        <th rowspan="3" style="{{ $w(4) }}">Judul</th>
                        <th rowspan="3" style="{{ $w(5) }}">Penerbit</th>
                        <th colspan="2">Jumlah</th>
                        <th colspan="4">Asal</th>
                        <th colspan="10">Klasifikasi</th>
                        <th rowspan="3" style="{{ $w(22) }}">Call Number</th>
                        <th colspan="3" rowspan="2">Bahasa</th>
                        <th rowspan="3" style="{{ $w(26) }}">Harga Rp.</th>
                        <th rowspan="3" style="{{ $w(27) }}">Ket</th>
                    </tr>
                    <tr>
                        <th rowspan="2" style="{{ $w(6) }}">Jdl</th>
                        <th rowspan="2" style="{{ $w(7) }}">Eks.</th>
                        <th colspan="2">Beli</th>
                        <th colspan="2">Hadiah</th>
                        @foreach ($klasKolom as $k)
                            <th rowspan="2" style="{{ $w(12 + $k) }}">{{ $k }}</th>
                        @endforeach
                    </tr>
                    <tr>
                        <th style="{{ $w(8) }}">Jdl</th>
                        <th style="{{ $w(9) }}">Eks.</th>
                        <th style="{{ $w(10) }}">Jdl</th>
                        <th style="{{ $w(11) }}">Eks.</th>
                        <th style="{{ $w(23) }}">Ind</th>
                        <th style="{{ $w(24) }}">Ingg</th>
                        <th style="{{ $w(25) }}">Lain</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($hal['pindahan'])
                        <tr class="jumlah">
                            <td colspan="6" class="c">JUMLAH (pindahan halaman sebelumnya)</td>
                            <td class="c">{{ $f($hal['pindahan']['jdl']) }}</td>
                            <td class="c">{{ $f($hal['pindahan']['eks']) }}</td>
                            <td class="c">{{ $f($hal['pindahan']['beli_jdl']) }}</td>
                            <td class="c">{{ $f($hal['pindahan']['beli_eks']) }}</td>
                            <td class="c">{{ $f($hal['pindahan']['hadiah_jdl']) }}</td>
                            <td class="c">{{ $f($hal['pindahan']['hadiah_eks']) }}</td>
                            <td colspan="14"></td>
                            <td class="r">{{ $f($hal['pindahan']['harga']) }}</td>
                            <td></td>
                        </tr>
                    @endif

                    @forelse ($hal['rows'] as $row)
                        <tr>
                            <td class="c" style="{{ $w(0) }}">{{ $row->no }}</td>
                            <td class="c" style="{{ $w(1) }}">{{ $row->tgl }}</td>
                            <td class="c" style="{{ $w(2) }}">{{ $row->no_inventori }}</td>
                            <td style="{{ $w(3) }}">{{ \Illuminate\Support\Str::limit($row->pengarang, 40) }}</td>
                            <td style="{{ $w(4) }}">{{ \Illuminate\Support\Str::limit($row->judul, 64) }}</td>
                            <td style="{{ $w(5) }}">{{ \Illuminate\Support\Str::limit($row->penerbit, 32) }}</td>
                            <td class="c" style="{{ $w(6) }}">{{ $row->jdl ?: '' }}</td>
                            <td class="c" style="{{ $w(7) }}">{{ $row->eks }}</td>
                            <td class="c" style="{{ $w(8) }}">{{ $row->beli_jdl ?: '' }}</td>
                            <td class="c" style="{{ $w(9) }}">{{ $row->beli_eks ?: '' }}</td>
                            <td class="c" style="{{ $w(10) }}">{{ $row->hadiah_jdl ?: '' }}</td>
                            <td class="c" style="{{ $w(11) }}">{{ $row->hadiah_eks ?: '' }}</td>
                            @foreach ($klasKolom as $k)
                                <td class="ck" style="{{ $w(12 + $k) }}">{{ $row->klas === $k ? '√' : '' }}</td>
                            @endforeach
                            <td style="{{ $w(22) }}">{{ $row->call_number }}</td>
                            @foreach ($bahasaKolom as $i => $b)
                                <td class="ck" style="{{ $w(23 + $i) }}">{{ $row->bahasa === $b ? '√' : '' }}</td>
                            @endforeach
                            <td class="r" style="{{ $w(26) }}">{{ \App\Services\BukuInduk::formatHarga($row->harga) }}</td>
                            <td style="{{ $w(27) }}">{{ $row->ket }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="28" class="c" style="padding:12px">Belum ada data koleksi.</td></tr>
                    @endforelse

                    <tr class="jumlah">
                        <td colspan="6" class="c">JUMLAH</td>
                        <td class="c">{{ $f($hal['jumlah']['jdl']) }}</td>
                        <td class="c">{{ $f($hal['jumlah']['eks']) }}</td>
                        <td class="c">{{ $f($hal['jumlah']['beli_jdl']) }}</td>
                        <td class="c">{{ $f($hal['jumlah']['beli_eks']) }}</td>
                        <td class="c">{{ $f($hal['jumlah']['hadiah_jdl']) }}</td>
                        <td class="c">{{ $f($hal['jumlah']['hadiah_eks']) }}</td>
                        <td colspan="14"></td>
                        <td class="r">{{ $f($hal['jumlah']['harga']) }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            @if ($idx === $jumlahHalaman - 1 && empty($tanpaTtd))
                @include('admin.laporan.pdf._ttd')
            @endif
        </div>
    @endforeach
</body>
</html>
