<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AntrianPaket;
use App\Models\AntrianPaketSetting;
use App\Models\PeminjamanBukuPaket;
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

        $activeAntrian = null;
        $riwayatAntrian = collect();
        $riwayatPeminjaman = collect();

        if ($anggota) {
            $activeAntrian = AntrianPaket::where('anggota_id', $anggota->id)
                ->where('status', 'menunggu')
                ->first();

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
            'activeAntrian', 
            'riwayatAntrian', 
            'riwayatPeminjaman'
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

        $hasActive = AntrianPaket::where('anggota_id', $anggota->id)
            ->where('status', 'menunggu')
            ->exists();

        if ($hasActive) {
            return back()->with('error', 'Anda masih memiliki antrian yang aktif (menunggu).');
        }

        $setting = AntrianPaketSetting::where('tanggal', $request->tanggal_kunjungan)->first();
        $terisi = AntrianPaket::where('tanggal_kunjungan', $request->tanggal_kunjungan)
                              ->where('status', '!=', 'batal')
                              ->count();

        if ($terisi >= $setting->kuota) {
            return back()->with('error', 'Kuota untuk tanggal tersebut sudah penuh.');
        }

        $maxNomor = AntrianPaket::where('tanggal_kunjungan', $request->tanggal_kunjungan)->max('nomor_antrian');
        $nomorAntrian = $maxNomor ? $maxNomor + 1 : 1;

        AntrianPaket::create([
            'anggota_id' => $anggota->id,
            'tanggal_kunjungan' => $request->tanggal_kunjungan,
            'nomor_antrian' => $nomorAntrian,
            'status' => 'menunggu',
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
}
