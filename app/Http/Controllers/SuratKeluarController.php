<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratKeluar;
use App\Models\KodeJenisSurat;
use App\Models\Instansi;
use Illuminate\Support\Facades\Storage;



class SuratKeluarController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = "surat-keluar";

        $search = $request->input('search');
        $category = $request->input('category', 'tujuan_surat');

        $query = SuratKeluar::with(['kodeJenisSurat', 'instansi']);

        if ($search && in_array($category, ['tujuan_surat', 'perihal', 'tanggal_keluar'])) {
            if ($category === 'tanggal_keluar') {
                // Khusus filter tanggal, parse dengan Carbon
                try {
                    $tanggal = \Carbon\Carbon::parse($search)->format('Y-m-d');
                    $query->whereDate('tanggal_keluar', $tanggal);
                } catch (\Exception $e) {
                    // Abaikan error parsing
                }
            } else {
                $query->where($category, 'like', "%{$search}%");
            }
        }

        $suratKeluar = $query->latest()->paginate(10)->appends([
            'search' => $search,
            'category' => $category
        ]);

        return view('admin.surat-keluar.index', [
            'activeMenu' => $activeMenu,
            'suratKeluar' => $suratKeluar,
            'search' => $search,
            'category' => $category,
        ]);
    }

    public function create()
    {
        $activeMenu = "surat-keluar";
        $kodeJenisList = KodeJenisSurat::all();
        $instansiList = Instansi::all();

        // Ambil nomor urut terakhir
        $lastNomorUrut = SuratKeluar::max('nomor_urut') ?? 0;
        $nextNomorUrut = $lastNomorUrut + 1;

        return view('admin.surat-keluar.create', [
            'activeMenu' => $activeMenu,
            'kodeJenisList' => $kodeJenisList,
            'instansiList' => $instansiList,
            'nextNomorUrut' => $nextNomorUrut,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_urut'    => ['required', 'integer'],
            'tanggal_keluar' => ['required', 'date'],
            'tujuan_surat'  => ['required', 'string', 'max:255'],
            'perihal'       => ['required', 'string', 'max:255'],
            'kode_jenis_id' => ['required', 'exists:kode_jenis_surat,id'],
            'instansi_id'   => ['required', 'exists:instansi,id'],
            'isi_surat'     => ['required', 'string'],
            'file_surat'    => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:2048'],
        ]);

        if ($request->hasFile('file_surat')) {
            $validated['file_surat'] = $request->file('file_surat')->store('surat-keluar', 'public');
        }

        SuratKeluar::create($validated);

        return redirect()->route('admin.surat-keluar.index')
            ->with('success', 'Surat keluar berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $activeMenu = "surat-keluar";
        $surat = SuratKeluar::findOrFail($id);
        $kodeJenisList = KodeJenisSurat::all();
        $instansiList = Instansi::all();

        return view('admin.surat-keluar.edit', [
            'activeMenu' => $activeMenu,
            'surat' => $surat,
            'kodeJenisList' => $kodeJenisList,
            'instansiList' => $instansiList
        ]);
    }

    public function update(Request $request, $id)
    {
        $surat = SuratKeluar::findOrFail($id);

        $validated = $request->validate([
            'nomor_urut'    => ['required', 'integer'],
            'tanggal_keluar' => ['required', 'date'],
            'tujuan_surat'  => ['required', 'string', 'max:255'],
            'perihal'       => ['required', 'string', 'max:255'],
            'kode_jenis_id' => ['required', 'exists:kode_jenis_surat,id'],
            'instansi_id'   => ['required', 'exists:instansi,id'],
            'isi_surat'     => ['required', 'string'],
            'file_surat'    => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:2048'],
        ]);

        if ($request->hasFile('file_surat')) {
            // Hapus file lama jika ada
            if ($surat->file_surat && Storage::disk('public')->exists($surat->file_surat)) {
                Storage::disk('public')->delete($surat->file_surat);
            }

            // Simpan file baru di storage/app/public/surat-keluar
            $validated['file_surat'] = $request->file('file_surat')->store('surat-keluar', 'public');
        }

        $surat->update($validated);

        return redirect()->route('admin.surat-keluar.index')
            ->with('success', 'Data surat keluar berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $surat = SuratKeluar::findOrFail($id);
        if ($surat->file_surat && Storage::exists($surat->file_surat)) {
            Storage::delete($surat->file_surat);
        }
        $surat->delete();
        return redirect()->route('admin.surat-keluar.index')->with('success', 'Data surat keluar berhasil dihapus.');
    }

    public function show($id)
    {
        $activeMenu = "surat-keluar";
        $surat = SuratKeluar::findOrFail($id);
        return view('admin.surat-keluar.show', compact('surat', 'activeMenu'));
    }
}
