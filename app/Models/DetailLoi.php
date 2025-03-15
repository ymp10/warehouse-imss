<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailLoi extends Model
{
    use HasFactory;
    protected $table = 'detail_loi';
    protected $fillable = [
        'loi_id',
        'id_del_loi',
        'id_detail_pr',
        'loi_qty',
        'harga',
        
    ];
}
