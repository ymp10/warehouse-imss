<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bpm extends Model
{
    use HasFactory;
    protected $table = 'bpm';
    protected $fillable = [
        'proyek_id',
        'no_bpm',
        'tgl_bpm',
        'dasar_bpm',
        'id_user',
        'nomor_lppb',
        'tanggal_lppb',
        'is_read'
    ];
}
