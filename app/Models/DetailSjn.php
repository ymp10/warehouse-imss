<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailSjn extends Model
{
    use HasFactory;
    protected $table = 'detail_sjn';
    protected $fillable = [
        'id_sjn',
        'user_id',
        'barang',
        'spek',
        'kode_material',
        'qty',
        'satuan',
        'keterangan',
        
    ];
}
