<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Eksemplar;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;

class EksemplarController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = "eksemplar";

        $search = $request->input('search');
        $category = $request->input('category', 'all');

        $query = Eksemplar::with('inventori')
            ->orderBy('no_induk', 'desc')
            ->orderBy('created_at', 'desc');


        if ($search) {
            $query->whereHas('inventori', function ($q) use ($search) {
                $q->where('judul_buku', 'like', "%{$search}%")
                    ->orWhere('pengarang', 'like', "%{$search}%");
            });
        }

        if ($category !== 'all') {
            $query->where('id_kategori_buku', $category);
        }

        $eksemplar = $query->paginate(10)->appends([
            'search' => $search,
            'category' => $category,
        ]);

        return view('admin.eksemplar.index', compact('eksemplar', 'activeMenu', 'search', 'category'));
    }

    public function cetakBarcode($id)
    {
        $eksemplar = Eksemplar::with('inventori')->findOrFail($id);
        return view('admin.eksemplar.cetak-barcode', compact('eksemplar'));
    }


    public function cetakBatch(Request $request)
    {
        $ids = $request->input('selected', []);
        $kosongAwal = (int) $request->input('kosong_awal', 0);
        $start = (int) $request->input('start_induk');
        $end = (int) $request->input('end_induk');

        $eksemplarList = collect();

        // Pilihan via checkbox
        if (!empty($ids)) {
            $eksemplarList = Eksemplar::with('inventori.katalog')
                ->whereIn('id', $ids)
                ->orderBy('no_induk', 'desc')
                ->get();

            //Tandai sudah dicetak
            Eksemplar::whereIn('id', $ids)->update(['sudah_dicetak' => true]);
        }

        elseif ($start && $end && $end >= $start) {
            $take = $end - $start + 1;

            $eksemplarList = Eksemplar::with('inventori.katalog')
                ->orderBy('no_induk', 'desc')
                ->orderBy('created_at', 'desc')
                ->skip($start - 1)
                ->take($take)
                ->get();

            // Ambil ID untuk update cetak
            $updateIds = $eksemplarList->pluck('id')->toArray();
            Eksemplar::whereIn('id', $updateIds)->update(['sudah_dicetak' => true]);
        }

        else {
            return back()->with('error', 'Pilih data lewat checkbox atau isi rentang No. Induk.');
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
