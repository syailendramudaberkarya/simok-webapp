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
        Schema::create('propinsi', function (Blueprint $table) {
            $table->char('id', 2)->primary();
            $table->string('code', 10)->unique();
            $table->string('propinsi', 100);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('island', 50)->nullable();
            $table->string('timezone', 20)->default('WIB');
            $table->unsignedInteger('total_members')->default(0);
            $table->unsignedInteger('total_cities')->default(0);
            $table->timestamps();
        });

        Schema::create('kabupaten', function (Blueprint $table) {
            $table->char('id', 5)->primary();
            $table->char('idpropinsi', 2);
            $table->string('kabupaten', 80);
            $table->char('idkabsatusehat', 6)->nullable();
            $table->string('code', 10)->nullable();
            
            $table->foreign('idpropinsi')->references('id')->on('propinsi')->onDelete('cascade');
        });

        Schema::create('kecamatan', function (Blueprint $table) {
            $table->char('id', 7)->primary(); // Using 7 based on standard ID lengths if needed, but keeping it flexible
            $table->char('idkabupaten', 5);
            $table->string('kecamatan', 80);
            $table->char('idkecsatusehat', 8)->nullable();
            $table->string('code', 10)->nullable();
            
            $table->foreign('idkabupaten')->references('id')->on('kabupaten')->onDelete('cascade');
        });

        Schema::create('kelurahan', function (Blueprint $table) {
            $table->char('id', 13)->primary(); // Standard kelurahan ID is ~10-13 chars
            $table->char('idkecamatan', 7);
            $table->char('idkecsatusehat', 6)->default('0');
            $table->char('kodebps', 12)->nullable();
            $table->string('kelurahan', 80);
            $table->string('code', 16)->nullable();
            
            $table->foreign('idkecamatan')->references('id')->on('kecamatan')->onDelete('cascade');
        });

        // Add foreign keys to anggota table to support hierarchical area selection
        Schema::table('anggota', function (Blueprint $table) {
            $table->char('idpropinsi', 2)->nullable()->after('alamat');
            $table->char('idkabupaten', 5)->nullable()->after('idpropinsi');
            $table->char('idkecamatan', 7)->nullable()->after('idkabupaten');
            $table->char('idkelurahan', 13)->nullable()->after('idkecamatan');
            
            // Also need to increase length of nomor_anggota for the new format
            $table->string('nomor_anggota', 30)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->dropColumn(['idpropinsi', 'idkabupaten', 'idkecamatan', 'idkelurahan']);
        });
        
        Schema::dropIfExists('kelurahan');
        Schema::dropIfExists('kecamatan');
        Schema::dropIfExists('kabupaten');
        Schema::dropIfExists('propinsi');
    }
};
