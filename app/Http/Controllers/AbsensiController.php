<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\JadwalKerja;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $riwayat = Absensi::where('user_id', $user->id)
            ->orderBy('tanggal', 'desc')
            ->take(10)
            ->get();

        return view('absensi.index', compact('riwayat'));
    }

    public function riwayatSemua()
    {
        // Ambil semua absensi beserta relasi karyawan
        $absensi = Absensi::with('user')->orderBy('tanggal', 'desc')->get();

        return view('absensi.riwayat', compact('absensi'));
    }

    public function profile()
    {
        return view('absensi.profile');
    }


    public function manual(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:masuk,pulang',
            'alasan' => 'required|string'
        ]);

        Absensi::create([
            'user_id' => Auth::id(),
            'jenis' => $request->jenis,
            'alasan' => $request->alasan,
            'tipe' => 'manual',
            'tanggal' => now()
        ]);

        return response()->json(['message' => 'Absen manual berhasil']);
    }

 public function absenMasuk(Request $request)
{
    $user  = Auth::user();
    $today = now()->toDateString();

    if (!$request->photo) {
        return response()->json([
            'message' => 'Foto absensi wajib diambil'
        ], 400);
    }

    // Cek absensi hari ini
    $absen = Absensi::where('user_id', $user->id)
        ->where('tanggal', $today)
        ->first();

    if ($absen && $absen->jam_masuk) {
        return response()->json([
            'message' => 'Anda sudah melakukan absen masuk hari ini'
        ], 409);
    }

    // Simpan foto masuk
    $fotoMasuk = $this->saveBase64Photo($request->photo, 'masuk');

    $jamKerja = \Carbon\Carbon::parse($today . ' 08:00:00');
    $now      = now();

    $status = $now->gt($jamKerja) ? 'TERLAMBAT' : 'HADIR';

    Absensi::updateOrCreate(
        [
            'user_id' => $user->id,
            'tanggal' => $today
        ],
        [
            'jam_masuk'  => $now,
            'status'     => $status,
            'foto_masuk' => $fotoMasuk
        ]
    );

    return response()->json([
        'status'  => 'success',
        'message' => 'Absen masuk berhasil'
    ]);
}


public function absenPulang(Request $request)
{
    $user  = Auth::user();
    $today = now()->toDateString();

    if (!$request->photo) {
        return response()->json([
            'message' => 'Foto absensi wajib diambil'
        ], 400);
    }

    $absen = Absensi::where('user_id', $user->id)
        ->where('tanggal', $today)
        ->first();

    if (!$absen || !$absen->jam_masuk) {
        return response()->json([
            'message' => 'Anda belum melakukan absen masuk hari ini'
        ], 409);
    }

    if ($absen->jam_keluar) {
        return response()->json([
            'message' => 'Anda sudah melakukan absen pulang hari ini'
        ], 409);
    }

    // Simpan foto pulang
    $fotoPulang = $this->saveBase64Photo($request->photo, 'pulang');

    $jamPulang = \Carbon\Carbon::parse($today . ' 17:00:00');
    $now       = now();

    $status = $now->lt($jamPulang)
        ? 'PULANG LEBIH AWAL'
        : $absen->status;

    $absen->update([
        'jam_keluar'  => $now,
        'status'      => $status,
        'foto_pulang' => $fotoPulang
    ]);

    return response()->json([
        'status'  => 'success',
        'message' => 'Absen pulang berhasil'
    ]);
}


private function saveBase64Photo(string $base64, string $type): string
{
    $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
    $image  = base64_decode($base64);

    $folder = 'absensi/' . now()->format('Y/m');
    $name   = $type . '_' . uniqid() . '.jpg';

    Storage::put("$folder/$name", $image);

    return "$folder/$name";
}


    // Riwayat absensi user login
    public function riwayat()
    {
        $user = Auth::user();

        $absensi = Absensi::where('user_id', $user->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('absensi.riwayat', compact('absensi'));
    }

    public function riwayatJson()
    {
        $riwayat = Absensi::where('user_id', Auth::id())
            ->orderBy('tanggal', 'desc')
            ->limit(10)
            ->get();

        return response()->json($riwayat);
    }


    // Detail absensi per tanggal
    public function detail($tanggal)
    {
        $user = Auth::user();

        $absensi = Absensi::where('user_id', $user->id)
            ->where('tanggal', $tanggal)
            ->firstOrFail();

        return view('absensi.detail', compact('absensi'));
    }




    // Riwayat absensi karyawan login
    public function history()
    {
        $user = Auth::user();

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



    public function deteksiWajah(Request $request)
    {
        $request->validate([
            'image' => 'required',
            'jenis' => 'required|in:masuk,pulang'
        ]);

        /* Simpan foto */
        $img = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->image));
        $path = 'absensi/' . Auth::user() . '_' . time() . '.jpg';
        file_put_contents(public_path($path), $img);

        /**
         * DI SINI:
         * 🔥 Panggil Face Recognition Engine (Python / OpenCV / YOLO)
         * return false jika wajah tidak cocok
         */

        $today = Carbon::today()->toDateString();
        $absen = Absensi::firstOrCreate(
            ['user_id' =>  Auth::id(), 'tanggal' => $today],
            ['status' => 'Hadir']
        );

        if ($request->jenis === 'masuk') {
            if ($absen->jam_masuk) {
                return response()->json(['message' => 'Sudah absen masuk'], 422);
            }
            $absen->jam_masuk = now()->format('H:i:s');
        } else {
            if (!$absen->jam_masuk) {
                return response()->json(['message' => 'Belum absen masuk'], 422);
            }
            $absen->jam_pulang = now()->format('H:i:s');
        }

        $absen->save();

        return response()->json([
            'message' => 'Absensi berhasil diverifikasi wajah'
        ]);
    }
}
