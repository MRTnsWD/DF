<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Klasifikasi extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_penduduk',
        'pekerjaan',
        'kondisi',
        'kecocokan',
        'keterangan',
        'pendidikan'
    ];

    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class, 'id_penduduk', 'id');
    }
}
