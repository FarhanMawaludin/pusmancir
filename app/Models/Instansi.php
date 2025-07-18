<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Instansi extends Model
{
    use HasFactory;
    protected $table = 'instansi';
    protected $primaryKey = 'id';
    protected $fillable = ['kode','nama_lengkap'];

    public function suratKeluar()
    {
        return $this->hasMany(SuratKeluar::class, 'instansi_id');
    }
}
