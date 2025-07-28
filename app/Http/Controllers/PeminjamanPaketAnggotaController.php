<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Anggota;
use App\Models\PaketBuku;
use App\Models\PeminjamanPaket;
use App\Models\DetailPeminjamanPaket;

class PeminjamanPaketAnggotaController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = 'peminjaman';

        $user     = Auth::user();
        $search   = $request->input('search');
        $category = $request->input('category', 'all');

        /* ──────────────────────────────────────────────
     | 1.  Query dasar
     ──────────────────────────────────────────────*/
        $query = DetailPeminjamanPaket::query()
            ->join(
                'peminjaman_paket',
                'detail_peminjaman_paket.peminjaman_id',
                '=',
                'peminjaman_paket.id'
            )
            ->with([
                'paketBuku',                       // relasi ke PaketBuku
                'peminjamanPaket.anggota.user',    // eager‑load anggota & user
            ])
            ->when($user->role === 'anggota', function ($q) use ($user) {
                $q->whereHas('peminjamanPaket.anggota', function ($qq) use ($user) {
                    $qq->where('user_id', $user->id);
                });
            })
            ->whereIn('peminjaman_paket.status', ['menunggu', 'berhasil', 'selesai']);

        /* ──────────────────────────────────────────────
     | 2.  Filter pencarian
     ──────────────────────────────────────────────*/
        if ($search) {
            $query->whereHas('peminjamanPaket.anggota.kelas', function ($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%");
            })
                ->orWhereHas('paketBuku', function ($q) use ($search) {
                    $q->where('judul_paket', 'like', "%{$search}%");
                });
        }

        /* ──────────────────────────────────────────────
     | 3.  Filter kategori kelas (opsional)
     ──────────────────────────────────────────────*/
        if ($category !== 'all' && preg_match('/(\d+)\s+(IPA|IPS|Bahasa)/i', $category, $m)) {
            $query->whereHas('peminjamanPaket.anggota.kelas', function ($q) use ($m) {
                $q->where('nama_kelas', 'like', $m[1] . ' ' . $m[2] . '%');
            });
        }

        /* ──────────────────────────────────────────────
     | 4.  Urutkan: menunggu → berhasil → terbaru
     ──────────────────────────────────────────────*/
        $query->orderByRaw("CASE WHEN peminjaman_paket.status = 'menunggu' THEN 1 ELSE 2 END")
            ->orderByDesc('peminjaman_paket.created_at')
            ->select('detail_peminjaman_paket.*');

        /* ──────────────────────────────────────────────
     | 5.  Paginate & kirim ke view
     ──────────────────────────────────────────────*/
        $peminjaman = $query->paginate(10)->appends([
            'search'   => $search,
            'category' => $category,
        ]);

        return view('anggota.peminjaman-paket.index', compact(
            'activeMenu',
            'peminjaman',
            'category',
            'search'
        ));
    }




    public function store(Request $request)
    {
        /* ───────────────────────────────────────────────
     | 1. Tentukan role & ambil anggota_id           |
     ───────────────────────────────────────────────*/
        $user      = Auth::user();
        $role      = $user->role;                // 'anggota', 'pustakawan', 'admin', …
        $isAnggota = $role === 'anggota';

        // Jika login‑nya anggota → pakai relasi miliknya
        if ($isAnggota) {
            $anggotaId = $user->anggota->id ?? null;  // relasi hasOne User→Anggota
            $request->merge(['anggota_id' => $anggotaId]);   // kunci agar tidak bisa di‑inject
        }

        /* ───────────────────────────────────────────────
     | 2. Validasi input                             |
     ───────────────────────────────────────────────*/
        $validated = $request->validate([
            'anggota_id' => 'required|exists:anggota,id',
            'paket_id'   => 'required|exists:paket_buku,id',
        ]);

        /* ───────────────────────────────────────────────
     | 3. Validasi logika bisnis                     |
     ───────────────────────────────────────────────*/
        $anggota = Anggota::find($validated['anggota_id']);
        $paket   = PaketBuku::find($validated['paket_id']);

        // a) Cek status anggota
        if (!$anggota || $anggota->status !== 'aktif') {
            return back()->withInput()
                ->with('warning', 'Anggota tidak aktif dan tidak dapat melakukan peminjaman.');
        }

        // b) Satu pinjam aktif per anggota
        $masihAktif = PeminjamanPaket::where('anggota_id', $anggota->id)
            ->whereIn('status', ['menunggu', 'berhasil'])
            ->exists();
        if ($masihAktif) {
            return back()->withInput()
                ->with('warning', 'Anggota masih memiliki peminjaman paket yang aktif.');
        }

        // c) Cek stok paket
        if ($paket->stok_tersedia < 1) {
            return back()->withInput()
                ->with('warning', 'Stok paket tidak tersedia.');
        }

        /* ───────────────────────────────────────────────
     | 4. Tentukan status & user_id                  |
     ───────────────────────────────────────────────*/
        $status  = $isAnggota ? 'menunggu' : 'berhasil';   // anggota → menunggu, petugas → berhasil
        $userId  = $isAnggota ? null        : $user->id;   // user_id NULL kalau anggota

        /* ───────────────────────────────────────────────
     | 5. Simpan dalam transaksi DB                  |
     ───────────────────────────────────────────────*/
        DB::beginTransaction();
        try {
            // Master: peminjaman_paket
            $peminjaman = PeminjamanPaket::create([
                'anggota_id' => $anggota->id,
                'user_id'    => $userId,
                'status'     => $status,
            ]);

            // Detail: detail_peminjaman_paket
            DetailPeminjamanPaket::create([
                'peminjaman_id' => $peminjaman->id,
                'paket_id'      => $paket->id,
            ]);

            // Kurangi stok paket
            $paket->decrement('stok_tersedia');

            DB::commit();

            /* ───────────────────────────────────────────
         | 6. Redirect & flash message               |
         ───────────────────────────────────────────*/
            $route = $isAnggota ? 'anggota.peminjaman-paket.index'
                : 'admin.peminjaman-paket.index';

            return redirect()->route($route)
                ->with('success', 'Permintaan peminjaman paket berhasil disimpan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal memproses peminjaman: ' . $th->getMessage());
        }
    }

    
}
