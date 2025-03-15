<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kasbon extends Model
{
    use HasFactory;
    protected $table = 'keuangan_kasbon';
    protected $fillable = [
        'nama',
        'jumlah_kasbon',
        'pekerjaan_proyek_kasbon',
        'pertanggung_jawaban',
        'tanggal_verifikasi_setelah_cair',
        'jangka_waktu',
        'status',
        'tanggal_waktu_close_kasbon',
        'realisasi',
        'no_ppk',
        'perpanjangan_batas_waktu_pertanggungjawaban_1',
        'perpanjangan_batas_waktu_pertanggungjawaban_2',
        'divisi',
        'kategori',
        'lampiran_foto',
    ];
    
}

    
    
