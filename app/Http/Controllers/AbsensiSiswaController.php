<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\AbsensiSiswa;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbsensiSiswaController extends Controller
{
    public function index(Request $request)
    {
        $kelasList = Kelas::aktif()->get();

        $query = AbsensiSiswa::with('siswa.kelasRelasi');

        if ($request->filled('tanggal')) {
            $query->where('tanggal', $request->tanggal);
        } else {
            $query->where('tanggal', now()->toDateString());
        }

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $absensi = $query->orderBy('jam_masuk', 'desc')->get();

        return view('absensi_siswa.index', compact('absensi', 'kelasList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'tanggal' => 'required|date',
            'jam_masuk' => 'nullable',
            'jam_keluar' => 'nullable',
            'status' => 'required|in:HADIR,TERLAMBAT,IZIN,SAKIT,ALPA,LIBUR',
            'keterangan' => 'nullable|string',
        ]);

        $absensi = AbsensiSiswa::updateOrCreate(
            [
                'siswa_id' => $request->siswa_id,
                'tanggal' => $request->tanggal,
            ],
            [
                'jam_masuk' => $request->jam_masuk,
                'jam_keluar' => $request->jam_keluar,
                'status' => $request->status,
                'keterangan' => $request->keterangan,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Absensi siswa berhasil disimpan',
            'data' => $absensi,
        ]);
    }

    public function massStore(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jam_masuk' => 'nullable',
            'data' => 'required|array',
            'data.*.siswa_id' => 'required|exists:siswas,id',
            'data.*.status' => 'required|in:HADIR,TERLAMBAT,IZIN,SAKIT,ALPA,LIBUR',
            'data.*.keterangan' => 'nullable|string',
        ]);

        $tanggal = $request->tanggal;
        $jamMasuk = $request->jam_masuk;
        $results = [];

        foreach ($request->data as $item) {
            $absensi = AbsensiSiswa::updateOrCreate(
                [
                    'siswa_id' => $item['siswa_id'],
                    'tanggal' => $tanggal,
                ],
                [
                    'jam_masuk' => $jamMasuk,
                    'status' => $item['status'],
                    'keterangan' => $item['keterangan'] ?? null,
                ]
            );
            $results[] = $absensi;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Absensi massal berhasil disimpan',
            'data' => $results,
        ]);
    }

    public function update(Request $request, $id)
    {
        $absensi = AbsensiSiswa::findOrFail($id);

        $request->validate([
            'jam_masuk' => 'nullable',
            'jam_keluar' => 'nullable',
            'status' => 'required|in:HADIR,TERLAMBAT,IZIN,SAKIT,ALPA,LIBUR',
            'keterangan' => 'nullable|string',
        ]);

        $absensi->update($request->only(['jam_masuk', 'jam_keluar', 'status', 'keterangan']));

        return response()->json([
            'status' => 'success',
            'message' => 'Absensi siswa berhasil diperbarui',
            'data' => $absensi,
        ]);
    }

    public function rekap(Request $request)
    {
        $kelasList = Kelas::aktif()->get();

        $start_date = $request->start_date ?? now()->startOfMonth()->toDateString();
        $end_date = $request->end_date ?? now()->endOfMonth()->toDateString();

        $query = Siswa::where('status', 'AKTIF');

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $rekap = $query->with(['kelasRelasi', 'absensi' => function ($q) use ($start_date, $end_date) {
            $q->whereBetween('tanggal', [$start_date, $end_date]);
        }])->get()->map(function ($siswa) {
            $hadir = $siswa->absensi->where('status', 'HADIR')->count();
            $terlambat = $siswa->absensi->where('status', 'TERLAMBAT')->count();
            $izin = $siswa->absensi->where('status', 'IZIN')->count();
            $sakit = $siswa->absensi->where('status', 'SAKIT')->count();
            $alpa = $siswa->absensi->where('status', 'ALPA')->count();
            $libur = $siswa->absensi->where('status', 'LIBUR')->count();

            $totalHadir = $hadir + $terlambat;

            return (object) [
                'nama' => $siswa->nama,
                'kelas' => $siswa->kelasRelasi->nama_kelas ?? $siswa->kelas,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'libur' => $libur,
                'total_hadir' => $totalHadir,
                'total' => $siswa->absensi->count(),
            ];
        });

        return view('rekap_siswa.index', compact('rekap', 'kelasList', 'start_date', 'end_date'));
    }

    public function dataSiswaByKelas(Request $request)
    {
        $request->validate(['kelas_id' => 'required|exists:kelas,id']);

        $siswa = Siswa::where('kelas_id', $request->kelas_id)
            ->where('status', 'AKTIF')
            ->orderBy('nama')
            ->get(['id', 'nama']);

        return response()->json($siswa);
    }

    public function cekAbsensiSiswa(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'tanggal' => 'required|date',
        ]);

        $absensi = AbsensiSiswa::where('siswa_id', $request->siswa_id)
            ->where('tanggal', $request->tanggal)
            ->first();

        return response()->json($absensi);
    }
}
