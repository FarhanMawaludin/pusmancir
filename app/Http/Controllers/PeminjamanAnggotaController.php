<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Anggota;
use App\Models\Eksemplar;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use Carbon\Carbon;


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
            ->whereIn('peminjaman.status', ['menunggu', 'berhasil','tolak', 'selesai'])
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
        $query->orderByRaw("FIELD(peminjaman.status, 'menunggu', 'berhasil', 'tolak', 'selesai')")
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
       1. Jika user login ber-role “anggota”, 
          pakai NISN miliknya → agar tidak bisa dimanipulasi
    ───────────────────────────────────────────── */
        if (Auth::user()->role === 'anggota') {
            $anggotaNisn = Auth::user()->anggota->nisn ?? null; // relasi hasOne User→Anggota
            $request->merge(['anggota_id' => $anggotaNisn]);
        }

        /* ─────────────────────────────────────────────
       2. Validasi request
    ───────────────────────────────────────────── */
        $validated = $request->validate([
            'anggota_id'      => 'required|exists:anggota,nisn',
            'eksemplar_id'    => 'required|string|exists:eksemplar,no_rfid',
            'tanggal_pinjam'  => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
        ]);

        /* ─────────────────────────────────────────────
       3. Lookup Anggota & Eksemplar
    ───────────────────────────────────────────── */
        $anggota   = Anggota::where('nisn', $validated['anggota_id'])->first();
        $eksemplar = Eksemplar::where('no_rfid', trim($validated['eksemplar_id']))->first();

        // Validasi keberadaan dan status
        if (!$anggota || !$eksemplar) {
            return back()->withInput()
                ->with('error', 'Anggota atau Eksemplar tidak ditemukan.');
        }

        if ($anggota->status !== 'aktif') {
            return back()->withInput()
                ->with('error', 'Anggota tidak aktif dan tidak dapat melakukan peminjaman.');
        }

        if ($eksemplar->status === 'dipinjam') {
            return back()->withInput()
                ->with('warning', "Buku dengan RFID '{$validated['eksemplar_id']}' sedang dipinjam.");
        }

        /* ─────────────────────────────────────────────
       4. Tentukan status & user_id
    ───────────────────────────────────────────── */
        $role          = Auth::user()->role;
        $status        = in_array($role, ['admin', 'pustakawan']) ? 'berhasil' : 'menunggu';
        $userIdPetugas = in_array($role, ['admin', 'pustakawan']) ? Auth::id() : null;

        /* ─────────────────────────────────────────────
       5. Transaksi Database
    ───────────────────────────────────────────── */
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::create([
                'anggota_id'      => $anggota->id,
                'user_id'         => $userIdPetugas,
                'tanggal_pinjam'  => $validated['tanggal_pinjam'],
                'tanggal_kembali' => $validated['tanggal_kembali'],
                'status'          => $status,
            ]);

            DetailPeminjaman::create([
                'peminjaman_id' => $peminjaman->id,
                'eksemplar_id'  => $eksemplar->id,
            ]);

            if ($status === 'berhasil') {
                $eksemplar->update(['status' => 'dipinjam']);
            }

            DB::commit();

            /* ─────────────────────────────────────────────
           6. Redirect + flash message
        ───────────────────────────────────────────── */
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

    public function perpanjang($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        // Validasi hanya boleh perpanjang jika status berhasil dan H-1
        if (
            $peminjaman->status !== 'berhasil' ||
            !\Carbon\Carbon::parse($peminjaman->tanggal_kembali)->isSameDay(\Carbon\Carbon::tomorrow())
        ) {
            return redirect()->back()->with('error', 'Perpanjangan hanya bisa dilakukan H-1 sebelum tanggal kembali.');
        }

        // Update tanggal dan ubah status menjadi 'menunggu'
        $peminjaman->update([
            'tanggal_kembali' => \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->addDays(7),
            'status' => 'menunggu',
        ]);

        return redirect()->back()->with('success', 'Perpanjangan berhasil diajukan dan menunggu konfirmasi.');
    }
}
