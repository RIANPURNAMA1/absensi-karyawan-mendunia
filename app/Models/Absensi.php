<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status',
        'keterangan'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    // Relasi ke jadwal kerja berdasarkan hari
    public function jadwal()
    {
        return $this->hasOne(JadwalKerja::class, 'karyawan_id', 'karyawan_id')
                    ->where('hari', \Carbon\Carbon::parse($this->tanggal)->format('l')); 
    }
}
