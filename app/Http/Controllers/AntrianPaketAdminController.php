<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AntrianPaket;
use App\Models\AntrianPaketSetting;
use App\Models\BukuPaketMapel;
use App\Models\PeminjamanBukuPaket;
use App\Models\DetailPeminjamanBukuPaket;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AntrianPaketAdminController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = 'antrianPaket';
        $tanggal = $request->tanggal ?? Carbon::today()->format('Y-m-d');

        $antrians = AntrianPaket::with(['anggota.user', 'anggota.kelas'])
            ->where('tanggal_kunjungan', $tanggal)
            ->orderBy('nomor_antrian', 'asc')
            ->paginate(20);

        $setting = AntrianPaketSetting::where('tanggal', $tanggal)->first();

        $terisi = AntrianPaket::where('tanggal_kunjungan', $tanggal)
            ->where('status', '!=', 'batal')
            ->count();

        return view('admin.antrian-paket.index', compact('activeMenu', 'antrians', 'setting', 'tanggal', 'terisi'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:hadir,tidak_hadir,batal'
        ]);

        $antrian = AntrianPaket::findOrFail($id);
        $antrian->update(['status' => $request->status]);

        return back()->with('success', 'Status antrian berhasil diperbarui.');
    }

    public function prosesPeminjaman($id)
    {
        $activeMenu = 'antrianPaket';
        $antrian = AntrianPaket::with(['anggota.user', 'anggota.kelas'])->findOrFail($id);
        
        $namaKelas = $antrian->anggota->kelas->nama_kelas ?? '';
        
        if (preg_match('/^(XII)\s+(IPA|IPS)/i', $namaKelas, $m)) {
            $tingkatKelas = strtoupper($m[1]) . ' ' . strtoupper($m[2]);
        } elseif (preg_match('/^(XI)\s+(IPA|IPS)/i', $namaKelas, $m)) {
            $tingkatKelas = strtoupper($m[1]) . ' ' . strtoupper($m[2]);
        } else {
            $tingkatKelas = 'X';
        }

        $bukuMapel = BukuPaketMapel::where('tingkat_kelas', $tingkatKelas)->get();

        return view('admin.antrian-paket.proses', compact('activeMenu', 'antrian', 'bukuMapel', 'tingkatKelas'));
    }

    public function storePeminjaman(Request $request)
    {
        $request->validate([
            'antrian_id' => 'required|exists:antrian_paket,id',
            'buku_ids' => 'required|array|min:1',
            'buku_ids.*' => 'exists:buku_paket_mapel,id',
        ]);

        DB::beginTransaction();
        try {
            $antrian = AntrianPaket::findOrFail($request->antrian_id);
            
            $peminjaman = PeminjamanBukuPaket::create([
                'antrian_id' => $antrian->id,
                'anggota_id' => $antrian->anggota_id,
                'user_id' => auth()->id(),
                'tanggal_pinjam' => Carbon::now()->format('Y-m-d'),
                'status' => 'dipinjam',
            ]);

            foreach ($request->buku_ids as $buku_id) {
                DetailPeminjamanBukuPaket::create([
                    'peminjaman_buku_paket_id' => $peminjaman->id,
                    'buku_paket_mapel_id' => $buku_id,
                ]);
            }

            $antrian->update(['status' => 'hadir']);

            DB::commit();
            return redirect()->route('admin.antrian-paket.index')->with('success', 'Peminjaman buku paket berhasil diproses.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat memproses peminjaman.');
        }
    }

    public function exportPdf(Request $request)
    {
        $tanggal = $request->tanggal ?? Carbon::today()->format('Y-m-d');
        
        $antrians = AntrianPaket::with(['anggota.user', 'anggota.kelas'])
            ->where('tanggal_kunjungan', $tanggal)
            ->orderBy('nomor_antrian', 'asc')
            ->get();

        $pdf = Pdf::loadView('admin.antrian-paket.pdf-antrian', compact('antrians', 'tanggal'));
        return $pdf->stream('laporan-antrian-paket-'.$tanggal.'.pdf');
    }

    public function riwayat(Request $request)
    {
        $activeMenu = 'antrianPaket';
        
        $query = PeminjamanBukuPaket::with([
            'anggota.user', 
            'anggota.kelas', 
            'detailPeminjamanBukuPaket.bukuPaketMapel', 
            'antrianPaket'
        ]);

        if ($request->tanggal_mulai && $request->tanggal_selesai) {
            $query->whereBetween('tanggal_pinjam', [$request->tanggal_mulai, $request->tanggal_selesai]);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('anggota.user', function($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                })->orWhereHas('anggota', function($q2) use ($search) {
                    $q2->where('nisn', 'like', "%{$search}%");
                });
            });
        }

        $riwayat = $query->orderBy('created_at', 'desc')->paginate(20)->appends($request->all());

        $search = $request->search;
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalSelesai = $request->tanggal_selesai;

        return view('admin.antrian-paket.riwayat', compact(
            'activeMenu', 'riwayat', 'search', 'tanggalMulai', 'tanggalSelesai'
        ));
    }
}
