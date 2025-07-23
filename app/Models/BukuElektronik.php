<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BukuElektronik extends Model
{
    use HasFactory;

    protected $table = 'buku_elektronik';
    protected $primaryKey = 'id';
    protected $fillable = [
        'judul',
        'penulis',
        'kelas',
        'kategori',
        'kurikulum',
        'pdf_path',
        'cover_image',
    ];
}
