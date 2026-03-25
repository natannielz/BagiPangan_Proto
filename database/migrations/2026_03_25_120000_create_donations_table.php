<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('qty_portions');
            $table->string('location_district');
            $table->dateTime('expiry_at');
            $table->string('photo_path')->nullable();
            $table->enum('status', ['available', 'claimed', 'picked_up', 'completed', 'cancelled'])->default('available');
            $table->timestamps();

            $table->index(['status', 'expiry_at']);
            $table->index(['donor_id']);
            $table->index(['category_id']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};

