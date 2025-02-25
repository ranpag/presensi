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
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->nullable()->constrained('siswa')->nullOnDelete()->cascadeOnUpdate();
            $table->date('tanggal');
            $table->enum('kehadiran', ['Hadir', 'Sakit', 'Izin', 'Alfa'])->nullable();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('jadwal_id')->nullable()->constrained('jadwal_kbm')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('mapel_id')->nullable()->constrained('mapel')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
