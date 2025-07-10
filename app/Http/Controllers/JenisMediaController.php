<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JenisMedia;

class JenisMediaController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = "master";

        $search = $request->input('search');
        $category = $request->input('category', 'all');

        $query = JenisMedia::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($category !== 'all') {
            $query->where('role', $category);
        }

        $jenis_media = $query->paginate(10)->appends([
            'search' => $search,
            'category' => $category
        ]);

        return view('admin.jenis-media.index', [
            'activeMenu' => $activeMenu,
            'jenis_media' => $jenis_media,
            'category' => $category,
            'search' => $search,
        ]);
    }

    public function create()
    {
        $activeMenu = "master";
        return view('admin.jenis-media.create', [
            'activeMenu' => $activeMenu,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jenis_media' => 'required',
        ]);

        // Cek duplikat (case-insensitive)
        $exists = JenisMedia::whereRaw('LOWER(nama_jenis_media) = ?', [strtolower($request->nama_jenis_media)])->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['nama_jenis_media' => 'Jenis media sudah ada.']);
        }

        JenisMedia::create([
            'nama_jenis_media' => strtoupper($request->nama_jenis_media),
        ]);

        return redirect()->route('admin.jenis-media.index')
            ->with('success', 'Jenis Media berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $activeMenu = "master";
        $jenis_media = JenisMedia::findOrFail($id);
        return view('admin.jenis-media.edit', [
            'activeMenu' => $activeMenu,
            'jenis_media' => $jenis_media,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_jenis_media' => 'required',
        ]);

        // Cek duplikat (case-insensitive)
        $exists = JenisMedia::whereRaw('LOWER(nama_jenis_media) = ?', [strtolower($request->nama_jenis_media)])->where('id', '!=', $id)->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['nama_jenis_media' => 'Jenis media sudah ada.']);
        }

        $jenis_media = JenisMedia::findOrFail($id);
        $jenis_media->update([
            'nama_jenis_media' => strtoupper($request->nama_jenis_media),
        ]);

        return redirect()->route('admin.jenis-media.index')
            ->with('success', 'Jenis Media berhasil diperbarui.');
    }

    public function destroy($id)
    {
        JenisMedia::findOrFail($id)->delete();
        return redirect()->route('admin.jenis-media.index')
            ->with('success', 'Jenis Media berhasil dihapus.');
    }
}
