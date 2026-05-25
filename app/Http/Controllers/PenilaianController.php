<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\Penilaian;
use App\Models\PenilaianSetting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PenilaianController extends Controller
{
    public function settingsIndex()
    {
        $divisis = Divisi::orderBy('nama_divisi')->get();
        $settings = PenilaianSetting::pluck('penilaian_aktif', 'divisi_id')->toArray();

        return view('penilaian.settings', compact('divisis', 'settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'penilaian_aktif' => 'array',
            'penilaian_aktif.*' => 'in:1',
        ]);

        $aktifIds = array_keys($request->penilaian_aktif ?? []);

        PenilaianSetting::query()->update(['penilaian_aktif' => false]);

        foreach ($aktifIds as $divisiId) {
            PenilaianSetting::updateOrCreate(
                ['divisi_id' => $divisiId],
                ['penilaian_aktif' => true]
            );
        }

        return redirect()->back()->with('success', 'Pengaturan penilaian berhasil disimpan.');
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'KARYAWAN') {
            $aktif = PenilaianSetting::where('divisi_id', $user->divisi_id)
                ->where('penilaian_aktif', true)->exists();

            if (!$aktif) {
                abort(403, 'Fitur penilaian tidak aktif untuk divisi Anda.');
            }
        }

        if (in_array($user->role, ['HR', 'MANAGER'])) {
            $penilaians = Penilaian::with('user')
                ->orderBy('tanggal_penilaian', 'desc')
                ->get();
        } else {
            $penilaians = Penilaian::where('user_id', $user->id)
                ->orderBy('tanggal_penilaian', 'desc')
                ->get();
        }

        return view('penilaian.index', compact('penilaians'));
    }

    public function karyawanIndex()
    {
        $user = auth()->user();

        $aktif = PenilaianSetting::where('divisi_id', $user->divisi_id)
            ->where('penilaian_aktif', true)->exists();

        if (!$aktif) {
            abort(403, 'Fitur penilaian tidak aktif untuk divisi Anda.');
        }

        $penilaians = Penilaian::where('user_id', $user->id)
            ->orderBy('tanggal_penilaian', 'desc')
            ->get();

        $kelasList = \App\Models\KelasSensei::where('user_id', $user->id)
            ->orderBy('nama_kelas')
            ->get();

        return view('penilaian.karyawan', compact('penilaians', 'kelasList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_siswa' => 'required|string|max:255',
            'kelas' => 'nullable|string|max:100',
            'mata_pelajaran' => 'nullable|string|max:255',
            'nilai' => 'nullable|numeric|min:0|max:100',
            'keterangan' => 'nullable|string',
            'tanggal_penilaian' => 'required|date',
        ]);

        Penilaian::create([
            'user_id' => auth()->id(),
            'nama_siswa' => $request->nama_siswa,
            'kelas' => $request->kelas,
            'mata_pelajaran' => $request->mata_pelajaran,
            'nilai' => $request->nilai,
            'keterangan' => $request->keterangan,
            'tanggal_penilaian' => $request->tanggal_penilaian,
        ]);

        return redirect()->back()->with('success', 'Penilaian berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (in_array($user->role, ['HR', 'MANAGER'])) {
            $penilaian = Penilaian::findOrFail($id);
        } else {
            $penilaian = Penilaian::where('user_id', $user->id)->findOrFail($id);
        }

        $request->validate([
            'nama_siswa' => 'required|string|max:255',
            'kelas' => 'nullable|string|max:100',
            'mata_pelajaran' => 'nullable|string|max:255',
            'nilai' => 'nullable|numeric|min:0|max:100',
            'keterangan' => 'nullable|string',
            'tanggal_penilaian' => 'required|date',
        ]);

        $penilaian->update($request->only([
            'nama_siswa', 'kelas', 'mata_pelajaran', 'nilai', 'keterangan', 'tanggal_penilaian',
        ]));

        return redirect()->back()->with('success', 'Penilaian berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = auth()->user();

        if (in_array($user->role, ['HR', 'MANAGER'])) {
            $penilaian = Penilaian::findOrFail($id);
        } else {
            $penilaian = Penilaian::where('user_id', $user->id)->findOrFail($id);
        }

        $penilaian->delete();

        return redirect()->back()->with('success', 'Penilaian berhasil dihapus.');
    }
}
