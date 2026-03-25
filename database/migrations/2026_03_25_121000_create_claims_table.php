<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('claimer_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('claimed_at')->useCurrent();
            $table->string('proof_photo_path')->nullable();
            $table->timestamp('proof_uploaded_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verifier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['claimed', 'awaiting_confirmation', 'completed'])->default('claimed');
            $table->timestamps();

            $table->unique(['donation_id', 'claimer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};

