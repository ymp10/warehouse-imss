<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontrakLampiran extends Model
{
    use HasFactory;

    protected $table = 'kontrak_lampiran';

    protected $fillable = [
        'kontrak_id',
        'file',
        'tipe',
    ];
}
