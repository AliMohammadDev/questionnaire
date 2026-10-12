<?php

use App\Models\Answer;
use App\Models\QuestionOption;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('answer_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Answer::class, 'answer_id')->index()->constrained();
            $table->foreignIdFor(QuestionOption::class, 'question_option_id')->index()->constrained();
            $table->unique(['answer_id', 'question_option_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answer_options');
    }
};
