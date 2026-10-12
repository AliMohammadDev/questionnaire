<?php

use App\Models\Project;
use App\Models\Question;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('answers', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Project::class, 'project_id')->index()->constrained();
            $table->foreignIdFor(Question::class, 'question_id')->index()->constrained();
            $table->text('value_text')->nullable();
            $table->decimal('value_number', 15, 4)->nullable();
            $table->boolean('value_boolean')->nullable();
            $table->date('value_date')->nullable();
            $table->time('value_time')->nullable();
            $table->json('value_json')->nullable();
            $table->unique(['project_id', 'question_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
