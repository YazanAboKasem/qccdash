<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique(); // client-generated UUID for deduplication
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->foreignId('device_id')->nullable()->constrained()->nullOnDelete();
            $table->string('language', 5)->default('en'); // en, ar
            $table->enum('status', ['completed', 'partial'])->default('completed');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('synced_at')->nullable();
            $table->unsignedInteger('survey_version')->default(1);
            $table->timestamps();

            $table->index(['survey_id', 'created_at']);
            $table->index(['device_id', 'synced_at']);
            $table->index('language');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_responses');
    }
};
