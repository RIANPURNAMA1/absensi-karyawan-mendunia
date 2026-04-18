<?php

namespace App\Http\Controllers;

use App\Models\AbsensiSensei;
use App\Models\KelasSensei;
use App\Models\User;
use Illuminate\Http\Request;

class KehadiranSenseiController extends Controller
{
    public function index(Request $request)
    {
        $list_cabang = \App\Models\Cabang::all();
        $list_divisi = \App\Models\Divisi::all();

        $list_sensei = User::where('role', 'KARYAWAN')
            ->whereHas('kelasSensei')
            ->with(['kelasSensei' => function ($q) {
                $q->orderBy('nama_kelas', 'asc');
            }])
            ->orderBy('name', 'asc')
            ->get();

        $list_kelas = KelasSensei::orderBy('nama_kelas', 'asc')->get();

        $start_date = $request->start_date ?? now('Asia/Jakarta')->startOfMonth()->toDateString();
        $end_date = $request->end_date ?? now('Asia/Jakarta')->toDateString();
        $user_id = $request->user_id;
        $kelas_id = $request->kelas_id;
        $status = $request->status;

        $query = AbsensiSensei::with(['user', 'kelasSensei'])
            ->whereBetween('tanggal', [$start_date, $end_date]);

        if ($user_id) {
            $query->where('user_id', $user_id);
        }

        if ($kelas_id) {
            $query->where('kelas_sensei_id', $kelas_id);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $absensis = $query->orderBy('kelas_sensei_id', 'asc')
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_masuk', 'asc')
            ->get();

        // Group by kelas
        $groupedAbsensis = $absensis->groupBy('kelas_sensei_id')->map(function ($items) {
            $firstItem = $items->first();
            $kelas = $firstItem->kelasSensei;
            $tanggalMulai = \Carbon\Carbon::parse($kelas->tanggal_mulai);
            $tanggalSelesai = \Carbon\Carbon::parse($kelas->tanggal_selesai);

            // Hitung total pertemuan dari selisih tanggal
            $totalPertemuan = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

            // Hitung pertemuan ke- untuk setiap absensi
            $items = $items->map(function ($absen) use ($tanggalMulai) {
                $tanggalAbsen = \Carbon\Carbon::parse($absen->tanggal);
                $absen->pertemuan_ke = $tanggalMulai->diffInDays($tanggalAbsen) + 1;

                return $absen;
            });

            return [
                'kelas' => $kelas,
                'absensis' => $items,
                'total' => $totalPertemuan,
                'total_absen' => $items->count(),
                'hadir' => $items->where('status', 'HADIR')->count(),
                'terlambat' => $items->where('status', 'TERLAMBAT')->count(),
                'pulang_cepat' => $items->where('status', 'PULANG LEBIH AWAL')->count(),
                'tidak_pulang' => $items->where('status', 'TIDAK ABSEN PULANG')->count(),
            ];
        });

        $rekap = $this->generateRekap($absensis);

        return view('admin.kehadiran_sensei.index', [
            'groupedAbsensis' => $groupedAbsensis,
            'absensis' => $absensis,
            'rekap' => $rekap,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'list_cabang' => $list_cabang,
            'list_divisi' => $list_divisi,
            'list_sensei' => $list_sensei,
            'list_kelas' => $list_kelas,
            'user_id_selected' => $user_id,
            'kelas_id_selected' => $kelas_id,
            'status_selected' => $status,
        ]);
    }

    private function generateRekap($absensis)
    {
        $total = $absensis->count();
        $hadir = $absensis->where('status', 'HADIR')->count();
        $terlambat = $absensis->where('status', 'TERLAMBAT')->count();
        $pulangCepat = $absensis->where('status', 'PULANG LEBIH AWAL')->count();
        $tidakAbsen = $absensis->where('status', 'TIDAK ABSEN PULANG')->count();

        return [
            'total' => $total,
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'pulang_cepat' => $pulangCepat,
            'tidak_absen_pulang' => $tidakAbsen,
        ];
    }

    public function getKelasByUser($userId)
    {
        $kelas = KelasSensei::where('user_id', $userId)
            ->where('status', 'aktif')
            ->get();

        return response()->json($kelas);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:absensi_sensei,id',
            'status' => 'required|in:HADIR,TERLAMBAT,PULANG LEBIH AWAL,TIDAK ABSEN PULANG',
        ]);

        $absen = AbsensiSensei::findOrFail($request->id);
        $absen->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status berhasil diupdate');
    }

    public function getRiwayat($userId, $kelasId)
    {
        $kelas = KelasSensei::with('user')->findOrFail($kelasId);

        $tanggalMulai = \Carbon\Carbon::parse($kelas->tanggal_mulai);
        $tanggalSelesai = \Carbon\Carbon::parse($kelas->tanggal_selesai);
        $totalPertemuan = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

        $absensis = AbsensiSensei::where('user_id', $userId)
            ->where('kelas_sensei_id', $kelasId)
            ->orderBy('tanggal', 'asc')
            ->get()
            ->map(function ($absen) use ($tanggalMulai) {
                $tanggalAbsen = \Carbon\Carbon::parse($absen->tanggal);
                $absen->pertemuan_ke = $tanggalMulai->diffInDays($tanggalAbsen) + 1;

                return $absen;
            });

        $stats = [
            'hadir' => $absensis->where('status', 'HADIR')->count(),
            'terlambat' => $absensis->where('status', 'TERLAMBAT')->count(),
            'pulang_cepat' => $absensis->where('status', 'PULANG LEBIH AWAL')->count(),
            'tidak_pulang' => $absensis->where('status', 'TIDAK ABSEN PULANG')->count(),
        ];

        return response()->json([
            'kelas' => [
                'nama_kelas' => $kelas->nama_kelas,
                'level' => $kelas->level,
                'tanggal_mulai' => \Carbon\Carbon::parse($kelas->tanggal_mulai)->format('d M Y'),
                'tanggal_selesai' => \Carbon\Carbon::parse($kelas->tanggal_selesai)->format('d M Y'),
                'sensei' => $kelas->user->name ?? '-',
            ],
            'total_pertemuan' => $totalPertemuan,
            'absensis' => $absensis,
            'stats' => $stats,
        ]);
    }
}
