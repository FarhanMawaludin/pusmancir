<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggota;
use App\Models\Peminjaman;
use App\Models\Eksemplar;
use App\Models\Inventori;
use App\Models\DetailPeminjaman;

class DashboardAdminController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = 'dashboard';

        // ========================
        // Statistik Dashboard
        // ========================
        $totalAnggota = Anggota::where('status', 'aktif')->count();
        $totalPeminjamanMenunggu = Peminjaman::where('status', 'menunggu')->count();
        $totalJudulBuku = Inventori::count();
        $totalEksemplar = Eksemplar::count();

        // ========================
        // Peminjaman Menunggu
        // ========================
        $search = $request->input('search');
        $category = $request->input('category', 'all');

        $queryPeminjaman = DetailPeminjaman::with(['eksemplar', 'peminjaman.anggota.user'])
            ->whereHas('peminjaman', function ($q) use ($search) {
                $q->where('status', 'menunggu');

                if (!empty($search)) {
                    $q->whereHas('anggota', function ($q2) use ($search) {
                        $q2->where('nisn', 'like', "%{$search}%");
                    });
                }
            });

        if ($category !== 'all') {
            $queryPeminjaman->whereHas('peminjaman.anggota.kelas', function ($q) use ($category) {
                preg_match('/(\d+) (IPA|IPS|Bahasa)/', $category, $match);
                if ($match) {
                    $kelas = $match[1] . ' ' . $match[2];
                    $q->where('nama_kelas', 'like', $kelas . '%');
                }
            });
        }

        $peminjaman = $queryPeminjaman->paginate(5)->appends([
            'search' => $search,
            'category' => $category
        ]);

        // ========================
        // Pengembalian Berhasil / Selesai
        // ========================
        $queryPengembalian = DetailPeminjaman::with([
            'peminjaman.anggota.user',
            'eksemplar.inventori'
        ])->whereHas('peminjaman', function ($q) {
            $q->whereIn('status', ['berhasil']);
        });

        $queryPengembalian->addSelect([
            'peminjaman_status' => Peminjaman::select('status')
                ->whereColumn('peminjaman.id', 'detail_peminjaman.peminjaman_id')
                ->limit(1),
            'tanggal_kembali' => Peminjaman::select('tanggal_kembali')
                ->whereColumn('peminjaman.id', 'detail_peminjaman.peminjaman_id')
                ->limit(1),
        ]);

        if ($search) {
            $queryPengembalian->whereHas('peminjaman.anggota', function ($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%");
            });
        }

        if ($category !== 'all' && preg_match('/(\d+)\s*(IPA|IPS|Bahasa)/', $category, $m)) {
            $queryPengembalian->whereHas('peminjaman.anggota', function ($q) use ($m) {
                $q->where('nama_kelas', 'like', "{$m[1]} {$m[2]}%");
            });
        }

        $queryPengembalian->orderByRaw("CASE WHEN peminjaman_status = 'berhasil' THEN 0 ELSE 1 END")
            ->orderByRaw("CASE WHEN tanggal_kembali < NOW() THEN 0 ELSE 1 END")
            ->orderBy('tanggal_kembali', 'asc');

        $pengembalian = $queryPengembalian->paginate(5)->appends([
            'search' => $search,
            'category' => $category,
        ]);

        // ========================
        // Return to Dashboard View
        // ========================
        return view('admin.dashboard', compact(
            'activeMenu',
            'totalAnggota',
            'totalPeminjamanMenunggu',
            'totalJudulBuku',
            'totalEksemplar',
            'peminjaman',
            'pengembalian',
            'search',
            'category'
        ));
    }
}
