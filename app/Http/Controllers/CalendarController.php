<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;

class CalendarController extends Controller
{

public function index()
{
    // Ini harus mengembalikan file blade, BUKAN data json
    return view('absensi.calendar.index'); 
}
 public function getRiwayatKalender(Request $request)
{
    $userId = auth()->id();
    $bulan = $request->query('bulan', date('m'));
    $tahun = $request->query('tahun', date('Y'));

    $data = Absensi::where('user_id', $userId)
        ->whereYear('tanggal', $tahun)
        ->whereMonth('tanggal', $bulan)
        ->get(['tanggal', 'status', 'jam_masuk', 'jam_keluar'])
        ->map(function ($item) {
            return [
                // Mengubah '2026-01-26T17:00:00.000000Z' menjadi '2026-01-26'
                'tanggal'   => \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d'),
                'status'    => $item->status,
                'jam_masuk' => $item->jam_masuk,
                'jam_keluar'=> $item->jam_keluar,
            ];
        });

    return response()->json($data);
}
}
