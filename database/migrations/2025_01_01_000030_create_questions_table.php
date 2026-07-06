<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['single_choice', 'multi_choice', 'rating', 'text', 'yes_no'])->default('single_choice');
            $table->json('text'); // {"en": "...", "ar": "..."}
            $table->json('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // image_path, layout, etc.
            $table->timestamps();

            $table->index(['survey_id', 'sort_order']);
            $table->index(['survey_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
