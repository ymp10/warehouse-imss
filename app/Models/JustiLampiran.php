<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JustiLampiran extends Model
{
    use HasFactory;

    protected $table = 'justi_lampiran';

    protected $fillable = [
        'id_justi',
        'file',
        'tipe',
    ];
}
