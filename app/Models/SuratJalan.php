<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
    use HasFactory;
    protected $table = 'surat_jalan';
    protected $fillable = [
        'id_sjn',
        'no_sjn',
        'tgl_sjn',
        'kepada',
        'lokasi',
        'pengirim',
        'id_user',
        'is_read'
    ];
}
