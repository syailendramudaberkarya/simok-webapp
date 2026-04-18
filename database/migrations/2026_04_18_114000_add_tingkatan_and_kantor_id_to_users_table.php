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
            $table->enum('tingkatan', ['DPN', 'DPD', 'DPC', 'PR', 'PAR'])->default('DPN')->after('role');
            $table->foreignId('kantor_id')->nullable()->after('tingkatan')->constrained('kantor')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['kantor_id']);
            $table->dropColumn(['tingkatan', 'kantor_id']);
        });
    }
};
