<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriBuku extends Model
{
    use HasFactory;
    protected $table = 'kategori_buku';
    protected $primaryKey = 'id';
    protected $fillable = ['nama_kategori'];

    public function inventori() {
        return $this->hasMany(Inventori::class, 'id_kategori_buku');
    }
}
