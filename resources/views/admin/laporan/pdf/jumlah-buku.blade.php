<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Jumlah Buku Dipinjam - {{ $periode }}</title>
    @include('admin.laporan.pdf._style')
</head>
<body>
    @php $f = fn($n) => number_format($n, 0, ',', '.'); @endphp

    <div class="footer">
        Laporan Jumlah Buku Dipinjam &mdash; {{ $periode }} &middot; Dicetak {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}
        <span class="kanan">Halaman <span class="pagenum"></span></span>
    </div>

    @include('admin.laporan.pdf._kop')

    <div class="judul-laporan">Laporan Jumlah Buku yang Dipinjam</div>
    <div class="sub-judul">Koleksi Non Paket &mdash; Periode: <strong>{{ $periode }}</strong></div>
    <div class="sub-kecil">Dihitung berdasarkan tanggal pinjam. Judul = jumlah judul berbeda; Eksemplar = jumlah fisik buku.</div>

    <h3 class="bagian">A. Ringkasan</h3>
    <table class="data">
        <thead>
            <tr>
                <th style="width:28px">No</th>
                <th>Uraian</th>
                <th style="width:90px">Jumlah Judul</th>
                <th style="width:90px">Jumlah Eksemplar</th>
            </tr>
        </thead>
        <tbody>
            <tr><td class="c">1</td><td>Buku yang dipinjam (total)</td><td class="r">{{ $f($ringkasan['judul_total']) }}</td><td class="r">{{ $f($ringkasan['eks_total']) }}</td></tr>
            <tr><td class="c">2</td><td>Buku yang masih/sedang dipinjam</td><td class="r">{{ $f($ringkasan['judul_aktif']) }}</td><td class="r">{{ $f($ringkasan['eks_aktif']) }}</td></tr>
            <tr><td class="c">3</td><td>Buku yang sudah selesai dipinjam (dikembalikan)</td><td class="r">{{ $f($ringkasan['judul_selesai']) }}</td><td class="r">{{ $f($ringkasan['eks_selesai']) }}</td></tr>
            <tr><td class="c">4</td><td>Buku terlambat dikembalikan (per hari ini)</td><td class="r muted">&ndash;</td><td class="r">{{ $f($ringkasan['eks_terlambat']) }}</td></tr>
            <tr><td class="c">5</td><td>Jumlah transaksi peminjaman</td><td class="r muted" colspan="2" style="text-align:right">{{ $f($ringkasan['transaksi']) }} transaksi</td></tr>
            <tr><td class="c">6</td><td>Jumlah peminjam (anggota berbeda)</td><td class="r muted" colspan="2" style="text-align:right">{{ $f($ringkasan['peminjam']) }} orang</td></tr>
        </tbody>
    </table>

    <h3 class="bagian">B. Rekap per {{ $granularitas }}</h3>
    <table class="data">
        <thead>
            <tr>
                <th rowspan="2" style="width:28px">No</th>
                <th rowspan="2">{{ $granularitas }}</th>
                <th colspan="3">Judul</th>
                <th colspan="3">Eksemplar</th>
            </tr>
            <tr>
                <th>Dipinjam</th><th>Sedang</th><th>Selesai</th>
                <th>Dipinjam</th><th>Sedang</th><th>Selesai</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rekap as $i => $r)
                <tr>
                    <td class="c">{{ $i + 1 }}</td>
                    <td>{{ $r['label'] }}{{ $granularitas === 'Tanggal' ? ' ' . \App\Services\StatistikPeminjaman::BULAN[$bulan] . ' ' . $tahun : '' }}</td>
                    <td class="r">{{ $f($r['judul_total']) }}</td>
                    <td class="r">{{ $f($r['judul_aktif']) }}</td>
                    <td class="r">{{ $f($r['judul_selesai']) }}</td>
                    <td class="r">{{ $f($r['eks_total']) }}</td>
                    <td class="r">{{ $f($r['eks_aktif']) }}</td>
                    <td class="r">{{ $f($r['eks_selesai']) }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="c muted">Tidak ada data peminjaman.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="c">JUMLAH</td>
                <td class="r">{{ $f($ringkasan['judul_total']) }}</td>
                <td class="r">{{ $f($ringkasan['judul_aktif']) }}</td>
                <td class="r">{{ $f($ringkasan['judul_selesai']) }}</td>
                <td class="r">{{ $f($ringkasan['eks_total']) }}</td>
                <td class="r">{{ $f($ringkasan['eks_aktif']) }}</td>
                <td class="r">{{ $f($ringkasan['eks_selesai']) }}</td>
            </tr>
        </tfoot>
    </table>
    <div class="muted" style="font-size:7.5pt; margin-top:2px;">* Jumlah judul dihitung sebagai judul berbeda dalam periode, sehingga bisa lebih kecil dari penjumlahan per baris.</div>

    <table style="width:100%; border-collapse:collapse; margin-top:4px;">
        <tr>
            <td style="width:50%; vertical-align:top; padding-right:6px;">
                <h3 class="bagian">C. Per Kategori Buku</h3>
                <table class="data">
                    <thead><tr><th style="width:22px">No</th><th>Kategori</th><th>Judul</th><th>Eks.</th></tr></thead>
                    <tbody>
                        @forelse ($perKategori as $i => $k)
                            <tr><td class="c">{{ $i + 1 }}</td><td>{{ $k->kategori }}</td><td class="r">{{ $f($k->judul) }}</td><td class="r">{{ $f($k->eksemplar) }}</td></tr>
                        @empty
                            <tr><td colspan="4" class="c muted">-</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
            <td style="width:50%; vertical-align:top; padding-left:6px;">
                <h3 class="bagian">D. Per Kelas / Guru</h3>
                <table class="data">
                    <thead><tr><th style="width:22px">No</th><th>Kelompok</th><th>Peminjam</th><th>Judul</th><th>Eks.</th></tr></thead>
                    <tbody>
                        @forelse ($perKelompok as $i => $k)
                            <tr><td class="c">{{ $i + 1 }}</td><td>{{ $k->kelompok }}</td><td class="r">{{ $f($k->peminjam) }}</td><td class="r">{{ $f($k->judul) }}</td><td class="r">{{ $f($k->eksemplar) }}</td></tr>
                        @empty
                            <tr><td colspan="5" class="c muted">-</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <h3 class="bagian">E. Daftar Judul Buku yang Dipinjam</h3>
    <table class="data">
        <thead>
            <tr>
                <th style="width:24px">No</th>
                <th>Judul Buku</th>
                <th style="width:110px">Pengarang</th>
                <th style="width:70px">Kategori</th>
                <th style="width:48px">Jumlah Dipinjam (eks)</th>
                <th style="width:42px">Sedang</th>
                <th style="width:42px">Selesai</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($perJudul as $i => $j)
                <tr>
                    <td class="c">{{ $i + 1 }}</td>
                    <td>{{ $j->judul_buku }}</td>
                    <td>{{ $j->pengarang }}</td>
                    <td>{{ $j->kategori }}</td>
                    <td class="r">{{ $f($j->kali_dipinjam) }}</td>
                    <td class="r">{{ $f($j->sedang_dipinjam) }}</td>
                    <td class="r">{{ $f($j->sudah_kembali) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="c muted">Tidak ada data peminjaman.</td></tr>
            @endforelse
        </tbody>
        @if ($perJudul->count())
            <tfoot>
                <tr>
                    <td colspan="4" class="c">JUMLAH: {{ $f($perJudul->count()) }} JUDUL</td>
                    <td class="r">{{ $f($perJudul->sum('kali_dipinjam')) }}</td>
                    <td class="r">{{ $f($perJudul->sum('sedang_dipinjam')) }}</td>
                    <td class="r">{{ $f($perJudul->sum('sudah_kembali')) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    @include('admin.laporan.pdf._ttd')
</body>
</html>
