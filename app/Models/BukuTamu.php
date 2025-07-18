<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BukuTamu extends Model
{
    use HasFactory;
    protected $table = 'buku_tamu';
    protected $primaryKey = 'id';
    protected $fillable = [
        'anggota_id',
        'nisn',
        'nama',
        'asal_instansi',
        'keperluan',
        'waktu_kunjungan'
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }
}
