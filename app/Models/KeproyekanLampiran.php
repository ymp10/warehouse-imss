<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeproyekanLampiran extends Model
{
    use HasFactory;

    protected $table = 'keproyekan_lampiran';

    protected $fillable = [
        'keproyekan_id',
        'file',
        'tipe',
    ];
}
