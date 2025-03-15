<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailJusti extends Model
{
    use HasFactory;
    protected $table = 'detail_justi';
    protected $fillable = [
        'id_justi',
        'id_detail_pr',
        'nama_vendor',
        'nomor_vendor',
        'harga_satuan',
        'keterangan',
    ];
}
