<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Statistik peminjaman buku NON PAKET (judul & eksemplar).
 *
 * Definisi:
 *  - Satu baris detail_peminjaman = 1 eksemplar yang dipinjam.
 *  - Judul = jumlah inventori (judul buku) berbeda.
 *  - "Sedang dipinjam" = peminjaman berstatus 'berhasil' & eksemplar belum dikembalikan.
 *  - "Selesai dipinjam" = eksemplar sudah dikembalikan (tanggal_kembali_asli terisi
 *     atau status peminjaman 'selesai').
 *  - Periode dihitung berdasarkan tanggal_pinjam.
 *
 * Agregasi dilakukan di PHP agar query portable (MySQL & SQLite) tanpa fungsi
 * tanggal spesifik database.
 */
class StatistikPeminjaman
{
    public const BULAN = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public const BULAN_SINGKAT = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
        7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
    ];

    /**
     * Ambil baris detail peminjaman (1 baris = 1 eksemplar) pada rentang tanggal pinjam.
     */
    public static function rows(?string $dari = null, ?string $sampai = null): Collection
    {
        $query = DB::table('detail_peminjaman as dp')
            ->join('peminjaman as p', 'p.id', '=', 'dp.peminjaman_id')
            ->join('eksemplar as e', 'e.id', '=', 'dp.eksemplar_id')
            ->join('inventori as i', 'i.id', '=', 'e.id_inventori')
            ->leftJoin('kategori_buku as kb', 'kb.id', '=', 'i.id_kategori_buku')
            ->leftJoin('anggota as a', 'a.id', '=', 'p.anggota_id')
            ->leftJoin('kelas as k', 'k.id', '=', 'a.kelas_id')
            ->whereIn('p.status', ['berhasil', 'selesai'])
            ->select([
                'dp.id as detail_id',
                'p.id as peminjaman_id',
                'p.anggota_id',
                'p.status',
                'p.tanggal_pinjam',
                'p.tanggal_kembali',
                'dp.tanggal_kembali_asli',
                'e.id as eksemplar_id',
                'i.id as inventori_id',
                'i.judul_buku',
                'i.pengarang',
                'kb.nama_kategori',
                'a.jenis_anggota',
                'k.nama_kelas',
            ]);

        if ($dari) {
            $query->whereDate('p.tanggal_pinjam', '>=', $dari);
        }
        if ($sampai) {
            $query->whereDate('p.tanggal_pinjam', '<=', $sampai);
        }

        $today = Carbon::today();

        return $query->get()->map(function ($r) use ($today) {
            $r->selesai = !is_null($r->tanggal_kembali_asli) || $r->status === 'selesai';
            $r->aktif = !$r->selesai && $r->status === 'berhasil';
            $r->terlambat = $r->aktif && $r->tanggal_kembali && Carbon::parse($r->tanggal_kembali)->lt($today);
            $tgl = Carbon::parse($r->tanggal_pinjam);
            $r->tahun = (int) $tgl->year;
            $r->bulan = (int) $tgl->month;
            $r->hari = (int) $tgl->day;
            return $r;
        });
    }

    /**
     * Ringkasan angka judul & eksemplar dari sekumpulan baris.
     */
    public static function ringkasan(Collection $rows): array
    {
        $aktif = $rows->where('aktif', true);
        $selesai = $rows->where('selesai', true);

        return [
            'judul_total' => $rows->pluck('inventori_id')->unique()->count(),
            'eks_total' => $rows->count(),
            'judul_aktif' => $aktif->pluck('inventori_id')->unique()->count(),
            'eks_aktif' => $aktif->count(),
            'judul_selesai' => $selesai->pluck('inventori_id')->unique()->count(),
            'eks_selesai' => $selesai->count(),
            'eks_terlambat' => $rows->where('terlambat', true)->count(),
            'transaksi' => $rows->pluck('peminjaman_id')->unique()->count(),
            'peminjam' => $rows->pluck('anggota_id')->filter()->unique()->count(),
        ];
    }

    /**
     * Rekap per periode.
     *  - $tahun null        => per tahun
     *  - $tahun & $bulan null => per bulan (Jan–Des) di tahun tsb
     *  - $tahun & $bulan    => per hari di bulan tsb
     *
     * @return array<int, array{label:string, key:int}+array>
     */
    public static function perPeriode(Collection $rows, ?int $tahun = null, ?int $bulan = null): array
    {
        $hasil = [];

        if (!$tahun) {
            $tahunList = $rows->pluck('tahun')->unique()->sort()->values();
            foreach ($tahunList as $t) {
                $hasil[] = ['label' => (string) $t, 'key' => $t] + self::ringkasan($rows->where('tahun', $t));
            }
            return $hasil;
        }

        $rowsTahun = $rows->where('tahun', $tahun);

        if (!$bulan) {
            foreach (self::BULAN as $b => $nama) {
                $hasil[] = ['label' => $nama, 'key' => $b] + self::ringkasan($rowsTahun->where('bulan', $b));
            }
            return $hasil;
        }

        $jumlahHari = Carbon::create($tahun, $bulan, 1)->daysInMonth;
        $rowsBulan = $rowsTahun->where('bulan', $bulan);
        for ($d = 1; $d <= $jumlahHari; $d++) {
            $hasil[] = ['label' => (string) $d, 'key' => $d] + self::ringkasan($rowsBulan->where('hari', $d));
        }
        return $hasil;
    }

    /**
     * Rentang tanggal [dari, sampai] untuk filter tahun/bulan.
     */
    public static function rentang(?int $tahun, ?int $bulan): array
    {
        if (!$tahun) {
            return [null, null];
        }
        if (!$bulan) {
            return ["$tahun-01-01", "$tahun-12-31"];
        }
        $awal = Carbon::create($tahun, $bulan, 1);
        return [$awal->toDateString(), $awal->copy()->endOfMonth()->toDateString()];
    }

    /**
     * Daftar tahun yang memiliki data peminjaman (desc).
     */
    public static function daftarTahun(): Collection
    {
        return DB::table('peminjaman')
            ->whereNotNull('tanggal_pinjam')
            ->pluck('tanggal_pinjam')
            ->map(fn($t) => (int) substr((string) $t, 0, 4))
            ->push((int) date('Y'))
            ->unique()
            ->sortDesc()
            ->values();
    }

    /**
     * Rekap per judul buku: berapa eksemplar & berapa kali dipinjam.
     */
    public static function perJudul(Collection $rows): Collection
    {
        return $rows->groupBy('inventori_id')->map(function ($g) {
            $first = $g->first();
            return (object) [
                'inventori_id' => $first->inventori_id,
                'judul_buku' => $first->judul_buku,
                'pengarang' => $first->pengarang,
                'kategori' => $first->nama_kategori ?? '-',
                'kali_dipinjam' => $g->count(),
                'eksemplar_berbeda' => $g->pluck('eksemplar_id')->unique()->count(),
                'sedang_dipinjam' => $g->where('aktif', true)->count(),
                'sudah_kembali' => $g->where('selesai', true)->count(),
            ];
        })->sortByDesc('kali_dipinjam')->values();
    }

    /**
     * Rekap per kategori buku (judul & eksemplar).
     */
    public static function perKategori(Collection $rows): Collection
    {
        return $rows->groupBy(fn($r) => $r->nama_kategori ?: 'Tanpa Kategori')
            ->map(fn($g, $nama) => (object) [
                'kategori' => $nama,
                'judul' => $g->pluck('inventori_id')->unique()->count(),
                'eksemplar' => $g->count(),
            ])->sortByDesc('eksemplar')->values();
    }

    /**
     * Rekap per kelompok peminjam (kelas siswa / guru).
     */
    public static function perKelompokPeminjam(Collection $rows): Collection
    {
        return $rows->groupBy(function ($r) {
            if (($r->jenis_anggota ?? 'siswa') === 'guru') {
                return 'Guru';
            }
            return $r->nama_kelas ?: 'Tanpa Kelas';
        })->map(fn($g, $nama) => (object) [
            'kelompok' => $nama,
            'peminjam' => $g->pluck('anggota_id')->unique()->count(),
            'judul' => $g->pluck('inventori_id')->unique()->count(),
            'eksemplar' => $g->count(),
        ])->sortByDesc('eksemplar')->values();
    }
}
