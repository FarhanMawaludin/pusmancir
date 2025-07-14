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
            ->whereHas('peminjaman', function ($q) {
                $q->where('status', 'menunggu');          // status peminjaman
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
        $request->validate([
            'anggota_id'      => 'required|exists:anggota,nisn',
            'eksemplar_id'    => 'required|string|exists:eksemplar,no_rfid',
            'tanggal_pinjam'  => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
        ]);

        // ── Lookup awal ───────────────────────
        $anggota   = \App\Models\Anggota::where('nisn', $request->anggota_id)->first();
        $eksemplar = \App\Models\Eksemplar::where('no_rfid', trim($request->eksemplar_id))->first();

        if (!$anggota || !$eksemplar) {
            return back()->withInput()
                ->with('error', 'Anggota atau Eksemplar tidak ditemukan.');
        }

        if ($eksemplar->status === 'dipinjam') {
            return back()->withInput()
                ->with('warning', "Buku dengan RFID '{$request->eksemplar_id}' sedang dipinjam.");
        }

        // ── Tentukan status awal berdasarkan role ─
        $role   = Auth::user()->role;          // pastikan kolom 'role' ada di tabel users
        $status = in_array($role, ['admin', 'pustakawan']) ? 'berhasil' : 'menunggu';

        DB::beginTransaction();
        try {
            $peminjaman = \App\Models\Peminjaman::create([
                'anggota_id'     => $anggota->id,
                'user_id'        => Auth::id(),
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tanggal_kembali' => $request->tanggal_kembali,
                'status'         => $status,
            ]);

            \App\Models\DetailPeminjaman::create([
                'peminjaman_id' => $peminjaman->id,
                'eksemplar_id'  => $eksemplar->id,
            ]);

            // Jika langsung diset berhasil, tandai buku “dipinjam”
            if ($status === 'berhasil') {
                $eksemplar->update(['status' => 'dipinjam']);
            }

            DB::commit();
            return redirect()->route('admin.peminjaman.index')
                ->with('success', 'Data peminjaman berhasil disimpan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal menyimpan data: ' . $th->getMessage());
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['aksi' => 'required|in:berhasil,tolak']);

        DB::beginTransaction();
        try {
            $pinjam = Peminjaman::with('detailPeminjaman.eksemplar')->findOrFail($id);

            if ($pinjam->status !== 'menunggu') {
                return back()->with('warning', 'Status sudah diproses.');
            }

            // update status + catat id petugas
            $pinjam->update([
                'status'  => $request->aksi,
                'user_id' => Auth::id(),             // ← petugas yang memproses
            ]);

            if ($request->aksi === 'berhasil') {
                foreach ($pinjam->detailPeminjaman as $detail) {
                    $detail->eksemplar->update(['status' => 'dipinjam']);
                }
            }

            DB::commit();
            return back()->with('success', 'Status berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $th->getMessage());
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
