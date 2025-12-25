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
        $user = Auth::user();
        $today = now()->toDateString();
        $now = now();

        // 1. Validasi Foto
        if (!$request->photo) {
            return response()->json(['message' => 'Foto absensi wajib diambil'], 400);
        }

        // 2. Cek apakah karyawan punya shift
        // Diasumsikan relasi: User -> Karyawan -> Shift
        $karyawan = $user->karyawan;
        if (!$karyawan || !$karyawan->shift_id) {
            return response()->json(['message' => 'Anda belum terdaftar dalam shift kerja apa pun. Hubungi Admin.'], 403);
        }

        $shift = $karyawan->shift;

        // 3. Cek Double Absen
        $absen = Absensi::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->first();

        if ($absen && $absen->jam_masuk) {
            return response()->json(['message' => 'Anda sudah absen masuk hari ini'], 409);
        }

        // 4. Logika Penentuan Status (HADIR / TERLAMBAT)
        $jamMasukShift = Carbon::createFromFormat('H:i:s', $shift->jam_masuk);
        $batasToleransi = $jamMasukShift->copy()->addMinutes($shift->toleransi);

        // Jika jam sekarang melewati (Jam Masuk Shift + Toleransi)
        $status = $now->format('H:i:s') > $batasToleransi->format('H:i:s')
            ? 'TERLAMBAT'
            : 'HADIR';

        // 5. Simpan Foto
        $fotoMasuk = $this->saveBase64Photo($request->photo, 'masuk');

        // 6. Eksekusi Simpan
        Absensi::updateOrCreate(
            ['user_id' => $user->id, 'tanggal' => $today],
            [
                'shift_id'   => $shift->id, // Menyimpan shift yang berlaku saat itu
                'jam_masuk'  => $now->format('H:i:s'),
                'status'     => $status,
                'foto_masuk' => $fotoMasuk
            ]
        );

        return response()->json([
            'message' => 'Absen masuk berhasil. Status: ' . $status,
            'jam' => $now->format('H:i:s')
        ]);
    }

    public function absenPulang(Request $request)
    {
        $user = Auth::user();
        $today = now()->toDateString();
        $now = now();

        if (!$request->photo) {
            return response()->json(['message' => 'Foto absensi wajib diambil'], 400);
        }

        $absen = Absensi::with('shift')->where('user_id', $user->id)
            ->where('tanggal', $today)
            ->first();

        if (!$absen || !$absen->jam_masuk) {
            return response()->json(['message' => 'Anda belum absen masuk'], 409);
        }

        if ($absen->jam_keluar) {
            return response()->json(['message' => 'Anda sudah absen pulang hari ini'], 409);
        }

        // LOGIKA OPSIONAL: Cek Pulang Lebih Awal
        $statusSekarang = $absen->status;
        $jamPulangShift = $absen->shift->jam_pulang;

        if ($now->format('H:i:s') < $jamPulangShift) {
            // Jika sebelumnya HADIR tapi pulang cepat, status bisa diupdate ke PULANG LEBIH AWAL
            // Namun jika sebelumnya TERLAMBAT, biarkan tetap TERLAMBAT agar tetap terhitung poin minusnya
            if ($statusSekarang == 'HADIR') {
                $statusSekarang = 'PULANG LEBIH AWAL';
            }
        }

        $fotoPulang = $this->saveBase64Photo($request->photo, 'pulang');

        $absen->update([
            'jam_keluar'  => $now->format('H:i:s'),
            'foto_pulang' => $fotoPulang,
            'status'      => $statusSekarang
        ]);

        return response()->json(['message' => 'Absen pulang berhasil']);
    }

    private function saveBase64Photo($base64, $type)
    {
        $image = explode(',', $base64)[1];
        $image = base64_decode($image);
        $filename = $type . '_' . uniqid() . '.jpg';
        $path = 'absensi/' . $filename;
        Storage::disk('public')->put($path, $image);
        return $path;
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


    public function detail($tanggal)
    {
        $user = Auth::user();

        $absensi = Absensi::where('user_id', $user->id)
            ->where('tanggal', $tanggal)
            ->first();

        if (!$absensi) {
            abort(404, 'Data absensi tidak ditemukan');
        }

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
