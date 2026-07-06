<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('answer_options', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->json('label'); // {"en": "...", "ar": "..."}
            $table->string('value'); // machine-readable value
            $table->string('icon')->nullable(); // emoji or icon name
            $table->string('color')->nullable(); // hex color for button
            $table->integer('score')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['question_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answer_options');
    }
};
