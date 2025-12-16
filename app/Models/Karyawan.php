<?php

// app/Models/Karyawan.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';

    protected $fillable = [
        'user_id',
        'divisi_id',
        'nip',
        'name',
        'jabatan',
        'departemen',
        'no_hp',
        'email',
        'alamat',
        'foto_profil',
        'tanggal_masuk',
        'status_kerja'
    ];



    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }




    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'karyawan_id', 'id');
    }

    public function jadwalKerja()
    {
        return $this->hasMany(JadwalKerja::class, 'karyawan_id', 'id');
    }
}
