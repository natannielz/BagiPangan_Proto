<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->enum('moderation_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
            $table->index(['moderation_status', 'created_at']);
        });

        DB::table('donations')->update(['moderation_status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropIndex(['moderation_status', 'created_at']);
            $table->dropColumn('moderation_status');
        });
    }
};

