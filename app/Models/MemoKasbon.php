<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemoKasbon extends Model
{
    use HasFactory;
    protected $table = 'memo_kasbon';
    protected $fillable = [
        'id_memo',
        'nomor',
        'divisi',
        'minggu',
    ];
    
}

    
    
