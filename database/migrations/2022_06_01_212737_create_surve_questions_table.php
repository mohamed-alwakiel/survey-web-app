<?php

use App\Models\Survey;
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
        Schema::create('surve_questions', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);
            $table->string('question', 2000);
            $table->longText('description')->nullable();
            $table->longText('data')->nullable();
            $table->foreignIdFor(Survey::class,'survey_id');
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
        Schema::dropIfExists('surve_questions');
    }
};
