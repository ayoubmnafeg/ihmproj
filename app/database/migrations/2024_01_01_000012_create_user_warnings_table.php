<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_warnings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('message');
            $table->string('severity'); // WarningSeverity: low, medium, high
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('issued_by')->nullable()->constrained('users')->nullOnDelete(); // Admin who issued it
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_warnings');
    }
};
