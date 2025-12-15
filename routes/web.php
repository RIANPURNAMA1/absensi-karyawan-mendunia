<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;



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
    Route::put('/karyawan/{karyawan}', [KaryawanController::class, 'update'])->name('karyawan.update');
    Route::delete('/karyawan/{karyawan}', [KaryawanController::class, 'destroy'])->name('karyawan.destroy');
});
