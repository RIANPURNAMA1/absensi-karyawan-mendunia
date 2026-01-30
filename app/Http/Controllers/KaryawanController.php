<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Divisi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KaryawanController extends Controller
{
    public function index()
    {
        // 1. Tambahkan 'shift' ke dalam with() agar data shift karyawan muncul di tabel
        $karyawan = User::with(['divisi', 'shift', 'cabang'])->where('role', 'KARYAWAN')->get();

        // 2. Ambil data divisi untuk dropdown modal
        $divisi = Divisi::orderBy('nama_divisi')->get();
        $cabang = Cabang::orderBy('nama_cabang')->get();

        // 3. TAMBAHKAN INI: Ambil data shift untuk dropdown modal (tambah & edit)
        $shifts = \App\Models\Shift::where('status', 'AKTIF')->get();

        // 4. Kirim $shifts ke dalam compact()
        return view('karyawan.index', compact('karyawan', 'divisi', 'cabang', 'shifts'));
    }

    public function store(Request $request)
    {
        // Ambil tahun masuk dari input
        $tahunMasuk = \Carbon\Carbon::parse($request->tanggal_masuk)->format('Y');

        // Cari NIP terakhir di tahun ini
        $lastNip = User::whereYear('tanggal_masuk', $tahunMasuk)
            ->orderBy('nip', 'desc')
            ->first();

        $urutan = $lastNip ? (int)substr($lastNip->nip, -4) + 1 : 1;

        // Buat NIP baru: YYYYXXXX
        $nipBaru = $tahunMasuk . str_pad($urutan, 4, '0', STR_PAD_LEFT);

        // Merge NIP ke request
        $request->merge(['nip' => $nipBaru]);

        // Validasi request
        $request->validate([
            'nip'                => 'required|string|max:50|unique:users,nip',
            'name'               => 'required|string|max:100',
            'email'              => 'required|email|unique:users,email',
            'jabatan'            => 'required|string|max:100',
            'divisi_id'          => 'required|exists:divisis,id',
            'cabang_id'          => 'required|exists:cabangs,id',
            'shift_id'           => 'required|exists:shifts,id',
            'no_hp'              => 'required|string|max:20',
            'alamat'             => 'nullable|string',
            'tanggal_masuk'      => 'required|date',
            'status_kerja'       => 'required|in:TETAP,KONTRAK,MAGANG',
            'foto_profil'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_ktp'         => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'foto_ijazah'      => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'foto_kk'          => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'cv_file'          => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'sertifikat_file'  => 'nullable|file|mimes:pdf,doc,docx|max:5120',

            'tempat_lahir'       => 'nullable|string|max:100',
            'tanggal_lahir'      => 'nullable|date',
            'jenis_kelamin'      => 'nullable|in:L,P',
            'agama'              => 'nullable|in:ISLAM,KRISTEN,KATOLIK,HINDU,BUDDHA,KONGHUCU',
            'status_pernikahan'  => 'nullable|in:BELUM_MENIKAH,MENIKAH,CERAI',
        ]);

        // Upload semua file jika ada
        $fileFields = [
            'foto_profil',
            'foto_ktp',
            'foto_ijazah',
            'foto_kk',
            'cv_file',
            'sertifikat_file'
        ];
        $uploadedFiles = [];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $namaFile = time() . '_' . $file->getClientOriginalName();
                $tujuanFolder = public_path('uploads/' . $field);
                if (!file_exists($tujuanFolder)) mkdir($tujuanFolder, 0755, true);
                $file->move($tujuanFolder, $namaFile);
                $uploadedFiles[$field] = $namaFile;
            } else {
                $uploadedFiles[$field] = null;
            }
        }

        // Simpan user karyawan
        $user = User::create(array_merge([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make('12345678'),
            'role'          => 'KARYAWAN',
            'status'        => 'AKTIF',
            'divisi_id'     => $request->divisi_id,
            'cabang_id'     => $request->cabang_id,
            'shift_id'      => $request->shift_id,
            'nip'           => $nipBaru,
            'jabatan'       => $request->jabatan,
            'no_hp'         => $request->no_hp,
            'alamat'        => $request->alamat,
            'tanggal_masuk' => $request->tanggal_masuk,
            'status_kerja'  => $request->status_kerja,
            'tempat_lahir'  => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'agama'         => $request->agama,
            'status_pernikahan' => $request->status_pernikahan,
        ], $uploadedFiles));

        return response()->json([
            'status'  => true,
            'message' => 'Karyawan berhasil dibuat',
            'akun'    => [
                'email'    => $request->email,
                'password' => '12345678',
                'nip'      => $nipBaru
            ],
            'data' => $user
        ], 201);
    }



    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validasi request, termasuk semua field opsional
        $request->validate([
            'nip'                => 'required|unique:users,nip,' . $user->id,
            'name'               => 'required|string|max:100',
            'email'              => 'required|email|unique:users,email,' . $user->id,
            'jabatan'            => 'required|string|max:100',
            'divisi_id'          => 'required|exists:divisis,id',
            'cabang_id'          => 'required|exists:cabangs,id',
            'shift_id'           => 'required|exists:shifts,id',
            'no_hp'              => 'required|string|max:20',
            'alamat'             => 'nullable|string',
            'tanggal_masuk'      => 'required|date',
            'status_kerja'       => 'required|in:TETAP,KONTRAK,MAGANG',
            'foto_profil'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_ktp'         => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'foto_ijazah'      => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'foto_kk'          => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'cv_file'          => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'sertifikat_file'  => 'nullable|file|mimes:pdf,doc,docx|max:5120',

            'tempat_lahir'       => 'nullable|string|max:100',
            'tanggal_lahir'      => 'nullable|date',
            'jenis_kelamin'      => 'nullable|in:L,P',
            'agama'              => 'nullable|in:ISLAM,KRISTEN,KATOLIK,HINDU,BUDDHA,KONGHUCU',
            'status_pernikahan'  => 'nullable|in:BELUM_MENIKAH,MENIKAH,CERAI',
        ]);

        // Array field file
        $fileFields = [
            'foto_profil',
            'foto_ktp',
            'foto_ijazah',
            'foto_kk',
            'cv_file',
            'sertifikat_file'
        ];
        $uploadedFiles = [];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $namaFile = time() . '_' . $file->getClientOriginalName();
                $tujuanFolder = public_path('uploads/' . $field);

                // Pastikan folder ada
                if (!file_exists($tujuanFolder)) {
                    mkdir($tujuanFolder, 0755, true);
                }

                // Hapus file lama jika ada
                if ($user->$field && file_exists($tujuanFolder . '/' . $user->$field)) {
                    unlink($tujuanFolder . '/' . $user->$field);
                }

                // Pindahkan file baru
                $file->move($tujuanFolder, $namaFile);
                $uploadedFiles[$field] = $namaFile;
            } else {
                $uploadedFiles[$field] = $user->$field; // tetap pakai file lama jika tidak diupload
            }
        }

        // Update user
        $user->update(array_merge([
            'nip'           => $request->nip,
            'name'          => $request->name,
            'email'         => $request->email,
            'jabatan'       => $request->jabatan,
            'divisi_id'     => $request->divisi_id,
            'cabang_id'     => $request->cabang_id,
            'shift_id'      => $request->shift_id,
            'no_hp'         => $request->no_hp,
            'alamat'        => $request->alamat,
            'tanggal_masuk' => $request->tanggal_masuk,
            'status_kerja'  => $request->status_kerja,
            'tempat_lahir'  => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'agama'         => $request->agama,
            'status_pernikahan' => $request->status_pernikahan,
        ], $uploadedFiles));

        return response()->json([
            'status'  => 'success',
            'message' => 'Data karyawan berhasil diperbarui',
            'data'    => $user
        ]);
    }


    public function show($id)
    {
        $karyawan = User::with('divisi')->findOrFail($id);
        return view('karyawan.detail', compact('karyawan'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (in_array(strtoupper($user->role), ['MANAGER', 'HR'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'User dengan role MANAGER atau HR tidak bisa dihapus!'
            ], 403);
        }

        if ($user->foto_profil && file_exists(public_path('foto-karyawan/' . $user->foto_profil))) {
            unlink(public_path('foto-karyawan/' . $user->foto_profil));
        }

        $user->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data karyawan berhasil dihapus'
        ]);
    }
}
