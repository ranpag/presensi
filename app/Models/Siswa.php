<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Siswa extends Model
{
    use HasFactory;
    
    protected $table = 'siswa';
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'gender',
        'nis',
        'no_telp',
        'walimurid',
        'alamat',
        'stack_alfa_hari',
        'last_alfa_update',
        'kelas_id',
    ];

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class, 'siswa_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function stackAlfaMapel(): HasMany
    {
        return $this->hasMany(SiswaMapelStack::class, 'siswa_id');
    }
}
