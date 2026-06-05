<?php

namespace App\Http\Controllers;

use App\Models\AbsensiSensei;
use App\Models\KelasSensei;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RekapKehadiranSenseiController extends Controller
{
    public function index()
    {
        $sensei = User::where('role', 'KARYAWAN')
            ->whereHas('kelasSensei')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.rekap-kehadiran-sensei', compact('sensei'));
    }

    public function getData(Request $request, $userId)
    {
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);

        $daysInMonth = Carbon::create($tahun, $bulan, 1)->daysInMonth;

        User::findOrFail($userId);

        $absensis = AbsensiSensei::where('user_id', $userId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->with('kelasSensei')
            ->get()
            ->groupBy(function ($item) {
                return $item->tanggal->toDateString();
            });

        $data = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateStr = sprintf('%s-%02s-%02s', $tahun, $bulan, $day);
            $dayOfWeek = Carbon::create($tahun, $bulan, $day)->dayOfWeek;

            $absensiArr = $absensis->get($dateStr);
            $rowData = [];

            if ($absensiArr && $absensiArr->isNotEmpty()) {
                foreach ($absensiArr as $absen) {
                    $kelas = $absen->kelasSensei;
                    if (!$kelas) continue;

                    $initial = strtoupper(substr($kelas->nama_kelas, 0, 1));
                    $status = $absen->status ?: 'BELUM ABSEN';

                    $color = match ($status) {
                        'HADIR' => 'bg-success',
                        'TERLAMBAT' => 'bg-warning',
                        'ALPA', 'TIDAK ABSEN PULANG' => 'bg-danger',
                        'PULANG LEBIH AWAL' => 'bg-info',
                        'LIBUR' => 'bg-secondary',
                        default => 'bg-light border',
                    };
                    $textColor = in_array($status, ['HADIR', 'TERLAMBAT', 'ALPA', 'TIDAK ABSEN PULANG', 'PULANG LEBIH AWAL', 'LIBUR']) ? 'text-white' : 'text-dark';

                    $rowData[] = [
                        'initial' => $initial,
                        'kelas_nama' => $kelas->nama_kelas,
                        'kelas_id' => $kelas->id,
                        'status' => $status,
                        'color' => $color,
                        'text_color' => $textColor,
                        'absensi_id' => $absen->id,
                    ];
                }
            } else {
                continue;
            }

            $data[$dateStr] = [
                'day' => $day,
                'day_of_week' => $dayOfWeek,
                'entries' => $rowData,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:absensi_sensei,id',
            'status' => 'required|in:HADIR,TERLAMBAT,PULANG LEBIH AWAL,TIDAK ABSEN PULANG,ALPA,LIBUR',
        ]);

        $absen = AbsensiSensei::findOrFail($request->id);
        $absen->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status kehadiran sensei berhasil diperbarui',
        ]);
    }
}
