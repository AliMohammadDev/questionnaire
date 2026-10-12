<?php

namespace App\Models;

use Database\Factories\QuestionOptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['question_id', 'label', 'value', 'sort_order', 'is_active'])]
class QuestionOption extends Model implements HasMedia
{
    /** @use HasFactory<QuestionOptionFactory> */
    use HasFactory;

    use InteractsWithMedia;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'question_id' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function questionConditions(): HasMany
    {
        return $this->hasMany(QuestionCondition::class, 'question_option_id');
    }

    public function answerOptions(): HasMany
    {
        return $this->hasMany(AnswerOption::class, 'question_option_id');
    }

    public function answers(): BelongsToMany
    {
        return $this->belongsToMany(Answer::class, 'answer_options')->withTimestamps();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->useDisk('public')->singleFile();
    }
}
