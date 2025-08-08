<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengaduan;

class PengaduanController extends Controller
{
    public function index()
    {
        $activeMenu = 'pengaduan';
        $pengaduans = Pengaduan::orderBy('created_at', 'desc')->paginate(10);  // paginate 10
        return view('admin.pengaduan.index', compact('activeMenu', 'pengaduans'));
    }

    public function create()
    {
        return view('pengaduan'); // view untuk form pengaduan
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'nullable|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'isi' => 'nullable|string',
        ]);

        // Tambahkan status default
        $validated['status'] = 'belum dibaca';

        Pengaduan::create($validated);

        return redirect()->route('pengaduan')->with('success', 'Pengaduan berhasil dikirim.');
    }

    public function markAsRead($id)
    {
        $pengaduan = Pengaduan::findOrFail($id);
        $pengaduan->status = 'telah dibaca';
        $pengaduan->save();

        return back()->with('success', 'Pengaduan ditandai sebagai telah dibaca.');
    }





    public function show($id)
    {
        $activeMenu = 'pengaduan';
        $pengaduan = Pengaduan::findOrFail($id);
        return view('admin.pengaduan.show', compact('pengaduan', 'activeMenu'));
    }
}
