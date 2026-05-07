<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publications', function (Blueprint $table) {
            $table->foreignUuid('id')->primary()->constrained('contents')->cascadeOnDelete();
            $table->string('title');
            $table->text('text');
            $table->string('media_type')->nullable(); // MediaType: image, video, audio, none
            $table->foreignUuid('category_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publications');
    }
};
