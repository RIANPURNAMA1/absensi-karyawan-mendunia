<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KehadiranController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->tanggal ?? now()->toDateString();

        $absensis = \App\Models\Absensi::with(['user.shift', 'cabang'])
            ->whereDate('tanggal', $tanggal)
            ->orderBy('jam_masuk', 'asc')
            ->get();

        return view('admin.kehadiran.index', compact('absensis', 'tanggal'));
    }
}
