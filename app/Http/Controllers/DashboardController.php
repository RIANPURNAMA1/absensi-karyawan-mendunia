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
    $bulanIni = Carbon::now()->month;
    $tahunIni = Carbon::now()->year;

    // -------------------------------------------------------
    // 1. RINGKASAN CARD ATAS
    // -------------------------------------------------------
    $totalKaryawan  = User::where('role', 'KARYAWAN')->count();
    $karyawanAktif  = User::where('role', 'KARYAWAN')->where('status', 'AKTIF')->count();
    $izinPendingCount = Izin::where('status', 'PENDING')->count();

    // Statistik absensi AKUMULATIF (semua waktu)
    $stats = Absensi::selectRaw("
        COUNT(CASE WHEN status = 'HADIR'     THEN 1 END) AS tepatWaktu,
        COUNT(CASE WHEN status = 'TERLAMBAT' THEN 1 END) AS terlambat,
        COUNT(CASE WHEN status = 'ALPA'      THEN 1 END) AS alpa,
        COUNT(CASE WHEN status = 'IZIN'      THEN 1 END) AS izinCuti,
        COUNT(CASE WHEN status = 'SAKIT'     THEN 1 END) AS sakit
    ")->first();

    $tepatWaktu   = (int) ($stats->tepatWaktu ?? 0);
    $terlambat    = (int) ($stats->terlambat  ?? 0);
    $alpa         = (int) ($stats->alpa       ?? 0);
    $izinCuti     = (int) ($stats->izinCuti   ?? 0) + (int) ($stats->sakit ?? 0); // gabung izin+sakit
    $totalHadirSemua = $tepatWaktu + $terlambat;
    $tidakHadir   = $alpa;   // alias untuk card "Alpa"
    $belumAbsen   = $alpa;   // dipakai di mini stats

    // Statistik HARI INI untuk card "Hadir"
    $hadirHariIni = Absensi::whereDate('tanggal', Carbon::today())
        ->whereIn('status', ['HADIR', 'TERLAMBAT'])
        ->count();

    // Persentase terlambat (dari total kehadiran akumulatif)
    $persenTerlambatGlobal = $totalHadirSemua > 0
        ? round(($terlambat / $totalHadirSemua) * 100, 1)
        : 0;

    // Project (opsional — sesuaikan model jika ada)
    $projectAktif   = 0; // ganti: \App\Models\Project::where('status','AKTIF')->count();
    $projectSelesai = 0; // ganti: \App\Models\Project::where('status','SELESAI')->count();

    // -------------------------------------------------------
    // 2. DONUT CHART — Komposisi Hari Ini
    // -------------------------------------------------------
    $donutToday = Absensi::selectRaw("
        COUNT(CASE WHEN status = 'HADIR'     THEN 1 END) AS hadir,
        COUNT(CASE WHEN status = 'TERLAMBAT' THEN 1 END) AS terlambat,
        COUNT(CASE WHEN status = 'IZIN'      THEN 1 END) AS izin,
        COUNT(CASE WHEN status = 'ALPA'      THEN 1 END) AS alpa
    ")->whereDate('tanggal', Carbon::today())->first();

    $donutData = [
        'hadir'     => (int) ($donutToday->hadir     ?? 0),
        'terlambat' => (int) ($donutToday->terlambat ?? 0),
        'izin'      => (int) ($donutToday->izin      ?? 0),
        'alpa'      => (int) ($donutToday->alpa      ?? 0),
    ];

    // -------------------------------------------------------
    // 3. DATA IZIN/SAKIT TERBARU
    // -------------------------------------------------------
    $dataIzinSakit = \App\Models\Izin::with(['user', 'cabang'])
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();

    // -------------------------------------------------------
    // 4. DATA ABSENSI TERBARU (log)
    // -------------------------------------------------------
    $absensis = Absensi::with(['user', 'cabang', 'shift'])
        ->orderBy('tanggal', 'desc')
        ->orderBy('created_at', 'desc')
        ->take(100)
        ->get();

    // -------------------------------------------------------
    // 5. RASIO KETERLAMBATAN PER DIVISI
    // -------------------------------------------------------
    $statistikDivisi = Divisi::with('users')->get()->map(function ($divisi) {
        $userIds   = $divisi->users->pluck('id');
        $hadir     = Absensi::whereIn('user_id', $userIds)->where('status', 'HADIR')->count();
        $terlambat = Absensi::whereIn('user_id', $userIds)->where('status', 'TERLAMBAT')->count();
        $total     = $hadir + $terlambat;

        return [
            'nama'             => $divisi->nama_divisi,
            'hadir'            => $hadir,
            'terlambat'        => $terlambat,
            'total'            => $total,
            'persen_hadir'     => $total > 0 ? round(($hadir     / $total) * 100, 1) : 0,
            'persen_terlambat' => $total > 0 ? round(($terlambat / $total) * 100, 1) : 0,
        ];
    })->filter(fn($d) => $d['total'] > 0)->values();

    $labelsRasio             = $statistikDivisi->pluck('nama')->toArray();
    $dataTotalKehadiran      = $statistikDivisi->pluck('total')->toArray();
    $dataHadir               = $statistikDivisi->pluck('hadir')->toArray();
    $dataTerlambat           = $statistikDivisi->pluck('terlambat')->toArray();
    $dataPersenHadir         = $statistikDivisi->pluck('persen_hadir')->toArray();
    $dataPersentaseTerlambat = $statistikDivisi->pluck('persen_terlambat')->toArray();

    // -------------------------------------------------------
    // 6. LEMBUR PENDING
    // -------------------------------------------------------
    $notifLembur = \App\Models\Lembur::with('user')
        ->where('status', 'PENDING')
        ->orderBy('created_at', 'desc')
        ->get();

    // -------------------------------------------------------
    // 7. TREN KEHADIRAN 6 BULAN
    // -------------------------------------------------------
    $labelsBar        = [];
    $dataHadirBar     = [];
    $dataTerlambatBar = [];
    $dataAlpaBar      = [];

    for ($m = 5; $m >= 0; $m--) {
        $date          = Carbon::now()->subMonths($m);
        $labelsBar[]   = $date->translatedFormat('F Y');
        $dataHadirBar[]     = Absensi::whereMonth('tanggal', $date->month)->whereYear('tanggal', $date->year)->where('status', 'HADIR')->count();
        $dataTerlambatBar[] = Absensi::whereMonth('tanggal', $date->month)->whereYear('tanggal', $date->year)->where('status', 'TERLAMBAT')->count();
        $dataAlpaBar[]      = Absensi::whereMonth('tanggal', $date->month)->whereYear('tanggal', $date->year)->where('status', 'ALPA')->count();
    }

    // -------------------------------------------------------
    // 8. LOKASI MARKERS MAP
    // -------------------------------------------------------
    $lokasiMarkers = $absensis->filter(fn($a) => $a->lat_masuk && $a->long_masuk)
        ->map(fn($a) => [
            'lat'     => $a->lat_masuk,
            'lng'     => $a->long_masuk,
            'nama'    => $a->user->name,
            'jam'     => $a->jam_masuk,
            'tanggal' => $a->tanggal,
            'tipe'    => 'Masuk',
        ])->values();

    // -------------------------------------------------------
    // COMPACT — semua variabel ke view
    // -------------------------------------------------------
    return view('admin.dashboard', compact(
        // Card stats
        'totalKaryawan',
        'karyawanAktif',
        'hadirHariIni',
        'totalHadirSemua',
        'tepatWaktu',
        'terlambat',
        'tidakHadir',
        'alpa',
        'izinCuti',
        'izinPendingCount',
        'belumAbsen',
        'persenTerlambatGlobal',
        'projectAktif',
        'projectSelesai',
        // Charts
        'donutData',
        'labelsRasio',
        'dataTotalKehadiran',
        'dataHadir',
        'dataTerlambat',
        'dataPersenHadir',
        'dataPersentaseTerlambat',
        'labelsBar',
        'dataHadirBar',
        'dataTerlambatBar',
        'dataAlpaBar',
        // Lists
        'absensis',
        'dataIzinSakit',
        'notifLembur',
        'lokasiMarkers',
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
