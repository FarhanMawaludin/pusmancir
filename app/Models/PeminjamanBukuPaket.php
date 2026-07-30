<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanBukuPaket extends Model
{
    use HasFactory;

    protected $table = 'peminjaman_buku_paket';

    protected $fillable = [
        'antrian_id',
        'anggota_id',
        'user_id',
        'tanggal_pinjam',
        'status',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
    ];

    public function antrianPaket()
    {
        return $this->belongsTo(AntrianPaket::class, 'antrian_id');
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailPeminjamanBukuPaket()
    {
        return $this->hasMany(DetailPeminjamanBukuPaket::class);
    }
}
