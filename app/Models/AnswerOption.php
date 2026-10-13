<?php

namespace App\Models;

use Database\Factories\AnswerOptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['answer_id', 'question_option_id'])]
class AnswerOption extends Model
{
  /** @use HasFactory<AnswerOptionFactory> */
  use HasFactory;

  /**
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'answer_id' => 'integer',
      'question_option_id' => 'integer',
    ];
  }

  public function answer(): BelongsTo
  {
    return $this->belongsTo(Answer::class, 'answer_id');
  }

  public function questionOption(): BelongsTo
  {
    return $this->belongsTo(QuestionOption::class, 'question_option_id');
  }
}