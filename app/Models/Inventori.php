<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventori extends Model
{
    use HasFactory;
    protected $table = 'inventori';
    protected $primaryKey = 'id';

    // Kolom yang boleh diisi secara massal (mass assignment)
    protected $fillable = [
        'id_jenis_media',
        'jumlah_eksemplar',
        'tanggal_pembelian',
        'id_jenis_sumber',
        'id_sumber',
        'id_kategori_buku',
        'judul_buku',
        'pengarang',
        'id_sekolah',
        'id_penerbit',
        'harga_satuan',
        'total_harga',
    ];

    // Relasi ke JenisMedia
    public function jenisMedia()
    {
        return $this->belongsTo(JenisMedia::class, 'id_jenis_media');
    }

    // Relasi ke JenisSumber
    public function jenisSumber()
    {
        return $this->belongsTo(JenisSumber::class, 'id_jenis_sumber');
    }

    // Relasi ke Sumber
    public function sumber()
    {
        return $this->belongsTo(Sumber::class, 'id_sumber');
    }

    // Relasi ke KategoriBuku
    public function kategoriBuku()
    {
        return $this->belongsTo(KategoriBuku::class, 'id_kategori_buku');
    }

    // Relasi ke Sekolah
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'id_sekolah');
    }

    // Relasi ke Penerbit
    public function penerbit()
    {
        return $this->belongsTo(Penerbit::class, 'id_penerbit');
    }

    public function eksemplar()
    {
        return $this->hasMany(Eksemplar::class, 'id_inventori');
    }

    public function katalog()
    {
        return $this->hasMany(Katalog::class, 'id_inventori');
    }
}
