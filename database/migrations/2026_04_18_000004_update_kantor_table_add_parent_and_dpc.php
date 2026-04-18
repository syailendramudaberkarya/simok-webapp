<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add parent_id to kantor
        Schema::table('kantor', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->constrained('kantor')->onDelete('set null');
        });

        // Update the enum 'jenjang' to include 'DPC'
        // Since sqlite doesn't easily support enum changes, or mysql uses native enum,
        // it's safest to just alter it via raw statement in MySQL if it's mysql.
        // Assuming Laravel 11/MySQL:
        DB::statement("ALTER TABLE kantor MODIFY COLUMN jenjang ENUM('DPN', 'DPD', 'DPC', 'PR', 'PAR') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kantor', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
        DB::statement("ALTER TABLE kantor MODIFY COLUMN jenjang ENUM('DPN', 'DPD', 'PR', 'PAR') NOT NULL");
    }
};
