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
    { {
            $hariIni = Carbon::today()->toDateString();
            $bulanIni = Carbon::now()->month;
            $tahunIni = Carbon::now()->year;

            // 1. Data Karyawan & Ringkasan (Box Bawah)
            $karyawanAktif = User::where('role', 'KARYAWAN')->where('status', 'AKTIF')->count();
            $izinPending = Izin::where('status', 'PENDING')->count(); // Pastikan model Izin ada

            // 2. Data Absensi HARI INI (Untuk Donut Chart & Box)
            $tepatWaktu = Absensi::whereDate('tanggal', $hariIni)->where('status', 'HADIR')->count();
            $terlambat = Absensi::whereDate('tanggal', $hariIni)->where('status', 'TERLAMBAT')->count();
            $alpa = Absensi::whereDate('tanggal', $hariIni)->where('status', 'ALPA')->count();
            $izinCuti = Absensi::whereDate('tanggal', $hariIni)->where('status', 'IZIN')->count();

            $hadirHariIni = $tepatWaktu + $terlambat;
            $belumAbsen = $karyawanAktif - ($hadirHariIni + $izinCuti);
            $belumAbsen = ($belumAbsen < 0) ? 0 : $belumAbsen;

            // Data untuk Donut Chart (Komposisi Hari Ini)
            $donutData = [
                'hadir' => $tepatWaktu,
                'terlambat' => $terlambat,
                'izin' => $izinCuti,
                'alpa' => $alpa
            ];

            // 3. STATISTIK TREN 6 BULAN TERAKHIR (Grouped Bar Chart)
            $labelsBar = [];
            $dataHadirBar = [];
            $dataTerlambatBar = [];
            $dataAlpaBar = [];

            for ($m = 5; $m >= 0; $m--) {
                $date = Carbon::now()->subMonths($m);
                $labelsBar[] = $date->translatedFormat('F Y'); // Contoh: Januari 2026

                // Hitung Hadir Tepat Waktu
                $dataHadirBar[] = Absensi::whereMonth('tanggal', $date->month)
                    ->whereYear('tanggal', $date->year)
                    ->where('status', 'HADIR')
                    ->count();

                // Hitung Terlambat
                $dataTerlambatBar[] = Absensi::whereMonth('tanggal', $date->month)
                    ->whereYear('tanggal', $date->year)
                    ->where('status', 'TERLAMBAT')
                    ->count();

                // Hitung Alpa
                $dataAlpaBar[] = Absensi::whereMonth('tanggal', $date->month)
                    ->whereYear('tanggal', $date->year)
                    ->where('status', 'ALPA')
                    ->count();
            }

            // 4. Data Map & Tabel Terbaru
            $absensis = Absensi::with(['user', 'cabang', 'shift'])
                ->orderBy('tanggal', 'desc') // Tanggal terbaru dulu
                ->orderBy('created_at', 'desc') // Jika tanggal sama, jam terbaru (inputan terakhir) di atas
                ->take(100)
                ->get();

            $lokasiMarkers = $absensis->filter(fn($a) => $a->lat_masuk && $a->long_masuk)
                ->map(fn($a) => [
                    'lat' => $a->lat_masuk,
                    'lng' => $a->long_masuk,
                    'nama' => $a->user->name,
                    'jam' => $a->jam_masuk,
                    'tipe' => 'Masuk'
                ])->values();


            // rasio
            // Ambil tanggal hari ini
            $hariIni = date('Y-m-d');

            // Hitung statistik per divisi langsung dari tabel users
            $statistikDivisi = Divisi::withCount([
                // Menghitung user yang hadir atau terlambat hari ini
                'users as total_hadir' => function ($query) use ($hariIni) {
                    $query->whereHas('absensi', function ($q) use ($hariIni) {
                        $q->whereDate('tanggal', $hariIni)
                            ->whereIn('status', ['HADIR', 'TERLAMBAT']);
                    });
                },
                // Menghitung user yang khusus terlambat hari ini
                'users as total_terlambat' => function ($query) use ($hariIni) {
                    $query->whereHas('absensi', function ($q) use ($hariIni) {
                        $q->whereDate('tanggal', $hariIni)
                            ->where('status', 'TERLAMBAT');
                    });
                }
            ])->get();

            $labelsRasio = [];
            $dataTotalKehadiran = [];
            $dataPersentaseTerlambat = [];

            foreach ($statistikDivisi as $divisi) {
                $labelsRasio[] = $divisi->nama_divisi; // Pastikan kolom ini ada di tabel divisi
                $dataTotalKehadiran[] = (int) $divisi->total_hadir;

                // Hitung persentase keterlambatan
                $persen = 0;
                if ($divisi->total_hadir > 0) {
                    $persen = round(($divisi->total_terlambat / $divisi->total_hadir) * 100);
                }
                $dataPersentaseTerlambat[] = $persen;
            }

            // 5. Return View dengan semua variabel
            return view('admin.dashboard', compact(
                'absensis',
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
