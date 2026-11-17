<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggota;
use App\Models\Peminjaman;
use App\Models\Eksemplar;
use App\Models\Inventori;
use App\Models\BukuTamu;
use App\Models\DetailPeminjaman;
use App\Models\WebVisit;  // Import model WebVisit
use App\Services\StatistikPeminjaman;
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{
    // Fungsi untuk menyimpan data kunjungan manual
    public function trackVisit(Request $request)
    {
        $ip = $request->ip();

        // Cek apakah IP ini sudah tercatat hari ini
        $exists = WebVisit::where('ip', $ip)
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        if (!$exists) {
            WebVisit::create([
                'ip' => $ip,
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
            ]);
        }

        return response()->json(['message' => 'Visit recorded']);
    }

    // public function index(Request $request)
    // {
    //     $activeMenu = 'dashboard';

    //     $totalAnggota = Anggota::where('status', 'aktif')->count();
    //     $totalPeminjamanMenunggu = Peminjaman::where('status', 'menunggu')->count();
    //     $totalJudulBuku = Inventori::count();
    //     $totalEksemplar = Eksemplar::count();

    //     // Ambil data tahun untuk filter (gabungkan tahun dari 2 tabel)
    //     $years = collect(array_unique(array_merge(
    //         Peminjaman::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year')->toArray(),
    //         BukuTamu::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year')->toArray()
    //     )))->sortDesc()->values();

    //     $selectedYear = $request->input('year', 'all');
    //     $selectedMonth = $request->input('month', 'all');
    //     $selectedDay = $request->input('day', 'all');

    //     $months = [
    //         1 => 'Jan',
    //         2 => 'Feb',
    //         3 => 'Mar',
    //         4 => 'Apr',
    //         5 => 'Mei',
    //         6 => 'Jun',
    //         7 => 'Jul',
    //         8 => 'Agu',
    //         9 => 'Sep',
    //         10 => 'Okt',
    //         11 => 'Nov',
    //         12 => 'Des'
    //     ];

    //     $days = [];

    //     if ($selectedYear === 'all') {
    //         $peminjamanData = Peminjaman::selectRaw('YEAR(created_at) as year, COUNT(*) as total')
    //             ->whereIn('status', ['berhasil', 'selesai'])
    //             ->groupBy('year')
    //             ->orderBy('year')
    //             ->pluck('total', 'year')
    //             ->toArray();

    //         $monthlyPeminjaman = array_values($peminjamanData);
    //         $peminjamanLabels = array_keys($peminjamanData);

    //         $pengunjungData = BukuTamu::selectRaw('YEAR(created_at) as year, COUNT(*) as total')
    //             ->groupBy('year')
    //             ->orderBy('year')
    //             ->pluck('total', 'year')
    //             ->toArray();

    //         $monthlyPengunjung = array_values($pengunjungData);
    //         $pengunjungLabels = array_keys($pengunjungData);

    //     } elseif ($selectedMonth === 'all') {
    //         $peminjamanRaw = Peminjaman::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
    //             ->whereYear('created_at', $selectedYear)
    //             ->whereIn('status', ['berhasil', 'selesai'])
    //             ->groupBy('month')
    //             ->orderBy('month')
    //             ->pluck('total', 'month')
    //             ->toArray();

    //         $monthlyPeminjaman = [];
    //         for ($i = 1; $i <= 12; $i++) {
    //             $monthlyPeminjaman[] = $peminjamanRaw[$i] ?? 0;
    //         }
    //         $peminjamanLabels = array_values($months);

    //         $pengunjungRaw = BukuTamu::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
    //             ->whereYear('created_at', $selectedYear)
    //             ->groupBy('month')
    //             ->orderBy('month')
    //             ->pluck('total', 'month')
    //             ->toArray();

    //         $monthlyPengunjung = [];
    //         for ($i = 1; $i <= 12; $i++) {
    //             $monthlyPengunjung[] = $pengunjungRaw[$i] ?? 0;
    //         }
    //         $pengunjungLabels = array_values($months);

    //     } else {
    //         $totalDays = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);
    //         for ($d = 1; $d <= $totalDays; $d++) {
    //             $days[] = $d;
    //         }

    //         $peminjamanRaw = Peminjaman::selectRaw('DAY(created_at) as day, COUNT(*) as total')
    //             ->whereYear('created_at', $selectedYear)
    //             ->whereMonth('created_at', $selectedMonth)
    //             ->whereIn('status', ['berhasil', 'selesai'])
    //             ->groupBy('day')
    //             ->orderBy('day')
    //             ->pluck('total', 'day')
    //             ->toArray();

    //         $monthlyPeminjaman = [];
    //         foreach ($days as $day) {
    //             $monthlyPeminjaman[] = $peminjamanRaw[$day] ?? 0;
    //         }
    //         $peminjamanLabels = $days;

    //         $pengunjungRaw = BukuTamu::selectRaw('DAY(created_at) as day, COUNT(*) as total')
    //             ->whereYear('created_at', $selectedYear)
    //             ->whereMonth('created_at', $selectedMonth)
    //             ->groupBy('day')
    //             ->orderBy('day')
    //             ->pluck('total', 'day')
    //             ->toArray();

    //         $monthlyPengunjung = [];
    //         foreach ($days as $day) {
    //             $monthlyPengunjung[] = $pengunjungRaw[$day] ?? 0;
    //         }
    //         $pengunjungLabels = $days;
    //     }

    //     // Tambahan: hitung jumlah pengunjung menggunakan WebVisit
    //     $totalPengunjungHariIni = WebVisit::whereDate('created_at', now()->toDateString())
    //         ->distinct('ip')
    //         ->count('ip');

    //     $totalPengunjungKeseluruhan = WebVisit::distinct('ip')->count('ip');

    //     $top10_kunjungan = DB::table('buku_tamu')
    //         ->select('nama', DB::raw('COUNT(*) as total_kunjungan'))
    //         ->whereNotNull('nama')
    //         ->groupBy('nama')
    //         ->orderByDesc('total_kunjungan')
    //         ->limit(10)
    //         ->get();

    //     $top10_peminjaman = Peminjaman::select('anggota_id', DB::raw('COUNT(*) as total_peminjaman'))
    //         ->with(['anggota.user'])
    //         ->groupBy('anggota_id')
    //         ->orderByDesc('total_peminjaman')
    //         ->limit(10)
    //         ->get();

    //     $top10_buku = DetailPeminjaman::selectRaw('inventori.judul_buku, COUNT(*) as total_dipinjam')
    //         ->join('eksemplar', 'detail_peminjaman.eksemplar_id', '=', 'eksemplar.id')
    //         ->join('inventori', 'eksemplar.id_inventori', '=', 'inventori.id')
    //         ->groupBy('inventori.judul_buku')
    //         ->orderByDesc('total_dipinjam')
    //         ->limit(10)
    //         ->get();

    //     return view('admin.dashboard', compact(
    //         'activeMenu',
    //         'totalAnggota',
    //         'totalPeminjamanMenunggu',
    //         'totalJudulBuku',
    //         'totalEksemplar',
    //         'years',
    //         'months',
    //         'days',
    //         'selectedYear',
    //         'selectedMonth',
    //         'selectedDay',
    //         'monthlyPeminjaman',
    //         'monthlyPengunjung',
    //         'peminjamanLabels',
    //         'pengunjungLabels',
    //         'top10_kunjungan',
    //         'top10_peminjaman',
    //         'top10_buku',
    //         'totalPengunjungHariIni',       
    //         'totalPengunjungKeseluruhan'     
    //     ));
    // }

    public function index(Request $request)
    {
        $activeMenu = 'dashboard';

        $totalAnggota = Anggota::where('status', 'aktif')->count();
        $totalPeminjamanMenunggu = Peminjaman::where('status', 'menunggu')->count();
        $totalJudulBuku = Inventori::count();
        $totalEksemplar = Eksemplar::count();

        $isSqlite = DB::getDriverName() === 'sqlite';
        $yearSql = $isSqlite ? "strftime('%Y', created_at)" : "YEAR(created_at)";
        $monthSql = $isSqlite ? "(strftime('%m', created_at) + 0)" : "MONTH(created_at)";
        $daySql = $isSqlite ? "(strftime('%d', created_at) + 0)" : "DAY(created_at)";

        // Ambil data tahun untuk filter (gabungkan tahun dari 2 tabel)
        $years = collect(array_unique(array_merge(
            Peminjaman::selectRaw("{$yearSql} as year")->distinct()->pluck('year')->toArray(),
            BukuTamu::selectRaw("{$yearSql} as year")->distinct()->pluck('year')->toArray()
        )))->sortDesc()->values();

        $selectedYear = $request->input('year', 'all');
        $selectedMonth = $request->input('month', 'all');
        $selectedDay = $request->input('day', 'all');

        $months = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des'
        ];

        $days = [];

        if ($selectedYear === 'all') {
            // Grafik Peminjaman dan Buku Tamu per Tahun
            $peminjamanData = Peminjaman::selectRaw("{$yearSql} as year, COUNT(*) as total")
                ->whereIn('status', ['berhasil', 'selesai'])
                ->groupBy('year')
                ->orderBy('year')
                ->pluck('total', 'year')
                ->toArray();

            $monthlyPeminjaman = array_values($peminjamanData);
            $peminjamanLabels = array_keys($peminjamanData);

            $pengunjungData = BukuTamu::selectRaw("{$yearSql} as year, COUNT(*) as total")
                ->groupBy('year')
                ->orderBy('year')
                ->pluck('total', 'year')
                ->toArray();

            $monthlyPengunjung = array_values($pengunjungData);
            $pengunjungLabels = array_keys($pengunjungData);

            // Pengunjung Website per Tahun
            $webVisitRaw = WebVisit::selectRaw("{$yearSql} as year, COUNT(DISTINCT ip) as total")
                ->groupBy('year')
                ->orderBy('year')
                ->pluck('total', 'year')
                ->toArray();

            $webVisitData = array_values($webVisitRaw);
            $webVisitLabels = array_keys($webVisitRaw);
        } elseif ($selectedMonth === 'all') {
            // Grafik Peminjaman dan Buku Tamu per Bulan
            $peminjamanRaw = Peminjaman::selectRaw("{$monthSql} as month, COUNT(*) as total")
                ->whereYear('created_at', $selectedYear)
                ->whereIn('status', ['berhasil', 'selesai'])
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $monthlyPeminjaman = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthlyPeminjaman[] = $peminjamanRaw[$i] ?? 0;
            }
            $peminjamanLabels = array_values($months);

            $pengunjungRaw = BukuTamu::selectRaw("{$monthSql} as month, COUNT(*) as total")
                ->whereYear('created_at', $selectedYear)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $monthlyPengunjung = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthlyPengunjung[] = $pengunjungRaw[$i] ?? 0;
            }
            $pengunjungLabels = array_values($months);

            // Pengunjung Website per Bulan
            $webVisitRaw = WebVisit::selectRaw("{$monthSql} as month, COUNT(DISTINCT ip) as total")
                ->whereYear('created_at', $selectedYear)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $webVisitData = [];
            for ($i = 1; $i <= 12; $i++) {
                $webVisitData[] = $webVisitRaw[$i] ?? 0;
            }
            $webVisitLabels = array_values($months);
        } else {
            // Grafik per Hari
            $totalDays = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);
            $days = range(1, $totalDays);

            $peminjamanRaw = Peminjaman::selectRaw("{$daySql} as day, COUNT(*) as total")
                ->whereYear('created_at', $selectedYear)
                ->whereMonth('created_at', $selectedMonth)
                ->whereIn('status', ['berhasil', 'selesai'])
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('total', 'day')
                ->toArray();

            $monthlyPeminjaman = [];
            foreach ($days as $day) {
                $monthlyPeminjaman[] = $peminjamanRaw[$day] ?? 0;
            }
            $peminjamanLabels = $days;

            $pengunjungRaw = BukuTamu::selectRaw("{$daySql} as day, COUNT(*) as total")
                ->whereYear('created_at', $selectedYear)
                ->whereMonth('created_at', $selectedMonth)
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('total', 'day')
                ->toArray();

            $monthlyPengunjung = [];
            foreach ($days as $day) {
                $monthlyPengunjung[] = $pengunjungRaw[$day] ?? 0;
            }
            $pengunjungLabels = $days;

            // Pengunjung Website per Hari
            $webVisitRaw = WebVisit::selectRaw("{$daySql} as day, COUNT(DISTINCT ip) as total")
                ->whereYear('created_at', $selectedYear)
                ->whereMonth('created_at', $selectedMonth)
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('total', 'day')
                ->toArray();

            $webVisitData = [];
            foreach ($days as $day) {
                $webVisitData[] = $webVisitRaw[$day] ?? 0;
            }
            $webVisitLabels = $days;
        }

        // Tambahan data total pengunjung unik
        $totalPengunjungHariIni = WebVisit::whereDate('created_at', now()->toDateString())
            ->distinct('ip')
            ->count('ip');

        $totalPengunjungKeseluruhan = WebVisit::distinct('ip')->count('ip');

        // Top 10 data
        $top10_kunjungan = DB::table('buku_tamu')
            ->select('nama', DB::raw('COUNT(*) as total_kunjungan'))
            ->whereNotNull('nama')
            ->groupBy('nama')
            ->orderByDesc('total_kunjungan')
            ->limit(10)
            ->get();

        $top10_peminjaman = Peminjaman::select('anggota_id', DB::raw('COUNT(*) as total_peminjaman'))
            ->with(['anggota.user'])
            ->groupBy('anggota_id')
            ->orderByDesc('total_peminjaman')
            ->limit(10)
            ->get();

        $top10_buku = DetailPeminjaman::selectRaw('inventori.judul_buku, COUNT(*) as total_dipinjam')
            ->join('eksemplar', 'detail_peminjaman.eksemplar_id', '=', 'eksemplar.id')
            ->join('inventori', 'eksemplar.id_inventori', '=', 'inventori.id')
            ->groupBy('inventori.judul_buku')
            ->orderByDesc('total_dipinjam')
            ->limit(10)
            ->get();

        $statistik = $this->statistikPeminjamanBuku($selectedYear, $selectedMonth);

        return view('admin.dashboard', $statistik + compact(
            'activeMenu',
            'totalAnggota',
            'totalPeminjamanMenunggu',
            'totalJudulBuku',
            'totalEksemplar',
            'years',
            'months',
            'days',
            'selectedYear',
            'selectedMonth',
            'selectedDay',
            'monthlyPeminjaman',
            'monthlyPengunjung',
            'peminjamanLabels',
            'pengunjungLabels',
            'webVisitLabels',
            'webVisitData',
            'top10_kunjungan',
            'top10_peminjaman',
            'top10_buku',
            'totalPengunjungHariIni',
            'totalPengunjungKeseluruhan'
        ));
    }

    /**
     * Data tambahan dashboard: statistik judul & eksemplar dipinjam (sedang / selesai)
     * per tahun / bulan / hari mengikuti filter, ditambah data koleksi & anggota.
     */
    private function statistikPeminjamanBuku($selectedYear, $selectedMonth): array
    {
        $tahun = $selectedYear !== 'all' ? (int) $selectedYear : null;
        $bulan = ($tahun && $selectedMonth !== 'all') ? (int) $selectedMonth : null;

        [$dari, $sampai] = StatistikPeminjaman::rentang($tahun, $bulan);
        $rows = StatistikPeminjaman::rows($dari, $sampai);

        $statPeriode = StatistikPeminjaman::ringkasan($rows);
        $rekapPeriode = StatistikPeminjaman::perPeriode($rows, $tahun, $bulan);
        if ($tahun && !$bulan) {
            // label bulan singkat agar muat di sumbu X
            foreach ($rekapPeriode as $i => $r) {
                $rekapPeriode[$i]['label_chart'] = StatistikPeminjaman::BULAN_SINGKAT[$r['key']];
            }
        }

        $labelPeriode = !$tahun ? 'Semua Tahun'
            : ($bulan ? StatistikPeminjaman::BULAN[$bulan] . ' ' . $tahun : 'Tahun ' . $tahun);

        // Snapshot kondisi saat ini (tidak terpengaruh filter)
        $aktifQuery = DB::table('detail_peminjaman as dp')
            ->join('peminjaman as p', 'p.id', '=', 'dp.peminjaman_id')
            ->join('eksemplar as e', 'e.id', '=', 'dp.eksemplar_id')
            ->where('p.status', 'berhasil')
            ->whereNull('dp.tanggal_kembali_asli');

        $snapshot = [
            'eks_aktif' => (clone $aktifQuery)->count(),
            'judul_aktif' => (clone $aktifQuery)->distinct()->count('e.id_inventori'),
            'eks_terlambat' => (clone $aktifQuery)->whereDate('p.tanggal_kembali', '<', now()->toDateString())->count(),
            'peminjam_aktif' => (clone $aktifQuery)->distinct()->count('p.anggota_id'),
        ];

        $bukuTerlambat = DetailPeminjaman::with(['eksemplar.inventori', 'peminjaman.anggota.user', 'peminjaman.anggota.kelas'])
            ->whereNull('tanggal_kembali_asli')
            ->whereHas('peminjaman', fn($q) => $q->where('status', 'berhasil')
                ->whereDate('tanggal_kembali', '<', now()->toDateString()))
            ->get()
            ->sortBy(fn($d) => $d->peminjaman->tanggal_kembali)
            ->take(8)
            ->values();

        // Kondisi eksemplar koleksi
        $statusEksemplar = Eksemplar::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
        $statusEksemplar = [
            'Tersedia' => (int) ($statusEksemplar['tersedia'] ?? 0),
            'Dipinjam' => (int) ($statusEksemplar['dipinjam'] ?? 0),
            'Rusak' => (int) ($statusEksemplar['rusak'] ?? 0),
            'Hilang' => (int) ($statusEksemplar['hilang'] ?? 0),
        ];

        // Komposisi anggota
        $komposisiAnggota = [
            'siswa' => Anggota::where('status', 'aktif')->where(fn($q) => $q->where('jenis_anggota', '!=', 'guru')->orWhereNull('jenis_anggota'))->count(),
            'guru' => Anggota::where('jenis_anggota', 'guru')->count(),
            'alumni' => Anggota::where('status', 'alumni')->count(),
        ];

        // Pertumbuhan koleksi (judul & eksemplar baru) berdasarkan tanggal pembelian
        $koleksiRows = Inventori::withCount('eksemplar')->get(['id', 'tanggal_pembelian']);
        $pertumbuhanKoleksi = ['labels' => [], 'judul' => [], 'eksemplar' => []];
        if (!$tahun) {
            $grup = $koleksiRows->groupBy(fn($i) => (int) substr((string) $i->tanggal_pembelian, 0, 4))->sortKeys();
            foreach ($grup as $t => $g) {
                $pertumbuhanKoleksi['labels'][] = (string) $t;
                $pertumbuhanKoleksi['judul'][] = $g->count();
                $pertumbuhanKoleksi['eksemplar'][] = (int) $g->sum('eksemplar_count');
            }
        } else {
            $grup = $koleksiRows->filter(fn($i) => (int) substr((string) $i->tanggal_pembelian, 0, 4) === $tahun)
                ->groupBy(fn($i) => (int) substr((string) $i->tanggal_pembelian, 5, 2));
            foreach (StatistikPeminjaman::BULAN_SINGKAT as $b => $nama) {
                $g = $grup->get($b, collect());
                $pertumbuhanKoleksi['labels'][] = $nama;
                $pertumbuhanKoleksi['judul'][] = $g->count();
                $pertumbuhanKoleksi['eksemplar'][] = (int) $g->sum('eksemplar_count');
            }
        }

        // Buku paket yang sedang dipinjam siswa (antrian paket)
        $paketAktif = DB::table('detail_peminjaman_buku_paket as d')
            ->join('peminjaman_buku_paket as p', 'p.id', '=', 'd.peminjaman_buku_paket_id')
            ->where('p.status', 'dipinjam')
            ->count();

        return [
            'statLabelPeriode' => $labelPeriode,
            'statPeriode' => $statPeriode,
            'statRekapPeriode' => $rekapPeriode,
            'statSnapshot' => $snapshot,
            'statPerKategori' => StatistikPeminjaman::perKategori($rows),
            'statPerKelompok' => StatistikPeminjaman::perKelompokPeminjam($rows)->take(10)->values(),
            'statBukuTerlambat' => $bukuTerlambat,
            'statStatusEksemplar' => $statusEksemplar,
            'statKomposisiAnggota' => $komposisiAnggota,
            'statPertumbuhanKoleksi' => $pertumbuhanKoleksi,
            'statPaketAktif' => $paketAktif,
            'statTahunFilter' => $tahun,
            'statBulanFilter' => $bulan,
        ];
    }
}
