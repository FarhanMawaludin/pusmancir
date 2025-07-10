<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = "kelas";

        $search = $request->input('search');
        $category = $request->input('category', 'all');

        $query = Kelas::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%");
            });
        }

        if ($category !== 'all') {
            preg_match('/(\d+) (IPA|IPS|Bahasa)/', $category, $match);
            if ($match) {
                $query->where('nama_kelas', 'like', $match[1] . ' ' . $match[2] . '%');
            }
        }

        $kelas = $query->paginate(10)->appends([
            'search' => $search,
            'category' => $category
        ]);

        return view('admin.kelas.index', [
            'activeMenu' => $activeMenu,
            'kelas' => $kelas,
            'category' => $category,
            'search' => $search,
        ]);
    }

    public function create()
    {
        $activeMenu = "kelas";
        return view('admin.kelas.create', [
            'activeMenu' => $activeMenu
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required',
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
        ]);

        $kelas = Kelas::firstOrCreate([
            'nama_kelas' => strtoupper($request->nama_kelas)
        ]);

        if ($kelas->wasRecentlyCreated) {
            return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil ditambahkan!');
        } else {
            return redirect()->route('admin.kelas.index')->with('error', 'Kelas sudah ada!');
        }
    }


    public function edit($id)
    {
        $activeMenu = 'kelas';
        $kelas = Kelas::findOrFail($id);
        return view('admin.kelas.edit', ['activeMenu' => $activeMenu, 'kelas' => $kelas]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kelas' => 'required',
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
        ]);

        $kelas = Kelas::findOrFail($id);
        $data = array_map('strtoupper', $request->all());
        $kelas->update($data);
        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil diubah');
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();
        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil dihapus');
    }
}
