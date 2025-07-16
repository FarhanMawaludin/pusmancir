<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPeminjamanPaket extends Model
{
    protected $table = 'detail_peminjaman_paket';
    protected $primaryKey = 'id';
    protected $fillable = [
        'peminjaman_id',
        'paket_id',
    ];

    public function peminjamanPaket()
    {
        return $this->belongsTo(PeminjamanPaket::class, 'peminjaman_id');
    }

    public function paketBuku()
    {
        return $this->belongsTo(PaketBuku::class, 'paket_id');
    }
}
