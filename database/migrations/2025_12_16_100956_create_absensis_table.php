<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('karyawan_id'); // relasi ke karyawan
            $table->date('tanggal');                  // tanggal absensi
            $table->time('jam_masuk')->nullable();   // jam masuk karyawan
            $table->time('jam_keluar')->nullable();  // jam keluar karyawan
            $table->enum('status', ['HADIR', 'TERLAMBAT', 'IZIN', 'ALPA'])->default('HADIR'); // status
            $table->text('keterangan')->nullable();  // tambahan catatan

            $table->timestamps();

            $table->foreign('karyawan_id')->references('id')->on('karyawan')->onDelete('cascade');
            $table->unique(['karyawan_id', 'tanggal']); // supaya tidak double absensi per hari
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
