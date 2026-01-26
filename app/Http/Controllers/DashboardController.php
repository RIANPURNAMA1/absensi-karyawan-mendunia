<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
{
    // Ambil semua absensi beserta relasi user, cabang, dan shift
    $absensis = Absensi::with(['user', 'cabang', 'shift'])
        ->orderBy('tanggal', 'desc')
        ->get();

    // Siapkan data lokasi untuk Leaflet
    $lokasiMarkers = $absensis->map(function($a) {
        $markers = [];

        if ($a->lat_masuk && $a->long_masuk) {
            $markers[] = [
                'lat' => $a->lat_masuk,
                'lng' => $a->long_masuk,
                'nama' => $a->user->name,
                'jam' => $a->jam_masuk,
                'tipe' => 'Masuk',
            ];
        }

        if ($a->lat_pulang && $a->long_pulang) {
            $markers[] = [
                'lat' => $a->lat_pulang,
                'lng' => $a->long_pulang,
                'nama' => $a->user->name,
                'jam' => $a->jam_keluar,
                'tipe' => 'Pulang',
            ];
        }

        return $markers;
    })->flatten(1); // gabungkan array marker

    return view('admin.dashboard', compact('absensis', 'lokasiMarkers'));
}

}
