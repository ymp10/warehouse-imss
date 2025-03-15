<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoiLampiran extends Model
{
    use HasFactory;

    protected $table = 'loi_lampiran';

    protected $fillable = [
        'loi_id',
        'file',
        'tipe',
    ];
}
