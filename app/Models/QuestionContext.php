<?php

namespace App\Models;

use Database\Factories\QuestionContextFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['question_id', 'department_id', 'project_category_id', 'project_type_id', 'design_style_id'])]
class QuestionContext extends Model
{
    /** @use HasFactory<QuestionContextFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'question_id' => 'integer',
            'department_id' => 'integer',
            'project_category_id' => 'integer',
            'project_type_id' => 'integer',
            'design_style_id' => 'integer',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function projectCategory(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    public function projectType(): BelongsTo
    {
        return $this->belongsTo(ProjectType::class, 'project_type_id');
    }

    public function designStyle(): BelongsTo
    {
        return $this->belongsTo(DesignStyle::class, 'design_style_id');
    }
}
