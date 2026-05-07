<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filter_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('rule'); // The pattern/keyword to filter
            $table->boolean('for_label_moderation')->default(false); // forLabelModerationStatus from ERD
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filter_rules');
    }
};
