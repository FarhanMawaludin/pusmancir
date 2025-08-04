<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Eksemplar;
use Illuminate\Support\Facades\DB;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;

class EksemplarController extends Controller
{


    //     public function index(Request $request)
    // {
    //     $activeMenu = "eksemplar";

    //     $search = $request->input('search');
    //     $category = $request->input('category', 'all');
    //     $sort = $request->input('sort', 'no_induk_asc'); // default sort

    //     // Pisahkan field dan arah sort (misal: 'judul_desc' → ['judul', 'desc'])
    //     [$sortField, $sortDirection] = explode('_', $sort) + ['no_induk', 'asc'];

    //     if (!in_array($sortDirection, ['asc', 'desc'])) {
    //         $sortDirection = 'asc';
    //     }

    //     $query = Eksemplar::with('inventori')
    //         ->join('inventori', 'eksemplar.id_inventori', '=', 'inventori.id')
    //         ->when($search, function ($q) use ($search) {
    //             $q->where(function ($subQuery) use ($search) {
    //                 $subQuery->where('inventori.judul_buku', 'like', "%{$search}%")
    //                          ->orWhere('inventori.pengarang', 'like', "%{$search}%");
    //             });
    //         })
    //         ->when($category !== 'all', function ($q) use ($category) {
    //             $q->where('eksemplar.id_kategori_buku', $category);
    //         });

    //     // Urutan berdasarkan pilihan sort
    //     switch ($sortField) {
    //         case 'judul':
    //             $query->orderBy('inventori.judul_buku', $sortDirection);
    //             break;

    //         case 'no_induk':
    //             $query->orderByRaw("CAST(eksemplar.no_induk AS UNSIGNED) {$sortDirection}");
    //             break;

    //         case 'created_at':
    //             $query->orderBy('eksemplar.created_at', $sortDirection);
    //             break;

    //         default:
    //             $query->orderByRaw("CAST(eksemplar.no_induk AS UNSIGNED) asc"); // default fallback
    //             break;
    //     }

    //     // Ambil data
    //     $eksemplar = $query->select('eksemplar.*')
    //         ->paginate(10)
    //         ->appends([
    //             'search' => $search,
    //             'category' => $category,
    //             'sort' => $sort,
    //         ]);

    //     return view('admin.eksemplar.index', compact('eksemplar', 'activeMenu', 'search', 'category', 'sort'));
    // }

    public function index(Request $request)
    {
        $activeMenu = "eksemplar";

        $search = $request->input('search');
        $category = $request->input('category', 'all');
        $sort = $request->input('sort', 'no_induk_asc');
        $tanggal = $request->input('tanggal'); // Tambahan filter tanggal

        // Pisahkan sort menjadi field dan direction
        [$sortField, $sortDirection] = explode('_', $sort) + [null, null];
        $sortField = $sortField ?? 'no_induk';
        $sortDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'asc';

        $query = \App\Models\Eksemplar::with('inventori')
            ->join('inventori', 'eksemplar.id_inventori', '=', 'inventori.id')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('inventori.judul_buku', 'like', "%{$search}%")
                        ->orWhere('inventori.pengarang', 'like', "%{$search}%");
                });
            })
            ->when($category !== 'all', function ($q) use ($category) {
                $q->where('eksemplar.id_kategori_buku', $category);
            })
            ->when($tanggal, function ($q) use ($tanggal) {
                $q->whereDate('eksemplar.created_at', $tanggal);
            });

        // Sorting
        switch ($sortField) {
            case 'judul':
                $query->orderBy('inventori.judul_buku', $sortDirection);
                break;

            case 'no_induk':
                $query->orderByRaw("CAST(eksemplar.no_induk AS UNSIGNED) $sortDirection");
                break;

            case 'created_at':
                $query->orderBy('eksemplar.created_at', $sortDirection);
                break;

            default:
                $query->orderByRaw("CAST(eksemplar.no_induk AS UNSIGNED) asc");
                break;
        }

        $eksemplar = $query->select('eksemplar.*')
            ->paginate(10)
            ->appends([
                'search' => $search,
                'category' => $category,
                'sort' => $sort,
                'tanggal' => $tanggal,
            ]);

        return view('admin.eksemplar.index', compact(
            'eksemplar',
            'activeMenu',
            'search',
            'category',
            'sort',
            'tanggal'
        ));
    }

    // public function index(Request $request)
    // {
    //     $activeMenu = "eksemplar";

    //     $search = $request->input('search');
    //     $category = $request->input('category', 'all');
    //     $sort = $request->input('sort', 'judul_asc');

    //     [$sortField, $sortDirection] = explode('_', $sort) + ['judul', 'asc'];

    //     if (!in_array($sortDirection, ['asc', 'desc'])) {
    //         $sortDirection = 'asc';
    //     }

    //     $query = Eksemplar::with('inventori')
    //         ->join('inventori', 'eksemplar.id_inventori', '=', 'inventori.id')
    //         ->when($search, function ($q) use ($search) {
    //             $q->where(function ($subQuery) use ($search) {
    //                 $subQuery->where('inventori.judul_buku', 'like', "%{$search}%")
    //                     ->orWhere('inventori.pengarang', 'like', "%{$search}%");
    //             });
    //         })
    //         ->when($category !== 'all', function ($q) use ($category) {
    //             $q->where('eksemplar.id_kategori_buku', $category);
    //         });

    //     switch ($sortField) {
    //         case 'judul':
    //             $query->orderBy('inventori.judul_buku', $sortDirection);
    //             break;

    //         case 'no_induk':
    //             $query->orderByRaw("CAST(eksemplar.no_induk AS UNSIGNED) {$sortDirection}");
    //             break;

    //         default:
    //             $query->orderBy('inventori.judul_buku', 'asc');
    //             break;
    //     }

    //     $eksemplar = $query->select('eksemplar.*')
    //         ->paginate(10)
    //         ->appends([
    //             'search' => $search,
    //             'category' => $category,
    //             'sort' => $sort,
    //         ]);

    //     return view('admin.eksemplar.index', compact('eksemplar', 'activeMenu', 'search', 'category', 'sort'));
    // }





    public function cetakBarcode($id)
    {
        $eksemplar = Eksemplar::with('inventori')->findOrFail($id);

        // Tandai sebagai sudah dicetak
        $eksemplar->update(['sudah_dicetak' => true]);

        return view('admin.eksemplar.cetak-barcode', compact('eksemplar'));
    }


    // public function cetakBatch(Request $request)
    // {
    //     $ids = $request->input('selected', []);
    //     $kosongAwal = (int) $request->input('kosong_awal', 0);
    //     $start = (int) $request->input('start_induk');
    //     $end = (int) $request->input('end_induk');

    //     $eksemplarList = collect();

    //     // Pilihan via checkbox
    //     if (!empty($ids)) {
    //         $eksemplarList = Eksemplar::with('inventori.katalog')
    //             ->whereIn('id', $ids)
    //             ->orderBy('no_induk', 'desc')
    //             ->get();

    //         //Tandai sudah dicetak
    //         Eksemplar::whereIn('id', $ids)->update(['sudah_dicetak' => true]);
    //     } elseif ($start && $end && $end >= $start) {
    //         $take = $end - $start + 1;

    //         $eksemplarList = Eksemplar::with('inventori.katalog')
    //             ->orderBy('no_induk', 'desc')
    //             ->orderBy('created_at', 'desc')
    //             ->skip($start - 1)
    //             ->take($take)
    //             ->get();

    //         // Ambil ID untuk update cetak
    //         $updateIds = $eksemplarList->pluck('id')->toArray();
    //         Eksemplar::whereIn('id', $updateIds)->update(['sudah_dicetak' => true]);
    //     } else {
    //         return back()->with('error', 'Pilih data lewat checkbox atau isi rentang No. Induk.');
    //     }

    //     return view('admin.eksemplar.cetak-batch-barcode', compact('eksemplarList', 'kosongAwal'));
    // }


    public function cetakBatch(Request $request)
    {
        $ids        = $request->input('selected', []);
        $kosongAwal = (int) $request->input('kosong_awal', 0);
        $startRow   = (int) $request->input('start_row');
        $endRow     = (int) $request->input('end_row');
        $search     = $request->input('search');
        $category   = $request->input('category', 'all');
        $tanggal    = $request->input('tanggal');
        $sort       = $request->input('sort', 'no_induk_asc');
    
        [$sortField, $sortDirection] = explode('_', $sort) + ['no_induk', 'asc'];
        $sortDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'asc';
    
        $eksemplarList = collect();
    
        if (!empty($ids)) {
            // Mode checkbox
            $eksemplarList = Eksemplar::with('inventori.katalog')
                ->whereIn('id', $ids)
                ->orderBy('created_at', 'asc')
                ->get();
    
            Eksemplar::whereIn('id', $ids)->update(['sudah_dicetak' => true]);
        } elseif ($startRow && $endRow && $endRow >= $startRow) {
            $take   = $endRow - $startRow + 1;
            $offset = $startRow - 1;
    
            if ($take > 500) {
                return back()->with('error', 'Maksimal hanya bisa mencetak 500 baris dalam sekali proses.');
            }
    
            // Query sesuai filter index
            $query = Eksemplar::with('inventori.katalog')
                ->join('inventori', 'eksemplar.id_inventori', '=', 'inventori.id')
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($sub) use ($search) {
                        $sub->where('inventori.judul_buku', 'like', "%{$search}%")
                            ->orWhere('inventori.pengarang', 'like', "%{$search}%");
                    });
                })
                ->when($category !== 'all', function ($q) use ($category) {
                    $q->where('eksemplar.id_kategori_buku', $category);
                })
                ->when($tanggal, function ($q) use ($tanggal) {
                    $q->whereDate('eksemplar.created_at', $tanggal);
                });
    
            // Sorting sama persis dengan index
            switch ($sortField) {
                case 'judul':
                    $query->orderBy('inventori.judul_buku', $sortDirection);
                    break;
                case 'no_induk':
                    $query->orderByRaw("CAST(eksemplar.no_induk AS UNSIGNED) {$sortDirection}");
                    break;
                case 'created_at':
                    $query->orderBy('eksemplar.created_at', $sortDirection);
                    break;
                default:
                    $query->orderByRaw("CAST(eksemplar.no_induk AS UNSIGNED) asc");
            }
    
            // ✅ Solusi untuk offset besar
            $page = (int) ceil($startRow / $take);
            $blockSize = $take + 50; // buffer
    
            $eksemplarBlock = $query->select('eksemplar.*')
                ->forPage($page, $blockSize)
                ->get();
    
            if ($eksemplarBlock->isEmpty()) {
                return back()->with('error', 'Rentang baris tidak ditemukan.');
            }
    
            // Slice supaya tepat sesuai startRow–endRow
            $startIndex = ($startRow - 1) % $blockSize;
            $eksemplarList = $eksemplarBlock->slice($startIndex, $take)->values();
    
            Eksemplar::whereIn('id', $eksemplarList->pluck('id'))
                ->update(['sudah_dicetak' => true]);
        } else {
            return back()->with('error', 'Pilih data lewat checkbox atau isi rentang baris.');
        }
    
        return view('admin.eksemplar.cetak-batch-barcode', compact('eksemplarList', 'kosongAwal'));
    }
    













    public function edit($id)
    {
        $activeMenu = "inventori";
        $eksemplar = Eksemplar::findOrFail($id);
        return view('admin.eksemplar.edit', [
            'activeMenu' => $activeMenu,
            'eksemplar' => $eksemplar
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:tersedia,dipinjam,rusak,hilang',
        ]);

        $eksemplar = Eksemplar::findOrFail($id);
        $eksemplar->update(['status' => $request->status]);

        return redirect()
            ->route('admin.inventori.show', $eksemplar->id_inventori)
            ->with('success', 'Status eksemplar berhasil diperbarui.');
    }

    public function indexBuku(Request $request)
    {
        /* ---------- Parameter URL ---------- */
        $activeMenu = 'statusbuku';
        $search     = $request->input('search');
        $status     = $request->input('status', 'all');   // dropdown

        /* ---------- Query dasar (tanpa status) ---------- */
        $baseQuery = Eksemplar::with('inventori')
            ->orderByDesc('no_induk')
            ->orderByDesc('created_at');

        // terapkan pencarian judul / pengarang / no_induk
        if ($search) {
            $baseQuery->where(function ($q) use ($search) {
                $q->whereHas('inventori', function ($sub) use ($search) {
                    $sub->where('judul_buku', 'like', "%{$search}%")
                        ->orWhere('pengarang',  'like', "%{$search}%");
                })
                    ->orWhere('no_induk', 'like', "%{$search}%");
            });
        }

        /* ---------- Hitung per‑status (masih pakai filter search) ---------- */
        $tersediaCount = (clone $baseQuery)->where('status', 'tersedia')->count();
        $dipinjamCount = (clone $baseQuery)->where('status', 'dipinjam')->count();
        $rusakCount    = (clone $baseQuery)->where('status', 'rusak')->count();
        $hilangCount   = (clone $baseQuery)->where('status', 'hilang')->count();

        /* ---------- Daftar eksemplar untuk tabel ---------- */
        $listQuery = clone $baseQuery;              // kloning lagi untuk listing
        if ($status !== 'all') {
            $listQuery->where('status', $status);
        }
        $eksemplar = $listQuery->paginate(10)->appends([
            'search' => $search,
            'status' => $status,
        ]);

        /* ---------- Kirim ke view ---------- */
        return view('admin.eksemplar.buku', [
            'eksemplar'      => $eksemplar,
            'activeMenu'     => $activeMenu,
            'search'         => $search,
            'status'         => $status,
            'tersediaCount'  => $tersediaCount,
            'dipinjamCount'  => $dipinjamCount,
            'rusakCount'     => $rusakCount,
            'hilangCount'    => $hilangCount,
        ]);
    }
}
