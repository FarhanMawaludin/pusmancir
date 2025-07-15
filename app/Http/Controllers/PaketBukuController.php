<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaketBuku;

class PaketBukuController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = "katalog";

        $search = $request->input('search');
        $category = $request->input('category', 'all');

        $query = PaketBuku::query();
        
        // with(['eksemplar' => function ($q) {
        //     $q->orderBy('created_at'); // urutan eksemplar
        // }, 'penerbit'])
        //     ->orderBy('created_at', 'desc');

        if ($search) {
            if ($category === 'judul_buku' || $category === 'all') {
                $query->where('judul_buku', 'like', "%{$search}%");
            }

            if ($category === 'tanggal_pembelian' || $category === 'all') {
                $query->orWhere('tanggal_pembelian', 'like', "%{$search}%");
            }
        }

        // misal id_kategori_buku sesuai kebutuhan, sesuaikan kalau perlu
        if ($category !== 'all' && in_array($category, ['judul_buku', 'tanggal_pembelian']) === false) {
            $query->where('id_kategori_buku', $category);
        }

        $paket = $query->paginate(10)->appends([
            'search' => $search,
            'category' => $category,
        ]);

        return view('admin.paket.index', compact('paket', 'activeMenu', 'search', 'category'));
    }

    public function create()
    {
        $activeMenu = "katalog";
        return view('admin.paket.create', compact('activeMenu'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_paket'    => ['required', 'string', 'max:255'],
            'deskripsi'     => ['nullable', 'string'],
            'stok_total'    => ['required', 'integer', 'min:0'],
            'stok_tersedia' => ['nullable', 'integer', 'min:0', 'lte:stok_total'],
        ]);

        // Jika stok_tersedia belum diisi, samakan dengan stok_total
        $data['stok_tersedia'] = $data['stok_tersedia'] ?? $data['stok_total'];

        PaketBuku::create($data);

        return redirect()
            ->route('admin.paket.index')
            ->with('success', 'Paket buku berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $activeMenu = "katalog";
        $paket = PaketBuku::findOrFail($id);
        return view('admin.paket.edit', compact('activeMenu', 'paket'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nama_paket'    => ['required', 'string', 'max:255'],
            'deskripsi'     => ['nullable', 'string'],
            'stok_total'    => ['required', 'integer', 'min:0'],
            'stok_tersedia' => ['nullable', 'integer', 'min:0', 'lte:stok_total'],
        ]);

        // Jika stok_tersedia belum diisi, samakan dengan stok_total
        $data['stok_tersedia'] = $data['stok_tersedia'] ?? $data['stok_total'];

        $paket = PaketBuku::findOrFail($id);
        $paket->update($data);

        return redirect()
            ->route('admin.paket.index')
            ->with('success', 'Paket buku berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $paket = PaketBuku::findOrFail($id);
            $paket->delete();
            return redirect()->route('admin.paket.index')
                ->with('success', 'Paket buku berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus paket buku.');
        }
    }
}
