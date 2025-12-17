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
}
