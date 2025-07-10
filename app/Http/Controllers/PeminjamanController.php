<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\Eksemplar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = "peminjaman";

        $search = $request->input('search');
        $category = $request->input('category', 'all');

        $query = DetailPeminjaman::with(['eksemplar', 'peminjaman.anggota.user'])
            ->whereHas('eksemplar', function ($q) {
                $q->where('status', 'dipinjam');
            });

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

        $peminjaman = $query->paginate(10)->appends([
            'search' => $search,
            'category' => $category
        ]);

        return view('admin.peminjaman.index', [
            'activeMenu' => $activeMenu,
            'peminjaman' => $peminjaman,
            'category' => $category,
            'search' => $search,
        ]);
    }


    public function store(Request $request)
    {
        // Validasi: cek apakah NISN ada di kolom `nisn` pada tabel anggota
        $request->validate([
            'anggota_id' => 'required|exists:anggota,nisn',
            'eksemplar_id' => 'required|string|exists:eksemplar,no_rfid',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
        ]);

        DB::beginTransaction();
        try {
            // Ambil ID anggota berdasarkan NISN
            $anggota = \App\Models\Anggota::where('nisn', $request->anggota_id)->first();

            if (!$anggota) {
                throw new \Exception("Anggota dengan NISN {$request->anggota_id} tidak ditemukan.");
            }

            // Simpan ke tabel peminjaman dengan ID anggota
            $peminjaman = \App\Models\Peminjaman::create([
                'anggota_id' => $anggota->id, // ← gunakan id, bukan nisn
                'user_id' => Auth::id(),
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tanggal_kembali' => $request->tanggal_kembali,
            ]);

            // Ambil eksemplar berdasarkan no_rfid
            $noRfid = trim($request->eksemplar_id);
            $eksemplar = \App\Models\Eksemplar::where('no_rfid', $noRfid)->first();

            if (!$eksemplar) {
                throw new \Exception("Eksemplar dengan RFID '{$noRfid}' tidak ditemukan.");
            }

            // Simpan ke detail_peminjaman
            \App\Models\DetailPeminjaman::create([
                'peminjaman_id' => $peminjaman->id,
                'eksemplar_id' => $eksemplar->id,
            ]);

            // Update status eksemplar
            $eksemplar->update(['status' => 'dipinjam']);

            DB::commit();

            return redirect()->route('admin.peminjaman.index')
                ->with('success', 'Data peminjaman berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors([
                'error' => 'Gagal menyimpan data: ' . $e->getMessage()
            ]);
        }
    }





    //     public function store(Request $request)
    // {
    //     $noRfid = trim($request->eksemplar_id); // bukan [0]
    //     $eksemplar = \App\Models\Eksemplar::where('no_rfid', $noRfid)->first();

    //     dd([
    //         'auth_user_id'     => Auth::id(),
    //         'anggota_id'       => $request->anggota_id,
    //         'tanggal_pinjam'   => $request->tanggal_pinjam,
    //         'tanggal_kembali' => $request->tanggal_kembali,
    //         'rfid_dikirim'     => $noRfid,
    //         'eksemplar_found'  => $eksemplar ? $eksemplar->id : 'Not Found',
    //     ]);
    // }
}
