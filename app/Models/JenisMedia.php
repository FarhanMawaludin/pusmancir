<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisMedia extends Model
{
    use HasFactory;
    protected $table = 'jenis_media';
    protected $primaryKey = 'id';
    protected $fillable = ['nama_jenis_media'];

    public function inventori()
    {
        return $this->hasMany(Inventori::class, 'id_jenis_media');
    }

}
