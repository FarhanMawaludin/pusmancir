<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sekolah extends Model
{
    use HasFactory;
    protected $table = 'sekolah';
    protected $primaryKey = 'id';
    protected $fillable = ['nama_sekolah', 'kode_sekolah']; 

    public function inventori()
    {
        return $this->hasMany(Inventori::class, 'id_sekolah');
    }
}
