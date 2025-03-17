<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestSppjp extends Model
{
    use HasFactory;
    protected $table = 'purchase_request_sppjp';
    protected $fillable = [
        'proyek_id',
        'no_pr_sppjp',
        'tgl_pr_sppjp',
        'dasar_pr_sppjp',
        'id_user',
        'nomor_lppb_sppjp',
        'tanggal_lppb_sppjp',
        'is_read'
    ];
    public function detailPrSppjp()
    {
        return $this->hasMany(DetailPRSppjp::class, 'id_pr_sppjp', 'id');
    }
}
