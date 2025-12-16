<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\JadwalKerja;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    // Halaman index absensi
    public function index()
    {
        return view('absensi.index');
    }

    public function riwayatSemua()
    {
        // Ambil semua absensi beserta relasi karyawan
        $absensi = Absensi::with('karyawan')->orderBy('tanggal', 'desc')->get();

        return view('absensi.riwayat', compact('absensi'));
    }


    // Absen Masuk
    public function absenMasuk(Request $request)
    {
        $user = Auth::user();

        // Cek apakah user punya karyawan
        if (!$user->karyawan) {
            return response()->json(['message' => 'Karyawan tidak ditemukan untuk user ini'], 404);
        }

        $karyawan_id = $user->karyawan->id;
        $tanggal = now()->format('Y-m-d');

        // Cek apakah sudah absen hari ini
        $absen = Absensi::where('karyawan_id', $karyawan_id)
            ->where('tanggal', $tanggal)
            ->first();

        if ($absen) {
            return response()->json(['message' => 'Sudah absen hari ini'], 400);
        }

        // Ambil jadwal hari ini
        $jadwal = JadwalKerja::where('karyawan_id', $karyawan_id)
            ->where('hari', Carbon::now()->translatedFormat('l'))
            ->first();

        $jam_masuk = now();
        $status = 'HADIR';
        if ($jadwal && $jam_masuk->gt(Carbon::parse($jadwal->jam_masuk))) {
            $status = 'TERLAMBAT';
        }

        $absensi = Absensi::create([
            'karyawan_id' => $karyawan_id,
            'tanggal'     => $tanggal,
            'jam_masuk'   => $jam_masuk,
            'status'      => $status,
        ]);

        return response()->json(['message' => 'Absen masuk berhasil', 'data' => $absensi], 201);
    }

    // Absen Pulang
    public function absenPulang(Request $request)
    {
        $user = Auth::user();

        if (!$user->karyawan) {
            return response()->json(['message' => 'Karyawan tidak ditemukan untuk user ini'], 404);
        }

        $karyawan_id = $user->karyawan->id;
        $tanggal = now()->format('Y-m-d');

        $absensi = Absensi::where('karyawan_id', $karyawan_id)
            ->where('tanggal', $tanggal)
            ->first();

        if (!$absensi) {
            return response()->json(['message' => 'Belum absen masuk hari ini'], 400);
        }

        if ($absensi->jam_keluar) {
            return response()->json(['message' => 'Sudah absen pulang hari ini'], 400);
        }

        $absensi->update([
            'jam_keluar' => now()
        ]);

        return response()->json(['message' => 'Absen pulang berhasil', 'data' => $absensi]);
    }

    // Riwayat absensi karyawan login
    public function history()
    {
        $user = auth()->user();

        if (!$user->karyawan) {
            return response()->json([], 404);
        }

        $karyawan_id = $user->karyawan->id;

        $absensi = Absensi::with('jadwal')
            ->where('karyawan_id', $karyawan_id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json($absensi);
    }
}
