<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\User;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    public function rekap(Request $request)
    {
        // 1. Ambil range tanggal dari request. 
        // Jika kosong, default ke awal bulan ini sampai akhir bulan ini.
        $start_date = $request->start_date ?? now()->startOfMonth()->toDateString();
        $end_date   = $request->end_date ?? now()->endOfMonth()->toDateString();

        $rekap = User::where('role', 'KARYAWAN')
            ->with(['cabang', 'absensi' => function ($q) use ($start_date, $end_date) {
                // 2. Filter absensi berdasarkan range tanggal
                $q->whereBetween('tanggal', [$start_date, $end_date]);
            }])
            ->get()
            ->map(function ($user) {
                // 3. Menghitung status berdasarkan data yang sudah difilter di atas
                $hadir       = $user->absensi->where('status', 'HADIR')->count();
                $terlambat   = $user->absensi->where('status', 'TERLAMBAT')->count();
                $izin        = $user->absensi->where('status', 'IZIN')->count();
                $alpa        = $user->absensi->where('status', 'ALPA')->count();
                $pulangAwal  = $user->absensi->where('status', 'PULANG LEBIH AWAL')->count();

                return (object)[
                    'nama'          => $user->name,
                    'cabang'        => $user->cabang->nama_cabang ?? '-',
                    'hadir'         => $hadir,
                    'terlambat'     => $terlambat,
                    'izin'          => $izin,
                    'alpa'          => $alpa,
                    'pulang_awal'   => $pulangAwal,
                    'total_hadir'   => $hadir + $terlambat,
                ];
            });

        // 4. Kirim variabel ke view
        return view('admin.rekap.index', compact('rekap', 'start_date', 'end_date'));
    }
}
