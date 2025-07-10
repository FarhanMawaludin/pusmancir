<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriBuku;

class KategoriBukuController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = "master";

        $search = $request->input('search');
        $category = $request->input('category', 'all');

        $query = KategoriBuku::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        if ($category !== 'all') {
            $query->where('role', $category);
        }

        $kategori_buku = $query->paginate(10)->appends([
            'search' => $search,
            'category' => $category
        ]);

        return view('admin.kategori-buku.index', [
            'activeMenu' => $activeMenu,
            'kategori_buku' => $kategori_buku,
            'category' => $category,
            'search' => $search,
        ]);
    }

    public function create()
    {
        $activeMenu = "master";
        return view('admin.kategori-buku.create', [
            'activeMenu' => $activeMenu
        ]);
    }

    public function edit($id)
    {
        $activeMenu = "master";
        $kategori_buku = KategoriBuku::findOrFail($id);
        return view('admin.kategori-buku.edit', [
            'activeMenu' => $activeMenu,
            'kategori_buku' => $kategori_buku
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required',
        ]);

        // Cek duplikat (case-insensitive)
        $exists = KategoriBuku::whereRaw('LOWER(nama_kategori) = ?', [strtolower($request->nama_kategori)])->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['nama_kategori' => 'Kategori sudah ada.']);
        }

        KategoriBuku::create([
            'nama_kategori' => strtoupper($request->nama_kategori),
        ]);

        return redirect()->route('admin.kategori-buku.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required',
        ]);

        // Cek duplikat (case-insensitive)
        $exists = KategoriBuku::whereRaw('LOWER(nama_kategori) = ?', [strtolower($request->nama_kategori)])->where('id', '!=', $id)->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['nama_kategori' => 'Kategori sudah ada.']);
        }

        $kategori_buku = KategoriBuku::findOrFail($id);
        $kategori_buku->update([
            'nama_kategori' => strtoupper($request->nama_kategori),
        ]);

        return redirect()->route('admin.kategori-buku.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        KategoriBuku::findOrFail($id)->delete();
        return redirect()->route('admin.kategori-buku.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
