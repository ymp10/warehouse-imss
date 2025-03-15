<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailSR extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'detail_sr';
    
    protected $fillable = [
        'id_sr',
        'kode_material',
        'desc_material',
        'spek',
        'p1',
        'p3',
        'p6',
        'p12',
        'p24',
        'p48',
        'p60',
        'p72',
        'vol_protective',
        'satuan',
    ];

    // Definisikan relasi dengan model Service
    // public function service()
    // {
    //     return $this->belongsTo(Service::class, 'id_sr', 'id');
    // }
}
