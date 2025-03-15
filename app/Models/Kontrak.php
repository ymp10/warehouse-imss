<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kontrak extends Model
{
    use HasFactory;
    protected $table = 'kontrak';
    protected $fillable = [
        'tanggal',
        'kode_proyek',
        'nomor_kontrak',
        'nama_pekerjaan',
        'nilai_pekerjaan',
        'nama_pelanggan',
        'status',
        // 'nilai',
        
    ];
}
