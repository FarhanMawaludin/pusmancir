<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';
    protected $primaryKey = 'id';
    protected $fillable = [
        'anggota_id',
        'user_id',
        'tanggal_pinjam',
        'tanggal_kembali',
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


    public function detailPeminjaman()
    {
        return $this->hasMany(DetailPeminjaman::class);
    }
}
