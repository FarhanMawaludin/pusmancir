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

        // Jika user memilih via checkbox
        if (!empty($ids)) {
            $eksemplarList = Eksemplar::with('inventori')
                ->whereIn('id', $ids)
                ->orderBy('no_induk', 'desc') // urutkan tetap agar konsisten
                ->get();
        }
        // Jika user memilih berdasarkan urutan tampilan (no_induk terbesar di atas)
        elseif ($start && $end && $end >= $start) {
            $take = $end - $start + 1;

            $eksemplarList = Eksemplar::with('inventori')
                ->orderBy('no_induk', 'desc')
                ->orderBy('created_at', 'desc') // fallback urutan jika no_induk sama
                ->skip($start - 1)
                ->take($take)
                ->get();
        }
        // Jika tidak memilih apapun
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

    
}
