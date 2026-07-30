<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BukuPaketMapel;

class BukuPaketMapelController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = 'antrianPaket';
        $query = BukuPaketMapel::query();
        
        if ($request->tingkat_kelas) {
            $query->where('tingkat_kelas', $request->tingkat_kelas);
        }
        
        $tingkatKelas = $request->tingkat_kelas;
        $bukuList = $query->paginate(20);

        return view('admin.buku-paket-mapel.index', compact('activeMenu', 'bukuList', 'tingkatKelas'));
    }

    public function create()
    {
        $activeMenu = 'antrianPaket';
        return view('admin.buku-paket-mapel.create', compact('activeMenu'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_buku' => 'required|string|max:255',
            'tingkat_kelas' => 'required|in:X,XI IPA,XI IPS,XII IPA,XII IPS',
        ]);

        BukuPaketMapel::create($request->all());

        return redirect()->route('admin.buku-paket-mapel.index')->with('success', 'Buku Paket berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $activeMenu = 'antrianPaket';
        $buku = BukuPaketMapel::findOrFail($id);
        return view('admin.buku-paket-mapel.edit', compact('activeMenu', 'buku'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_buku' => 'required|string|max:255',
            'tingkat_kelas' => 'required|in:X,XI IPA,XI IPS,XII IPA,XII IPS',
        ]);

        $buku = BukuPaketMapel::findOrFail($id);
        $buku->update($request->all());

        return redirect()->route('admin.buku-paket-mapel.index')->with('success', 'Buku Paket berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $buku = BukuPaketMapel::findOrFail($id);
        $buku->delete();

        return redirect()->route('admin.buku-paket-mapel.index')->with('success', 'Buku Paket berhasil dihapus.');
    }
}
