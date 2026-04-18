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
        Schema::create('kantor', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kantor');
            $table->enum('jenjang', ['DPN', 'DPD', 'PR', 'PAR'])->index();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->string('kode_pos', 10)->nullable();
            
            // Regional references
            $table->string('idpropinsi', 2)->nullable()->index();
            $table->string('idkabupaten', 4)->nullable()->index();
            $table->string('idkecamatan', 7)->nullable()->index();
            $table->string('idkelurahan', 10)->nullable()->index();
            
            // Textual regional names for easier display
            $table->string('provinsi')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kelurahan')->nullable();

            // Coordinates
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            $table->string('status')->default('Aktif')->index(); // Aktif, Non-Aktif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kantor');
    }
};
