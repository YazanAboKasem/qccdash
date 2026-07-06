<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->json('title'); // {"en": "...", "ar": "..."}
            $table->json('description')->nullable();
            $table->enum('status', ['draft', 'active', 'inactive'])->default('draft');
            $table->unsignedInteger('version')->default(1);
            $table->json('settings')->nullable(); // timeout, theme colors, welcome message, etc.
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index(['campaign_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
