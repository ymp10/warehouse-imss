<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPRSppjp extends Model
{
    use HasFactory;
    protected $table = 'detail_pr_sppjp';
    protected $fillable = [
       'id_pr_sppjp',
        'user_id',
        'id_spph',
        'id_loi',
        'id_nego',
        'id_po',
        'id_proyek',
        'kode_material',
        'uraian',
        'spek',
        'qty',
        'qty_spph',
        'qty2',
        'qty_loi',
        'qty_loi1',
        'qty_nego',
        'qty_nego1',    
        'qty_po',
        'qty_po1',
        'id_del',
        'id_spph_spph',
        'satuan',
        'lampiran',
        'waktu',
        'keterangan',
        'status',
        'no_sph',
        'tanggal_sph',
        'no_just',
        'tanggal_just',
        'no_nego1',
        'tanggal_nego1',
        'batas_nego1',
        'no_nego2',
        'tanggal_nego2',
        'batas_nego2',
        'penerimaan',
        'hasil_ok',
        'hasil_nok',
        'diterima_eks',
        'belum_diterima_eks',
        'diterima_qc',
        'belum_diterima_qc',
        'tgl_diterima',
    ];
}
