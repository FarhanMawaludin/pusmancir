<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Katalog;
use Illuminate\Support\Facades\Storage;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = 'katalog';

        $search = $request->input('search');
        $category = $request->input('category', 'judul'); // default 'judul'

        $query = Katalog::with('inventori')->orderBy('created_at', 'desc');

        if ($search && $category !== 'all') {
            $query->where(function ($q) use ($search, $category) {
                switch ($category) {
                    case 'judul':
                        $q->where('judul_buku', 'like', "%{$search}%");
                        break;
                    case 'penerbit':
                        $q->where('penerbit', 'like', "%{$search}%");
                        break;
                    case 'pengarang':
                        $q->where('pengarang', 'like', "%{$search}%");
                        break;
                    case 'kategori':
                        $q->where('kategori', 'like', "%{$search}%");
                        break;
                    case 'isbn':
                        $q->where('isbn', 'like', "%{$search}%");
                        break;
                }
            });
        }

        $katalog = $query->paginate(10)->appends([
            'search' => $search,
            'category' => $category
        ]);

        return view('admin.katalog.index', compact('activeMenu', 'katalog', 'search', 'category'));
    }



    public function edit($id)
    {
        $katalog = Katalog::with('inventori')->findOrFail($id);
        $activeMenu = "katalog";

        return view('admin.katalog.edit', compact('katalog', 'activeMenu'));
    }

    /**
     * Simpan perubahan data katalog.
     */
    public function update(Request $request, $id)
    {
        $katalog = Katalog::findOrFail($id);

        $validated = $request->validate([
            'isbn'            => 'nullable|string|max:255',
            'ringkasan_buku'  => 'nullable|string',
            'kode_ddc'        => 'nullable|string|max:100',
            'no_panggil'      => 'nullable|string|max:100',
            'cover_buku'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Handle upload cover jika diunggah
        if ($request->hasFile('cover_buku')) {
            // Hapus file lama jika ada
            if ($katalog->cover_buku && Storage::exists('public/' . $katalog->cover_buku)) {
                Storage::delete('public/' . $katalog->cover_buku);
            }

            // Simpan file baru
            $validated['cover_buku'] = $request->file('cover_buku')->store('cover_buku', 'public');
        }

        // Update data katalog
        $katalog->update($validated);

        return redirect()->route('admin.katalog.index')
            ->with('success', 'Data katalog berhasil diperbarui.');
    }
}
