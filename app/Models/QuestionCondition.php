<?php

namespace App\Models;

use Database\Factories\QuestionConditionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['question_id', 'depends_on_question_id', 'operator', 'question_option_id', 'value', 'logic_type'])]
class QuestionCondition extends Model
{
    /** @use HasFactory<QuestionConditionFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'question_id' => 'integer',
            'depends_on_question_id' => 'integer',
            'question_option_id' => 'integer',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function dependsOnQuestion(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'depends_on_question_id');
    }

    public function questionOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'question_option_id');
    }
}
