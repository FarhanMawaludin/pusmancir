<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AntrianPaketSetting;
use App\Models\AntrianPaket;
use Carbon\Carbon;

class AntrianPaketSettingController extends Controller
{
    public function index()
    {
        $activeMenu = 'antrianPaket';
        $settings = AntrianPaketSetting::orderBy('tanggal', 'desc')->get();
        
        foreach ($settings as $setting) {
            $setting->terisi = AntrianPaket::where('tanggal_kunjungan', $setting->tanggal)
                            ->where('status', '!=', 'batal')
                            ->count();
        }

        return view('admin.antrian-paket.setting', compact('activeMenu', 'settings'));
    }

    public function store(Request $request)
    {
        $rules = [
            'tanggal' => 'required|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal',
            'kuota' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ];

        // Only enforce unique when not creating a range
        if (!$request->tanggal_akhir) {
            $rules['tanggal'] .= '|unique:antrian_paket_settings,tanggal';
        }

        $request->validate($rules);

        $startDate = Carbon::parse($request->tanggal);
        $endDate = $request->tanggal_akhir ? Carbon::parse($request->tanggal_akhir) : $startDate->copy();

        $created = 0;
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $result = AntrianPaketSetting::firstOrCreate(
                ['tanggal' => $date->format('Y-m-d')],
                [
                    'kuota' => $request->kuota,
                    'keterangan' => $request->keterangan,
                ]
            );
            if ($result->wasRecentlyCreated) {
                $created++;
            }
        }

        return back()->with('success', "Pengaturan antrian berhasil disimpan ({$created} hari dibuat).");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kuota' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $setting = AntrianPaketSetting::findOrFail($id);
        $setting->update([
            'kuota' => $request->kuota,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Pengaturan antrian berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $setting = AntrianPaketSetting::findOrFail($id);
        
        $hasAntrian = AntrianPaket::where('tanggal_kunjungan', $setting->tanggal)
                                  ->where('status', '!=', 'batal')
                                  ->exists();
        if ($hasAntrian) {
            return back()->with('error', 'Tidak dapat menghapus pengaturan tanggal ini karena sudah ada antrian yang terdaftar.');
        }

        $setting->delete();

        return back()->with('success', 'Pengaturan antrian berhasil dihapus.');
    }
}
