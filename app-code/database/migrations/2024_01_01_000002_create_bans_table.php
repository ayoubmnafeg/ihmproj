<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type'); // BanType: user, ip, device
            $table->string('value'); // The banned value (user_id, IP address, device id)
            $table->text('reason');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable(); // null = permanent
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('banned_by')->nullable()->constrained('users')->nullOnDelete(); // Admin who issued it
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bans');
    }
};
