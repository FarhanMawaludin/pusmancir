<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PeminjamanPaket extends Model
{
    use HasFactory;

    protected $table = 'peminjaman_paket';
    protected $primaryKey = 'id';
    protected $fillable = [
        'anggota_id',
        'user_id',
        'status',
    ];

    /**
     * Relasi ke model Anggota (yang meminjam)
     */
    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    /**
     * Relasi ke model User (petugas yang membuat peminjaman)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke model DetailPeminjamanPaket
     */
    public function detailPeminjamanPaket()
    {
        return $this->hasMany(DetailPeminjamanPaket::class, 'peminjaman_id');
    }
}
