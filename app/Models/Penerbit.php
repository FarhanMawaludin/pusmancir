<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penerbit extends Model
{
    use HasFactory;
    protected $table = 'penerbit';
    protected $primaryKey = 'id';
    protected $fillable = ['nama_penerbit'];

    public function inventori()
    {
        return $this->hasMany(Inventori::class, 'id_penerbit');
    }
}
