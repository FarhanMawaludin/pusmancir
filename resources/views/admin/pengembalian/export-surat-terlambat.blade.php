<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Terlambat</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h2 { text-align: center; }
        .content { margin-top: 30px; }
    </style>
</head>
<body>
    <h2>Surat Keterangan Terlambat</h2>

    <p>Dengan ini kami menyatakan bahwa:</p>

    <table>
        <tr><td>Nama</td><td>: {{ $peminjaman->anggota->user->name }}</td></tr>
        <tr><td>NISN</td><td>: {{ $peminjaman->anggota->nisn }}</td></tr>
        <tr><td>Judul Buku</td><td>: {{ $pengembalian->eksemplar->inventori->judul_buku }}</td></tr>
        <tr><td>Tanggal Pinjam</td><td>: {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d-m-Y') }}</td></tr>
        <tr><td>Tanggal Kembali (seharusnya)</td><td>: {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d-m-Y') }}</td></tr>
    </table>

    <div class="content">
        <p>Telah melakukan keterlambatan dalam pengembalian buku. Mohon segera menyelesaikan pengembalian.</p>

        <p>Demikian surat ini dibuat untuk dipergunakan sebagaimana mestinya.</p>
    </div>

    <br><br>
    <p>Admin Perpustakaan</p>
</body>
</html>
