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
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->fullText('nama_fulltext');
            $table->enum('gender', ['L', 'P']);
            $table->string('nis', 20)->unique();
            $table->string('no_telp', 16);
            $table->string('walimurid', 100);
            $table->string('alamat', 100);
            $table->foreignId('kelas_id')->nullable()->constrained('kelas', 'id')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
