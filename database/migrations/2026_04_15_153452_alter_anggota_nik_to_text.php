<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * NIK column must be long enough to store Laravel's encrypted values.
     */
    public function up(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->dropIndex(['nik']);
            $table->text('nik')->change();
            // Re-add a length-limited index
            $table->index([DB::raw('nik(255)')], 'anggota_nik_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->dropIndex('anggota_nik_index');
            $table->string('nik', 16)->change();
            $table->index('nik');
        });
    }
};
