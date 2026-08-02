<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AntrianPaket;
use App\Models\AntrianPaketSetting;
use App\Models\PeminjamanBukuPaket;
use App\Models\BukuPaketMapel;
use App\Models\DetailPeminjamanBukuPaket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AntrianPaketAnggotaController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = 'antrianPaket';
        $user = Auth::user();
        $anggota = $user ? $user->anggota : null;

        $availableSettings = AntrianPaketSetting::where('tanggal', '>=', Carbon::today()->format('Y-m-d'))
            ->orderBy('tanggal', 'asc')
            ->get();

        $availableDates = [];
        foreach ($availableSettings as $setting) {
            $terisi = AntrianPaket::where('tanggal_kunjungan', $setting->tanggal)
                                  ->where('status', '!=', 'batal')
                                  ->count();
            if ($terisi < $setting->kuota) {
                $setting->terisi = $terisi;
                $availableDates[] = $setting;
            }
        }

        $antrianAktif = null;
        $riwayatAntrian = collect();
        $riwayatPeminjaman = collect();
        $bukuMapel = collect();
        $peminjamanAktif = null;

        if ($anggota) {
            // Otomatis ubah status antrian menjadi 'selesai' jika peminjaman buku paketnya sudah dikembalikan
            AntrianPaket::where('anggota_id', $anggota->id)
                ->where('status', 'menunggu')
                ->whereHas('peminjamanBukuPaket', function($q) {
                    $q->where('status', 'dikembalikan');
                })
                ->update(['status' => 'selesai']);

            // Otomatis ubah status antrian 'menunggu' yang tanggal kunjungannya sudah lewat (sebelum hari ini) menjadi 'hangus'
            AntrianPaket::where('anggota_id', $anggota->id)
                ->where('status', 'menunggu')
                ->where('tanggal_kunjungan', '<', Carbon::today()->format('Y-m-d'))
                ->update(['status' => 'hangus']);

            // Antrian aktif hanya yang statusnya 'menunggu' DAN tanggal_kunjungan >= hari ini
            $antrianAktif = AntrianPaket::where('anggota_id', $anggota->id)
                ->where('status', 'menunggu')
                ->where('tanggal_kunjungan', '>=', Carbon::today()->format('Y-m-d'))
                ->first();

            if ($antrianAktif) {
                $tingkatKelas = $this->getTingkatKelas($anggota);
                $bukuMapel = BukuPaketMapel::where('tingkat_kelas', $tingkatKelas)->get();
                $peminjamanAktif = PeminjamanBukuPaket::with('detailPeminjamanBukuPaket')
                    ->where('antrian_id', $antrianAktif->id)
                    ->first();
            }

            $riwayatAntrian = AntrianPaket::where('anggota_id', $anggota->id)
                ->where('status', '!=', 'menunggu')
                ->orderBy('tanggal_kunjungan', 'desc')
                ->get();

            $riwayatPeminjaman = PeminjamanBukuPaket::with('detailPeminjamanBukuPaket.bukuPaketMapel')
                ->where('anggota_id', $anggota->id)
                ->orderBy('tanggal_pinjam', 'desc')
                ->get();
        }

        return view('anggota.antrian-paket.index', compact(
            'activeMenu', 
            'availableDates', 
            'antrianAktif', 
            'riwayatAntrian', 
            'riwayatPeminjaman',
            'bukuMapel',
            'peminjamanAktif'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_kunjungan' => 'required|date|after_or_equal:today|exists:antrian_paket_settings,tanggal',
        ]);

        $user = Auth::user();
        $anggota = $user ? $user->anggota : null;

        if (!$anggota) {
            return back()->with('error', 'Data anggota Anda tidak ditemukan.');
        }

        // Sync antrian jika peminjaman sudah dikembalikan
        AntrianPaket::where('anggota_id', $anggota->id)
            ->where('status', 'menunggu')
            ->whereHas('peminjamanBukuPaket', function($q) {
                $q->where('status', 'dikembalikan');
            })
            ->update(['status' => 'selesai']);

        // Cek antrian aktif hanya untuk tanggal yang belum lewat
        $hasActive = AntrianPaket::where('anggota_id', $anggota->id)
            ->where('status', 'menunggu')
            ->where('tanggal_kunjungan', '>=', Carbon::today()->format('Y-m-d'))
            ->exists();

        if ($hasActive) {
            return back()->with('error', 'Anda masih memiliki antrian yang aktif. Antrian hanya berlaku pada tanggal booking.');
        }

        $setting = AntrianPaketSetting::where('tanggal', $request->tanggal_kunjungan)->first();
        $terisi = AntrianPaket::where('tanggal_kunjungan', $request->tanggal_kunjungan)
                              ->where('status', '!=', 'batal')
                              ->where('status', '!=', 'hangus')
                              ->count();

        if ($terisi >= $setting->kuota) {
            return back()->with('error', 'Kuota untuk tanggal tersebut sudah penuh.');
        }

        $maxNomor = AntrianPaket::where('tanggal_kunjungan', $request->tanggal_kunjungan)->max('nomor_antrian');
        $nomorAntrian = $maxNomor ? $maxNomor + 1 : 1;

        $antrian = AntrianPaket::create([
            'anggota_id' => $anggota->id,
            'tanggal_kunjungan' => $request->tanggal_kunjungan,
            'nomor_antrian' => $nomorAntrian,
            'status' => 'menunggu',
        ]);

        PeminjamanBukuPaket::create([
            'antrian_id' => $antrian->id,
            'anggota_id' => $anggota->id,
            'user_id' => null,
            'tanggal_pinjam' => $request->tanggal_kunjungan,
            'status' => 'dipinjam',
        ]);

        return back()->with('success', 'Antrian berhasil diambil. Nomor Antrian Anda: ' . $nomorAntrian);
    }

    public function batal($id)
    {
        $user = Auth::user();
        $anggota = $user ? $user->anggota : null;

        if (!$anggota) {
            return back()->with('error', 'Data anggota Anda tidak ditemukan.');
        }

        $antrian = AntrianPaket::where('id', $id)
            ->where('anggota_id', $anggota->id)
            ->where('status', 'menunggu')
            ->firstOrFail();

        $antrian->update(['status' => 'batal']);

        return back()->with('success', 'Antrian berhasil dibatalkan.');
    }

    public function pilihBuku(Request $request, $id)
    {
        $request->validate([
            'buku_ids' => 'required|array|min:1',
            'buku_ids.*' => 'exists:buku_paket_mapel,id',
        ]);

        $user = Auth::user();
        $anggota = $user ? $user->anggota : null;

        if (!$anggota) {
            return back()->with('error', 'Data anggota Anda tidak ditemukan.');
        }

        $antrian = AntrianPaket::where('id', $id)
            ->where('anggota_id', $anggota->id)
            ->where('status', 'menunggu')
            ->firstOrFail();

        $peminjaman = PeminjamanBukuPaket::firstOrCreate(
            ['antrian_id' => $antrian->id],
            [
                'anggota_id' => $anggota->id,
                'user_id' => null,
                'tanggal_pinjam' => $antrian->tanggal_kunjungan,
                'status' => 'dipinjam',
            ]
        );

        DB::beginTransaction();
        try {
            DetailPeminjamanBukuPaket::where('peminjaman_buku_paket_id', $peminjaman->id)->delete();

            foreach ($request->buku_ids as $buku_id) {
                DetailPeminjamanBukuPaket::create([
                    'peminjaman_buku_paket_id' => $peminjaman->id,
                    'buku_paket_mapel_id' => $buku_id,
                ]);
            }
            DB::commit();
            return back()->with('success', 'Pilihan buku berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan pilihan buku.');
        }
    }

    private function getTingkatKelas($anggota)
    {
        $namaKelas = $anggota->kelas->nama_kelas ?? '';
        if (preg_match('/^(XII)\s+(IPA|IPS)/i', $namaKelas, $m)) {
            return strtoupper($m[1]) . ' ' . strtoupper($m[2]);
        } elseif (preg_match('/^(XI)\s+(IPA|IPS)/i', $namaKelas, $m)) {
            return strtoupper($m[1]) . ' ' . strtoupper($m[2]);
        }
        return 'X';
    }
}
