<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
 public function monitoring(Request $request)
{
    $tanggal   = $request->tanggal ?? now()->toDateString();
    $cabangId  = $request->cabang_id;

    $absensis = Absensi::with(['user', 'cabang'])
    ->whereDate('tanggal', $tanggal)
    ->where(function($q) {
        $q->whereNotNull('lat_masuk')
          ->orWhereNotNull('lat_pulang');
    })
    ->get();


    return view('admin.monitoring.index', compact('absensis', 'tanggal', 'cabangId'));
}

}
