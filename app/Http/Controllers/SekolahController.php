<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sekolah;

class SekolahController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = "master";

        $search = $request->input('search');
        $category = $request->input('category', 'all');

        $query = Sekolah::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        if ($category !== 'all') {
            $query->where('role', $category);
        }

        $sekolah = $query->paginate(10)->appends([
            'search' => $search,
            'category' => $category
        ]);

        return view('admin.sekolah.index', [
            'activeMenu' => $activeMenu,
            'sekolah' => $sekolah,
            'category' => $category,
            'search' => $search,
        ]);
    }

    public function create()
    {
        $activeMenu = "master";
        return view('admin.sekolah.create', [
            'activeMenu' => $activeMenu
        ]);
    }

    public function edit($id)
    {
        $activeMenu = "master";
        $sekolah = Sekolah::findOrFail($id);
        return view('admin.sekolah.edit', [
            'activeMenu' => $activeMenu,
            'sekolah' => $sekolah
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required',
            'kode_sekolah' => 'required',
        ]);

        // Cek duplikat (case-insensitive)
        $exists = Sekolah::whereRaw(
            'LOWER(nama_sekolah) = ? AND LOWER(kode_sekolah) = ?',
            [strtolower($request->nama_sekolah), strtolower($request->kode_sekolah)]
        )->exists();


        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['nama' => 'Sekolah sudah ada.', 'kode_sekolah' => 'Kode Sekolah sudah ada.']);
        }

        Sekolah::create([
            'nama_sekolah' => strtoupper($request->nama_sekolah),
            'kode_sekolah' => strtoupper($request->kode_sekolah),
        ]);

        return redirect()->route('admin.sekolah.index')
            ->with('success', 'Sekolah berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_sekolah' => 'required',
            'kode_sekolah' => 'required',
        ]);

        // Cek duplikat (case-insensitive), dan abaikan data saat ini (by id)
        $exists = Sekolah::whereRaw(
            'LOWER(nama_sekolah) = ? AND LOWER(kode_sekolah) = ?',
            [strtolower($request->nama_sekolah), strtolower($request->kode_sekolah)]
        )
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'nama_sekolah' => 'Sekolah dengan nama dan kode ini sudah ada.'
                ]);
        }

        // Update data
        $sekolah = Sekolah::findOrFail($id);
        $sekolah->update([
            'nama_sekolah' => strtoupper($request->nama_sekolah),
            'kode_sekolah' => strtoupper($request->kode_sekolah),
        ]);

        return redirect()->route('admin.sekolah.index')
            ->with('success', 'Sekolah berhasil diperbarui.');
    }


    public function destroy($id)
    {
        $sekolah = Sekolah::findOrFail($id);
        $sekolah->delete();
        return redirect()->route('admin.sekolah.index')
            ->with('success', 'Sekolah berhasil dihapus.');
    }
}
