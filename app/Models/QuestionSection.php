<?php

namespace App\Models;

use Database\Factories\QuestionSectionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description', 'sort_order', 'is_active'])]
class QuestionSection extends Model
{
    /** @use HasFactory<QuestionSectionFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'question_section_id');
    }
}
