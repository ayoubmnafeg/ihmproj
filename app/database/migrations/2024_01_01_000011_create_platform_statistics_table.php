<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_statistics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('total_publications')->default(0);
            $table->unsignedBigInteger('total_reports')->default(0);
            $table->unsignedBigInteger('total_responses')->default(0); // comments/replies
            $table->timestamp('recorded_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_statistics');
    }
};
