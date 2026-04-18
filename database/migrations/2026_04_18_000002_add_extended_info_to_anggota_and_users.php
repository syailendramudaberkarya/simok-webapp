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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
        });

        Schema::table('anggota', function (Blueprint $table) {
            $table->string('status_perkawinan')->nullable()->after('agama');
            $table->string('pekerjaan')->nullable()->after('status_perkawinan');
            $table->string('kewarganegaraan')->default('WNI')->after('pekerjaan');
            $table->string('golongan_darah', 5)->nullable()->after('kewarganegaraan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });

        Schema::table('anggota', function (Blueprint $table) {
            $table->dropColumn(['status_perkawinan', 'pekerjaan', 'kewarganegaraan', 'golongan_darah']);
        });
    }
};
