<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetailPeminjamanPaket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengembalianPaketController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = 'peminjamanPaket';

        $search   = $request->input('search');
        $category = $request->input('category', 'all');

        /* ──────────────────────────────────────────
         | 1.  Query dasar
         ──────────────────────────────────────────*/
        $query = DetailPeminjamanPaket::query()
            ->select('detail_peminjaman_paket.*')                // fokus kolom detail
            ->join('peminjaman_paket', 'detail_peminjaman_paket.peminjaman_id', '=', 'peminjaman_paket.id')
            ->with([
                'peminjamanPaket.anggota.user',                  // peminjam
                'paketBuku',                                     // info paket
            ])
            ->whereIn('peminjaman_paket.status', ['berhasil', 'selesai'])  // hanya yang masih dipinjam
            ->orderByRaw("CASE WHEN peminjaman_paket.status = 'berhasil' THEN 0 ELSE 1 END")  // urutkan berdasarkan status, berhasil dulu
            ->orderByDesc('peminjaman_paket.created_at');  // kemudian urutkan berdasarkan tanggal pembuatan

        /* ──────────────────────────────────────────
         | 2.  Filter pencarian NISN atau Nama
         ──────────────────────────────────────────*/
        if ($search) {
            $query->whereHas('peminjamanPaket.anggota', function ($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        /* ──────────────────────────────────────────
         | 3.  Filter kategori kelas
         ──────────────────────────────────────────*/
        if ($category !== 'all' && preg_match('/(\d+)\s*(IPA|IPS|Bahasa)/i', $category, $m)) {
            $query->whereHas('peminjamanPaket.anggota.kelas', function ($q) use ($m) {
                $q->where('nama_kelas', 'like', "{$m[1]} {$m[2]}%");
            });
        }

        /* ──────────────────────────────────────────
         | 4.  Urutan – terbaru dipinjam tampil atas
         |     (ganti sesuai kolom due date bila ada)
         ──────────────────────────────────────────*/
        $query->orderByDesc('peminjaman_paket.created_at');

        $limit = $request->input('limit', 10);
        $perPage = ($limit === 'all') ? 999999 : (int) $limit;

        /* ──────────────────────────────────────────
         | 5.  Paginate & kirim ke view
         ──────────────────────────────────────────*/
        $pengembalian = $query->paginate($perPage)->appends([
            'search'   => $search,
            'category' => $category,
            'limit'    => $limit,
        ]);

        return view('admin.pengembalian-paket.index', [
            'activeMenu'   => $activeMenu,
            'pengembalian' => $pengembalian,
            'category'     => $category,
            'search'       => $search,
            'limit'        => $limit,
        ]);
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'exists:detail_peminjaman_paket,id',
        ], [
            'ids.required' => 'Pilih minimal satu data pengembalian.',
        ]);

        $detailIds = $validated['ids'];
        $details = DetailPeminjamanPaket::with('peminjamanPaket', 'paketBuku')
            ->whereIn('id', $detailIds)
            ->get();

        DB::beginTransaction();
        try {
            $count = 0;
            foreach ($details as $detail) {
                if ($detail->peminjamanPaket && $detail->peminjamanPaket->status === 'berhasil') {
                    $detail->peminjamanPaket->update([
                        'status'  => 'selesai',
                        'user_id' => Auth::id(),
                    ]);
                    if ($detail->paketBuku) {
                        $detail->paketBuku->increment('stok_tersedia');
                    }
                    $count++;
                }
            }

            DB::commit();
            return back()->with('success', "Berhasil menerima pengembalian $count pengembalian paket.");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pengembalian: ' . $th->getMessage());
        }
    }

    public function update(Request $request, $detailId)
    {
        $detail = DetailPeminjamanPaket::with('peminjamanPaket', 'paketBuku')->findOrFail($detailId);

        // pastikan memang masih 'berhasil'
        if ($detail->peminjamanPaket->status !== 'berhasil') {
            return back()->with('warning', 'Transaksi sudah diproses.');
        }

        DB::transaction(function () use ($detail) {
            // 1. ubah status peminjaman → selesai
            $detail->peminjamanPaket()->update([
                'status'  => 'selesai',
                'user_id' => Auth::id(),               // petugas yg menerima
            ]);

            // 2. kembalikan stok paket
            $detail->paketBuku()->increment('stok_tersedia');
        });

        return back()->with('success', 'Pengembalian paket berhasil diproses.');
    }
}
