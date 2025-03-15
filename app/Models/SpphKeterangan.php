<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpphKeterangan extends Model
{
    use HasFactory;

    protected $table = 'spph_keterangan';

    protected $fillable = [
        'spph_id',
        'keterangan',
        'di_spph',
    ];

    public function spph()
    {
        return $this->belongsTo(Spph::class, 'spph_id');
    }
}
