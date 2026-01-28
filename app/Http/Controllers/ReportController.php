<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $user_id = Auth::user()->id;
        $now = Carbon::now();
        $bulanIni = $now->month;
        $tahunIni = $now->year;

        // 1. Ambil Statistik Ringkasan Bulan Ini
        $stats = [
            'hadir' => Absensi::where('user_id', $user_id)
                        ->whereMonth('tanggal', $bulanIni)
                        ->whereYear('tanggal', $tahunIni)
                        ->whereIn('status', ['HADIR', 'TERLAMBAT'])
                        ->count(),

            'terlambat' => Absensi::where('user_id', $user_id)
                        ->whereMonth('tanggal', $bulanIni)
                        ->whereYear('tanggal', $tahunIni)
                        ->where('status', 'TERLAMBAT')
                        ->count(),

            'izin' => Absensi::where('user_id', $user_id)
                        ->whereMonth('tanggal', $bulanIni)
                        ->whereYear('tanggal', $tahunIni)
                        ->where('status', 'IZIN')
                        ->count(),

            'alpa' => Absensi::where('user_id', $user_id)
                        ->whereMonth('tanggal', $bulanIni)
                        ->whereYear('tanggal', $tahunIni)
                        ->where('status', 'ALPA')
                        ->count(),
        ];

        // 2. Hitung Skor Disiplin (Contoh sederhana: Persentase Kehadiran)
        // Asumsi hari kerja efektif adalah 22 hari
        $hariEfektif = 22;
        $skor = ($stats['hadir'] > 0) ? round(($stats['hadir'] / $hariEfektif) * 100) : 0;
        $skor = $skor > 100 ? 100 : $skor; // Cap di 100%

        // 3. Ambil 5 Aktivitas Terakhir
        $riwayatTerakhir = Absensi::where('user_id', $user_id)
                            ->orderBy('tanggal', 'desc')
                            ->orderBy('jam_masuk', 'desc')
                            ->take(5)
                            ->get();

        return view('absensi.report.index', compact('stats', 'skor', 'riwayatTerakhir'));
    }
}