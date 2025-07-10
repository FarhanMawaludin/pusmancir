<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Katalog extends Model
{
    use HasFactory;
    protected $table = 'katalog';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_inventori',
        'kategori_buku',
        'judul_buku',
        'pengarang',
        'penerbit',
        'isbn',
        'cover_buku',
        'ringkasan_buku',
        'kode_ddc',
        'no_panggil',
    ];

    public function inventori()
    {
        return $this->belongsTo(Inventori::class, 'id_inventori');
    }
}
