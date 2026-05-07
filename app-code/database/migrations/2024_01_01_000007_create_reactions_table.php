<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type'); // ReactionType: like, love, laugh, sad, angry, etc.
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('content_id')->constrained('contents')->cascadeOnDelete();
            $table->timestamps();

            // A user can only react once per content
            $table->unique(['user_id', 'content_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};
