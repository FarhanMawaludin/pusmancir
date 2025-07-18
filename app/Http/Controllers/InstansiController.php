<?php

namespace App\Http\Controllers;

use App\Models\Instansi;
use Illuminate\Http\Request;

class InstansiController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = "master-surat";

        $search = $request->input('search');

        $query = Instansi::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('nama_lengkap', 'like', "%{$search}%");
            });
        }

        $instansi = $query->paginate(10)->appends([
            'search' => $search
        ]);

        return view('admin.instansi.index', [
            'activeMenu' => $activeMenu,
            'instansi' => $instansi,
            'search' => $search,
        ]);
    }

    public function create()
    {
        $activeMenu = "master-surat";
        return view('admin.instansi.create', [
            'activeMenu' => $activeMenu,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:instansi,kode',
            'nama_lengkap' => 'required',
        ]);

        Instansi::create([
            'kode' => $request->kode,
            'nama_lengkap' => $request->nama_lengkap,
        ]);

        return redirect()->route('admin.instansi.index')
            ->with('success', 'Instansi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $activeMenu = "master-surat";
        $instansi = Instansi::findOrFail($id);
        return view('admin.instansi.edit', [
            'activeMenu' => $activeMenu,
            'instansi' => $instansi,
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required|unique:instansi,kode,' . $id,
            'nama_lengkap' => 'required',
        ]);

        $instansi = Instansi::findOrFail($id);
        $instansi->update([
            'kode' => $request->kode,
            'nama_lengkap' => $request->nama_lengkap,
        ]);

        return redirect()->route('admin.instansi.index')
            ->with('success', 'Instansi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Instansi::findOrFail($id)->delete();
        return redirect()->route('admin.instansi.index')
            ->with('success', 'Instansi berhasil dihapus.');
    }
}
