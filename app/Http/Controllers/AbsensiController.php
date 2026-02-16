<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Cabang;
use App\Models\HariLibur;
use App\Models\Karyawan;
use App\Models\JadwalKerja;
use App\Models\User;
use App\Models\Shift; // <--- TAMBAHKAN BARIS INI
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    public function index()
    {
        // Load cabang dan shift user yang sedang login
        $user = Auth::user()->load(['cabang', 'shift']);

        if (!$user->cabang) {
            abort(403, 'User belum terdaftar di cabang manapun.');
        }

        // 1. Ambil 10 riwayat terakhir
        $riwayat = Absensi::where('user_id', $user->id)
            ->orderBy('tanggal', 'desc')
            ->take(10)
            ->get();

        // 2. Ambil semua jadwal shift yang aktif
        $allShifts = \App\Models\Shift::where('status', 'AKTIF')->get();

        // --- LOGIKA NOTIFIKASI BROWSER ---
        $showNotification = false;
        $notifMessage = "";

        if ($user->shift_id && $user->shift) {
            $now = Carbon::now();
            $today = Carbon::today()->toDateString();

            // Perbaikan: Ambil H:i:s saja untuk mencegah error "Double date specification"
            $jamHanya = Carbon::parse($user->shift->jam_masuk)->format('H:i:s');
            $jamMasuk = Carbon::parse($today . ' ' . $jamHanya);

            // Cek apakah sudah absen hari ini
            $sudahAbsen = Absensi::where('user_id', $user->id)
                ->where('tanggal', $today)
                ->exists();

            if (!$sudahAbsen) {
                // false agar menghasilkan nilai negatif jika waktu sekarang sudah melewati jam masuk
                $selisihMenit = $now->diffInMinutes($jamMasuk, false);

                // Munculkan notifikasi jika waktu masuk sisa 1 - 30 menit lagi
                if ($selisihMenit > 0 && $selisihMenit <= 30) {
                    $showNotification = true;
                    $notifMessage = "Waktunya bersiap! Jam masuk Anda pukul " . $jamMasuk->format('H:i') . " (" . $selisihMenit . " menit lagi).";
                }
            }
        }
        // ----------------------------------

        // 3. Cari shift yang berlaku saat ini
        $currentTime = now()->format('H:i:s');
        $currentShift = \App\Models\Shift::where('status', 'AKTIF')
            ->where('jam_masuk', '<=', $currentTime)
            ->where('jam_pulang', '>=', $currentTime)
            ->first();

        $cabang = $user->cabang;

        return view('absensi.index', [
            'riwayat'          => $riwayat,
            'shifts'           => $allShifts,
            'currentShift'     => $currentShift,
            'cabangLat'        => $cabang->latitude ?? 0,
            'cabangLong'       => $cabang->longitude ?? 0,
            'radiusMeter'      => $cabang->radius ?? 100,
            'namaCabang'       => $cabang->nama_cabang ?? '-',
            // Kirim data notifikasi ke view
            'showNotification' => $showNotification,
            'notifMessage'     => $notifMessage
        ]);
    }

    public function riwayatTerbaru()
    {
        $riwayat = Absensi::where('user_id', Auth::id())
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();

        return response()->json($riwayat);
    }


    public function riwayatSemua()
    {
        // Ambil semua absensi beserta relasi karyawan
        $absensi = Absensi::with('user')->orderBy('tanggal', 'desc')->get();

        return view('absensi.riwayat', compact('absensi'));
    }

    public function profile()
    {
        $user = Auth::user()->load(['divisi']);

        // Hitung statistik bulan ini
        $stats = [
            'hadir' => \App\Models\Absensi::where('user_id', $user->id)->whereMonth('tanggal', now()->month)->where('status', 'HADIR')->count(),
            'izin' => \App\Models\Absensi::where('user_id', $user->id)->whereMonth('tanggal', now()->month)->where('status', 'IZIN')->count(),
            'terlambat' => \App\Models\Absensi::where('user_id', $user->id)->whereMonth('tanggal', now()->month)->where('status', 'TERLAMBAT')->count(),
        ];

        return view('absensi.profile', compact('user', 'stats'));
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



    public function absenMasuk(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // --- VALIDASI HARI LIBUR ---
        if (HariLibur::apakahLibur($today)) {
            return response()->json([
                'message' => 'Hari ini adalah hari libur (Weekend/Nasional). Absensi tidak dibuka.'
            ], 403);
        }

        // 0. Validasi input embedding wajah
        if (!$request->has('face_embedding')) {
            return response()->json(['message' => 'Face embedding diperlukan untuk absen'], 422);
        }

        $faceEmbeddingInput = json_decode($request->face_embedding);

        // 1. Cocokkan wajah
        $user = $this->cocokkanFaceEmbedding($faceEmbeddingInput);
        if (!$user) {
            return response()->json(['message' => 'Wajah tidak terdaftar atau tidak dikenali'], 422);
        }

        // 2. Cek apakah sudah absen hari ini
        $cek = Absensi::where('user_id', $user->id)->where('tanggal', $today)->first();
        if ($cek) return response()->json(['message' => 'Anda sudah absen masuk hari ini'], 422);

        // 3. Ambil cabang & shift
        $cabang = Cabang::find($user->cabang_id);
        if (!$cabang) return response()->json(['message' => 'Cabang tidak ditemukan'], 422);

        $shift = Shift::find($user->shift_id);
        if (!$shift) return response()->json(['message' => 'Jadwal shift tidak ditemukan'], 422);

        // 4. Validasi jarak
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

        // 5. Logika terlambat
        $status = 'HADIR';
        $jamMasukShift = Carbon::parse($shift->jam_masuk);
        $batasToleransi = $jamMasukShift->copy()->addMinutes($shift->toleransi);

        if ($now->gt($batasToleransi)) {
            $status = 'TERLAMBAT';
        }

        // 6. Simpan absensi
        $absensi = Absensi::create([
            'user_id'    => $user->id,
            'cabang_id'  => $cabang->id,
            'shift_id'   => $shift->id,
            'tanggal'    => $today,
            'jam_masuk'  => $now->toTimeString(),
            'lat_masuk'  => $request->latitude,
            'long_masuk' => $request->longitude,
            'status'     => $status,
        ]);

        return response()->json([
            'message' => 'Absen masuk berhasil. Status: ' . $status,
            'absensi' => $absensi
        ]);
    }

    /**
     * Fungsi mencocokkan embedding wajah input dengan database
     */
    /**
     * Fungsi mencocokkan embedding wajah input dengan database
     * Diperketat dengan Euclidean Distance dan Threshold rendah
     */
    // --- HELPER METHODS ---

    private function cocokkanFaceEmbedding(array $embeddingInput)
    {
        $users = User::whereNotNull('face_embedding')->get();
        $bestMatch = null;
        $minDistance = 1.0;

        foreach ($users as $user) {
            $embeddingDb = json_decode($user->face_embedding, true);
            $distance = $this->euclideanDistance($embeddingInput, $embeddingDb);

            if ($distance < 0.40 && $distance < $minDistance) {
                $minDistance = $distance;
                $bestMatch = $user;
            }
        }
        return $bestMatch;
    }
    /**
     * Hitung cosine similarity antara dua array embedding
     */
    private function cosineSimilarity(array $vecA, array $vecB)
    {
        $dot = 0;
        $normA = 0;
        $normB = 0;

        for ($i = 0; $i < count($vecA); $i++) {
            $dot += $vecA[$i] * $vecB[$i];
            $normA += pow($vecA[$i], 2);
            $normB += pow($vecB[$i], 2);
        }

        $normA = sqrt($normA);
        $normB = sqrt($normB);

        if ($normA * $normB == 0) return 0;

        return $dot / ($normA * $normB);
    }


    public function absenPulang(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $now   = Carbon::now();

        // 0️⃣ Validasi wajah
        if (!$request->has('face_embedding')) {
            return response()->json(['message' => 'Face embedding diperlukan'], 422);
        }

        $faceEmbeddingInput = json_decode($request->face_embedding);

        // 1️⃣ Cocokkan wajah
        $user = $this->cocokkanFaceEmbedding($faceEmbeddingInput);
        if (!$user) {
            return response()->json(['message' => 'Wajah tidak dikenali'], 422);
        }

        // 2️⃣ Ambil absensi hari ini
        $absensi = Absensi::with('shift')
            ->where('user_id', $user->id)
            ->where('tanggal', $today)
            ->first();

        if (!$absensi) return response()->json(['message' => 'Belum absen masuk'], 422);
        if ($absensi->jam_keluar) return response()->json(['message' => 'Sudah absen pulang'], 422);
        if (!$absensi->shift) return response()->json(['message' => 'Shift tidak ditemukan'], 422);

        // 3️⃣ Validasi lokasi
        $cabang = Cabang::find($user->cabang_id);
        if (!$cabang) return response()->json(['message' => 'Cabang tidak ditemukan'], 422);

        $jarak = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $cabang->latitude,
            $cabang->longitude
        );

        if ($jarak > $cabang->radius) {
            return response()->json([
                'message' => 'Di luar radius! Jarak: ' . round($jarak) . 'm'
            ], 422);
        }

        // 4️⃣ Jam shift + tanggal
        $jamMasukShift  = Carbon::parse($absensi->shift->jam_masuk);
        $jamPulangShift = Carbon::parse($absensi->shift->jam_pulang);


        if ($jamPulangShift->lt($jamMasukShift)) {
            $jamPulangShift->addDay(); // shift malam
        }

        // 🔥 Batas akhir (MODE TEST 3 menit)
        $batasAkhir = $jamPulangShift->copy()->addHours(5);

        // ⛔ Sudah lewat batas → langsung tandai
        if ($now->greaterThan($batasAkhir)) {

            $absensi->update([
                'status' => 'TIDAK ABSEN PULANG',
                'keterangan' => 'Terlambat absen pulang (>3 menit)'
            ]);

            return response()->json([
                'message' => 'Waktu habis. Anda dianggap TIDAK ABSEN PULANG.'
            ], 422);
        }

        // 5️⃣ Status normal
        $statusBaru = $absensi->status;

        if ($now->lt($jamPulangShift) && $absensi->status !== 'TERLAMBAT') {
            $statusBaru = 'PULANG LEBIH AWAL';
        }

        // 6️⃣ Simpan
        $absensi->update([
            'jam_keluar'  => $now->toTimeString(),
            'lat_pulang'  => $request->latitude,
            'long_pulang' => $request->longitude,
            'status'      => $statusBaru,
        ]);

        return response()->json([
            'message' => "Absen pulang berhasil",
            'status'  => $statusBaru
        ]);
    }



    public function statusAbsensi(Request $request)
    {
        $today = Carbon::today()->toDateString();

        if (!$request->has('face_embedding')) {
            return response()->json(['message' => 'Face embedding diperlukan'], 422);
        }

        $faceEmbeddingInput = json_decode($request->face_embedding);
        $user = $this->cocokkanFaceEmbedding($faceEmbeddingInput);

        if (!$user) {
            return response()->json(['message' => 'Wajah tidak terdaftar', 'status' => 'TIDAK_TERDAFTAR'], 422);
        }

        // --- CEK JIKA HARI INI LIBUR ---
        if (HariLibur::apakahLibur($today)) {
            return response()->json([
                'status' => 'LIBUR',
                'user_id' => $user->id,
                'user_name' => $user->name,
                'message' => 'Hari ini libur. Selamat beristirahat!'
            ]);
        }

        $absensi = Absensi::where('user_id', $user->id)->where('tanggal', $today)->first();

        if (!$absensi) {
            return response()->json([
                'status' => 'BELUM_MASUK',
                'user_id' => $user->id,
                'user_name' => $user->name
            ]);
        }

        if ($absensi->jam_keluar === null) {
            return response()->json([
                'status' => 'SUDAH_MASUK',
                'user_id' => $user->id,
                'user_name' => $user->name,
                'jam_masuk' => $absensi->jam_masuk
            ]);
        }

        return response()->json([
            'status' => 'SUDAH_PULANG',
            'user_id' => $user->id,
            'user_name' => $user->name,
            'jam_masuk' => $absensi->jam_masuk,
            'jam_keluar' => $absensi->jam_keluar
        ]);
    }





    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
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

    public function updateFace(Request $request)
    {
        $newDescriptor = json_decode($request->face_embedding);

        if (!$newDescriptor) {
            return response()->json(['status' => 'error', 'message' => 'Data wajah tidak valid'], 400);
        }

        // Ambil user lain untuk pengecekan duplikasi wajah
        $otherUsers = User::whereNotNull('face_embedding')
            ->where('id', '!=', auth()->id())
            ->get();

        foreach ($otherUsers as $user) {
            $existingDescriptor = json_decode($user->face_embedding);
            $distance = $this->euclideanDistance($newDescriptor, $existingDescriptor);

            // Jika distance < 0.40, berarti wajah ini "terlalu mirip" dengan orang lain
            if ($distance < 0.40) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal! Wajah ini terdeteksi mirip dengan: ' . $user->name . '. Silakan ambil ulang dengan posisi lebih tegak.'
                ], 422);
            }
        }

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
        $distance = sqrt($sum);

        // LOGIKA: Jika distance > 0.4, maka dianggap ORANG BERBEDA
        return $distance;
    }


    // absen foto
    public function absenFoto(Request $request)
    {
        $user  = Auth::user();
        $today = Carbon::today()->toDateString();
        $now   = Carbon::now();

        // ===============================
        // 1. VALIDASI INPUT
        // ===============================
        $request->validate([
            'photo'     => 'required',
            'latitude'  => 'required',
            'longitude' => 'required',
        ]);

        // ===============================
        // 2. VALIDASI HARI LIBUR
        // ===============================
        if (HariLibur::apakahLibur($today)) {
            return response()->json([
                'message' => 'Hari ini adalah hari libur. Absensi tidak dibuka.'
            ], 403);
        }

        // ===============================
        // 3. VALIDASI CABANG & RADIUS
        // ===============================
        $cabang = $user->cabang;
        if (!$cabang) {
            return response()->json(['message' => 'Cabang tidak ditemukan'], 422);
        }

        $jarak = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $cabang->latitude,
            $cabang->longitude
        );

        if ($jarak > $cabang->radius) {
            return response()->json([
                'message' => 'Gagal! Anda di luar radius cabang. Jarak: ' . round($jarak) . 'm'
            ], 422);
        }

        // ===============================
        // 4. SIMPAN FOTO
        // ===============================
        $fotoPath = $this->saveBase64Photo($request->photo, 'absen_manual');

        // ===============================
        // 5. CEK ABSENSI HARI INI
        // ===============================
        $absensi = Absensi::with('shift')
            ->where('user_id', $user->id)
            ->where('tanggal', $today)
            ->first();

        // =====================================================
        // =================== MODE MASUK ======================
        // =====================================================
        if (!$absensi) {

            $shift = $user->shift;
            if (!$shift) {
                return response()->json(['message' => 'Shift tidak ditemukan'], 422);
            }

            $jamMasukShift  = Carbon::parse($shift->jam_masuk);
            $jamPulangShift = Carbon::parse($shift->jam_pulang);

            // Handle shift malam
            if ($jamPulangShift->lt($jamMasukShift)) {
                $jamPulangShift->addDay();
            }

            // Batas toleransi
            $batasToleransi = $jamMasukShift->copy()->addMinutes($shift->toleransi);

            // Default status
            $status = 'HADIR';

            // Jika melewati toleransi → TERLAMBAT
            if ($now->gt($batasToleransi)) {
                $status = 'TERLAMBAT';
            }

            $absensi = Absensi::create([
                'user_id'    => $user->id,
                'cabang_id'  => $cabang->id,
                'shift_id'   => $shift->id,
                'tanggal'    => $today,
                'jam_masuk'  => $now->toTimeString(),
                'lat_masuk'  => $request->latitude,
                'long_masuk' => $request->longitude,
                'foto_masuk' => $fotoPath,
                'status'     => $status,
            ]);

            return response()->json([
                'message' => 'Absen masuk berhasil. Status: ' . $status,
                'status'  => $status,
                'path'    => $fotoPath
            ]);
        }

        // =====================================================
        // =================== MODE PULANG =====================
        // =====================================================

        if ($absensi->jam_keluar) {
            return response()->json(['message' => 'Anda sudah absen pulang'], 422);
        }

        if (!$absensi->shift) {
            return response()->json(['message' => 'Shift tidak ditemukan'], 422);
        }

        $jamMasukShift  = Carbon::parse($absensi->shift->jam_masuk);
        $jamPulangShift = Carbon::parse($absensi->shift->jam_pulang);

        // Handle shift malam
        if ($jamPulangShift->lt($jamMasukShift)) {
            $jamPulangShift->addDay();
        }

        // Batas akhir absen pulang (5 jam setelah shift selesai)
        $batasAkhir = $jamPulangShift->copy()->addHours(5);

        if ($now->gt($batasAkhir)) {
            $absensi->update([
                'status'     => 'TIDAK ABSEN PULANG',
                'keterangan' => 'Terlambat absen pulang (melebihi batas)'
            ]);

            return response()->json([
                'message' => 'Waktu habis. Anda dianggap TIDAK ABSEN PULANG.'
            ], 422);
        }

        // Default ambil status dari masuk
        $statusBaru = $absensi->status;

        // Jika pulang sebelum jam shift selesai
        if ($now->lt($jamPulangShift)) {
            $statusBaru = 'PULANG LEBIH AWAL';
        }

        $absensi->update([
            'jam_keluar'  => $now->toTimeString(),
            'lat_pulang'  => $request->latitude,
            'long_pulang' => $request->longitude,
            'foto_pulang' => $fotoPath,
            'status'      => $statusBaru,
        ]);

        return response()->json([
            'message' => 'Absen pulang berhasil. Status: ' . $statusBaru,
            'status'  => $statusBaru,
            'path'    => $fotoPath
        ]);
    }
}
