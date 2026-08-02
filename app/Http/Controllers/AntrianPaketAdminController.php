<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AntrianPaket;
use App\Models\AntrianPaketSetting;

use App\Models\PeminjamanBukuPaket;
use App\Models\DetailPeminjamanBukuPaket;
use App\Models\BukuPaketMapel;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AntrianPaketAdminController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = 'antrianPaket';
        $tanggal = $request->tanggal ?? Carbon::today()->format('Y-m-d');

        $antrians = AntrianPaket::with(['anggota.user', 'anggota.kelas', 'peminjamanBukuPaket.detailPeminjamanBukuPaket.bukuPaketMapel'])
            ->where('tanggal_kunjungan', $tanggal)
            ->orderBy('nomor_antrian', 'asc')
            ->paginate(20);

        $setting = AntrianPaketSetting::where('tanggal', $tanggal)->first();

        $terisi = AntrianPaket::where('tanggal_kunjungan', $tanggal)
            ->where('status', '!=', 'batal')
            ->count();

        $allBukuMapel = BukuPaketMapel::orderBy('tingkat_kelas')->orderBy('nama_buku')->get();

        return view('admin.antrian-paket.index', compact('activeMenu', 'antrians', 'setting', 'tanggal', 'terisi', 'allBukuMapel'));
    }

    public function rekap(Request $request)
    {
        $activeMenu = 'antrianPaket';
        
        $query = DetailPeminjamanBukuPaket::select('buku_paket_mapel_id', DB::raw('count(*) as total_dipinjam'))
            ->groupBy('buku_paket_mapel_id')
            ->with('bukuPaketMapel');
        
        if ($request->tingkat_kelas) {
            $query->whereHas('bukuPaketMapel', function($q) use ($request) {
                $q->where('tingkat_kelas', $request->tingkat_kelas);
            });
        }
        
        if ($request->tanggal_mulai && $request->tanggal_selesai) {
            $query->whereHas('peminjamanBukuPaket', function($q) use ($request) {
                $q->whereBetween('tanggal_pinjam', [$request->tanggal_mulai, $request->tanggal_selesai]);
            });
        }
        
        $rekapBuku = $query->orderByDesc('total_dipinjam')->get();
        
        $totalSeluruhBuku = $rekapBuku->sum('total_dipinjam');
        $tingkatKelas = $request->tingkat_kelas;
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalSelesai = $request->tanggal_selesai;
        
        return view('admin.antrian-paket.rekap', compact(
            'activeMenu', 'rekapBuku', 'totalSeluruhBuku', 'tingkatKelas', 'tanggalMulai', 'tanggalSelesai'
        ));
    }

    public function exportPdf(Request $request)
    {
        $tanggal = $request->tanggal ?? Carbon::today()->format('Y-m-d');
        
        $antrians = AntrianPaket::with(['anggota.user', 'anggota.kelas', 'peminjamanBukuPaket.detailPeminjamanBukuPaket.bukuPaketMapel'])
            ->where('tanggal_kunjungan', $tanggal)
            ->orderBy('nomor_antrian', 'asc')
            ->get();

        $pdf = Pdf::loadView('admin.antrian-paket.pdf-antrian', compact('antrians', 'tanggal'));
        return $pdf->stream('laporan-antrian-paket-'.$tanggal.'.pdf');
    }

    public function riwayat(Request $request)
    {
        $activeMenu = 'antrianPaket';
        
        $kelasList = Kelas::orderBy('nama_kelas', 'asc')->get();

        $query = PeminjamanBukuPaket::with([
            'anggota.user', 
            'anggota.kelas', 
            'detailPeminjamanBukuPaket.bukuPaketMapel', 
            'antrianPaket'
        ]);

        $statsQuery = PeminjamanBukuPaket::query();
        if ($request->tanggal_mulai && $request->tanggal_selesai) {
            $statsQuery->whereBetween('tanggal_pinjam', [$request->tanggal_mulai, $request->tanggal_selesai]);
        }
        if ($request->kelas) {
            $statsQuery->whereHas('anggota.kelas', function($q) use ($request) {
                $q->where('id', $request->kelas)->orWhere('nama_kelas', 'like', "%{$request->kelas}%");
            });
        }
        $totalPeminjam = (clone $statsQuery)->distinct('anggota_id')->count('anggota_id');
        $totalBukuDipinjam = DetailPeminjamanBukuPaket::whereIn('peminjaman_buku_paket_id', (clone $statsQuery)->pluck('id'))->count();

        // Popular books
        $bukuPopuler = DetailPeminjamanBukuPaket::whereIn('peminjaman_buku_paket_id', (clone $statsQuery)->pluck('id'))
            ->select('buku_paket_mapel_id', DB::raw('count(*) as total'))
            ->groupBy('buku_paket_mapel_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('bukuPaketMapel')
            ->get();

        if ($request->tanggal_mulai && $request->tanggal_selesai) {
            $query->whereBetween('tanggal_pinjam', [$request->tanggal_mulai, $request->tanggal_selesai]);
        }

        if ($request->kelas) {
            $query->whereHas('anggota.kelas', function($q) use ($request) {
                $q->where('id', $request->kelas)->orWhere('nama_kelas', 'like', "%{$request->kelas}%");
            });
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

        $perPageInput = $request->per_page ?? 10;
        if ($perPageInput === 'all' || $perPageInput === 'semua') {
            $totalCount = (clone $query)->count();
            $perPage = $totalCount > 0 ? $totalCount : 10;
        } else {
            $perPage = (int) $perPageInput;
        }

        $riwayat = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->all());

        $search = $request->search;
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalSelesai = $request->tanggal_selesai;
        $kelas = $request->kelas;
        $perPage = $perPageInput;

        $allBukuMapel = BukuPaketMapel::orderBy('tingkat_kelas')->orderBy('nama_buku')->get();

        return view('admin.antrian-paket.riwayat', compact(
            'activeMenu', 'riwayat', 'search', 'tanggalMulai', 'tanggalSelesai',
            'totalPeminjam', 'totalBukuDipinjam', 'bukuPopuler', 'kelas', 'kelasList', 'perPage', 'allBukuMapel'
        ));
    }

    public function kembali($id)
    {
        $peminjaman = PeminjamanBukuPaket::findOrFail($id);
        $peminjaman->update([
            'status' => 'dikembalikan',
            'tanggal_kembali' => Carbon::now()->format('Y-m-d')
        ]);

        if ($peminjaman->antrianPaket) {
            $peminjaman->antrianPaket->update(['status' => 'selesai']);
        }

        return back()->with('success', 'Buku berhasil ditandai telah dikembalikan pada tanggal ' . Carbon::now()->format('d-m-Y') . '.');
    }

    public function updateBuku(Request $request, $id)
    {
        $peminjaman = PeminjamanBukuPaket::findOrFail($id);

        $request->validate([
            'buku_ids' => 'nullable|array',
            'buku_ids.*' => 'exists:buku_paket_mapel,id',
        ]);

        DB::beginTransaction();
        try {
            DetailPeminjamanBukuPaket::where('peminjaman_buku_paket_id', $peminjaman->id)->delete();

            if ($request->has('buku_ids') && is_array($request->buku_ids)) {
                foreach ($request->buku_ids as $buku_id) {
                    DetailPeminjamanBukuPaket::create([
                        'peminjaman_buku_paket_id' => $peminjaman->id,
                        'buku_paket_mapel_id' => $buku_id,
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', 'Data pilihan buku berhasil diperbarui oleh Admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui data buku: ' . $e->getMessage());
        }
    }
}
