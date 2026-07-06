<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('response_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('response_id')->constrained('survey_responses')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('answer_option_id')->nullable()->constrained()->nullOnDelete();
            $table->text('text_value')->nullable(); // for text-type questions
            $table->integer('score')->default(0);
            $table->timestamps();

            $table->index(['response_id', 'question_id']);
            $table->unique(['response_id', 'question_id']); // one answer per question per response
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('response_answers');
    }
};
