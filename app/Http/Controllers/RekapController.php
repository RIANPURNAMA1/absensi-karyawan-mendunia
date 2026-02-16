<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RekapController extends Controller
{
    public function rekap(Request $request)
    {
        // 1. Ambil data untuk dropdown filter
        $list_cabang = \App\Models\Cabang::all();
        $list_divisi = \App\Models\Divisi::all();

        // 2. Ambil parameter dari request
        $start_date = $request->start_date ?? now()->startOfMonth()->toDateString();
        $end_date   = $request->end_date ?? now()->endOfMonth()->toDateString();
        $cabang_id  = $request->cabang_id;
        $divisi_id  = $request->divisi_id;

        // 3. Query User dengan Filter dan Relasi
        $rekap = User::where('role', 'KARYAWAN')
            ->when($cabang_id, function ($q) use ($cabang_id) {
                return $q->where('cabang_id', $cabang_id);
            })
            ->when($divisi_id, function ($q) use ($divisi_id) {
                return $q->where('divisi_id', $divisi_id);
            })
            ->with(['cabang', 'absensi' => function ($q) use ($start_date, $end_date) {
                $q->whereBetween('tanggal', [$start_date, $end_date]);
            }, 'lembur' => function ($q) use ($start_date, $end_date) {
                // Hanya ambil lembur yang sudah APPROVED dalam range tanggal
                $q->where('status', 'APPROVED')
                    ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
            }])
            ->get()
            ->map(function ($user) {
                // Hitung statistik status kehadiran
                $hadir       = $user->absensi->where('status', 'HADIR')->count();
                $terlambat   = $user->absensi->where('status', 'TERLAMBAT')->count();
                $izin        = $user->absensi->where('status', 'IZIN')->count();
                $alpa        = $user->absensi->where('status', 'ALPA')->count();
                $pulangAwal  = $user->absensi->where('status', 'PULANG LEBIH AWAL')->count();

                // 1. Hitung Total Jam Kerja Reguler (dalam detik)
                $totalDetikKerja = 0;
                foreach ($user->absensi as $absen) {
                    if (!empty($absen->jam_masuk) && !empty($absen->jam_keluar)) {
                        $totalDetikKerja += \Carbon\Carbon::parse($absen->jam_masuk)->diffInSeconds(\Carbon\Carbon::parse($absen->jam_keluar));
                    }
                }

                // 2. Hitung Total Jam Lembur (dalam detik)
                $jumlahLembur = $user->lembur->count();
                $totalDetikLembur = 0;
                foreach ($user->lembur as $l) {
                    if (!empty($l->jam_masuk) && !empty($l->jam_keluar)) {
                        $totalDetikLembur += \Carbon\Carbon::parse($l->jam_masuk)->diffInSeconds(\Carbon\Carbon::parse($l->jam_keluar));
                    }
                }

                // 3. Hitung Grand Total (Kerja + Lembur)
                $grandTotalDetik = $totalDetikKerja + $totalDetikLembur;

                // Fungsi Helper format jam (Contoh: 160j 30m)
                $formatWaktu = function ($detikTotal) {
                    $h = floor($detikTotal / 3600);
                    $m = floor(($detikTotal / 60) % 60);
                    return "{$h}j {$m}m";
                };

                return (object)[
                    'nama'             => $user->name,
                    'cabang'           => $user->cabang->nama_cabang ?? '-',
                    'hadir'            => $hadir,
                    'terlambat'        => $terlambat,
                    'izin'             => $izin,
                    'alpa'             => $alpa,
                    'pulang_awal'      => $pulangAwal,
                    'jumlah_lembur'    => $jumlahLembur,
                    'total_jam_lembur' => $formatWaktu($totalDetikLembur),
                    'total_hadir'      => $hadir + $terlambat + $pulangAwal,
                    'total_jam_kerja'  => $formatWaktu($totalDetikKerja),
                    'grand_total_jam'  => $formatWaktu($grandTotalDetik),
                ];
            });

        return view('admin.rekap.index', compact('rekap', 'start_date', 'end_date', 'list_cabang', 'list_divisi'));
    }
}
