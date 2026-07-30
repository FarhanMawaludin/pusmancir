<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AntrianPaketSetting extends Model
{
    use HasFactory;

    protected $table = 'antrian_paket_settings';

    protected $fillable = [
        'tanggal',
        'kuota',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function antrianPaket()
    {
        return AntrianPaket::where('tanggal_kunjungan', $this->tanggal)->get();
    }
}
