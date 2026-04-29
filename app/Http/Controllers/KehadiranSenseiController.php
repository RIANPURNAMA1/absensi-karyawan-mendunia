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

        // Get all kelas sensei grouped by user (creator)
        $kelasByUser = User::where('role', 'KARYAWAN')
            ->whereHas('kelasSensei')
            ->with(['kelasSensei' => function ($q) {
                $q->orderBy('tanggal_mulai', 'desc');
            }])
            ->get()
            ->map(function ($user) {
                foreach ($user->kelasSensei as $kelas) {
                    $tglMulai = \Carbon\Carbon::parse($kelas->tanggal_mulai);
                    $tglSelesai = \Carbon\Carbon::parse($kelas->tanggal_selesai);
                    $kelas->total_pertemuan = $tglMulai->copy()->diffInDaysFiltered(function ($date) {
                        return $date->dayOfWeek !== 0 && $date->dayOfWeek !== 6;
                    }, $tglSelesai->addDay()) + 1;
                    $kelas->jumlah_absen = \App\Models\AbsensiSensei::where('kelas_sensei_id', $kelas->id)->count();
                }

                return $user;
            });

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

        $this->generateAlphaForRange($start_date, $end_date);

        $absensis = AbsensiSensei::with(['user', 'kelasSensei'])
            ->whereBetween('tanggal', [$start_date, $end_date]);

        if ($user_id) {
            $absensis->where('user_id', $user_id);
        }

        if ($kelas_id) {
            $absensis->where('kelas_sensei_id', $kelas_id);
        }

        if ($status) {
            $absensis->where('status', $status);
        }

        $absensis = $absensis->orderBy('kelas_sensei_id', 'asc')
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_masuk', 'asc')
            ->get();

        // Group by kelas
        $groupedAbsensis = $absensis->groupBy('kelas_sensei_id')->map(function ($items) {
            $firstItem = $items->first();
            $kelas = $firstItem->kelasSensei;
            $tanggalMulai = \Carbon\Carbon::parse($kelas->tanggal_mulai);
            $tanggalSelesai = \Carbon\Carbon::parse($kelas->tanggal_selesai);

            // Hitung total pertemuan (hanya hari Senin-Jumat)
            $totalPertemuan = $tanggalMulai->diffInDaysFiltered(function ($date) {
                return $date->dayOfWeek !== 0 && $date->dayOfWeek !== 6;
            }, $tanggalSelesai->addDay()) + 1;

            // Hitung pertemuan ke- untuk setiap absensi
            $items = $items->map(function ($absen) use ($tanggalMulai) {
                $tanggalAbsen = \Carbon\Carbon::parse($absen->tanggal);

                // Hitung pertemuan ke dengan skip weekend
                $pertemuanKe = 1;
                $checkDate = $tanggalMulai->copy();
                while ($checkDate->lt($tanggalAbsen)) {
                    if ($checkDate->dayOfWeek !== 0 && $checkDate->dayOfWeek !== 5) {
                        $pertemuanKe++;
                    }
                    $checkDate->addDay();
                }
                $absen->pertemuan_ke = $pertemuanKe;

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
                'alpa' => $items->where('status', 'ALPA')->count(),
                'libur' => $items->where('status', 'LIBUR')->count(),
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
            'kelasByUser' => $kelasByUser,
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

    private function generateAlphaForRange($startDate, $endDate)
    {
        $start = \Carbon\Carbon::parse($startDate, 'Asia/Jakarta');
        $end = \Carbon\Carbon::parse($endDate, 'Asia/Jakarta');
        $now = \Carbon\Carbon::now('Asia/Jakarta');

        $kelasAktif = KelasSensei::where('status', 'aktif')
            ->with('user')
            ->get();

        foreach ($kelasAktif as $kelas) {
            $sensei = $kelas->user;
            if (! $sensei) {
                continue;
            }

            $tanggalMulai = \Carbon\Carbon::parse($kelas->tanggal_mulai, 'Asia/Jakarta');
            $tanggalSelesai = \Carbon\Carbon::parse($kelas->tanggal_selesai, 'Asia/Jakarta');

            if ($tanggalSelesai->lt($start) || $tanggalMulai->gt($end)) {
                continue;
            }

            $shift = $sensei->shift;
            $jamMasukShift = $shift
                ? \Carbon\Carbon::parse($shift->jam_masuk, 'Asia/Jakarta')
                : \Carbon\Carbon::parse('09:00:00', 'Asia/Jakarta');
            $toleransi = $shift ? ($shift->toleransi ?? 0) : 0;
            $batasJamMasuk = $jamMasukShift->copy()->addMinutes(30 + $toleransi);

            $current = $start->copy();
            while ($current->lte($end)) {
                $tanggalStr = $current->toDateString();

                if ($current->gt($now)) {
                    $current->addDay();

                    continue;
                }

                if ($current->lt($tanggalMulai) || $current->gt($tanggalSelesai)) {
                    $current->addDay();

                    continue;
                }

                $hariLibur = \App\Models\HariLibur::apakahLibur($tanggalStr);
                if ($hariLibur) {
                    $current->addDay();

                    continue;
                }

                // Skip hari Sabtu (5) dan Minggu (0)
                if ($current->dayOfWeek === 0 || $current->dayOfWeek === 5) {
                    $current->addDay();

                    continue;
                }

                $existingAbsensi = AbsensiSensei::where('kelas_sensei_id', $kelas->id)
                    ->where('user_id', $sensei->id)
                    ->where('tanggal', $tanggalStr)
                    ->first();

                if (! $existingAbsensi) {
                    $jamMasukBatas = $batasJamMasuk->copy()->setTimeFromTimeString($current->format('H:i:s'));
                    if ($jamMasukBatas->lt($jamMasukShift)) {
                        $jamMasukBatas->addDay();
                    }

                    if ($now->gte($jamMasukBatas)) {
                        AbsensiSensei::create([
                            'kelas_sensei_id' => $kelas->id,
                            'user_id' => $sensei->id,
                            'tanggal' => $tanggalStr,
                            'status' => 'ALPA',
                            'catatan' => 'Sistem otomatis: Tidak melakukan absensi setelah melewati batas jam masuk shift.',
                        ]);
                    }
                }

                $current->addDay();
            }
        }
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
            'status' => 'required|in:HADIR,TERLAMBAT,PULANG LEBIH AWAL,TIDAK ABSEN PULANG,ALPA,LIBUR',
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

        // Hitung total pertemuan (hanya hari Senin-Jumat)
        $totalPertemuan = $tanggalMulai->copy()->diffInDaysFiltered(function ($date) {
            return $date->dayOfWeek !== 0 && $date->dayOfWeek !== 6;
        }, $tanggalSelesai->addDay()) + 1;

        $absensis = AbsensiSensei::where('user_id', $userId)
            ->where('kelas_sensei_id', $kelasId)
            ->orderBy('tanggal', 'asc')
            ->get()
            ->map(function ($absen) use ($tanggalMulai) {
                $tanggalAbsen = \Carbon\Carbon::parse($absen->tanggal);

                // Hitung pertemuan ke dengan skip weekend
                $pertemuanKe = 1;
                $checkDate = $tanggalMulai->copy();
                while ($checkDate->lt($tanggalAbsen)) {
                    if ($checkDate->dayOfWeek !== 0 && $checkDate->dayOfWeek !== 5) {
                        $pertemuanKe++;
                    }
                    $checkDate->addDay();
                }
                $absen->pertemuan_ke = $pertemuanKe;

                return $absen;
            });

        $stats = [
            'hadir' => $absensis->where('status', 'HADIR')->count(),
            'terlambat' => $absensis->where('status', 'TERLAMBAT')->count(),
            'pulang_cepat' => $absensis->where('status', 'PULANG LEBIH AWAL')->count(),
            'tidak_pulang' => $absensis->where('status', 'TIDAK ABSEN PULANG')->count(),
            'alpa' => $absensis->where('status', 'ALPA')->count(),
            'libur' => $absensis->where('status', 'LIBUR')->count(),
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

    public function kelasIndex(Request $request)
    {
        $user_id = $request->user_id;
        $status = $request->status;

        $query = KelasSensei::with('user');

        if ($user_id) {
            $query->where('user_id', $user_id);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $kelas = $query->orderBy('tanggal_mulai', 'desc')->get();

        $kelas = $kelas->map(function ($kelasItem) {
            $tglMulai = \Carbon\Carbon::parse($kelasItem->tanggal_mulai);
            $tglSelesai = \Carbon\Carbon::parse($kelasItem->tanggal_selesai);
            $kelasItem->total_pertemuan = $tglMulai->copy()->diffInDaysFiltered(function ($date) {
                return $date->dayOfWeek !== 0 && $date->dayOfWeek !== 6;
            }, $tglSelesai->addDay()) + 1;
            $kelasItem->jumlah_absen = \App\Models\AbsensiSensei::where('kelas_sensei_id', $kelasItem->id)->count();

            return $kelasItem;
        });

        $list_sensei = User::where('role', 'KARYAWAN')
            ->whereHas('kelasSensei')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.kelas_sensei.index', [
            'kelas' => $kelas,
            'list_sensei' => $list_sensei,
            'user_id_selected' => $user_id,
            'status_selected' => $status,
        ]);
    }

    public function destroy($id)
    {
        $kelas = KelasSensei::findOrFail($id);
        $kelas->delete();

        return redirect()->back()->with('success', 'Kelas berhasil dihapus');
    }
}
