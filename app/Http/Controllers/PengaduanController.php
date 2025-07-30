<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengaduan;

class PengaduanController extends Controller
{

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

        Pengaduan::create($validated);

        return redirect()->route('pengaduan')->with('success', 'Pengaduan berhasil dikirim.');
    }
}
