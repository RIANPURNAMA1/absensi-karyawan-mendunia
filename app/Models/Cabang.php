<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    // Pastikan _token TIDAK ada di sini
    protected $fillable = [
        'nama_cabang',
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
