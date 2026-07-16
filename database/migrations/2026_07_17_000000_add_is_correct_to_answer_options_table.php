<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('answer_options', function (Blueprint $table) {
            $table->boolean('is_correct')->default(false)->after('score');
            $table->index(['question_id', 'is_correct']);
        });

        // Preserve the current quiz answers while moving to the explicit flag.
        DB::table('answer_options')->where('score', '>', 0)->update(['is_correct' => true]);
    }

    public function down(): void
    {
        Schema::table('answer_options', function (Blueprint $table) {
            $table->dropIndex(['question_id', 'is_correct']);
            $table->dropColumn('is_correct');
        });
    }
};
