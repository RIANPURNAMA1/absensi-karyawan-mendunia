<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensis';

    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status',
        'foto_masuk',
        'foto_pulang',
        'keterangan',
    ];

    protected $casts = [
        'tanggal'    => 'date',
        'jam_masuk'  => 'datetime:H:i:s',
        'jam_keluar' => 'datetime:H:i:s',
    ];

    /* =========================
       RELASI
    ========================== */

    // User (karyawan)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Jadwal kerja berdasarkan hari (opsional)
    public function jadwal()
    {
        return $this->hasOne(JadwalKerja::class, 'user_id', 'user_id')
            ->where('hari', now()->translatedFormat('l'));
    }

    /* =========================
       ACCESSOR (OPSIONAL)
    ========================== */

    public function getFotoMasukUrlAttribute()
    {
        return $this->foto_masuk
            ? asset('storage/' . $this->foto_masuk)
            : null;
    }

    public function getFotoPulangUrlAttribute()
    {
        return $this->foto_pulang
            ? asset('storage/' . $this->foto_pulang)
            : null;
    }
}
