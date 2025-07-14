<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Anggota;
use App\Models\Eksemplar;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;

class PeminjamanAnggotaController extends Controller
{

    public function index(Request $request)
    {
        $activeMenu = 'peminjaman';
        $user       = Auth::user();
        $search     = $request->input('search');
        $category   = $request->input('category', 'all');

        /* ─────────────────────────────────────────
       1.  Query dasar
    ───────────────────────────────────────── */
        $query = DetailPeminjaman::query()
            ->join('peminjaman', 'detail_peminjaman.peminjaman_id', '=', 'peminjaman.id')
            ->with(['eksemplar', 'peminjaman.anggota.user'])
            ->whereHas('peminjaman.anggota', function ($q) use ($user) {
                $q->where('user_id', $user->id);          // hanya data milik user login
            })
            ->whereIn('peminjaman.status', ['menunggu', 'berhasil'])
            ->orderByDesc('peminjaman.created_at');

        /* ─────────────────────────────────────────
       2.  Filter pencarian (opsional)
    ───────────────────────────────────────── */
        if ($search) {
            $query->whereHas('peminjaman.anggota.kelas', function ($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%");
            });
        }

        /* ─────────────────────────────────────────
       3.  Filter kategori kelas (opsional)
    ───────────────────────────────────────── */
        if ($category !== 'all') {
            if (preg_match('/(\d+) (IPA|IPS|Bahasa)/', $category, $m)) {
                $query->whereHas('peminjaman.anggota.kelas', function ($q) use ($m) {
                    $q->where('nama_kelas', 'like', $m[1] . ' ' . $m[2] . '%');
                });
            }
        }

        /* ─────────────────────────────────────────
       4.  Urutan: menunggu → berhasil, lalu terbaru
    ───────────────────────────────────────── */
        $query->orderByRaw("FIELD(peminjaman.status, 'menunggu', 'berhasil')")
            ->orderByDesc('peminjaman.created_at')
            // supaya select * berasal dari detail_peminjaman
            ->select('detail_peminjaman.*');

        /* ─────────────────────────────────────────
       5.  Paginate
    ───────────────────────────────────────── */
        $peminjaman = $query->paginate(10)->appends([
            'search'   => $search,
            'category' => $category,
        ]);

        return view('anggota.peminjaman.index', [
            'activeMenu' => $activeMenu,
            'peminjaman' => $peminjaman,
            'category'   => $category,
            'search'     => $search,
        ]);
    }



    public function store(Request $request)
    {
        /* ─────────────────────────────────────────────
       1.  Jika user login ber‑role “anggota”, 
           pakai NISN miliknya → agar tidak bisa dimanipulasi
    ───────────────────────────────────────────── */
        if (Auth::user()->role === 'anggota') {
            $anggotaNisn = Auth::user()->anggota->nisn ?? null;   // relasi hasOne User→Anggota
            $request->merge(['anggota_id' => $anggotaNisn]);
        }

        /* ─────────────────────────────────────────────
       2.  Validasi request
    ───────────────────────────────────────────── */
        $validated = $request->validate([
            'anggota_id'      => 'required|exists:anggota,nisn',
            'eksemplar_id'    => 'required|string|exists:eksemplar,no_rfid',
            'tanggal_pinjam'  => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
        ]);

        /* ─────────────────────────────────────────────
       3.  Lookup Anggota & Eksemplar + validasi logika
    ───────────────────────────────────────────── */
        $anggota   = Anggota::where('nisn', $validated['anggota_id'])->first();
        $eksemplar = Eksemplar::where('no_rfid', trim($validated['eksemplar_id']))->first();

        if (!$anggota || !$eksemplar) {
            return back()->withInput()
                ->with('error', 'Anggota atau Eksemplar tidak ditemukan.');
        }

        if ($eksemplar->status === 'dipinjam') {
            return back()->withInput()
                ->with('warning', "Buku dengan RFID '{$validated['eksemplar_id']}' sedang dipinjam.");
        }

        /* ─────────────────────────────────────────────
       4.  Tentukan status awal & user_id petugas
           - admin/pustakawan → status=berhasil, user_id=ID petugas
           - anggota          → status=menunggu, user_id=null
    ───────────────────────────────────────────── */
        $role          = Auth::user()->role;
        $status        = in_array($role, ['admin', 'pustakawan']) ? 'berhasil' : 'menunggu';
        $userIdPetugas = in_array($role, ['admin', 'pustakawan']) ? Auth::id() : null;

        /* ─────────────────────────────────────────────
       5.  Transaksi Database
    ───────────────────────────────────────────── */
        DB::beginTransaction();
        try {
            // Insert peminjaman
            $peminjaman = Peminjaman::create([
                'anggota_id'      => $anggota->id,
                'user_id'         => $userIdPetugas,
                'tanggal_pinjam'  => $validated['tanggal_pinjam'],
                'tanggal_kembali' => $validated['tanggal_kembali'],
                'status'          => $status,
            ]);

            // Insert detail peminjaman
            DetailPeminjaman::create([
                'peminjaman_id' => $peminjaman->id,
                'eksemplar_id'  => $eksemplar->id,
            ]);

            // Jika langsung “berhasil” → tandai eksemplar dipinjam
            if ($status === 'berhasil') {
                $eksemplar->update(['status' => 'dipinjam']);
            }

            DB::commit();

            /* ─────────────────────────────────────────
           6.  Redirect sesuai role + flash message
        ───────────────────────────────────────── */
            $redirectRoute = $role === 'anggota'
                ? 'anggota.peminjaman.index'
                : 'admin.peminjaman.index';

            return redirect()->route($redirectRoute)
                ->with('success', 'Data peminjaman berhasil disimpan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal menyimpan data: ' . $th->getMessage());
        }
    }
}
