<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Justi extends Model
{
    use HasFactory;
    protected $table = 'justi';
    protected $fillable = [
        'nomor_justi',
        'justi',
        'dasar',
        'perihal',
        'id_pr',
        'nomor_pr',
        'pr',
        'nomor_spph',
        'spph',
        'lampiran',
        'vendor_id',
        'penerima',
        'alamat',
        
    ];
}

