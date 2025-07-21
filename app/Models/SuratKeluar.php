<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuratKeluar extends Model
{
    use HasFactory;

    protected $table = 'surat_keluar';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nomor_urut',
        'tanggal_keluar',
        'tujuan_surat',
        'perihal',
        'kode_jenis_id',
        'instansi_id',
        'isi_surat',
        'file_surat',
    ];

    protected $casts = [
        'tanggal_keluar' => 'date', // ✅ penting untuk menghindari error
    ];

    public function getNomorSuratAttribute()
    {
        $kodeJenis = $this->kodeJenisSurat->kode ?? 'XX';
        $instansi = $this->instansi->kode ?? 'INST';
        $bulanRomawi = $this->convertToRomawi($this->tanggal_keluar->format('m'));
        $tahun = $this->tanggal_keluar->format('Y');
        $nomorUrut = str_pad($this->nomor_urut, 3, '0', STR_PAD_LEFT);

        return "{$kodeJenis}.{$nomorUrut}/{$instansi}/{$bulanRomawi}/{$tahun}";
    }

    private function convertToRomawi($bulan)
    {
        $romawi = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];

        return $romawi[(int) $bulan] ?? '';
    }

    public function kodeJenisSurat()
    {
        return $this->belongsTo(KodeJenisSurat::class, 'kode_jenis_id');
    }

    public function instansi()
    {
        return $this->belongsTo(Instansi::class, 'instansi_id');
    }
}
