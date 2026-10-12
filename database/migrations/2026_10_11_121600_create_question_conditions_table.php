<?php

use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_conditions', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Question::class, 'question_id')->index()->constrained();
            $table->foreignIdFor(Question::class, 'depends_on_question_id')->index()->constrained();
            $table->string('operator', 50);
            $table->foreignIdFor(QuestionOption::class, 'question_option_id')->nullable()->index()->constrained();
            $table->text('value')->nullable();
            $table->string('logic_type', 10)->default('AND');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_conditions');
    }
};
