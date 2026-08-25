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

    public function copy(Request $request)
    {
        $request->validate([
            'from_class' => 'required|in:X,XI IPA,XI IPS,XII IPA,XII IPS',
            'to_class' => 'required|in:X,XI IPA,XI IPS,XII IPA,XII IPS',
        ]);

        $fromClass = $request->from_class;
        $toClass = $request->to_class;

        if ($fromClass === $toClass) {
            return back()->with('error', 'Kelas asal dan kelas tujuan tidak boleh sama.');
        }

        // Get all books in source class
        $sourceBooks = BukuPaketMapel::where('tingkat_kelas', $fromClass)->get();

        if ($sourceBooks->isEmpty()) {
            return back()->with('error', "Tidak ada data buku di Kelas $fromClass untuk disalin.");
        }

        // Get existing books in destination class to avoid duplication
        $existingBooks = BukuPaketMapel::where('tingkat_kelas', $toClass)
            ->pluck('nama_buku')
            ->map(fn($item) => strtolower(trim($item)))
            ->toArray();

        $copiedCount = 0;
        foreach ($sourceBooks as $book) {
            $cleanedName = strtolower(trim($book->nama_buku));
            if (!in_array($cleanedName, $existingBooks)) {
                BukuPaketMapel::create([
                    'nama_buku' => $book->nama_buku,
                    'tingkat_kelas' => $toClass,
                ]);
                $copiedCount++;
            }
        }

        return redirect()->route('admin.buku-paket-mapel.index', ['tingkat_kelas' => $toClass])
            ->with('success', "$copiedCount buku berhasil disalin dari Kelas $fromClass ke Kelas $toClass.");
    }
}
