<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PaketBuku;

class KatalogPaketAnggotaController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = "katalog";

        $search    = $request->input('search');
        $searchBy  = $request->input('search_by', 'all');
        $kategori  = $request->input('kategori');

        // Mulai query
        $katalogQuery = PaketBuku::latest();

        $katalogList = $katalogQuery->get();

        return view('anggota.katalog-paket.index', compact(
            'katalogList',
            'activeMenu'
        ));
    }

    public function show($id)
    {
        $activeMenu = "katalogPaket";
        $buku = PaketBuku::findOrFail($id);
        $profilLengkap = $this->isProfilLengkap();
        return view('anggota.katalog-paket.show', compact('buku', 'activeMenu', 'profilLengkap'));
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
