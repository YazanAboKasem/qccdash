<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('device_identifier')->unique(); // hardware ID
            $table->string('api_token', 80)->unique()->nullable();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->dateTime('last_sync_at')->nullable();
            $table->json('device_info')->nullable(); // model, OS version, app version, screen size
            $table->json('settings')->nullable(); // per-device overrides
            $table->timestamps();

            $table->index('status');
            $table->index('api_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
