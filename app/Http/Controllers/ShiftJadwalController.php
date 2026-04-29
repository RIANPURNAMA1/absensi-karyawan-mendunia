<?php

namespace App\Http\Controllers;

use App\Models\ShiftJadwal;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ShiftJadwalController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);
        $userId = $request->get('user_id');

        $query = ShiftJadwal::with(['user', 'shift']);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($bulan && $tahun) {
            $query->whereMonth('tanggal', $bulan)
                  ->whereYear('tanggal', $tahun);
        }

        $jadwals = $query->orderBy('tanggal', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $jadwals
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'required|exists:shifts,id',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $existing = ShiftJadwal::where('user_id', $request->user_id)
                ->where('tanggal', $request->tanggal)
                ->first();

            if ($existing) {
                $existing->update([
                    'shift_id' => $request->shift_id,
                    'keterangan' => $request->keterangan
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Jadwal shift berhasil diperbarui'
                ]);
            }

            ShiftJadwal::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Jadwal shift berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'required|exists:shifts,id',
            'tanggal_list' => 'required|array',
            'tanggal_list.*' => 'required|date',
            'keterangan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $count = 0;
            foreach ($request->tanggal_list as $tanggal) {
                ShiftJadwal::updateOrCreate(
                    [
                        'user_id' => $request->user_id,
                        'tanggal' => $tanggal
                    ],
                    [
                        'shift_id' => $request->shift_id,
                        'keterangan' => $request->keterangan
                    ]
                );
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "Jadwal shift berhasil dibuat untuk {$count} hari"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $jadwal = ShiftJadwal::findOrFail($id);
            $jadwal->delete();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal shift berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getJadwalKaryawan(Request $request, $userId)
    {
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);

        $jadwals = ShiftJadwal::where('user_id', $userId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->with('shift')
            ->get()
            ->keyBy(function ($item) {
                return $item->tanggal->toDateString();
            });

        $user = User::findOrFail($userId);
        $defaultShift = $user->shift;

        return response()->json([
            'success' => true,
            'jadwals' => $jadwals,
            'default_shift' => $defaultShift
        ]);
    }
}