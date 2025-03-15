<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loi extends Model
{
    use HasFactory;
    protected $table = 'loi';
    protected $fillable = [
        'nomor_loi',
        'id_pr',
        'nomor_pr',
        'lampiran',
        'vendor_id',
        'tanggal_loi',
        'batas_loi',
        'perihal',
        'penerima',
        'alamat',
        'keterangan_loi',
        'nomor_po',
        'tanggal_po',
    ];
}
