<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Katalog;
use Illuminate\Support\Facades\Auth;

class KatalogAnggotaController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = "katalog";
        // ── query string ───────────────────────────────
        $search      = $request->input('search');        // kata kunci
        $searchBy    = $request->input('search_by', 'all'); // kolom mana (judul, pengarang, dll.)
        $kategori    = $request->input('kategori');      // nilai kategori buku (filter)

        // ── query buku ─────────────────────────────────
        $katalogQuery = Katalog::with('inventori.eksemplar')->latest();

        // filter kategori (jika tombol kategori diklik)
        if ($kategori) {
            $katalogQuery->where('kategori_buku', $kategori);
        }

        // filter pencarian
        if ($search) {
            if ($searchBy === 'all') {
                $katalogQuery->where(function ($q) use ($search) {
                    $q->where('judul_buku',     'like', "%{$search}%")
                        ->orWhere('pengarang',    'like', "%{$search}%")
                        ->orWhere('penerbit',     'like', "%{$search}%")
                        ->orWhere('kategori_buku', 'like', "%{$search}%")
                        ->orWhere('isbn',         'like', "%{$search}%");
                });
            } else {
                $katalogQuery->where($searchBy, 'like', "%{$search}%");
            }
        }

        $katalogList  = $katalogQuery->get();

        // ── ambil daftar kategori unik ─────────────────
        $kategoriList = Katalog::select('kategori_buku')
            ->distinct()
            ->orderBy('kategori_buku')
            ->pluck('kategori_buku');

        return view('anggota.katalog.index', compact(
            'katalogList',  // data buku
            'kategoriList', // daftar tombol kategori
            'kategori',     // kategori terpilih
            'search',       // kata kunci
            'searchBy',      // kolom pencarian
            'activeMenu'
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
