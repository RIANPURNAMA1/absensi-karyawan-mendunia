<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RekapController extends Controller
{
    public function rekap(Request $request)
    {
        $list_cabang = \App\Models\Cabang::all();
        $list_divisi = \App\Models\Divisi::all();

        $start_date = $request->start_date ?? now()->startOfMonth()->toDateString();
        $end_date   = $request->end_date   ?? now()->endOfMonth()->toDateString();
        $cabang_id  = $request->cabang_id;
        $divisi_id  = $request->divisi_id;

        $rekap = User::where('role', 'KARYAWAN')
            ->when($cabang_id, fn($q) => $q->whereJsonContains('cabang_ids', (string) $cabang_id))
            ->when($divisi_id, fn($q) => $q->where('divisi_id', $divisi_id))
            ->with([
                'divisi',
                'absensi' => fn($q) => $q->whereBetween('tanggal', [$start_date, $end_date]),
                'lembur'  => fn($q) => $q->where('status', 'APPROVED')
                                         ->whereBetween('created_at', [$start_date.' 00:00:00', $end_date.' 23:59:59']),
            ])
            ->get()
            ->map(function ($user) {
                $hadir      = $user->absensi->where('status', 'HADIR')->count();
                $terlambat  = $user->absensi->where('status', 'TERLAMBAT')->count();
                $izin       = $user->absensi->where('status', 'IZIN')->count();
                $alpa       = $user->absensi->where('status', 'ALPA')->count();
                $pulangAwal = $user->absensi->where('status', 'PULANG LEBIH AWAL')->count();
                $libur      = $user->absensi->where('status', 'LIBUR')->count();

                $totalDetikKerja = 0;
                foreach ($user->absensi as $absen) {
                    if (!empty($absen->jam_masuk) && !empty($absen->jam_keluar)) {
                        $totalDetikKerja += Carbon::parse($absen->jam_masuk)
                            ->diffInSeconds(Carbon::parse($absen->jam_keluar));
                    }
                }

                $jumlahLembur     = $user->lembur->count();
                $totalDetikLembur = 0;
                foreach ($user->lembur as $l) {
                    if (!empty($l->jam_masuk) && !empty($l->jam_keluar)) {
                        $totalDetikLembur += Carbon::parse($l->jam_masuk)
                            ->diffInSeconds(Carbon::parse($l->jam_keluar));
                    }
                }

                $grandTotalDetik = $totalDetikKerja + $totalDetikLembur;

                $fmt = fn($s) => floor($s/3600).'j '.floor(($s/60)%60).'m';

                $namaCabang = $user->cabang->pluck('nama_cabang')->implode(', ') ?: '-';

                return (object) [
                    'nama'             => $user->name,
                    'jabatan'          => $user->jabatan,
                    'cabang'           => $namaCabang,
                    'hadir'            => $hadir,
                    'terlambat'        => $terlambat,
                    'izin'             => $izin,
                    'alpa'             => $alpa,
                    'pulang_awal'      => $pulangAwal,
                    'libur'            => $libur,
                    'jumlah_lembur'    => $jumlahLembur,
                    'total_jam_lembur' => $fmt($totalDetikLembur),
                    'total_hadir'      => $hadir + $terlambat + $pulangAwal,
                    'total_jam_kerja'  => $fmt($totalDetikKerja),
                    'grand_total_jam'  => $fmt($grandTotalDetik),
                ];
            });

        return view('admin.rekap.index', compact(
            'rekap', 'start_date', 'end_date', 'list_cabang', 'list_divisi'
        ));
    }
}