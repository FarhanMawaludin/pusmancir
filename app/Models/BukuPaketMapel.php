<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuPaketMapel extends Model
{
    use HasFactory;

    protected $table = 'buku_paket_mapel';

    protected $fillable = [
        'nama_buku',
        'tingkat_kelas',
    ];

    public function detailPeminjamanBukuPaket()
    {
        return $this->hasMany(DetailPeminjamanBukuPaket::class);
    }
}
