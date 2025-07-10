<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sumber;

class SumberController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = "master";

        $search = $request->input('search');
        $category = $request->input('category', 'all');

        $query = Sumber::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        if ($category !== 'all') {
            $query->where('role', $category);
        }

        $sumber = $query->paginate(10)->appends([
            'search' => $search,
            'category' => $category
        ]);

        return view('admin.sumber.index', [
            'activeMenu' => $activeMenu,
            'sumber' => $sumber,
            'category' => $category,
            'search' => $search,
        ]);
    }

    public function create()
    {
        $activeMenu = "master";
        return view('admin.sumber.create', [
            'activeMenu' => $activeMenu
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
        ]);

        // Cek duplikat (case-insensitive)
        $exists = Sumber::whereRaw('LOWER(nama) = ?', [strtolower($request->nama)])->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['nama' => 'Sumber sudah ada.']);
        }

        Sumber::create([
            'nama' => strtoupper($request->nama),
        ]);

        return redirect()->route('admin.sumber.index')
            ->with('success', 'Sumber berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $activeMenu = "master";
        $sumber = Sumber::findOrFail($id);
        return view('admin.sumber.edit', [
            'activeMenu' => $activeMenu,
            'sumber' => $sumber
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
        ]);

        // Cek duplikat (case-insensitive)
        $exists = Sumber::whereRaw('LOWER(nama) = ?', [strtolower($request->nama)])->where('id', '!=', $id)->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['nama' => 'Sumber sudah ada.']);
        }

        $sumber = Sumber::findOrFail($id);
        $sumber->update([
            'nama' => strtoupper($request->nama),
        ]);

        return redirect()->route('admin.sumber.index')
            ->with('success', 'Sumber berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Sumber::findOrFail($id)->delete();
        return redirect()->route('admin.sumber.index')
            ->with('success', 'Sumber berhasil dihapus.');
    }
}
