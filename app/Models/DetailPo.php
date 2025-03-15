<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPo extends Model
{
    use HasFactory;
    protected $table = 'detail_po';

    protected $fillable = [
        'id_po',
        'id_del_po',
        'id_pr',
        'id_detail_pr',
        'po_qty',
        'batas_akhir',
        'harga',
        'mata_uang',
        'vat',
    ];
}
