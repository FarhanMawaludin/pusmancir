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

        // === Grafik Peminjaman ===
        if ($selectedYear === 'all') {
            $peminjamanData = Peminjaman::selectRaw('YEAR(created_at) as year, COUNT(*) as total')
                ->whereIn('status', ['berhasil', 'selesai'])
                ->groupBy('year')
                ->orderBy('year')
                ->pluck('total', 'year')
                ->toArray();

            $monthlyPeminjaman = array_values($peminjamanData);
            $peminjamanLabels = array_keys($peminjamanData);
        } else {
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
            $peminjamanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        }

        // === Grafik Pengunjung ===
        if ($selectedYear === 'all') {
            $pengunjungData = BukuTamu::selectRaw('YEAR(created_at) as year, COUNT(*) as total')
                ->groupBy('year')
                ->orderBy('year')
                ->pluck('total', 'year')
                ->toArray();

            $monthlyPengunjung = array_values($pengunjungData);
            $pengunjungLabels = array_keys($pengunjungData);
        } else {
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
            $pengunjungLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        }

        return view('admin.dashboard', compact(
            'activeMenu',
            'totalAnggota',
            'totalPeminjamanMenunggu',
            'totalJudulBuku',
            'totalEksemplar',
            'years',
            'selectedYear',
            'monthlyPeminjaman',
            'monthlyPengunjung',
            'peminjamanLabels',
            'pengunjungLabels'
        ));
    }
}
