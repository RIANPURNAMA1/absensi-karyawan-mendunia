<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shift; // <--- TAMBAHKAN BARIS INI
use App\Models\Absensi;
use App\Models\Divisi;
use App\Models\Izin;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = Carbon::today()->toDateString();
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        // 1. Ringkasan Box Atas
        $karyawanAktif = User::where('role', 'KARYAWAN')->where('status', 'AKTIF')->count();
        $izinPending = Izin::where('status', 'PENDING')->count();

        // 2. Statistik Hari Ini (Query langsung ke DB agar akurat)
        $stats = Absensi::whereDate('tanggal', $hariIni)
            ->selectRaw("
            count(case when status = 'HADIR' then 1 end) as tepatWaktu,
            count(case when status = 'TERLAMBAT' then 1 end) as terlambat,
            count(case when status = 'ALPA' then 1 end) as alpa,
            count(case when status = 'IZIN' then 1 end) as izinCuti
        ")->first();

        $tepatWaktu = $stats->tepatWaktu;
        $terlambat = $stats->terlambat;
        $alpa = $stats->alpa;
        $izinCuti = $stats->izinCuti;

        $hadirHariIni = $tepatWaktu + $terlambat;
        $belumAbsen = max(0, $karyawanAktif - ($hadirHariIni + $izinCuti));

        $donutData = [
            'hadir' => $tepatWaktu,
            'terlambat' => $terlambat,
            'izin' => $izinCuti,
            'alpa' => $alpa
        ];

        // Ganti query $dataIzinSakit yang lama dengan ini:
        $dataIzinSakit = \App\Models\Izin::with(['user'])
            ->orderBy('created_at', 'desc')
            ->take(10) // Ambil 10 pengajuan terbaru
            ->get();

        // 4. Data Absensi Umum (Untuk Map/Tabel Utama)
        $absensis = Absensi::with(['user', 'cabang', 'shift'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(100)
            ->get();

        // 5. Statistik Per Divisi (Rasio)
        $statistikDivisi = Divisi::withCount([
            'users as total_hadir' => function ($query) use ($hariIni) {
                $query->whereHas('absensi', fn($q) => $q->whereDate('tanggal', $hariIni)->whereIn('status', ['HADIR', 'TERLAMBAT']));
            },
            'users as total_terlambat' => function ($query) use ($hariIni) {
                $query->whereHas('absensi', fn($q) => $q->whereDate('tanggal', $hariIni)->where('status', 'TERLAMBAT'));
            }
        ])->get();

        $labelsRasio = [];
        $dataTotalKehadiran = [];
        $dataPersentaseTerlambat = [];

        foreach ($statistikDivisi as $divisi) {
            $labelsRasio[] = $divisi->nama_divisi;
            $hadir = (int) $divisi->total_hadir;
            $dataTotalKehadiran[] = $hadir;
            $dataPersentaseTerlambat[] = ($hadir > 0) ? round(($divisi->total_terlambat / $hadir) * 100) : 0;
        }

        // 6. Data Lembur
        $notifLembur = \App\Models\Lembur::with('user')
            ->where('status', 'PENDING')
            ->orderBy('created_at', 'desc')
            ->get();

        // 7. Lokasi Markers
        $lokasiMarkers = $absensis->filter(fn($a) => $a->lat_masuk && $a->long_masuk)
            ->map(fn($a) => [
                'lat' => $a->lat_masuk,
                'lng' => $a->long_masuk,
                'nama' => $a->user->name,
                'jam' => $a->jam_masuk,
                'tipe' => 'Masuk'
            ])->values();

        // 8. Statistik Tren (Loop tetap sama)
        $labelsBar = [];
        $dataHadirBar = [];
        $dataTerlambatBar = [];
        $dataAlpaBar = [];
        for ($m = 5; $m >= 0; $m--) {
            $date = Carbon::now()->subMonths($m);
            $labelsBar[] = $date->translatedFormat('F Y');
            $dataHadirBar[] = Absensi::whereMonth('tanggal', $date->month)->whereYear('tanggal', $date->year)->where('status', 'HADIR')->count();
            $dataTerlambatBar[] = Absensi::whereMonth('tanggal', $date->month)->whereYear('tanggal', $date->year)->where('status', 'TERLAMBAT')->count();
            $dataAlpaBar[] = Absensi::whereMonth('tanggal', $date->month)->whereYear('tanggal', $date->year)->where('status', 'ALPA')->count();
        }

        return view('admin.dashboard', compact(
            'notifLembur',
            'absensis',
            'dataIzinSakit',
            'labelsRasio',
            'dataTotalKehadiran',
            'dataPersentaseTerlambat',
            'lokasiMarkers',
            'karyawanAktif',
            'hadirHariIni',
            'tepatWaktu',
            'terlambat',
            'belumAbsen',
            'izinPending',
            'labelsBar',
            'dataHadirBar',
            'dataTerlambatBar',
            'dataAlpaBar',
            'donutData'
        ));
    }
    /**
     * Get filtered attendance data via AJAX
     */
    public function getFilteredData(Request $request)
    {
        $cabang = $request->input('cabang');
        $jamKerja = $request->input('jam_kerja');
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());

        $query = Absensi::with(['user', 'cabang', 'shift'])
            ->whereDate('tanggal', $tanggal);

        if ($cabang && $cabang !== 'all') {
            $query->where('cabang_id', $cabang);
        }

        if ($jamKerja && $jamKerja !== 'all') {
            $query->where('shift_id', $jamKerja);
        }

        $data = $query->get();

        $tepatWaktu = $data->where('status', 'HADIR')->count();
        $terlambat = $data->where('status', 'TERLAMBAT')->count();
        $alpa = $data->where('status', 'ALPA')->count();
        $izinCuti = $data->where('status', 'IZIN')->count();

        return response()->json([
            'tepatWaktu' => $tepatWaktu,
            'terlambat' => $terlambat,
            'alpa' => $alpa,
            'izinCuti' => $izinCuti,
            'belumAbsen' => User::where('role', 'KARYAWAN')->where('status', 'AKTIF')->count() - ($tepatWaktu + $terlambat + $izinCuti + $alpa)
        ]);
    }


    public function filter(Request $request)
    {
        $hariIni = Carbon::today()->toDateString();

        // Mulai query dasar
        $query = Absensi::whereDate('tanggal', $hariIni);

        // Filter berdasarkan cabang jika dipilih
        if ($request->cabang && $request->cabang != 'all') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('cabang_id', $request->cabang);
            });
        }

        // Filter berdasarkan jam kerja/shift jika dipilih
        if ($request->jam_kerja && $request->jam_kerja != 'all') {
            $query->where('shift_id', $request->jam_kerja);
        }

        $tepatWaktu = (clone $query)->where('status', 'HADIR')->count();
        $terlambat = (clone $query)->where('status', 'TERLAMBAT')->count();
        $izinCuti = (clone $query)->where('status', 'IZIN')->count();
        $alpa = (clone $query)->where('status', 'ALPA')->count();

        // Hitung belum absen (sesuaikan logika dengan kebutuhan)
        $totalKaryawanFilter = User::where('role', 'KARYAWAN')->where('status', 'AKTIF');
        if ($request->cabang && $request->cabang != 'all') {
            $totalKaryawanFilter->where('cabang_id', $request->cabang);
        }
        $karyawanAktifCount = $totalKaryawanFilter->count();

        $belumAbsen = $karyawanAktifCount - ($tepatWaktu + $terlambat + $izinCuti + $alpa);

        return response()->json([
            'tepatWaktu' => $tepatWaktu,
            'terlambat' => $terlambat,
            'belumAbsen' => max(0, $belumAbsen),
            'izinCuti' => $izinCuti,
            'alpa' => $alpa
        ]);
    }

    /**
     * Approve or reject izin
     */
    public function updateIzinStatus(Request $request, $id)
    {
        $izin = Izin::findOrFail($id);

        $request->validate([
            'status' => 'required|in:APPROVED,REJECTED',
            'catatan' => 'nullable|string'
        ]);

        $izin->status = $request->status;
        $izin->approved_by = auth()->id();
        $izin->approved_at = Carbon::now();
        $izin->save();

        return response()->json([
            'success' => true,
            'message' => 'Status izin berhasil diperbarui'
        ]);
    }
}
