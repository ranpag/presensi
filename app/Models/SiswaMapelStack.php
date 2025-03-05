<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SiswaMapelStack extends Model
{
    use HasFactory;

    protected $table = 'siswa_mapel_stack';

    protected $fillable = [
        'siswa_id',
        'mapel_id',
        'stack_alfa',
        'last_alfa_update',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }
}
