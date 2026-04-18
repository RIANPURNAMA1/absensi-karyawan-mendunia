<?php

namespace App\Console\Commands;

use App\Models\AbsensiSensei;
use App\Models\HariLibur;
use App\Models\KelasSensei;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateAlphaSensei extends Command
{
    protected $signature = 'absensi:generate-alpha-sensei';

    protected $description = 'Otomatis set status ALPA/LIBUR/TIDAK ABSEN PULANG untuk sensei di akhir hari';

    public function handle()
    {
        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $now = Carbon::now('Asia/Jakarta');

        // 1. Cek apakah hari ini Libur (Sabtu/Minggu atau hari libur admin)
        $isLibur = HariLibur::apakahLibur($today);

        // Ambil sensei yang memiliki kelas aktif hari ini
        $kelasAktif = KelasSensei::where('status', 'aktif')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->with('user')
            ->get();

        foreach ($kelasAktif as $kelas) {
            $sensei = $kelas->user;
            if (! $sensei) {
                continue;
            }

            // 2. CEK APAKAH SUDAH ADA RECORD ABSENSI SENSEI
            $absensi = AbsensiSensei::where('kelas_sensei_id', $kelas->id)
                ->where('user_id', $sensei->id)
                ->where('tanggal', $today)
                ->first();

            // JIKA SUDAH ADA DATA
            if ($absensi) {
                // Jika dia sudah masuk tapi lupa absen pulang sampai akhir hari
                if ($absensi->jam_masuk && ! $absensi->jam_keluar) {
                    if ($isLibur) {
                        // Kalau hari libur, tetap LIBUR
                        $absensi->update([
                            'status' => 'LIBUR',
                            'catatan' => 'Hari libur otomatis',
                        ]);
                    } else {
                        // Bukan libur tapi lupa absen pulang
                        $absensi->update([
                            'status' => 'TIDAK ABSEN PULANG',
                            'catatan' => 'Sistem otomatis: Lupa absen pulang.',
                        ]);
                    }
                }

                continue;
            }

            // JIKA BELUM ADA DATA SAMA SEKALI
            if ($now->hour >= 20 || $isLibur) {
                AbsensiSensei::create([
                    'kelas_sensei_id' => $kelas->id,
                    'user_id' => $sensei->id,
                    'tanggal' => $today,
                    'status' => $isLibur ? 'LIBUR' : 'ALPA',
                    'catatan' => $isLibur
                        ? 'Libur otomatis (Weekend/Nasional)'
                        : 'Tidak melakukan absensi',
                ]);
            }
        }

        $this->info('Generate status sensei harian selesai.');
    }
}
