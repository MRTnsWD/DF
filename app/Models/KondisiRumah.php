<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KondisiRumah extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_penduduk',
        'Luas_lantai',
        'Jenis_lantai',
        'Jenis_dinding',
        'Fasilitas_BAB',
        'Penerangan',
        'Air_minum',
        'BB_masak',
        'foto_rumah'
    ];

    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class, 'id_penduduk', 'id');
    }
}
