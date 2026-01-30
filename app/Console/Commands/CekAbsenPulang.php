<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Absensi;
use Carbon\Carbon;

class CekAbsenPulang extends Command
{
    protected $signature = 'app:cek-absen-pulang';
    protected $description = 'Menandai absensi yang tidak absen pulang lewat 3 jam dari jam shift';

    public function handle()
    {
        $now = Carbon::now();

        $absensis = Absensi::with('shift')
            ->whereNull('jam_keluar')
            ->whereIn('status', ['HADIR', 'TERLAMBAT'])
            ->get();

        foreach ($absensis as $absen) {

            if (!$absen->shift) continue;

            $jamMasukShift  = Carbon::parse($absen->shift->jam_masuk);
            $jamPulangShift = Carbon::parse($absen->shift->jam_pulang);

            if ($jamPulangShift->lt($jamMasukShift)) {
                $jamPulangShift->addDay();
            }

        $batasAkhir = $jamPulangShift->copy()->addHours(5);



            if ($now->greaterThan($batasAkhir)) {

                $absen->update([
                    'status' => 'TIDAK ABSEN PULANG',
                    'keterangan' => 'Otomatis sistem: tidak absen pulang'
                ]);

                $this->info("User {$absen->user_id} → TIDAK ABSEN PULANG");
            }
        }

        $this->info('Selesai cek absen pulang.');
    }
}
