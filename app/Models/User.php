<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'divisi_id',
        'cabang_id',
        'shift_id', // PASTIKAN ADA INI
        'status',
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

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'last_login' => 'datetime',
    ];

    public function isHR()
    {
        return $this->role === 'HR';
    }

    public function isManager()
    {
        return $this->role === 'MANAGER';
    }

    public function isKaryawan()
    {
        return $this->role === 'KARYAWAN';
    }


    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }
    public function shift()
    {
        // Pastikan foreign key di tabel users adalah shift_id
        return $this->belongsTo(Shift::class, 'shift_id');
    }
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }
}
