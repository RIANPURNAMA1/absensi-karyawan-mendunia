<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\User;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    public function rekap(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $rekap = User::where('role', 'KARYAWAN')
            ->with(['cabang', 'absensi' => function ($q) use ($bulan, $tahun) {
                $q->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun);
            }])
            ->get()
            ->map(function ($user) {

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

        return view('admin.rekap.index', compact('rekap', 'bulan', 'tahun'));
    }
}
