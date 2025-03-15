<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailKontrak extends Model
{
    use HasFactory;
    protected $table = 'detail_kontrak';
    protected $fillable = [
        'kontrak_id',
        'nomor_dokumen',
        'tanggal_dokumen',
        'perihal',
        'keterangan',
        'lampiran',
    ];
}
