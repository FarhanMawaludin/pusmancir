<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetailPeminjaman;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Builder;



class PengembalianController extends Controller
{


    public function index(Request $request)
    {
        $activeMenu = 'peminjaman';

        $search   = $request->input('search');
        $category = $request->input('category', 'all');

        $query = DetailPeminjaman::with([
            'peminjaman.anggota.user',
            'eksemplar.inventori'
        ])->whereHas('peminjaman', function ($q) {
            $q->whereIn('status', ['berhasil', 'selesai']);
        });

        // Tambahkan subquery untuk status & tanggal_kembali agar bisa diurutkan
        $query->addSelect([
            'peminjaman_status' => Peminjaman::select('status')
                ->whereColumn('peminjaman.id', 'detail_peminjaman.peminjaman_id')
                ->limit(1),
            'tanggal_kembali' => Peminjaman::select('tanggal_kembali')
                ->whereColumn('peminjaman.id', 'detail_peminjaman.peminjaman_id')
                ->limit(1),
        ]);

        // Filter NISN atau Nama Peminjam
        if ($search) {
            $query->whereHas('peminjaman.anggota', function ($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter kelas (contoh: "11 IPA")
        if ($category !== 'all' && preg_match('/(\d+)\s*(IPA|IPS|Bahasa)/', $category, $m)) {
            $query->whereHas('peminjaman.anggota', function ($q) use ($m) {
                $q->where('nama_kelas', 'like', "{$m[1]} {$m[2]}%");
            });
        }

        // Urutan: berhasil → terlambat → jatuh tempo terdekat
        $query->orderByRaw("CASE WHEN peminjaman_status = 'berhasil' THEN 0 ELSE 1 END")
            ->orderByRaw("CASE WHEN tanggal_kembali < NOW() THEN 0 ELSE 1 END")
            ->orderBy('tanggal_kembali', 'asc');

        $limit = $request->input('limit', 10);
        $perPage = ($limit === 'all') ? 999999 : (int) $limit;

        $pengembalian = $query->paginate($perPage)->appends([
            'search'   => $search,
            'category' => $category,
            'limit'    => $limit,
        ]);

        return view('admin.pengembalian.index', compact(
            'pengembalian',
            'activeMenu',
            'search',
            'category',
            'limit'
        ));
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'exists:detail_peminjaman,id',
        ], [
            'ids.required' => 'Pilih minimal satu data pengembalian.',
        ]);

        $detailIds = $validated['ids'];
        $details = DetailPeminjaman::with(['eksemplar', 'peminjaman'])
            ->whereIn('id', $detailIds)
            ->get();

        DB::beginTransaction();
        try {
            $count = 0;
            foreach ($details as $detail) {
                if ($detail->eksemplar) {
                    $detail->eksemplar->update(['status' => 'tersedia']);
                }

                $detail->update([
                    'tanggal_kembali_asli' => now(),
                    'user_id'              => Auth::id(),
                ]);

                if ($detail->peminjaman) {
                    $detail->peminjaman->update(['status' => 'selesai']);
                }

                $count++;
            }

            DB::commit();
            return back()->with('success', "Berhasil menerima pengembalian $count pengembalian buku.");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pengembalian: ' . $th->getMessage());
        }
    }






    public function update($id)
    {
        DB::beginTransaction();

        try {
            // Ambil detail peminjaman beserta relasi peminjaman sekaligus
            $detail = DetailPeminjaman::with(['eksemplar', 'peminjaman'])
                ->findOrFail($id);

            // Ambil eksemplar terkait
            $eksemplar = $detail->eksemplar;
            if (!$eksemplar) {
                throw new \Exception("Eksemplar tidak ditemukan.");
            }

            // 1. Update status eksemplar ke 'tersedia'
            $eksemplar->update(['status' => 'tersedia']);

            // 2. Isi tanggal_kembali_asli dan user_id
            $detail->update([
                'tanggal_kembali_asli' => now(),
                'user_id'              => Auth::id(),
            ]);

            /* ------------------------------------------------------------------
           3. Update status peminjaman ke 'selesai'
           ------------------------------------------------------------------ */
            if ($detail->peminjaman) {
                $detail->peminjaman->update(['status' => 'selesai']);   // NEW
            }

            DB::commit();

            return redirect()
                ->route('admin.pengembalian.index')
                ->with('success', 'Status eksemplar dan peminjaman berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'Gagal meng‑update status: ' . $e->getMessage()
            ]);
        }
    }

    public function exportSuratTerlambat($id)
    {
        $pengembalian = DetailPeminjaman::with(['peminjaman.anggota.user', 'eksemplar.inventori'])->findOrFail($id);
        $peminjaman = $pengembalian->peminjaman;

        $pdf = Pdf::loadView('admin.pengembalian.export-surat-terlambat', [
            'pengembalian' => $pengembalian,
            'peminjaman' => $peminjaman
        ]);

        return $pdf->download('Surat-Keterangan-Terlambat-' . $peminjaman->anggota->user->name . '.pdf');
    }

    public function kirimWhatsapp($id)
    {
        $pengembalian = DetailPeminjaman::with(['peminjaman.anggota.user', 'eksemplar.inventori'])->findOrFail($id);
        $peminjaman = $pengembalian->peminjaman;

        $no_wa = $peminjaman->anggota->no_telp;
        $nama = $peminjaman->anggota->user->name;
        $judul = $pengembalian->eksemplar->inventori->judul_buku;

        $pesan = "Halo $nama, Anda terlambat mengembalikan buku '$judul'. Mohon segera dikembalikan ke perpustakaan. Terima kasih.";

        // Hapus karakter non-digit agar format nomor sesuai wa.me
        $no_wa_clean = preg_replace('/[^0-9]/', '', $no_wa);

        // Buat URL wa.me
        $waUrl = "https://wa.me/{$no_wa_clean}?text=" . urlencode($pesan);

        // Redirect ke wa.me
        return redirect()->away($waUrl);
    }
}
