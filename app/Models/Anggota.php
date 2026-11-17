<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Anggota extends Model
{
    use HasFactory;
    protected $table = 'anggota';
    protected $primaryKey = 'id';

    protected $fillable = [
        'no_telp',
        'email',
        'email_verified_at',
        'nisn',
        'user_id',
        'kelas_id',
        'status',
        'qr_code',
        'jenis_anggota',
        'jenis_identitas',
    ];

    public function isGuru(): bool
    {
        return ($this->jenis_anggota ?? 'siswa') === 'guru';
    }

    /** Label nomor identitas, mis. "NISN", "NIP", "NIK". */
    public function getLabelIdentitasAttribute(): string
    {
        return $this->jenis_identitas ?: ($this->isGuru() ? 'NIP' : 'NISN');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function bukuTamu()
    {
        return $this->hasMany(BukuTamu::class);
    }
}
