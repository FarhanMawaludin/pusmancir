<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratMasuk;
use Illuminate\Support\Facades\Storage;

class SuratMasukController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = "surat-masuk";

        $search = $request->input('search');
        $category = $request->input('category', 'asal_surat');

        $query = SuratMasuk::query();

        if ($search && in_array($category, ['asal_surat', 'perihal', 'tanggal_terima'])) {
            if ($category === 'tanggal_terima') {
                try {
                    $tanggal = \Carbon\Carbon::parse($search)->format('Y-m-d');
                    $query->whereDate('tanggal_terima', $tanggal);
                } catch (\Exception $e) {
                    // abaikan error
                }
            } else {
                $query->where($category, 'like', "%{$search}%");
            }
        }

        $suratMasuk = $query->latest()->paginate(10)->appends([
            'search' => $search,
            'category' => $category
        ]);

        return view('admin.surat-masuk.index', compact('suratMasuk', 'search', 'category', 'activeMenu'));
    }

    public function create()
    {
        $activeMenu = "surat-masuk";
        return view('admin.surat-masuk.create', compact('activeMenu'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_surat' => 'required|string|max:255',
            'tanggal_terima' => 'required|date',
            'asal_surat' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'lampiran' => 'nullable|integer',
            'file_surat' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        if ($request->hasFile('file_surat')) {
            $validated['file_surat'] = $request->file('file_surat')->store('surat-masuk');
        }

        SuratMasuk::create($validated);

        return redirect()->route('admin.surat-masuk.index')->with('success', 'Surat masuk berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $activeMenu = "surat-masuk";
        $suratMasuk = SuratMasuk::findOrFail($id);
        return view('admin.surat-masuk.edit', compact('suratMasuk', 'activeMenu'));
    }

    public function update(Request $request, $id)
    {
        $surat = SuratMasuk::findOrFail($id);

        $validated = $request->validate([
            'nomor_surat' => 'required|string|max:255',
            'tanggal_terima' => 'required|date',
            'asal_surat' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'lampiran' => 'nullable|integer',
            'file_surat' => 'nullable|file|mimes:pdf|max:2048',
            'status' => 'required|in:Diterima,Didisposisikan,Selesai'
        ]);

        if ($request->hasFile('file_surat')) {
            if ($surat->file_surat && Storage::exists($surat->file_surat)) {
                Storage::delete($surat->file_surat);
            }

            $validated['file_surat'] = $request->file('file_surat')->store('surat-masuk');
        }

        $surat->update($validated);

        return redirect()->route('admin.surat-masuk.index')->with('success', 'Data surat masuk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $surat = SuratMasuk::findOrFail($id);
        if ($surat->file_surat && Storage::exists($surat->file_surat)) {
            Storage::delete($surat->file_surat);
        }
        $surat->delete();
        return redirect()->route('admin.surat-masuk.index')->with('success', 'Data surat masuk berhasil dihapus.');
    }

    public function show($id)
    {
        $activeMenu = "surat-masuk";
        $surat = SuratMasuk::findOrFail($id);
        return view('admin.surat-masuk.show', compact('surat', 'activeMenu'));
    }
}
