<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KehadiranController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil data pendukung untuk dropdown filter
        $list_cabang = \App\Models\Cabang::all();
        $list_divisi = \App\Models\Divisi::all();

        // 2. Inisialisasi Tanggal (Default: Awal bulan s/d hari ini)
        $tanggal    = $request->tanggal ?? now()->toDateString();
        $start_date = $request->start_date ?? now()->startOfMonth()->toDateString();
        $end_date   = $request->end_date ?? now()->endOfMonth()->toDateString();

        // 3. Ambil Input Filter Cabang & Divisi
        $cabang_id  = $request->cabang_id;
        $divisi_id  = $request->divisi_id;

        // 4. Query Absensi dengan Filter
        $absensis = \App\Models\Absensi::with(['user.shift', 'user.divisi', 'cabang'])
            // Filter berdasarkan rentang tanggal
            ->whereBetween('tanggal', [$start_date, $end_date])

            // Filter berdasarkan Cabang (jika dipilih)
            ->when($cabang_id, function ($query) use ($cabang_id) {
                return $query->whereHas('user', function ($q) use ($cabang_id) {
                    $q->where('cabang_id', $cabang_id);
                });
            })

            // Filter berdasarkan Divisi (jika dipilih)
            ->when($divisi_id, function ($query) use ($divisi_id) {
                return $query->whereHas('user', function ($q) use ($divisi_id) {
                    $q->where('divisi_id', $divisi_id);
                });
            })

            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_masuk', 'asc')
            ->get();

        // 5. Kirim semua variabel ke view
        return view('admin.kehadiran.index', compact(
            'absensis',
            'start_date',
            'end_date',
            'tanggal',
            'list_cabang',
            'list_divisi'
        ));
    }
}
