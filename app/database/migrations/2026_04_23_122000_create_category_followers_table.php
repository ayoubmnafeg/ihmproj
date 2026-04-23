<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_followers', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['category_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_followers');
    }
};

