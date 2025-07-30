<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggota;
use App\Models\Peminjaman;
use App\Models\Eksemplar;
use App\Models\Inventori;
use App\Models\BukuTamu;
use App\Models\DetailPeminjaman;
use App\Models\WebVisit;  // jangan lupa import modelnya
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{
    // Fungsi untuk simpan data kunjungan manual
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

    public function index(Request $request)
    {
        $activeMenu = 'dashboard';

        $totalAnggota = Anggota::where('status', 'aktif')->count();
        $totalPeminjamanMenunggu = Peminjaman::where('status', 'menunggu')->count();
        $totalJudulBuku = Inventori::count();
        $totalEksemplar = Eksemplar::count();

        // Ambil data pengunjung dari tabel web_visits:
        $totalPengunjungHariIni = WebVisit::whereDate('created_at', now()->toDateString())
            ->distinct('ip')->count('ip');

        $totalPengunjungKeseluruhan = WebVisit::distinct('ip')->count('ip');

        // (Kode filter tahun/bulan/hari dan data lainnya seperti semula)
        $years = collect(array_unique(array_merge(
            Peminjaman::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year')->toArray(),
            BukuTamu::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year')->toArray()
        )))->sortDesc()->values();

        $selectedYear = $request->input('year', 'all');
        $selectedMonth = $request->input('month', 'all');
        $selectedDay = $request->input('day', 'all');

        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $days = [];

        if ($selectedYear === 'all') {
            $peminjamanData = Peminjaman::selectRaw('YEAR(created_at) as year, COUNT(*) as total')
                ->whereIn('status', ['berhasil', 'selesai'])
                ->groupBy('year')
                ->orderBy('year')
                ->pluck('total', 'year')
                ->toArray();

            $monthlyPeminjaman = array_values($peminjamanData);
            $peminjamanLabels = array_keys($peminjamanData);

            $pengunjungData = BukuTamu::selectRaw('YEAR(created_at) as year, COUNT(*) as total')
                ->groupBy('year')
                ->orderBy('year')
                ->pluck('total', 'year')
                ->toArray();

            $monthlyPengunjung = array_values($pengunjungData);
            $pengunjungLabels = array_keys($pengunjungData);

        } elseif ($selectedMonth === 'all') {
            $peminjamanRaw = Peminjaman::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
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

            $pengunjungRaw = BukuTamu::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
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

        } else {
            $totalDays = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);
            for ($d = 1; $d <= $totalDays; $d++) {
                $days[] = $d;
            }

            $peminjamanRaw = Peminjaman::selectRaw('DAY(created_at) as day, COUNT(*) as total')
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

            $pengunjungRaw = BukuTamu::selectRaw('DAY(created_at) as day, COUNT(*) as total')
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
        }

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

        return view('admin.dashboard', compact(
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
            'top10_kunjungan',
            'top10_peminjaman',
            'top10_buku',
            'totalPengunjungHariIni',      // kirim data ke view
            'totalPengunjungKeseluruhan'   // kirim data ke view
        ));
    }
}
