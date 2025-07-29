<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggota;
use App\Models\Peminjaman;
use App\Models\Eksemplar;
use App\Models\Inventori;
use App\Models\BukuTamu;

class DashboardAdminController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = 'dashboard';

        $totalAnggota = Anggota::where('status', 'aktif')->count();
        $totalPeminjamanMenunggu = Peminjaman::where('status', 'menunggu')->count();
        $totalJudulBuku = Inventori::count();
        $totalEksemplar = Eksemplar::count();

        $years = collect(array_unique(array_merge(
            Peminjaman::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year')->toArray(),
            BukuTamu::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year')->toArray()
        )))->sortDesc()->values();

        $selectedYear = $request->input('year', 'all');
        $selectedMonth = $request->input('month', 'all');
        $selectedDay = $request->input('day', 'all');

        // Siapkan data bulan untuk dropdown
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

        // Siapkan array hari kosong untuk nanti diisi sesuai bulan & tahun
        $days = [];

        // Ambil data berdasarkan filter
        if ($selectedYear === 'all') {
            // Data per tahun
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
            // Data per bulan di tahun tertentu
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
            // Data per hari di bulan dan tahun tertentu

            // Hitung jumlah hari di bulan tersebut
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
            'pengunjungLabels'
        ));
    }
}
