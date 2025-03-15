<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;
    protected $table = 'purchase_request';
    protected $fillable = [
        'proyek_id',
        'no_pr',
        'tgl_pr',
        'dasar_pr',
        'id_user',
        'nomor_lppb',
        'tanggal_lppb',
        'is_read'
    ];
    public function detailPr()
    {
        return $this->hasMany(DetailPR::class, 'id_pr', 'id');
    }
}
