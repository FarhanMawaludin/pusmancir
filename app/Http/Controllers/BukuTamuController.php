<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BukuTamu;
use App\Models\Anggota;

class BukuTamuController extends Controller
{
    public function create()
    {
        $activeMenu = 'buku-tamu';
        return view('admin.buku-tamu.form', compact('activeMenu'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nisn' => 'nullable|string',
        ]);

        $anggota = Anggota::where('nisn', $request->nisn)->first();

        if ($anggota) {
            // Anggota: keperluan opsional
            BukuTamu::create([
                'anggota_id' => $anggota->id,
                'nisn' => $request->nisn ?? null,
                'nama' => $anggota->user->name, // asumsikan user relasi ada dan ada nama
                'asal_instansi' => null,
                'keperluan' => $request->keperluan ?? null,
            ]);

            return redirect()->back()->with('success', 'Kunjungan anggota berhasil dicatat.');
        } else {
            // Non anggota wajib isi nama, asal_instansi, dan keperluan
            $request->validate([
                'nama' => 'required|string',
                'asal_instansi' => 'required|string',
                'keperluan' => 'required|string',
            ], [
                'nama.required' => 'Nama wajib diisi untuk non anggota.',
                'asal_instansi.required' => 'Asal instansi wajib diisi untuk non anggota.',
                'keperluan.required' => 'Keperluan wajib diisi untuk non anggota.',
            ]);

            BukuTamu::create([
                'anggota_id' => null,
                'nisn' => $request->nisn,
                'nama' => $request->nama,
                'asal_instansi' => $request->asal_instansi,
                'keperluan' => $request->keperluan,
            ]);

            return redirect()->back()->with('success', 'Kunjungan tamu berhasil dicatat.');
        }
    }

    public function LogTamu(Request $request)
    {
        $activeMenu = 'buku-tamu';

        // Ambil tanggal dari request, default hari ini
        $tanggal = $request->input('tanggal', now()->toDateString());

        // Ambil data buku tamu berdasarkan tanggal
        $bukuTamu = BukuTamu::whereDate('created_at', $tanggal)->get();

        return view('admin.buku-tamu.log-tamu', compact('bukuTamu', 'activeMenu', 'tanggal'));
    }
}
