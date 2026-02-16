<?php

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
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\LemburController;
use App\Http\Controllers\ProfileController;




Route::get('/keep-alive', function () {
    return response()->json(['status' => 'active']);
})->middleware('auth');
/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/


// Tampilkan form lupa password / ubah password
Route::get('/forgot-password', [AuthController::class, 'show'])->name('password.request');

// Proses ubah password langsung (submit email + password baru)
Route::post('/forgot-password', [AuthController::class, 'reset'])->name('password.update');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login/process', [AuthController::class, 'login'])->name('login.process');
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:MANAGER,HR'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'role:HR'])->group(function () {
    Route::get('/hr/dashboard', fn() => view('dashboard'))->name('hr.dashboard');
});

Route::middleware(['auth', 'role:MANAGER'])->group(function () {
    Route::get('/manager/dashboard', fn() => view('dashboard'))->name('manager.dashboard');
});

Route::middleware(['auth', 'role:KARYAWAN'])->group(function () {
    Route::get('/karyawan/dashboard', fn() => view('karyawan.dashboard'))->name('karyawan.dashboard');
});

/*
|--------------------------------------------------------------------------
| HR & Manager Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:HR,MANAGER'])->group(function () {

    // Karyawan Management
    Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan.index');
    Route::post('/karyawan', [KaryawanController::class, 'store'])->name('karyawan.store');
    Route::get('/karyawan/{id}', [KaryawanController::class, 'show'])->name('karyawan.show');
    Route::put('/karyawan/{id}', [KaryawanController::class, 'update'])->name('karyawan.update');
    Route::delete('/karyawan/{id}', [KaryawanController::class, 'destroy'])->name('karyawan.destroy');

    // Divisi Management
    Route::resource('divisi', DivisiController::class)->only(['index', 'store', 'update', 'destroy']);

    // Shift Management
    Route::resource('shift', ShiftController::class);

    // Cabang Management
    Route::get('/cabang', [CabangController::class, 'index'])->name('cabang.index');
    Route::post('/cabang', [CabangController::class, 'store'])->name('cabang.store');
    Route::put('/cabang/{id}', [CabangController::class, 'update'])->name('cabang.update');
    Route::delete('/cabang/{id}', [CabangController::class, 'destroy'])->name('cabang.destroy');

    // Kehadiran & Monitoring
    Route::get('/data-kehadiran', [KehadiranController::class, 'index']);
    Route::post('/admin/absensi/update-status', [AbsensiController::class, 'updateStatus'])->name('admin.absensi.updateStatus');
    Route::get('/rekap-absensi', [RekapController::class, 'rekap'])->name('absensi.rekap');
    Route::get('/monitoring-lokasi', [MonitoringController::class, 'monitoring'])->name('absensi.monitoring');

    // User Management
    Route::get('/daftar-user', [UserController::class, 'userKaryawan'])->name('user.karyawan');
    Route::prefix('/daftar-user')->group(function () {
        Route::post('/store', [UserController::class, 'store'])->name('user.karyawan.store');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('user.karyawan.edit');
        Route::post('/update/{id}', [UserController::class, 'update'])->name('user.karyawan.update');
        Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('user.karyawan.delete');
    });

    // Pengaturan
    Route::get('/pengaturan', [UserController::class, 'index']);
    Route::post('/pengaturan', [UserController::class, 'store'])->name('users.store');
    Route::put('/pengaturan/{id}', [UserController::class, 'update'])->name('pengaturan.update');


    // lembur
    Route::get('/approval-lembur', [LemburController::class, 'approvalIndex'])->name('lembur.approval');
    Route::post('/approval-lembur/{id}/status', [LemburController::class, 'updateStatus'])->name('lembur.status');
});

/*
|--------------------------------------------------------------------------
| Izin/Cuti Routes (All Roles)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:HR,MANAGER,KARYAWAN'])->group(function () {
    Route::get('/izin-cuti', [IzinController::class, 'approvalList'])->name('izin.approval.list');
    Route::post('/izin/{id}/approve', [IzinController::class, 'approve'])->name('izin.approve');
    Route::post('/izin/{id}/reject', [IzinController::class, 'reject'])->name('izin.reject');
    Route::get('/lampiran/{filename}', [IzinController::class, 'lihatLampiran'])->name('izin.lampiran');
});

/*
|--------------------------------------------------------------------------
| Karyawan Routes
|--------------------------------------------------------------------------
*/


Route::get('/test-akses', function() {
    return "Akses Berhasil!";
});
Route::middleware(['auth', 'role:KARYAWAN'])->group(function () {
    Route::get('/absensi/karyawan', [AbsensiController::class, 'index'])->name('absensi.index');

    // absensi foto
    Route::post('/absensi/foto/proses', [AbsensiController::class, 'absenFoto'])->middleware('auth');

    // Absensi
    Route::get('/absensi/mobile', fn() => view('absensi.mobile'))->name('absensi.mobile');
    Route::post('/absen/masuk', [AbsensiController::class, 'absenMasuk'])->name('absen.masuk');
    Route::post('/absensi/masuk', [AbsensiController::class, 'absenMasuk']);
    Route::post('/absensi/pulang', [AbsensiController::class, 'absenPulang'])->name('absen.pulang');
    Route::post('/absensi/manual', [AbsensiController::class, 'manual']);
    Route::post('/absensi/status', [AbsensiController::class, 'statusAbsensi'])->name('statusAbsensi');
    Route::post('/absensi/deteksi', [AbsensiController::class, 'deteksiWajah'])->name('absensi.deteksi');

    // Riwayat Absensi
    Route::get('/absensi/history', [AbsensiController::class, 'history'])->name('absensi.history');
    Route::get('/absensi/riwayat', [AbsensiController::class, 'riwayat'])->name('absensi.riwayat');
    Route::get('/absensi/riwayat-terbaru', [AbsensiController::class, 'riwayatTerbaru']);
    Route::get('/absensi/riwayat-json', [AbsensiController::class, 'riwayatJson'])->name('absensi.riwayat.json');
    Route::get('/absensi/detail/{tanggal}', [AbsensiController::class, 'detail'])->name('absensi.detail');

    // Profile
    Route::get('/absensi/profile', [AbsensiController::class, 'profile'])->name('absensi.profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/password', [ProfileController::class, 'changePassword'])->name('password.change');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update');

    // Izin/Cuti
    Route::get('/izin/create', [IzinController::class, 'create'])->name('izin.create');
    Route::post('/izin/store', [IzinController::class, 'store'])->name('izin.store');
    Route::get('/absensi/izin', [IzinController::class, 'index'])->name('izin.index');

    // Calendar
    Route::get('/calendar', [CalendarController::class, 'index']);
    Route::get('/absensi/riwayat-kalender', [CalendarController::class, 'getRiwayatKalender'])->name('riwayatKalender');



    // lembur
    Route::get('/absensi/lembur', [LemburController::class, 'index'])->name('lembur.index');
    Route::post('/absensi/lembur/store', [LemburController::class, 'store'])->name('absensi.lembur.store');
});


/*
|--------------------------------------------------------------------------
| General Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/user/update-face', [AbsensiController::class, 'updateFace'])->name('user.update-face');
});


// hari libur
use App\Http\Controllers\HariLiburController;

Route::get('/hari-libur', [HariLiburController::class, 'index'])->name('hari-libur.index');
Route::post('/hari-libur', [HariLiburController::class, 'store'])->name('hari-libur.store');
Route::delete('/hari-libur/{id}', [HariLiburController::class, 'destroy'])->name('hari-libur.destroy');


use App\Http\Controllers\ReportController;
use App\Http\Controllers\TaskController;

Route::middleware(['auth'])->group(function () {
    // View Utama Report
    Route::get('/report', [ReportController::class, 'index'])->name('report.index');

    // API untuk data kalender (jika diperlukan oleh script kalender)
    Route::get('/api/report/calendar', [ReportController::class, 'getCalendarData'])->name('report.calendar');
});






// task management
Route::get('/project/dashboard', [TaskController::class, 'index']);
