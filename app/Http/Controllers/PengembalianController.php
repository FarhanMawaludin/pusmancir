<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetailPeminjaman;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;



class PengembalianController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = "pengembalian";

        $search = $request->input('search');
        $category = $request->input('category', 'all');

        // Mulai query dengan join ke tabel peminjaman supaya bisa order by tanggal_kembali
        $query = \App\Models\DetailPeminjaman::query()
            ->select('detail_peminjaman.*') // penting supaya query tetap ambil kolom detail_peminjaman
            ->join('peminjaman', 'detail_peminjaman.peminjaman_id', '=', 'peminjaman.id')
            ->with(['peminjaman.anggota.user', 'eksemplar.inventori'])
            ->whereHas('eksemplar', function ($query) {
                $query->where('status', 'dipinjam');
            });

        // Filter search NISN anggota
        if ($search) {
            $query->whereHas('peminjaman.anggota', function ($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%");
            });
        }

        // Filter kategori kelas
        if ($category !== 'all') {
            preg_match('/(\d+) (IPA|IPS|Bahasa)/', $category, $match);
            if ($match) {
                $query->where('nama_kelas', 'like', "{$match[1]} {$match[2]}%");
            }
        }

        // Urutkan: data yang tanggal_kembali sudah lewat (terlambat) muncul dulu, lalu urut tanggal_kembali ascending
        $query->orderByRaw("CASE WHEN peminjaman.tanggal_kembali < NOW() THEN 0 ELSE 1 END")
            ->orderBy('peminjaman.tanggal_kembali', 'asc');

        $pengembalian = $query->paginate(10)->appends([
            'search' => $search,
            'category' => $category,
        ]);

        return view('admin.pengembalian.index', [
            'activeMenu' => $activeMenu,
            'pengembalian' => $pengembalian,
            'category' => $category,
            'search' => $search,
        ]);
    }



    public function update($id)
    {
        DB::beginTransaction();

        try {
            // Cari detail peminjaman berdasarkan ID
            $detail = \App\Models\DetailPeminjaman::findOrFail($id);

            // Ambil eksemplar terkait
            $eksemplar = $detail->eksemplar;

            if (!$eksemplar) {
                throw new \Exception("Eksemplar tidak ditemukan.");
            }

            // Update status eksemplar ke tersedia
            $eksemplar->update(['status' => 'tersedia']);

            // Update tanggal_kembali_asli di detail peminjaman ke tanggal hari ini
            $detail->tanggal_kembali_asli = now();

            // Update user_id yang mengubah (misal user yang sedang login)
            $detail->user_id = Auth::id();

            // Simpan perubahan detail
            $detail->save();

            DB::commit();

            return redirect()->route('admin.pengembalian.index')
                ->with('success', 'Status eksemplar dan tanggal pengembalian berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal mengupdate status: ' . $e->getMessage()]);
        }
    }
}
