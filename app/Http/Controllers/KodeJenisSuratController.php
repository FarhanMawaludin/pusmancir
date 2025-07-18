<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KodeJenisSurat;
use Illuminate\Validation\Rule;

class KodeJenisSuratController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = "master-surat";

        $search = $request->input('search');

        $query = KodeJenisSurat::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('nama_surat', 'like', "%{$search}%");
            });
        }

        $jenis_surat = $query->paginate(10)->appends([
            'search' => $search
        ]);

        return view('admin.kode-jenis-surat.index', [
            'activeMenu' => $activeMenu,
            'jenis_surat' => $jenis_surat,
            'search' => $search,
        ]);
    }

    public function create()
    {
        $activeMenu = "master-surat";
        return view('admin.kode-jenis-surat.create', [
            'activeMenu' => $activeMenu,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|numeric|min:10|max:99|unique:kode_jenis_surat,kode',
            'nama_surat' => 'required',
        ]);

        KodeJenisSurat::create([
            'kode' => $request->kode,
            'nama_surat' => $request->nama_surat,
        ]);

        return redirect()->route('admin.kode-jenis-surat.index')
            ->with('success', 'Kode Jenis Surat berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $activeMenu = "master-surat";
        $jenis_surat = KodeJenisSurat::findOrFail($id);
        return view('admin.kode-jenis-surat.edit', [
            'activeMenu' => $activeMenu,
            'jenis_surat' => $jenis_surat,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => [
                'required',
                'numeric',
                'min:0',
                'max:99',
                Rule::unique('kode_jenis_surat', 'kode')->ignore($id),
            ],
            'nama_surat' => 'required',
        ],[
            'kode.unique' => 'Kode Jenis Surat sudah ada.',
            'kode.ignore' => 'Kode Jenis Surat sudah ada.',
            'kode.required' => 'Kode Jenis Surat harus diisi.',
            'kode.numeric' => 'Kode Jenis Surat harus berupa angka.',
            'kode.max' => 'Kode Jenis Surat harus maksimal 99 angka.',


        ]);

        $jenis_surat = KodeJenisSurat::findOrFail($id);
        $jenis_surat->update([
            'kode' => $request->kode,
            'nama_surat' => $request->nama_surat,
        ]);

        return redirect()->route('admin.kode-jenis-surat.index')
            ->with('success', 'Kode Jenis Surat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        KodeJenisSurat::findOrFail($id)->delete();
        return redirect()->route('admin.kode-jenis-surat.index')
            ->with('success', 'Kode Jenis Surat berhasil dihapus.');
    }
}
