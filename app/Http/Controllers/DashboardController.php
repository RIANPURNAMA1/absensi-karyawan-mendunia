<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absensi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = Carbon::today()->toDateString();

        // 1. Data Karyawan
        $totalKaryawan = User::where('role', 'KARYAWAN')->count();
        $karyawanAktif = User::where('role', 'KARYAWAN')->where('status', 'AKTIF')->count();

        // 2. Data Absensi (Sesuai ENUM database Anda)
        // Menghitung Hadir (Hadir tepat waktu + Terlambat + Pulang Awal)
        $hadirHariIni = Absensi::whereDate('tanggal', $hariIni)
                            ->whereIn('status', ['HADIR', 'TERLAMBAT', 'PULANG LEBIH AWAL'])
                            ->count();

        // Menghitung Tepat Waktu
        $tepatWaktu = Absensi::whereDate('tanggal', $hariIni)
                            ->where('status', 'HADIR')
                            ->count();

        // Menghitung Terlambat (Langsung ambil dari status enum)
        $terlambat = Absensi::whereDate('tanggal', $hariIni)
                            ->where('status', 'TERLAMBAT')
                            ->count();

        // Menghitung Tidak Hadir (ALPA atau yang belum absen sama sekali)
        $alpa = Absensi::whereDate('tanggal', $hariIni)
                        ->where('status', 'ALPA')
                        ->count();
        
        // Kalkulasi sisa karyawan yang belum absen hari ini
        $belumAbsen = $karyawanAktif - $hadirHariIni;
        $tidakHadir = ($belumAbsen > 0 ? $belumAbsen : 0) + $alpa;

        // 3. Izin & Sakit
        $izinCuti = Absensi::whereDate('tanggal', $hariIni)
                            ->where('status', 'IZIN')
                            ->count();

        // --- Logika Map & Data Lainnya (Dummy jika belum ada tabelnya) ---
        $projectAktif = 0; $projectSelesai = 0;
        $totalTask = 0; $taskProgress = 0;
        $totalArtikel = 0; $artikelPublished = 0;
        $izinPending = 0;

        $absensis = Absensi::with(['user', 'cabang', 'shift'])
            ->orderBy('tanggal', 'desc')->get();

        $lokasiMarkers = $absensis->map(function($a) {
            $markers = [];
            if ($a->lat_masuk && $a->long_masuk) {
                $markers[] = ['lat' => $a->lat_masuk, 'lng' => $a->long_masuk, 'nama' => $a->user->name, 'jam' => $a->jam_masuk, 'tipe' => 'Masuk'];
            }
            return $markers;
        })->flatten(1);

        return view('admin.dashboard', compact(
            'absensis', 'lokasiMarkers', 'totalKaryawan', 'karyawanAktif', 
            'hadirHariIni', 'tepatWaktu', 'terlambat', 'tidakHadir',
            'projectAktif', 'projectSelesai', 'totalTask', 'taskProgress',
            'izinCuti', 'izinPending', 'totalArtikel', 'artikelPublished'
        ));
    }
}