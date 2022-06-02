<?php

use App\Models\SurveAnswer;
use App\Models\SurveQuestion;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surve_question_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SurveQuestion::class, 'survey_question_id');
            $table->foreignIdFor(SurveAnswer::class, 'survey_answer_id');
            $table->text('answer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('surve_question_answers');
    }
};
