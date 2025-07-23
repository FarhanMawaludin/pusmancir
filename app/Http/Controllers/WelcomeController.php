<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Katalog;
use App\Models\BukuElektronik;

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

            // Filter kategori
            if (!empty($kategori)) {
                $ebookQuery->whereIn('kategori', (array) $kategori);
            }

            // Filter kelas
            if (!empty($kelas)) {
                $ebookQuery->whereIn('kelas', (array) $kelas);
            }

            // Filter pencarian
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

            // Ambil hasil
            $ebookList = $ebookQuery->get();

            // Ambil kategori dan kelas unik
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
            $katalogQuery = Katalog::with('inventori.eksemplar')->latest();

            // Filter kategori
            if (!empty($kategori)) {
                $katalogQuery->whereIn('kategori_buku', (array) $kategori);
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

            // Ambil hasil
            $katalogList = $katalogQuery->get();

            // Ambil kategori unik
            $kategoriList = Katalog::select('kategori_buku')
                ->distinct()
                ->orderBy('kategori_buku')
                ->pluck('kategori_buku')
                ->unique()
                ->values();
        }

        return view('welcome', compact(
            'katalogList',
            'ebookList',
            'kategoriList',
            'kelasList',
            'kategori',
            'kelas',
            'search',
            'searchBy'
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
