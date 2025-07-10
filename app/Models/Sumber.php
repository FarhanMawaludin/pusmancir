<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sumber extends Model
{
    use HasFactory;
    protected $table = 'sumber';
    protected $primaryKey = 'id';
    protected $fillable = ['nama'];

    public function inventori()
    {
        return $this->hasMany(Inventori::class, 'id_sumber');
    }
}
