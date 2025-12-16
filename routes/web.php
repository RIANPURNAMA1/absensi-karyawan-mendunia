<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisiController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');


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
Route::middleware(['auth'])->group(function () {
    Route::resource('divisi', DivisiController::class)
        ->only(['index', 'store', 'update', 'destroy']);
});


// absensi
use App\Http\Controllers\AbsensiController;

Route::middleware('auth')->group(function() {
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi/masuk', [AbsensiController::class, 'absenMasuk'])->name('absensi.masuk');
    Route::post('/absensi/pulang', [AbsensiController::class, 'absenPulang'])->name('absensi.pulang');
    Route::get('/absensi/history', [AbsensiController::class, 'history'])->name('absensi.history');
});
