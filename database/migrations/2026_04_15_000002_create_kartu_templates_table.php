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
        Schema::create('kartu_templates', function (Blueprint $table) {
            $table->id();
            $table->string('nama_template');
            $table->string('warna_utama', 7)->default('#1E40AF');
            $table->string('warna_sekunder', 7)->default('#3B82F6');
            $table->string('logo_path')->nullable();
            $table->boolean('is_active')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kartu_templates');
    }
};
