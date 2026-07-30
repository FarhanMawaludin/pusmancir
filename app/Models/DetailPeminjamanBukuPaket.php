<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPeminjamanBukuPaket extends Model
{
    use HasFactory;

    protected $table = 'detail_peminjaman_buku_paket';

    protected $fillable = [
        'peminjaman_buku_paket_id',
        'buku_paket_mapel_id',
    ];

    public function peminjamanBukuPaket()
    {
        return $this->belongsTo(PeminjamanBukuPaket::class);
    }

    public function bukuPaketMapel()
    {
        return $this->belongsTo(BukuPaketMapel::class);
    }
}
