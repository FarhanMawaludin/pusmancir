<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailPeminjaman extends Model
{
    use HasFactory;

    protected $table = 'detail_peminjaman';
    protected $primaryKey = 'id';
    protected $fillable = [
        'peminjaman_id',
        'eksemplar_id',
        'tanggal_kembali_asli',
        'user_id',
    ];

    /**
     * Relasi ke model Peminjaman
     */
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    /**
     * Relasi ke model Eksemplar
     */
    public function eksemplar()
    {
        return $this->belongsTo(Eksemplar::class, 'eksemplar_id');
    }

    /**
     * Relasi ke model User (petugas yang menerima)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
