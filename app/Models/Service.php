<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $table = 'service';
    protected $fillable = [
        'nama_tempat',
        'lokasi',
        'nama_proyek',
        'trainset',
        'car',
        'perawatan',
        'perawatan_mulai',
        'perawatan_selesai',
        'komponen_diganti',
        'tanggal_komponen',
        'pic',
        'keterangan',
    ];
}
