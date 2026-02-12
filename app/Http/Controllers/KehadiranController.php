<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KehadiranController extends Controller
{
    public function index(Request $request)
{

// Gunakan satu variabel tanggal saja
    $tanggal = $request->tanggal ?? now()->toDateString();
    // Mengambil tanggal mulai dan selesai. 
    // Jika tidak ada, default ke awal bulan sampai akhir bulan ini.
    $start_date = $request->start_date ?? now()->startOfMonth()->toDateString();
    $end_date = $request->end_date ?? now()->endOfMonth()->toDateString();

    $absensis = \App\Models\Absensi::with(['user.shift', 'cabang'])
        // Menggunakan whereBetween untuk memfilter rentang tanggal
        ->whereBetween('tanggal', [$start_date, $end_date])
        ->orderBy('tanggal', 'desc') // Diurutkan berdasarkan tanggal terbaru
        ->orderBy('jam_masuk', 'asc')
        ->get();

    // Kirim start_date dan end_date ke view agar input date tetap terisi setelah diklik
    return view('admin.kehadiran.index', compact('absensis', 'start_date', 'end_date', 'tanggal'));
}
}
