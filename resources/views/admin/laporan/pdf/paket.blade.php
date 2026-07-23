<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman Paket</title>
    <style>
        @page { margin: 1.5cm 1.5cm 1.5cm 1.5cm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; color: #1a1a1a; margin: 0; padding: 0; }
        .kop-container { text-align: center; border-bottom: 3px double #1a1a1a; padding-bottom: 8px; margin-bottom: 15px; position: relative; }
        .kop-logo { position: absolute; left: 0; top: 0; width: 70px; height: 70px; }
        .kop-text { margin: 0; }
        .kop-text .instansi { font-size: 12pt; font-weight: bold; margin: 0; letter-spacing: 1px; }
        .kop-text .sekolah { font-size: 16pt; font-weight: bold; margin: 2px 0; letter-spacing: 2px; }
        .kop-text .alamat { font-size: 9pt; margin: 2px 0; }
        .kop-text .kontak { font-size: 9pt; margin: 0; }
        .judul-laporan { text-align: center; margin: 15px 0 5px 0; font-size: 13pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .sub-judul { text-align: center; margin: 0 0 15px 0; font-size: 10pt; color: #555; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th { background-color: #2c3e7a; color: #fff; padding: 7px 5px; font-size: 9pt; text-align: center; border: 1px solid #1a1a1a; font-weight: bold; }
        table.data td { padding: 5px 5px; font-size: 9pt; border: 1px solid #444; vertical-align: top; }
        table.data tr:nth-child(even) { background-color: #f5f5f5; }
        table.data td.center { text-align: center; }
        .status-badge { padding: 2px 8px; border-radius: 10px; font-size: 8pt; font-weight: bold; color: #fff; display: inline-block; }
        .status-menunggu { background-color: #e67e22; }
        .status-berhasil { background-color: #27ae60; }
        .status-selesai { background-color: #7f8c8d; }
        .status-tolak { background-color: #c0392b; }
        .footer { margin-top: 20px; font-size: 9pt; color: #777; text-align: center; border-top: 1px solid #ccc; padding-top: 8px; }
        .meta-left { float: left; font-size: 9pt; color: #555; }
        .meta-right { float: right; font-size: 9pt; color: #555; }
        .clearfix::after { content: ""; display: table; clear: both; }
        .ttd-section { margin-top: 30px; width: 100%; }
        .ttd-section td { border: none; text-align: center; padding: 5px; font-size: 10pt; vertical-align: top; }
    </style>
</head>
<body>
    {{-- Kop Surat --}}
    <div class="kop-container">
        @if(file_exists(public_path('logo-smancir.png')))
            <img src="{{ public_path('logo-smancir.png') }}" class="kop-logo" alt="Logo">
        @endif
        <div class="kop-text">
            <p class="instansi">PEMERINTAH PROVINSI BANTEN</p>
            <p class="instansi">DINAS PENDIDIKAN DAN KEBUDAYAAN</p>
            <p class="sekolah">UPT SMA NEGERI 1 CIRUAS</p>
            <p class="alamat">Jalan Raya Jakarta Km 9,5 Serang Telp. 280043</p>
            <p class="kontak">Web: www.sman1cir.sch.id | Email: ciruas@sman1cir.sch.id</p>
        </div>
    </div>

    {{-- Judul --}}
    <div class="judul-laporan">Laporan Peminjaman Paket</div>
    <div class="sub-judul">Status: {{ $statusLabel }} &mdash; Periode: {{ $periode }}</div>

    {{-- Meta Info --}}
    <div class="clearfix" style="margin-bottom: 10px;">
        <div class="meta-left">Total Data: {{ count($data) }}</div>
        <div class="meta-right">Dicetak: {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}</div>
    </div>

    {{-- Tabel Data --}}
    <table class="data">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama Peminjam</th>
                <th>NISN</th>
                <th>Kelas</th>
                <th>Nama Paket Buku</th>
                <th style="width: 70px;">Tgl. Pinjam</th>
                <th style="width: 65px;">Status</th>
                <th style="width: 70px;">Tgl. Kembali</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $key => $item)
                @php
                    $pinjam = $item->peminjamanPaket;
                    $status = $pinjam->status ?? '-';
                    $tanggalKembali = ($status === 'selesai' && $pinjam->updated_at)
                        ? $pinjam->updated_at->format('d-m-Y')
                        : '-';
                @endphp
                <tr>
                    <td class="center">{{ $key + 1 }}</td>
                    <td>{{ $pinjam->anggota->user->name ?? '-' }}</td>
                    <td class="center">{{ $pinjam->anggota->nisn ?? '-' }}</td>
                    <td class="center">{{ $pinjam->anggota->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $item->paketBuku->nama_paket ?? '-' }}</td>
                    <td class="center">{{ $pinjam->created_at ? $pinjam->created_at->format('d-m-Y') : '-' }}</td>
                    <td class="center">
                        <span class="status-badge status-{{ $status }}">
                            @if ($status === 'menunggu') Menunggu
                            @elseif ($status === 'berhasil') Dipinjam
                            @elseif ($status === 'tolak') Ditolak
                            @else Selesai
                            @endif
                        </span>
                    </td>
                    <td class="center">{{ $tanggalKembali }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; color: #999;">Tidak ada data peminjaman paket ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Tanda Tangan --}}
    <table class="ttd-section">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%;">
                Ciruas, {{ \Carbon\Carbon::now()->format('d F Y') }}<br>
                Kepala Perpustakaan,
                <br><br><br><br>
                (........................................)
            </td>
        </tr>
    </table>

    {{-- Footer --}}
    <div class="footer">
        Dokumen ini dicetak secara otomatis oleh Sistem Informasi Perpustakaan SMAN 1 Ciruas
    </div>
</body>
</html>
