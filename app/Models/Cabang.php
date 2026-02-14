<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
 protected $fillable = [
    'kode_cabang',
    'nama_cabang',
    'status_pusat',
    'latitude',
    'longitude',
    'radius',
    'alamat'
];

    public function user()
    {
        return $this->hasMany(User::class, 'cabang_id');
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }
}
