<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kelas extends Model
{
    use HasFactory;
    
    protected $table = 'kelas';

    protected $fillable = [
        'nama',
        'tingkatan',
        'user_id'
    ];

    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    public function jadwal(): HasMany
    {
        return $this->hasMany(JadwalKBM::class, 'kelas_id');
    }

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class, 'kelas_id');
    }

    public function walas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
