<?php

namespace App\Models;

use Database\Factories\AnswerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['project_id', 'question_id', 'value_text', 'value_number', 'value_boolean', 'value_date', 'value_time', 'value_json'])]
class Answer extends Model implements HasMedia
{
  /** @use HasFactory<AnswerFactory> */
  use HasFactory;

  use InteractsWithMedia;

  /**
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'project_id' => 'integer',
      'question_id' => 'integer',
      'value_number' => 'decimal:4',
      'value_boolean' => 'boolean',
      'value_date' => 'date',
      'value_json' => 'array',
    ];
  }

  public function project(): BelongsTo
  {
    return $this->belongsTo(Project::class, 'project_id');
  }

  public function question(): BelongsTo
  {
    return $this->belongsTo(Question::class, 'question_id');
  }

  public function answerOptions(): HasMany
  {
    return $this->hasMany(AnswerOption::class, 'answer_id');
  }

  public function options(): BelongsToMany
  {
    return $this->belongsToMany(QuestionOption::class, 'answer_options')->withTimestamps();
  }

  public function registerMediaCollections(): void
  {
    $this->addMediaCollection('attachments')->useDisk('public');
  }
}
