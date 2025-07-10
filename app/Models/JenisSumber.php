<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisSumber extends Model
{
    use HasFactory;
    protected $table = 'jenis_sumber';
    protected $primaryKey = 'id';
    protected $fillable = ['nama_sumber'];

    public function inventori()
    {
        return $this->hasMany(Inventori::class, 'id_jenis_sumber');
    }

}
