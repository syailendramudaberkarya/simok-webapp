<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kartu_anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggota')->cascadeOnDelete();
            $table->string('nomor_anggota', 30);
            $table->string('qr_code_url')->nullable();
            $table->string('pdf_path')->nullable();
            $table->foreignId('template_id')->nullable()->constrained('kartu_templates')->nullOnDelete();
            $table->date('berlaku_hingga')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kartu_anggota');
    }
};
