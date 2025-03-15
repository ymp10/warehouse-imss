<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenerimaanBarang extends Model
{
    use HasFactory;
    
    protected $table = 'penerimaan_barang';
    
    protected $fillable = [
        'id_detail_pr',
        'id_po',
        'diterima_eks',
        'belum_diterima_eks',
        'diterima_qc',
        'belum_diterima_qc',
        'tanggal_diterima',
        'penerimaan',
        'hasil_ok',
        'hasil_nok'
    ];
}
