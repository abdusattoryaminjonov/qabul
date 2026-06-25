<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->string('form_type', 20)->default('survey')->after('is_quiz');
        });

        DB::table('forms')->where('is_quiz', true)->update(['form_type' => 'quiz']);

        Schema::table('question_options', function (Blueprint $table) {
            $table->integer('score_value')->default(0)->after('is_correct');
        });

        Schema::table('form_responses', function (Blueprint $table) {
            $table->json('result_data')->nullable()->after('max_score');
        });
    }

    public function down(): void
    {
        Schema::table('form_responses', function (Blueprint $table) {
            $table->dropColumn('result_data');
        });

        Schema::table('question_options', function (Blueprint $table) {
            $table->dropColumn('score_value');
        });

        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn('form_type');
        });
    }
};
