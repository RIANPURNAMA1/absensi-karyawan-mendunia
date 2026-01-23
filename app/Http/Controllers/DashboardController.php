<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil semua absensi beserta relasi karyawan
        $absensi = Absensi::with('user', 'cabang')->orderBy('tanggal', 'desc')->get();
        return view('admin.dashboard', compact('absensi'));
    }
}
