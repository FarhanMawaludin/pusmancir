<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AntrianPaket extends Model
{
    use HasFactory;

    protected $table = 'antrian_paket';

    protected $fillable = [
        'anggota_id',
        'tanggal_kunjungan',
        'nomor_antrian',
        'status',
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function peminjamanBukuPaket()
    {
        return $this->hasOne(PeminjamanBukuPaket::class, 'antrian_id');
    }
}
