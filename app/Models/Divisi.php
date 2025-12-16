<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Karyawan;

class Divisi extends Model
{

    protected $fillable = ['nama_divisi'];

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'divisi_id');
    }
}
