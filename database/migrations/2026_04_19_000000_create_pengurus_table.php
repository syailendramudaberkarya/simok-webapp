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
        Schema::create('pengurus', function (Blueprint $table) {
            $table->id();
            $table->string('noanggota')->nullable(); // Can be linked to anggota.nomor_anggota
            $table->foreignId('anggota_id')->nullable()->constrained('anggota')->nullOnDelete();
            $table->string('nama')->nullable();
            $table->string('kategorijabatan')->nullable();
            $table->string('subkategorijabatan')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('kantor')->nullable(); // e.g DPP Jaga Pangan Nusantara
            $table->foreignId('kantor_id')->nullable()->constrained('kantor')->nullOnDelete(); // Optional FK if linked to kantor table directly
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengurus');
    }
};
