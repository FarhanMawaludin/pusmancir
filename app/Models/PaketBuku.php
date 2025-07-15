<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaketBuku extends Model
{
    use HasFactory;
    protected $table = 'paket_buku';
    protected $primaryKey = 'id';
    protected $fillable = ['nama_paket', 'deskripsi', 'stok_total', 'stok_tersedia'];

}
