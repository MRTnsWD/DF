<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penduduk extends Model
{
    use HasFactory;

    protected $fillable = [
        'No_KK',
        'NIK',
        'pas_foto', // Add this line
        'Nama_lengkap',
        'Hbg_kel',
        'JK',
        'tmpt_lahir',
        'tgl_lahir',
        'Agama',
        'Pendidikan_terakhir',
        'Jenis_bantuan',
        'Penerima_bantuan',
        'Jenis_bantuan_lain'
    ];

    public function pekerjaan()
    {
        return $this->hasOne(Pekerjaan::class, 'id_penduduk', 'id');
    }

    public function kondisiRumah()
    {
        return $this->hasOne(KondisiRumah::class, 'id_penduduk', 'id');
    }

    public function klasifikasi()
    {
        return $this->hasOne(Klasifikasi::class, 'id_penduduk', 'id');
    }
}
