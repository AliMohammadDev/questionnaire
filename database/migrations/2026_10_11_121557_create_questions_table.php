<?php

use App\Models\AnswerType;
use App\Models\QuestionSection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(QuestionSection::class, 'question_section_id')->index()->constrained();
            $table->foreignIdFor(AnswerType::class, 'answer_type_id')->index()->constrained();
            $table->text('title');
            $table->text('description')->nullable();
            $table->string('placeholder', 500)->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('validation_rules')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
