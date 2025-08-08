<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Katalog;
use App\Models\BukuElektronik;
use App\Models\Berita;

class WelcomeController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $searchBy   = $request->input('search_by', 'all');
        $kategori   = $request->input('kategori', []);
        $kelas      = $request->input('kelas', []);
        $isEbook    = $request->boolean('ebook');

        // Hasil data yang akan dikirim ke view
        $katalogList = collect();
        $ebookList   = collect();
        $kategoriList = collect();
        $kelasList    = collect();

        if ($isEbook) {
            // ✅ EBOOK MODE
            $ebookQuery = BukuElektronik::query()->latest();

            if (!empty($kategori)) {
                $ebookQuery->whereIn('kategori', (array) $kategori);
            }

            if (!empty($kelas)) {
                $ebookQuery->whereIn('kelas', (array) $kelas);
            }

            if ($search) {
                if ($searchBy === 'all') {
                    $ebookQuery->where(function ($q) use ($search) {
                        $q->where('judul', 'like', "%{$search}%")
                            ->orWhere('penulis', 'like', "%{$search}%")
                            ->orWhere('kategori', 'like', "%{$search}%")
                            ->orWhere('kelas', 'like', "%{$search}%")
                            ->orWhere('kurikulum', 'like', "%{$search}%");
                    });
                } else {
                    $ebookQuery->where($searchBy, 'like', "%{$search}%");
                }
            }

            $ebookList = $ebookQuery->get();
            $kategoriList = BukuElektronik::select('kategori')
                ->distinct()
                ->orderBy('kategori')
                ->pluck('kategori')
                ->unique()
                ->values();

            $kelasList = BukuElektronik::select('kelas')
                ->distinct()
                ->orderBy('kelas')
                ->pluck('kelas')
                ->unique()
                ->values();
        } else {
            // ✅ KATALOG MODE
            $katalogQuery = Katalog::whereHas('inventori.eksemplar', function ($q) {
                $q->where('status', ['tersedia', 'dipinjam']);
            })->with([
                'inventori' => function ($q) {
                    $q->with(['eksemplar' => function ($q2) {
                        $q2->where('status', ['tersedia', 'dipinjam']);
                    }]);
                }
            ])->latest();
            

            if (!empty($kategori)) {
                $katalogQuery->whereIn('kategori_buku', (array) $kategori);
            }

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

            $katalogList = $katalogQuery->get();

            $kategoriList = Katalog::select('kategori_buku')
                ->distinct()
                ->orderBy('kategori_buku')
                ->pluck('kategori_buku')
                ->unique()
                ->values();
        }

        $berita = Berita::where('status', 'publish')->latest()->get();
        $hero = $berita->first(); // Hero section: berita pertama
        $popular = Berita::where('status', 'publish')->latest()->take(5)->get(); // ✅ 5 berita terbaru
        $informasi = Berita::where('status', 'informasi')->latest()->first();


        return view('welcome', compact(
            'katalogList',
            'ebookList',
            'kategoriList',
            'kelasList',
            'kategori',
            'kelas',
            'search',
            'searchBy',
            'berita',
            'hero',
            'popular',
            'informasi'
        ));
    }


    public function show($id)
    {
        $buku = Katalog::with('inventori.eksemplar')->findOrFail($id);
        return view('detail-buku', compact('buku'));
    }


    public function showEbook($id)
    {
        $buku = BukuElektronik::findOrFail($id);
        return view('detail-buku-elektronik', compact('buku'));
    }

}
