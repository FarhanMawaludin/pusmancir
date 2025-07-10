<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JenisSumber;

class JenisSumberController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = "master";

        $search = $request->input('search');
        $category = $request->input('category', 'all');

        $query = JenisSumber::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($category !== 'all') {
            $query->where('role', $category);
        }

        $jenis_sumber = $query->paginate(10)->appends([
            'search' => $search,
            'category' => $category
        ]);

        return view('admin.jenis-sumber.index', [
            'activeMenu' => $activeMenu,
            'jenis_sumber' => $jenis_sumber,
            'category' => $category,
            'search' => $search,
        ]);
    }

    public function create()
    {
        $activeMenu = "master";
        return view('admin.jenis-sumber.create', [
            'activeMenu' => $activeMenu,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_sumber' => 'required',
        ]);

        // Cek duplikat (case-insensitive)
        $exists = JenisSumber::whereRaw('LOWER(nama_sumber) = ?', [strtolower($request->nama_sumber)])->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['nama_sumber' => 'Jenis sumber sudah ada.']);
        }

        JenisSumber::create([
            'nama_sumber' => strtoupper($request->nama_sumber),
        ]);

        return redirect()->route('admin.jenis-sumber.index')
            ->with('success', 'Jenis Sumber berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $activeMenu = "master";
        $jenis_sumber = JenisSumber::findOrFail($id);
        return view('admin.jenis-sumber.edit', [
            'activeMenu' => $activeMenu,
            'jenis_sumber' => $jenis_sumber,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_sumber' => 'required',
        ]);

        // Cek duplikat (case-insensitive)
        $exists = JenisSumber::whereRaw('LOWER(nama_sumber) = ?', [strtolower($request->nama_sumber)])->where('id', '!=', $id)->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['nama_sumber' => 'Jenis sumber sudah ada.']);
        }

        $jenis_sumber = JenisSumber::findOrFail($id);
        $jenis_sumber->update([
            'nama_sumber' => strtoupper($request->nama_sumber),
        ]);

        return redirect()->route('admin.jenis-sumber.index')
            ->with('success', 'Jenis Sumber berhasil diperbarui.');
    }

    public function destroy($id)
    {
        JenisSumber::findOrFail($id)->delete();
        return redirect()->route('admin.jenis-sumber.index')
            ->with('success', 'Jenis Sumber berhasil dihapus.');
    }
}
