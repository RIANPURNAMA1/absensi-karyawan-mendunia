<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agendas', function (Blueprint $table) {
            $table->time('jam_absen_masuk')->nullable()->after('jam_selesai');
            $table->time('jam_absen_keluar')->nullable()->after('jam_absen_masuk');
            $table->enum('status_absen', ['terjadwal', 'hadir', 'selesai'])->default('terjadwal')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('agendas', function (Blueprint $table) {
            $table->dropColumn(['jam_absen_masuk', 'jam_absen_keluar', 'status_absen']);
        });
    }
};
