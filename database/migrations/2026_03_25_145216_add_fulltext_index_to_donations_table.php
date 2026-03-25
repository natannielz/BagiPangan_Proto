<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // FULLTEXT indexes require MySQL/MariaDB InnoDB (MySQL 5.6+).
        // This migration is skipped gracefully on SQLite (dev/test).
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('donations', function (Blueprint $table) {
            $table->fullText(['title', 'location_district', 'description'], 'donations_fulltext');
        });
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('donations', function (Blueprint $table) {
            $table->dropFullText('donations_fulltext');
        });
    }
};
