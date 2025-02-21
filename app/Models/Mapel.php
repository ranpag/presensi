<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mapel extends Model
{
    use HasFactory;
    
    protected $table = 'mapel';

    protected $fillable = [
        'nama',
    ];

    public function jadwal(): HasMany
    {
        return $this->hasMany(JadwalKBM::class, 'mapel_id');
    }

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class, 'mapel_id');
    }
}
