<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Absensi;
use App\Models\HariLibur; // Tambahkan ini
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateAlphaAbsensi extends Command
{
    protected $signature = 'absensi:generate-alpha';
    protected $description = 'Otomatis set status ALPA atau LIBUR bagi karyawan';

    public function handle()
    {
        $today = Carbon::today();
        
        // 1. Cek apakah hari ini Libur (Weekend atau Tanggal Merah)
        $isLibur = HariLibur::apakahLibur($today);

        // Ambil karyawan aktif
        $users = User::where('role', 'KARYAWAN')
            ->where('status', 'AKTIF')
            ->whereNotNull('shift_id')
            ->with('shift')
            ->get();

        foreach ($users as $user) {
            $shift = $user->shift;
            if (!$shift) continue;

            // 2. Logic Tambahan: Jika hari ini LIBUR, kita set status LIBUR
            // Jika hari ini KERJA, kita cek apakah sudah lewat batas hadir untuk set ALPA
            if (!$isLibur) {
                $jamMasukShift = Carbon::parse($shift->jam_masuk)->setDateFrom($today);
                $batasHadir = $jamMasukShift->copy()->addMinutes($shift->toleransi);

                // Jika belum lewat batas waktu toleransi, jangan di-ALPA dulu
                if (Carbon::now()->lt($batasHadir)) {
                    continue;
                }
            }

            // 3. Cek apakah sudah ada record absensi (biar tidak double)
            $sudahAbsen = Absensi::where('user_id', $user->id)
                ->whereDate('tanggal', $today)
                ->exists();

            if (!$sudahAbsen) {
                Absensi::create([
                    'user_id'   => $user->id,
                    'shift_id'  => $shift->id,
                    'cabang_id' => $user->cabang_id,
                    'tanggal'   => $today,
                    // Jika libur set LIBUR, jika tidak set ALPA
                    'status'    => $isLibur ? 'LIBUR' : 'ALPA',
                    'keterangan' => $isLibur 
                        ? 'Libur otomatis (Weekend/Nasional)' 
                        : 'Tidak melakukan absensi sampai batas waktu'
                ]);
            }
        }

        $this->info('Generate status harian selesai: ' . ($isLibur ? 'Status LIBUR' : 'Status ALPA'));
    }
}