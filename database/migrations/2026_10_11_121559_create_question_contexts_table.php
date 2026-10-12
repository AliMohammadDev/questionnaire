<?php

use App\Models\Department;
use App\Models\DesignStyle;
use App\Models\ProjectCategory;
use App\Models\ProjectType;
use App\Models\Question;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_contexts', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Question::class, 'question_id')->index()->constrained();
            $table->foreignIdFor(Department::class, 'department_id')->nullable()->index()->constrained();
            $table->foreignIdFor(ProjectCategory::class, 'project_category_id')->nullable()->index()->constrained();
            $table->foreignIdFor(ProjectType::class, 'project_type_id')->nullable()->index()->constrained();
            $table->foreignIdFor(DesignStyle::class, 'design_style_id')->nullable()->index()->constrained();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_contexts');
    }
};
