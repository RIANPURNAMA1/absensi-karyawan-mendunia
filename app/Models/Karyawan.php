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
        'user_id', 'nip', 'jabatan', 'departemen', 
        'no_hp', 'alamat', 'foto_profil', 'tanggal_masuk', 'status_kerja'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
