<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Peminjam Buku</title>
    @include('admin.laporan.pdf._style')
    <style>
        tr.kepala td { background: #dbeafe; border: 1px solid #333; font-size: 8.5pt; font-weight: bold; }
        .st { font-size: 7.5pt; font-weight: bold; }
        .st-sedang { color: #1d4ed8; }
        .st-terlambat { color: #b91c1c; }
        .st-selesai { color: #166534; }
    </style>
</head>
<body>
    @php
        $f = fn($n) => number_format($n, 0, ',', '.');
        $tgl = fn($t) => $t ? \Carbon\Carbon::parse($t)->format('d-m-Y') : '-';
        $label = ['sedang' => 'Dipinjam', 'terlambat' => 'Terlambat', 'selesai' => 'Selesai'];
    @endphp

    <div class="footer">
        Laporan Data Peminjam Buku &mdash; {{ $keterangan['status'] }} &middot; Dicetak {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}
        <span class="kanan">Halaman <span class="pagenum"></span></span>
    </div>

    @include('admin.laporan.pdf._kop')

    <div class="judul-laporan">Laporan Data Peminjam Buku</div>
    <div class="sub-judul">{{ $keterangan['status'] }} &mdash; Periode Pinjam: <strong>{{ $keterangan['periode'] }}</strong></div>
    <div class="sub-kecil">
        Koleksi Non Paket{{ count($keterangan['lain']) ? ' · ' . implode(' · ', $keterangan['lain']) : '' }}
    </div>

    <table class="data" style="margin-bottom:10px;">
        <tr>
            <th>Jumlah Peminjam</th>
            <th>Jumlah Judul</th>
            <th>Jumlah Eksemplar</th>
            <th>Sedang Dipinjam</th>
            <th>Terlambat</th>
            <th>Selesai Dipinjam</th>
        </tr>
        <tr>
            <td class="c">{{ $f($ringkasan['peminjam']) }} orang</td>
            <td class="c">{{ $f($ringkasan['judul']) }}</td>
            <td class="c">{{ $f($ringkasan['eksemplar']) }}</td>
            <td class="c">{{ $f($ringkasan['sedang']) }}</td>
            <td class="c">{{ $f($ringkasan['terlambat']) }}</td>
            <td class="c">{{ $f($ringkasan['selesai']) }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width:22px">No</th>
                <th>Judul Buku</th>
                <th style="width:62px">No. Induk</th>
                <th style="width:56px">Tgl Pinjam</th>
                <th style="width:56px">Batas Kembali</th>
                <th style="width:56px">Tgl Kembali</th>
                <th style="width:52px">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $noPeminjam = 0; @endphp
            @forelse ($grup as $anggotaId => $items)
                @php
                    $noPeminjam++;
                    $a = $items->first()->peminjaman->anggota;
                    $guru = $a && $a->isGuru();
                @endphp
                <tr class="kepala">
                    <td class="c">{{ $noPeminjam }}</td>
                    <td colspan="6">
                        {{ $a->user->name ?? '-' }}
                        &nbsp;&middot;&nbsp; {{ $a->label_identitas ?? 'NISN' }}: {{ $a->nisn ?? '-' }}
                        &nbsp;&middot;&nbsp; {{ $guru ? 'Guru' : 'Kelas ' . ($a->kelas->nama_kelas ?? '-') }}
                        <span style="float:right; font-weight:normal;">{{ $items->pluck('eksemplar.id_inventori')->unique()->count() }} judul / {{ $items->count() }} eksemplar</span>
                    </td>
                </tr>
                @foreach ($items as $i => $d)
                    @php $st = \App\Http\Controllers\LaporanController::statusDetail($d); @endphp
                    <tr>
                        <td class="c muted">{{ $noPeminjam }}.{{ $i + 1 }}</td>
                        <td>
                            {{ $d->eksemplar->inventori->judul_buku ?? '-' }}
                            @if (!empty($d->eksemplar->inventori->pengarang))
                                <span class="muted">&mdash; {{ $d->eksemplar->inventori->pengarang }}</span>
                            @endif
                        </td>
                        <td class="c">{{ $d->eksemplar->no_induk ?? '-' }}</td>
                        <td class="c">{{ $tgl($d->peminjaman->tanggal_pinjam) }}</td>
                        <td class="c">{{ $tgl($d->peminjaman->tanggal_kembali) }}</td>
                        <td class="c">{{ $tgl($d->tanggal_kembali_asli) }}</td>
                        <td class="c"><span class="st st-{{ $st }}">{{ $label[$st] }}</span></td>
                    </tr>
                @endforeach
            @empty
                <tr><td colspan="7" class="c muted" style="padding:14px">Tidak ada data peminjaman ditemukan.</td></tr>
            @endforelse
        </tbody>
    </table>

    @include('admin.laporan.pdf._ttd')
</body>
</html>
