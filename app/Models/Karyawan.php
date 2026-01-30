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
        // Login & role
        'name',
        'email',
        'password',
        'role',
        'status',
        'last_login',
        'cabang_id',

        // Info karyawan
        'divisi_id',
        'shift_id',
        'nip',
        'jabatan',
        'no_hp',
        'alamat',
        'foto_profil',
        'tanggal_masuk',
        'status_kerja',
        'face_embedding',

        // Data tambahan
        'foto_ktp',
        'foto_ijazah',
        'foto_kk',
        'cv_file',
        'sertifikat_file',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'status_pernikahan'
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
