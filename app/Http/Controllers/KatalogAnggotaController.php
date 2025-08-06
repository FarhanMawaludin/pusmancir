<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Katalog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KatalogAnggotaController extends Controller
{
    public function index(Request $request)
{
    $activeMenu = "katalog";
    $search     = $request->input('search');
    $searchBy   = $request->input('search_by', 'all');
    $kategori   = $request->input('kategori');
    $isRekomendasi = $request->boolean('rekomendasi', false);

    $katalogQuery = Katalog::with('inventori.eksemplar');

    // Filter kategori
    if ($kategori) {
        $katalogQuery->where('kategori_buku', $kategori);
    }

    // Filter pencarian
    if ($search) {
        if ($searchBy === 'all') {
            $katalogQuery->where(function ($q) use ($search) {
                $q->where('judul_buku', 'like', "%{$search}%")
                  ->orWhere('pengarang', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%")
                  ->orWhere('kategori_buku', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        } else {
            $katalogQuery->where($searchBy, 'like', "%{$search}%");
        }
    }

    // Filter rekomendasi
    if ($isRekomendasi && Auth::check()) {
        $anggotaId = Auth::user()->anggota->id ?? null;

        if ($anggotaId) {
            // Ambil semua kategori buku yang pernah dipinjam user
            $favoritKategori = \App\Models\DetailPeminjaman::join('peminjaman', 'detail_peminjaman.peminjaman_id', '=', 'peminjaman.id')
                ->join('eksemplar', 'detail_peminjaman.eksemplar_id', '=', 'eksemplar.id')
                ->join('inventori', 'eksemplar.id_inventori', '=', 'inventori.id')
                ->join('katalog', 'katalog.id_inventori', '=', 'inventori.id')
                ->where('peminjaman.anggota_id', $anggotaId)
                ->select('katalog.kategori_buku')
                ->groupBy('katalog.kategori_buku')
                ->pluck('katalog.kategori_buku');

            if ($favoritKategori->isNotEmpty()) {
                // Tampilkan semua buku dari kategori favorit
                $katalogQuery->whereIn('kategori_buku', $favoritKategori)->limit(20);
            } else {
                // Jika tidak ada favorit, tampilkan katalog berdasarkan peminjaman terbanyak
                $terpopuler = \App\Models\DetailPeminjaman::join('eksemplar', 'detail_peminjaman.eksemplar_id', '=', 'eksemplar.id')
                    ->join('inventori', 'eksemplar.id_inventori', '=', 'inventori.id')
                    ->join('katalog', 'katalog.id_inventori', '=', 'inventori.id')
                    ->select('katalog.id', DB::raw('COUNT(*) as total'))
                    ->groupBy('katalog.id')
                    ->orderByDesc('total')
                    ->pluck('katalog.id')
                    ->toArray();

                if (!empty($terpopuler)) {
                    $katalogQuery->whereIn('id', $terpopuler)
                        ->orderByRaw('FIELD(id, ' . implode(',', $terpopuler) . ')')
                        ->limit(20);
                } else {
                    $katalogQuery->latest()->limit(20);
                }
            }
        }
    } else {
        $katalogQuery->latest();
    }

    $katalogList = $katalogQuery->get();

    $kategoriList = Katalog::select('kategori_buku')
        ->distinct()
        ->orderBy('kategori_buku')
        ->pluck('kategori_buku');

    return view('anggota.katalog.index', compact(
        'katalogList',
        'kategoriList',
        'kategori',
        'search',
        'searchBy',
        'activeMenu',
        'isRekomendasi'
    ));
}







    public function show($id)
    {
        $activeMenu = "katalog";
        $buku = Katalog::with('inventori.eksemplar')->findOrFail($id);
        $profilLengkap = $this->isProfilLengkap();
        return view('anggota.katalog.show', compact('buku', 'activeMenu', 'profilLengkap'));
    }

    private function isProfilLengkap(): bool
    {
        $anggota = Auth::user()->anggota; // relasi hasOne

        if (!$anggota) return false;

        $wajib = [
            $anggota->nisn,
            $anggota->no_telp,
            $anggota->email,
            $anggota->kelas_id,
        ];

        return collect($wajib)->every(fn($v) => !empty($v));
    }
}
