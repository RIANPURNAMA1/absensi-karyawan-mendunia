<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Izin;
use App\Models\IzinApproval;
use App\Services\IzinApprovalService as ServicesIzinApprovalService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use IzinApprovalService;

class IzinController extends Controller
{
    public function create()
    {
        return view('absensi.izin.create'); // Menampilkan view form yang kita buat sebelumnya
    }



    public function approvalList()
    {
        $izins = Izin::with('user')
            ->latest()
            ->get();

        return view('admin.izin.index', compact('izins'));
    }


    public function approve($id)
    {
        $izin = Izin::findOrFail($id);

        if ($izin->status !== 'PENDING') {
            return back()->with('error', 'Izin sudah diproses');
        }

        if (!auth()->user()->isHR() && !auth()->user()->isManager()) {
            abort(403, 'Tidak punya akses approval');
        }

        DB::transaction(function () use ($izin) {

            // 1️⃣ Update status izin utama
            $izin->update([
                'status' => 'APPROVED'
            ]);

            // 2️⃣ Simpan log approval
            IzinApproval::create([
                'izin_id'     => $izin->id,
                'approved_by' => auth()->id(),
                'status'      => 'APPROVED',
                'approved_at' => now(),
            ]);

            // 3️⃣ Generate absensi otomatis
            ServicesIzinApprovalService::generateAbsensi($izin);
        });

        return back()->with('success', 'Izin disetujui & absensi otomatis dibuat');
    }


    public function reject(Request $request, $id)
    {
        $izin = Izin::findOrFail($id);

        if ($izin->status !== 'PENDING') {
            return back()->with('error', 'Izin sudah diproses');
        }

        DB::transaction(function () use ($izin, $request) {

            $izin->update([
                'status' => 'REJECTED'
            ]);

            IzinApproval::create([
                'izin_id'     => $izin->id,
                'approved_by' => auth()->id(),
                'status'      => 'REJECTED',
                'catatan'     => $request->catatan,
                'approved_at' => now(),
            ]);
        });

        return back()->with('success', 'Izin ditolak');
    }

    /**
     * Menyimpan data pengajuan izin ke database
     */
    public function store(Request $request)
    {
        $userId = Auth::id();
        $today = \Carbon\Carbon::today();
        $tglMulai = $request->tgl_mulai;

        // 🔒 1. CEK: Apakah hari ini user sudah membuat pengajuan izin?
        // Pengecekan berdasarkan kolom created_at (tanggal input data)
        $sudahInputHariIni = Izin::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->exists();

        if ($sudahInputHariIni) {
            $msg = 'Anda sudah membuat pengajuan izin hari ini. Silakan ajukan kembali besok jika ada keperluan lain.';
            return $request->ajax()
                ? response()->json(['status' => 'error', 'message' => $msg], 422)
                : redirect()->back()->with('error', $msg)->withInput();
        }

        // 🔒 2. CEK: user hanya boleh punya 1 izin PENDING (Opsional, tetap dipertahankan agar tidak double)
        $izinPending = Izin::where('user_id', $userId)
            ->where('status', 'PENDING')
            ->exists();

        if ($izinPending) {
            $msg = 'Anda masih memiliki pengajuan izin yang sedang menunggu persetujuan.';
            return $request->ajax()
                ? response()->json(['status' => 'error', 'message' => $msg], 422)
                : redirect()->back()->with('error', $msg)->withInput();
        }

        // 🔒 3. CEK: Apakah sudah ada absen pada tanggal mulai izin?
        $sudahAbsen = \App\Models\Absensi::where('user_id', $userId)
            ->where('tanggal', $tglMulai)
            ->exists();

        if ($sudahAbsen) {
            $msg = 'Gagal mengajukan izin. Anda tercatat sudah melakukan absensi pada tanggal ' . \Carbon\Carbon::parse($tglMulai)->format('d-m-Y') . '.';
            return $request->ajax()
                ? response()->json(['status' => 'error', 'message' => $msg], 422)
                : redirect()->back()->with('error', $msg)->withInput();
        }

        // 3️⃣ VALIDASI INPUT
        $request->validate([
            'jenis_izin' => 'required|in:SAKIT,CUTI,IZIN',
            'tgl_mulai'   => 'required|date|after_or_equal:today',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'alasan'      => 'required|string|min:10',
            'lampiran'    => 'nullable|mimes:jpeg,png,jpg,pdf|max:2048',
        ], [
            'tgl_mulai.after_or_equal'   => 'Tanggal mulai tidak boleh masa lalu.',
            'tgl_selesai.after_or_equal' => 'Tanggal berakhir harus setelah atau sama dengan tanggal mulai.',
            'alasan.min'                 => 'Berikan alasan yang lebih detail (minimal 10 karakter).',
        ]);

        try {
            // 4️⃣ SIMPAN DATA IZIN
            $izin = new Izin();
            $izin->user_id     = $userId;
            $izin->jenis_izin  = $request->jenis_izin;
            $izin->tgl_mulai   = $request->tgl_mulai;
            $izin->tgl_selesai = $request->tgl_selesai;
            $izin->alasan      = $request->alasan;
            $izin->status      = 'PENDING';

            // 5️⃣ UPLOAD LAMPIRAN
            if ($request->hasFile('lampiran')) {
                $file = $request->file('lampiran');
                $namaFile = $userId . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/izin'), $namaFile);
                $izin->lampiran = $namaFile;
            }

            $izin->save();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Pengajuan izin berhasil dikirim.'
                ]);
            }

            return redirect('/absensi')->with('success', 'Pengajuan izin berhasil dikirim.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem.')->withInput();
        }
    }
    public function index()
    {
        // Mengambil data izin milik user yang sedang login saja
        $riwayatIzin = \App\Models\Izin::where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('absensi.izin.index', compact('riwayatIzin'));
    }


    public function lihatLampiran($filename)
    {
        $path = storage_path('app/public/' . $filename);

        if (!file_exists($path)) {
            dd($path); // 🔥 biar kita tahu dia nyari dimana
        }

        return response()->file($path);
    }




    // public function index()
    // {
    //     $riwayatIzin = Auth::user()->izin()->orderBy('created_at', 'desc')->get();
    //     return view('izin.index', compact('riwayatIzin'));
    // }

}
