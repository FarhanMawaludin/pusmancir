<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penerbit;

class PenerbitController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = "master";

        $search = $request->input('search');
        $category = $request->input('category', 'all');

        $query = Penerbit::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_penerbit', 'like', "%{$search}%");
            });
        }

        if ($category !== 'all') {
            $query->where('role', $category);
        }

        $penerbit = $query->paginate(10)->appends([
            'search' => $search,
            'category' => $category
        ]);

        return view('admin.penerbit.index', [
            'activeMenu' => $activeMenu,
            'penerbit' => $penerbit,
            'category' => $category,
            'search' => $search,
        ]);
    }

    public function create()
    {
        $activeMenu = "master";
        return view('admin.penerbit.create', [
            'activeMenu' => $activeMenu
        ]);
    }

    public function edit($id)
    {
        $activeMenu = "master";
        $penerbit = Penerbit::findOrFail($id);
        return view('admin.penerbit.edit', [
            'activeMenu' => $activeMenu,
            'penerbit' => $penerbit
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_penerbit' => 'required',
        ]);

        // Cek duplikat (case-insensitive)
        $exists = Penerbit::whereRaw('LOWER(nama_penerbit) = ?', [strtolower($request->nama_penerbit)])->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['nama_penerbit' => 'Penerbit sudah ada.']);
        }

        Penerbit::create([
            'nama_penerbit' => strtoupper($request->nama_penerbit),
        ]);

        return redirect()->route('admin.penerbit.index')
            ->with('success', 'Penerbit berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_penerbit' => 'required',
        ]);

        // Cek duplikat (case-insensitive)
        $exists = Penerbit::whereRaw('LOWER(nama_penerbit) = ?', [strtolower($request->nama_penerbit)])->where('id', '!=', $id)->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['nama_penerbit' => 'Penerbit sudah ada.']);
        }

        $penerbit = Penerbit::findOrFail($id);
        $penerbit->update([
            'nama_penerbit' => strtoupper($request->nama_penerbit),
        ]);

        return redirect()->route('admin.penerbit.index')
            ->with('success', 'Penerbit berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $penerbit = Penerbit::findOrFail($id);
        $penerbit->delete();
        return redirect()->route('admin.penerbit.index')
            ->with('success', 'Penerbit berhasil dihapus.');
    }
}
