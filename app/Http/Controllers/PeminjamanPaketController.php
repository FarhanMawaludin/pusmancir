<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetailPeminjamanPaket;
use App\Models\PeminjamanPaket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PeminjamanPaketController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = "peminjamanPaket";

        $search = $request->input('search');
        $category = $request->input('category', 'all');

        $query = DetailPeminjamanPaket::with(['paketBuku', 'peminjamanPaket.anggota.user'])
            ->whereHas('peminjamanPaket', function ($q) use ($search) {
                $q->where('status', 'menunggu');

                // Filter berdasarkan NISN jika ada pencarian
                if (!empty($search)) {
                    $q->whereHas('anggota', function ($q2) use ($search) {
                        $q2->where('nisn', 'like', "%{$search}%");
                    });
                }
            });

        // Filter kategori kelas jika ada
        if ($category !== 'all') {
            $query->whereHas('peminjaman.anggota.kelas', function ($q) use ($category) {
                preg_match('/(\d+) (IPA|IPS|Bahasa)/', $category, $match);
                if ($match) {
                    $kelas = $match[1] . ' ' . $match[2];
                    $q->where('nama_kelas', 'like', $kelas . '%');
                }
            });
        }

        $peminjamanPaket = $query->paginate(10)->appends([
            'search' => $search,
            'category' => $category
        ]);

        return view('admin.peminjaman-paket.index', [
            'activeMenu' => $activeMenu,
            'peminjamanPaket' => $peminjamanPaket,
            'category' => $category,
            'search' => $search,
        ]);
    }


    public function updateStatus(Request $request, $id)
    {
        // 1. Validasi aksi
        $validated = $request->validate([
            'aksi' => 'required|in:berhasil,tolak',
        ]);
        $aksiBaru = $validated['aksi'];     // 'berhasil' | 'tolak'

        // 2. Ambil peminjaman beserta detail & paket
        $peminjaman = PeminjamanPaket::with('detailPeminjamanPaket.paketBuku')
            ->findOrFail($id);

        // 3. Cek sudah diproses?
        if ($peminjaman->status !== 'menunggu') {
            return back()->with('warning', 'Peminjaman sudah diproses sebelumnya.');
        }

        DB::beginTransaction();
        try {
            // 4. Update status & petugas yang memproses
            $peminjaman->update([
                'status'  => $aksiBaru,
                'user_id' => Auth::id(),     // petugas login
            ]);

            // 5. Jika ditolak, kembalikan stok
            if ($aksiBaru === 'tolak') {
                foreach ($peminjaman->detailPeminjamanPaket as $detail) {
                    $detail->paketBuku->increment('stok_tersedia');
                }
            }

            DB::commit();
            return back()->with('success', 'Status peminjaman berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui status: ' . $th->getMessage());
        }
    }
}
