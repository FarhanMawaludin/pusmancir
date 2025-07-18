<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuratMasuk extends Model
{
    use HasFactory;

    protected $table = 'surat_masuk';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nomor_surat',
        'tanggal_terima',
        'asal_surat',
        'perihal',
        'lampiran',
        'file_surat',
        'status',
    ];

    protected $casts = [
        'tanggal_terima' => 'date',
    ];
}
