<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailBpm extends Model
{
    use HasFactory;
    protected $table = 'detail_bpm';
    protected $fillable = [
        'id_bpm',
        'user_id',
        'id_proyek',
        'kode_material',
        'uraian',
        'spek',
        'qty',
        'satuan',
        'tanggal_permintaan',
        'keterangan',
        
    ];
}
