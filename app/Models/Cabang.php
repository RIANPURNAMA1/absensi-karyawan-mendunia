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

    public function users()
    {
        // Mencari user yang di dalam kolom JSON 'cabang_ids' terdapat ID cabang ini
        return User::whereJsonContains('cabang_ids', (string) $this->id)->get();
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }
}
