<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LemburController extends Controller
{
    // --- ADMIN SECTION ---
    public function approvalIndex()
    {
        $lembur = Lembur::with(['user.divisi'])->orderBy('created_at', 'desc')->get();
        return view('admin.approval_lembur.index', compact('lembur'));
    }

    public function updateStatus(Request $request, $id)
    {
        $lembur = Lembur::findOrFail($id);
        $lembur->update(['status' => $request->status]);
        return response()->json(['message' => 'Success']);
    }


    // --- KARYAWAN SECTION ---
    public function index()
    {
        $user_id = Auth::id();
        
        $riwayat = Lembur::where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalApproved = Lembur::where('user_id', $user_id)->where('status', 'APPROVED')->count();
        $totalPending = Lembur::where('user_id', $user_id)->where('status', 'PENDING')->count();

        return view('absensi.lembur.index', compact('riwayat', 'totalApproved', 'totalPending'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'keterangan' => 'required|string|max:1000',
            'foto_data' => 'required',
            'tipe' => 'required|in:MASUK,KELUAR' // Input hidden dari form
        ]);

        try {
            $imageData = $request->foto_data;
            $image_parts = explode(";base64,", $imageData);
            $image_base64 = base64_decode($image_parts[1]);

            $fileName = 'lembur_' . Auth::id() . '_' . time() . '.jpg';
            $destinationPath = public_path('uploads/lembur');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            file_put_contents($destinationPath . '/' . $fileName, $image_base64);

            if ($request->tipe === 'MASUK') {
                // Buat data lembur baru
                Lembur::create([
                    'user_id' => Auth::id(),
                    'jam_masuk' => Carbon::now(),
                    'keterangan' => $request->keterangan,
                    'foto' => $fileName,
                    'status' => 'PENDING',
                ]);
                $msg = 'Berhasil memulai lembur!';
            } else {
                // Cari data lembur hari ini yang belum ada jam keluar
                $lembur = Lembur::where('user_id', Auth::id())
                    ->whereNull('jam_keluar')
                    ->latest()
                    ->first();

                if (!$lembur) {
                    return redirect()->back()->with('error', 'Data lembur masuk tidak ditemukan.');
                }

                $lembur->update([
                    'jam_keluar' => Carbon::now(),
                    // Gabungkan keterangan masuk dengan keterangan keluar
                    'keterangan' => $lembur->keterangan . " | Keluar: " . $request->keterangan,
                ]);
                $msg = 'Berhasil menyelesaikan lembur!';
            }

            return redirect()->back()->with('success', $msg);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}