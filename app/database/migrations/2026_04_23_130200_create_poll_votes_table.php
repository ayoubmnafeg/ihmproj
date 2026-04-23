<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poll_votes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('poll_id')->constrained('polls')->cascadeOnDelete();
            $table->foreignUuid('poll_option_id')->constrained('poll_options')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['poll_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poll_votes');
    }
};
