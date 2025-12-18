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

            // Relasi user
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Tanggal absensi (1 user = 1 record per hari)
            $table->date('tanggal');

            // Waktu absensi
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();

            // Status absensi
            $table->enum('status', [
                'HADIR',
                'TERLAMBAT',
                'IZIN',
                'ALPA',
                'PULANG LEBIH AWAL'
            ])->default('HADIR');

            // Foto dari kamera
            $table->string('foto_masuk')->nullable();
            $table->string('foto_pulang')->nullable();

            // Catatan tambahan
            $table->text('keterangan')->nullable();

            // Audit
            $table->timestamps();

            // 🔐 Constraint: 1 user hanya boleh 1 absensi per hari
            $table->unique(['user_id', 'tanggal']);
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
