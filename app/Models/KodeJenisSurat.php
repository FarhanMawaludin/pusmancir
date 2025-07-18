<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KodeJenisSurat extends Model
{
    use HasFactory;
    protected $table = 'kode_jenis_surat';
    protected $primaryKey = 'id';
    protected $fillable = ['kode','nama_surat'];

    public function suratKeluar()
    {
        return $this->hasMany(SuratKeluar::class, 'kode_jenis_id');
    }

}
