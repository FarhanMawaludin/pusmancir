<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;

class PeringkatController extends Controller
{
    public function index()
    {
        // Top 3 kunjungan
        $top3_kunjungan = DB::table('buku_tamu')
            ->select('nama', DB::raw('COUNT(*) as total_kunjungan'))
            ->whereNotNull('nama')
            ->groupBy('nama')
            ->orderByDesc('total_kunjungan')
            ->limit(3)
            ->get();

        // Top 3 peminjam
        $top3_peminjaman = Peminjaman::select('anggota_id', DB::raw('COUNT(*) as total_peminjaman'))
            ->with(['anggota.user'])
            ->groupBy('anggota_id')
            ->orderByDesc('total_peminjaman')
            ->limit(3)
            ->get();

        // Top 3 buku paling sering dipinjam
        $top3_buku = DetailPeminjaman::selectRaw('inventori.judul_buku, COUNT(*) as total_dipinjam')
            ->join('eksemplar', 'detail_peminjaman.eksemplar_id', '=', 'eksemplar.id')
            ->join('inventori', 'eksemplar.id_inventori', '=', 'inventori.id')
            ->groupBy('inventori.judul_buku')
            ->orderByDesc('total_dipinjam')
            ->limit(3)
            ->get();

        return view('peringkat.index', compact('top3_kunjungan', 'top3_peminjaman', 'top3_buku'));
    }
}
