<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shift; // <--- TAMBAHKAN BARIS INI
use App\Models\Absensi;
use App\Models\Izin;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = Carbon::today()->toDateString();
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        // 1. Data Karyawan & Ringkasan (Box Bawah)
        $karyawanAktif = User::where('role', 'KARYAWAN')->where('status', 'AKTIF')->count();
        $izinPending = Izin::where('status', 'PENDING')->count(); // Pastikan model Izin ada

        // 2. Data Absensi HARI INI (Untuk Donut Chart & Box)
        $tepatWaktu = Absensi::whereDate('tanggal', $hariIni)->where('status', 'HADIR')->count();
        $terlambat = Absensi::whereDate('tanggal', $hariIni)->where('status', 'TERLAMBAT')->count();
        $alpa = Absensi::whereDate('tanggal', $hariIni)->where('status', 'ALPA')->count();
        $izinCuti = Absensi::whereDate('tanggal', $hariIni)->where('status', 'IZIN')->count();

        $hadirHariIni = $tepatWaktu + $terlambat;
        $belumAbsen = $karyawanAktif - ($hadirHariIni + $izinCuti);
        $belumAbsen = ($belumAbsen < 0) ? 0 : $belumAbsen;

        // Data untuk Donut Chart (Komposisi Hari Ini)
        $donutData = [
            'hadir' => $tepatWaktu,
            'terlambat' => $terlambat,
            'izin' => $izinCuti,
            'alpa' => $alpa
        ];

        // 3. STATISTIK TREN 6 BULAN TERAKHIR (Grouped Bar Chart)
        $labelsBar = [];
        $dataHadirBar = [];
        $dataTerlambatBar = [];
        $dataAlpaBar = [];

        for ($m = 5; $m >= 0; $m--) {
            $date = Carbon::now()->subMonths($m);
            $labelsBar[] = $date->translatedFormat('F Y'); // Contoh: Januari 2026

            // Hitung Hadir Tepat Waktu
            $dataHadirBar[] = Absensi::whereMonth('tanggal', $date->month)
                ->whereYear('tanggal', $date->year)
                ->where('status', 'HADIR')
                ->count();

            // Hitung Terlambat
            $dataTerlambatBar[] = Absensi::whereMonth('tanggal', $date->month)
                ->whereYear('tanggal', $date->year)
                ->where('status', 'TERLAMBAT')
                ->count();

            // Hitung Alpa
            $dataAlpaBar[] = Absensi::whereMonth('tanggal', $date->month)
                ->whereYear('tanggal', $date->year)
                ->where('status', 'ALPA')
                ->count();
        }

        // 4. Data Map & Tabel Terbaru
        $absensis = Absensi::with(['user', 'cabang', 'shift'])
            ->orderBy('tanggal', 'desc') // Tanggal terbaru dulu
            ->orderBy('created_at', 'desc') // Jika tanggal sama, jam terbaru (inputan terakhir) di atas
            ->take(100)
            ->get();

        $lokasiMarkers = $absensis->filter(fn($a) => $a->lat_masuk && $a->long_masuk)
            ->map(fn($a) => [
                'lat' => $a->lat_masuk,
                'lng' => $a->long_masuk,
                'nama' => $a->user->name,
                'jam' => $a->jam_masuk,
                'tipe' => 'Masuk'
            ])->values();

        // 5. Return View dengan semua variabel
        return view('admin.dashboard', compact(
            'absensis',
            'lokasiMarkers',
            'karyawanAktif',
            'hadirHariIni',
            'tepatWaktu',
            'terlambat',
            'belumAbsen',
            'izinPending',
            'labelsBar',
            'dataHadirBar',
            'dataTerlambatBar',
            'dataAlpaBar',
            'donutData'
        ));
    }
}
