<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// JALANKAN DI AKHIR HARI (Contoh: Jam 23:55)
// Agar tidak mengganggu karyawan yang sedang absen di jam kerja
Schedule::command('absensi:generate-alpha')->dailyAt('23:55');

// Cek absen pulang bisa tetap setiap 10 menit atau di akhir shift
Schedule::command('app:cek-absen-pulang')->everyTenMinutes();