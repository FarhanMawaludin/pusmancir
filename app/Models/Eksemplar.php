<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Eksemplar extends Model
{
    use HasFactory;

    protected $table = 'eksemplar';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_inventori',
        'no_induk',
        'no_inventori',
        'no_rfid',
        'status',
    ];

    /**
     * Relasi ke model Inventori.
     */
    public function inventori()
    {
        return $this->belongsTo(Inventori::class, 'id_inventori');
    }

    public function detailPeminjaman()
{
    return $this->hasMany(DetailPeminjaman::class);
}
}
