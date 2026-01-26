<?php

use App\Http\Controllers\AbsensiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KehadiranController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\RekapController;

Route::middleware(['auth', 'role:MANAGER, HR'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');
});


Route::middleware('guest')->group(
    function () {
        // authenticate
        Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
        Route::post('/login/process', [AuthController::class, 'login'])->name('login.process');
        Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register']);
    }
);


Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::middleware(['auth', 'role:HR'])->group(function () {
    Route::get('/hr/dashboard', fn() => view('dashboard'))->name('hr.dashboard');
});

Route::middleware(['auth', 'role:MANAGER'])->group(function () {
    Route::get('/manager/dashboard', fn() => view('dashboard'))->name('manager.dashboard');
});

Route::middleware(['auth', 'role:KARYAWAN'])->group(function () {
    Route::get('/karyawan/dashboard', fn() => view('karyawan.dashboard'))->name('karyawan.dashboard');
});


// karyawan data

Route::middleware(['auth', 'role:HR,MANAGER'])->group(function () {
    Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan.index');
    Route::post('/karyawan', [KaryawanController::class, 'store'])->name('karyawan.store');
    Route::put('/karyawan/{id}', [KaryawanController::class, 'update'])->name('karyawan.update');
    Route::delete('/karyawan/{id}', [KaryawanController::class, 'destroy'])
        ->name('karyawan.destroy');
    Route::get('/karyawan/{id}', [KaryawanController::class, 'show'])
        ->name('karyawan.show');
});


// divisi
Route::middleware(['auth', 'role:HR,MANAGER'])->group(function () {
    Route::resource('divisi', DivisiController::class)
        ->only(['index', 'store', 'update', 'destroy']);
});


// admin approval izin
Route::middleware(['auth','role:HR,MANAGER,KARYAWAN'])->group(function () {
    Route::get('/izin-cuti', [IzinController::class, 'approvalList'])->name('izin.approval.list');
    Route::post('/izin/{id}/approve', [IzinController::class, 'approve'])->name('izin.approve');
    Route::post('/izin/{id}/reject', [IzinController::class, 'reject'])->name('izin.reject');
});

Route::get('/lampiran/{filename}', [IzinController::class, 'lihatLampiran'])
    ->name('izin.lampiran')
    ->middleware('auth'); // cukup login saja

// kehadiran karyawan 
Route::get('/data-kehadiran', [KehadiranController::class, 'index']);
// revisi kehadiran
Route::post('/admin/absensi/update-status', [AbsensiController::class, 'updateStatus'])
    ->name('admin.absensi.updateStatus');

// rekap
Route::get('/rekap-absensi', [RekapController::class, 'rekap'])->name('absensi.rekap');

// monitoring absensi
Route::get('/monitoring-lokasi', [MonitoringController::class, 'monitoring'])->name('absensi.monitoring');


// daftar wajah
Route::post('/user/update-face', [AbsensiController::class, 'updateFace'])->name('user.update-face');


// daftar user
Route::get('/daftar-user', [UserController::class, 'userKaryawan'])->name('user.karyawan');
Route::prefix('/daftar-user')->group(function () {
    Route::post('/store', [App\Http\Controllers\UserController::class, 'store'])->name('user.karyawan.store');
    Route::get('/edit/{id}', [App\Http\Controllers\UserController::class, 'edit'])->name('user.karyawan.edit');
    Route::post('/update/{id}', [App\Http\Controllers\UserController::class, 'update'])->name('user.karyawan.update');
    Route::delete('/delete/{id}', [App\Http\Controllers\UserController::class, 'destroy'])->name('user.karyawan.delete');
});


// absensi
Route::middleware(['auth', 'role:KARYAWAN'])->group(function () {
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/absensi/history', [AbsensiController::class, 'history'])->name('absensi.history');
    Route::get('/absensi/profile', [AbsensiController::class, 'profile'])->name('absensi.profile');
    Route::post('/absensi/manual', [AbsensiController::class, 'manual'])
        ->middleware('auth');
});

// routes/web.php
Route::middleware(['auth'])->group(function () {
    Route::post('/absen/masuk', [AbsensiController::class, 'absenMasuk'])->name('absen.masuk');
    Route::post('/absen/pulang', [AbsensiController::class, 'absenPulang'])->name('absen.pulang');
});


Route::middleware('auth')->group(function () {
    Route::get('/absensi/mobile', fn() => view('absensi.mobile'))->name('absensi.mobile');

    Route::get('/absensi/history', [AbsensiController::class, 'history'])
        ->name('absensi.history');

    Route::post('/absensi/deteksi', [AbsensiController::class, 'deteksiWajah'])
        ->name('absensi.deteksi');
});


// Halaman riwayat semua absensi user login
Route::get('/absensi/riwayat', [AbsensiController::class, 'riwayat'])->name('absensi.riwayat')->middleware('auth');


// Halaman detail absensi per tanggal
Route::get('/absensi/detail/{tanggal}', [AbsensiController::class, 'detail'])
    ->name('absensi.detail');

Route::get('/absensi/riwayat-json', [AbsensiController::class, 'riwayatJson'])
    ->name('absensi.riwayat.json');



// Shift Routes
Route::middleware(['auth'])->group(function () {
    Route::resource('shift', ShiftController::class);
    
    // atau jika ingin manual:
    Route::get('/shift', [ShiftController::class, 'index'])->name('shift.index');
    Route::post('/shift', [ShiftController::class, 'store'])->name('shift.store');
    Route::get('/shift/{id}', [ShiftController::class, 'show'])->name('shift.show');
    Route::put('/shift/{id}', [ShiftController::class, 'update'])->name('shift.update');
    Route::delete('/shift/{id}', [ShiftController::class, 'destroy'])->name('shift.destroy');
});



// izin
// Pengajuan Izin
Route::get('/izin/create', [IzinController::class, 'create'])->name('izin.create')->middleware('auth');
Route::post('/izin/store', [IzinController::class, 'store'])->name('izin.store');
Route::get('/izin', [IzinController::class, 'index'])->name('izin.index');


use App\Http\Controllers\CabangController;

// Halaman Utama Daftar Cabang
Route::get('/cabang', [CabangController::class, 'index'])->name('cabang.index');

// Proses Simpan Cabang Baru
Route::post('/cabang', [CabangController::class, 'store'])->name('cabang.store');

Route::put('/cabang/{id}', [CabangController::class, 'update'])->name('cabang.update');

// Proses Hapus Cabang
Route::delete('/cabang/{id}', [CabangController::class, 'destroy'])->name('cabang.destroy');



