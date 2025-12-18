<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\UserController;


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
use App\Http\Controllers\KaryawanController;

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


// daftar user
Route::get('/daftar-user', [UserController::class, 'userKaryawan'])->name('user.karyawan');
Route::prefix('/daftar-user')->group(function () {
    Route::post('/store', [App\Http\Controllers\UserController::class, 'store'])->name('user.karyawan.store');
    Route::get('/edit/{id}', [App\Http\Controllers\UserController::class, 'edit'])->name('user.karyawan.edit');
    Route::post('/update/{id}', [App\Http\Controllers\UserController::class, 'update'])->name('user.karyawan.update');
    Route::delete('/delete/{id}', [App\Http\Controllers\UserController::class, 'destroy'])->name('user.karyawan.delete');
});


// absensi
use App\Http\Controllers\AbsensiController;


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
Route::get('/absensi/riwayat', [AbsensiController::class, 'riwayat'])->name('absensi.riwayat');

// Halaman detail absensi per tanggal
Route::get('/absensi/detail/{tanggal}', [AbsensiController::class, 'detail'])
    ->name('absensi.detail');

Route::get('/absensi/riwayat-json', [AbsensiController::class, 'riwayatJson'])
    ->name('absensi.riwayat.json');
