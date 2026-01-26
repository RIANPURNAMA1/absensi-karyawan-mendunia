<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Cabang;
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
        $user = Auth::user()->load('cabang');

        // 🔒 Pengaman kalau user belum punya cabang
        if (!$user->cabang) {
            abort(403, 'User belum terdaftar di cabang manapun.');
        }

        // Ambil 10 riwayat absensi terakhir
        $riwayat = Absensi::where('user_id', $user->id)
            ->orderBy('tanggal', 'desc')
            ->take(10)
            ->get();

        // Pastikan data cabang valid
        $cabang = $user->cabang;

        return view('absensi.index', [
            'riwayat'     => $riwayat,
            'cabangLat'   => $cabang->latitude ?? 0,
            'cabangLong'  => $cabang->longitude ?? 0,
            'radiusMeter' => $cabang->radius ?? 100,
            'namaCabang'  => $cabang->nama_cabang ?? '-',
        ]);
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

    // revisi
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:absensis,id',
            'status' => 'required|in:HADIR,TERLAMBAT,IZIN,ALPA,PULANG LEBIH AWAL',
        ]);

        $absen = Absensi::findOrFail($request->id);

        $absen->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Status absensi berhasil diperbarui');
    }



    // contrroller manual absensi 
    public function absenMasuk(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // 1. Cek apakah sudah absen hari ini
        $cek = Absensi::where('user_id', $user->id)->where('tanggal', $today)->first();
        if ($cek) return response()->json(['message' => 'Anda sudah absen masuk hari ini'], 422);

        // 2. Ambil data cabang & Shift user (Penting untuk validasi status)
        $cabang = Cabang::find($user->cabang_id);
        if (!$cabang) return response()->json(['message' => 'Cabang tidak ditemukan'], 422);

        // Muat data shift user
        $shift = \App\Models\Shift::find($user->shift_id);
        if (!$shift) return response()->json(['message' => 'Jadwal shift tidak ditemukan'], 422);

        // 3. Validasi Radius
        $jarak = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $cabang->latitude,
            $cabang->longitude
        );

        if ($jarak > $cabang->radius) {
            return response()->json([
                'message' => 'Gagal! Jarak Anda ' . round($jarak) . 'm. Di luar radius ' . $cabang->radius . 'm.'
            ], 422);
        }

        // --- 4. LOGIKA CEK TERLAMBAT ---
        $status = 'HADIR';

        // Ambil jam masuk shift dan tambahkan toleransi (menit)
        // Contoh: Jam masuk 08:00 + toleransi 15 menit = 08:15
        $jamMasukShift = Carbon::parse($shift->jam_masuk);
        $batasToleransi = $jamMasukShift->copy()->addMinutes($shift->toleransi);

        // Jika jam sekarang lebih besar dari batas toleransi
        if ($now->gt($batasToleransi)) {
            $status = 'TERLAMBAT';
        }

        // absenMasuk
        Absensi::create([
            'user_id'    => $user->id,
            'cabang_id'  => $cabang->id,
            'shift_id'   => $shift->id,
            'tanggal'    => $today,
            'jam_masuk'  => $now->toTimeString(),
            'lat_masuk'  => $request->latitude,  // <=== SAMAKAN DENGAN JS
            'long_masuk' => $request->longitude,
            'status'     => $status,
        ]);



        return response()->json([
            'message' => 'Absen masuk berhasil. Status: ' . $status
        ]);
    }

    public function absenPulang(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now();
        $today = $now->toDateString();

        // 1. Cari data absensi beserta relasi shift-nya
        $absensi = Absensi::with('shift')
            ->where('user_id', $user->id)
            ->where('tanggal', $today)
            ->first();

        if (!$absensi) return response()->json(['message' => 'Belum absen masuk'], 422);
        if ($absensi->jam_keluar) return response()->json(['message' => 'Anda sudah absen pulang'], 422);
        if (!$absensi->shift) return response()->json(['message' => 'Jadwal shift tidak ditemukan'], 422);

        // 2. VALIDASI RADIUS
        $cabang = Cabang::find($user->cabang_id);
        if (!$cabang) return response()->json(['message' => 'Data cabang tidak ditemukan'], 422);

        $jarak = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $cabang->latitude,
            $cabang->longitude
        );

        if ($jarak > $cabang->radius) {
            return response()->json([
                'message' => 'Di luar radius! Jarak: ' . round($jarak) . 'm. Maks: ' . $cabang->radius . 'm.'
            ], 422);
        }

        // 3. LOGIKA STATUS WAKTU (Mendukung Shift Malam)
        $statusBaru = $absensi->status; // Ambil status awal (Bisa HADIR atau TERLAMBAT)

        $jamMasukShift = Carbon::parse($absensi->shift->jam_masuk);
        $jamPulangShift = Carbon::parse($absensi->shift->jam_pulang);

        // Jika shift melewati tengah malam
        if ($jamPulangShift->lt($jamMasukShift)) {
            $jamPulangShift->addDay();
        }

        // LOGIKA TAMBAHAN: 
        // Jika sekarang belum jam pulang DAN status saat ini BUKAN TERLAMBAT
        if ($now->lt($jamPulangShift) && $absensi->status !== 'TERLAMBAT') {
            $statusBaru = 'PULANG LEBIH AWAL';
        }
        // Jika status saat ini TERLAMBAT, variabel $statusBaru tetap berisi 'TERLAMBAT' 
        // karena tidak masuk ke dalam blok IF di atas.

        // absenPulang
        $absensi->update([
            'jam_keluar'  => $now->toTimeString(),
            'lat_pulang'  => $request->latitude,
            'long_pulang' => $request->longitude,
            'status'      => $statusBaru,
        ]);


        return response()->json([
            'message' => "Absen pulang berhasil. Status: $statusBaru",
            'jam_keluar' => $now->toTimeString()
        ]);
    }



    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    // public function absenMasuk(Request $request)
    // {
    //     $user = Auth::user();
    //     $today = now()->toDateString();
    //     $now = now();

    //     // 1. Validasi Foto
    //     if (!$request->photo) {
    //         return response()->json(['message' => 'Foto absensi wajib diambil'], 400);
    //     }

    //     // 2. Cek apakah karyawan punya shift
    //     // Diasumsikan relasi: User -> Karyawan -> Shift
    //     $karyawan = $user->karyawan;
    //     if (!$karyawan || !$karyawan->shift_id) {
    //         return response()->json(['message' => 'Anda belum terdaftar dalam shift kerja apa pun. Hubungi Admin.'], 403);
    //     }

    //     $shift = $karyawan->shift;

    //     // 3. Cek Double Absen
    //     $absen = Absensi::where('user_id', $user->id)
    //         ->where('tanggal', $today)
    //         ->first();

    //     if ($absen && $absen->jam_masuk) {
    //         return response()->json(['message' => 'Anda sudah absen masuk hari ini'], 409);
    //     }

    //     // 4. Logika Penentuan Status (HADIR / TERLAMBAT)
    //     $jamMasukShift = Carbon::createFromFormat('H:i:s', $shift->jam_masuk);
    //     $batasToleransi = $jamMasukShift->copy()->addMinutes($shift->toleransi);

    //     // Jika jam sekarang melewati (Jam Masuk Shift + Toleransi)
    //     $status = $now->format('H:i:s') > $batasToleransi->format('H:i:s')
    //         ? 'TERLAMBAT'
    //         : 'HADIR';

    //     // 5. Simpan Foto
    //     $fotoMasuk = $this->saveBase64Photo($request->photo, 'masuk');

    //     // 6. Eksekusi Simpan
    //     Absensi::updateOrCreate(
    //         ['user_id' => $user->id, 'tanggal' => $today],
    //         [
    //             'shift_id'   => $shift->id, // Menyimpan shift yang berlaku saat itu
    //             'jam_masuk'  => $now->format('H:i:s'),
    //             'status'     => $status,
    //             'foto_masuk' => $fotoMasuk
    //         ]
    //     );

    //     return response()->json([
    //         'message' => 'Absen masuk berhasil. Status: ' . $status,
    //         'jam' => $now->format('H:i:s')
    //     ]);
    // }

    // public function absenPulang(Request $request)
    // {
    //     $user = Auth::user();
    //     $today = now()->toDateString();
    //     $now = now();

    //     if (!$request->photo) {
    //         return response()->json(['message' => 'Foto absensi wajib diambil'], 400);
    //     }

    //     $absen = Absensi::with('shift')->where('user_id', $user->id)
    //         ->where('tanggal', $today)
    //         ->first();

    //     if (!$absen || !$absen->jam_masuk) {
    //         return response()->json(['message' => 'Anda belum absen masuk'], 409);
    //     }

    //     if ($absen->jam_keluar) {
    //         return response()->json(['message' => 'Anda sudah absen pulang hari ini'], 409);
    //     }

    //     // LOGIKA OPSIONAL: Cek Pulang Lebih Awal
    //     $statusSekarang = $absen->status;
    //     $jamPulangShift = $absen->shift->jam_pulang;

    //     if ($now->format('H:i:s') < $jamPulangShift) {
    //         // Jika sebelumnya HADIR tapi pulang cepat, status bisa diupdate ke PULANG LEBIH AWAL
    //         // Namun jika sebelumnya TERLAMBAT, biarkan tetap TERLAMBAT agar tetap terhitung poin minusnya
    //         if ($statusSekarang == 'HADIR') {
    //             $statusSekarang = 'PULANG LEBIH AWAL';
    //         }
    //     }

    //     $fotoPulang = $this->saveBase64Photo($request->photo, 'pulang');

    //     $absen->update([
    //         'jam_keluar'  => $now->format('H:i:s'),
    //         'foto_pulang' => $fotoPulang,
    //         'status'      => $statusSekarang
    //     ]);

    //     return response()->json(['message' => 'Absen pulang berhasil']);
    // }

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

    public function updateFace(Request $request)
    {
        // 1. Ambil descriptor wajah baru dari request
        $newDescriptor = json_decode($request->face_embedding);

        if (!$newDescriptor) {
            return response()->json(['status' => 'error', 'message' => 'Data wajah tidak valid'], 400);
        }

        // 2. Ambil semua user yang SUDAH punya data wajah (kecuali user yang sedang login)
        $otherUsers = User::whereNotNull('face_embedding')
            ->where('id', '!=', auth()->id())
            ->get();

        // 3. Bandingkan wajah baru dengan semua wajah di database
        foreach ($otherUsers as $user) {
            $existingDescriptor = json_decode($user->face_embedding);

            // Hitung jarak Euclidean
            $distance = $this->euclideanDistance($newDescriptor, $existingDescriptor);

            // Threshold 0.45 (Semakin kecil semakin mirip/identik)
            // Jika di bawah 0.45, sistem menganggap ini orang yang sama
            if ($distance < 0.45) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal! Wajah ini sudah terdaftar di sistem atas nama: ' . $user->name
                ], 422); // Kirim status 422 (Unprocessable Entity)
            }
        }

        // 4. Jika tidak ada kemiripan ditemukan, baru simpan ke akun user saat ini
        auth()->user()->update([
            'face_embedding' => $request->face_embedding
        ]);

        return response()->json(['status' => 'success', 'message' => 'Wajah berhasil didaftarkan']);
    }

    /**
     * Fungsi menghitung jarak antara dua vektor wajah
     */
    private function euclideanDistance($arr1, $arr2)
    {
        $sum = 0;
        for ($i = 0; $i < count($arr1); $i++) {
            $sum += pow($arr1[$i] - $arr2[$i], 2);
        }
        return sqrt($sum);
    }
}
