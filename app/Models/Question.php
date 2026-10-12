<?php

namespace App\Models;

use Database\Factories\QuestionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['question_section_id', 'answer_type_id', 'title', 'description', 'placeholder', 'is_required', 'is_active', 'sort_order', 'validation_rules'])]
class Question extends Model
{
    /** @use HasFactory<QuestionFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'question_section_id' => 'integer',
            'answer_type_id' => 'integer',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'validation_rules' => 'array',
        ];
    }

    public function questionSection(): BelongsTo
    {
        return $this->belongsTo(QuestionSection::class, 'question_section_id');
    }

    public function answerType(): BelongsTo
    {
        return $this->belongsTo(AnswerType::class, 'answer_type_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class, 'question_id');
    }

    public function contexts(): HasMany
    {
        return $this->hasMany(QuestionContext::class, 'question_id');
    }

    public function conditions(): HasMany
    {
        return $this->hasMany(QuestionCondition::class, 'question_id');
    }

    public function dependentConditions(): HasMany
    {
        return $this->hasMany(QuestionCondition::class, 'depends_on_question_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'question_id');
    }
}
