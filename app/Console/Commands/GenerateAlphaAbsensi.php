<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateAlphaAbsensi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absensi:generate-alpha';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        // Ambil semua karyawan aktif yang punya shift
        $users = User::where('role', 'KARYAWAN')
            ->where('status', 'AKTIF')
            ->whereNotNull('shift_id')
            ->with('shift')
            ->get();

        foreach ($users as $user) {

            $shift = $user->shift;
            if (!$shift) continue;

            $jamMasukShift = Carbon::parse($shift->jam_masuk)->setDateFrom($today);
            $batasHadir = $jamMasukShift->copy()->addMinutes($shift->toleransi);




            // Kalau sekarang belum lewat batas → skip
            if (Carbon::now()->lt($batasHadir)) {
                continue;
            }

            // Cek apakah sudah ada absensi hari ini
            $sudahAbsen = Absensi::where('user_id', $user->id)
                ->whereDate('tanggal', $today)
                ->exists();

            if (!$sudahAbsen) {
                Absensi::create([
                    'user_id'   => $user->id,
                    'shift_id'  => $shift->id,
                    'cabang_id' => $user->cabang_id,
                    'tanggal'   => $today,
                    'status'    => 'ALPA',
                    'keterangan' => 'Tidak melakukan absensi sampai batas waktu'
                ]);
            }
        }

        $this->info('Generate ALPA selesai');
    }
}
