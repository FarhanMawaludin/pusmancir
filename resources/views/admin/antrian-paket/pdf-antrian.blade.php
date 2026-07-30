<!DOCTYPE html>
<html>
<head>
    <title>Daftar Antrian Peminjaman Buku Paket</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
        h2, h3 {
            text-align: center;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Daftar Antrian Peminjaman Buku Paket</h2>
    <h3>Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}</h3>

    <table>
        <thead>
            <tr>
                <th class="text-center">No Antrian</th>
                <th>Nama Siswa</th>
                <th>NISN</th>
                <th>Kelas</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($antrians as $antrian)
            <tr>
                <td class="text-center">{{ $antrian->nomor_antrian }}</td>
                <td>{{ $antrian->anggota->user->name ?? '-' }}</td>
                <td>{{ $antrian->anggota->nisn ?? '-' }}</td>
                <td>{{ $antrian->anggota->kelas->nama_kelas ?? '-' }}</td>
                <td class="text-center">
                    @if($antrian->status == 'menunggu')
                        Menunggu
                    @elseif($antrian->status == 'hadir')
                        Hadir
                    @elseif($antrian->status == 'batal')
                        Batal
                    @elseif($antrian->status == 'tidak_hadir')
                        Tidak Hadir
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Belum ada antrian.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
