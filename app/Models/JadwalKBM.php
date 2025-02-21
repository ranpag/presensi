<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JadwalKBM extends Model
{
    use HasFactory;

    protected $table = 'jadwal_kbm';

    protected $fillable = [
        'kelas_id',
        'mapel_id',
        'user_id',
        'hari',
        'mulai',
        'selesai',
    ];

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class, 'jadwal_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
