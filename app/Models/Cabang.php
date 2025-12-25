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
}
