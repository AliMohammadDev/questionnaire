<?php

namespace App\Models;

use Database\Factories\AnswerTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'code', 'has_options', 'allows_multiple', 'is_active', 'sort_order'])]
class AnswerType extends Model
{
    /** @use HasFactory<AnswerTypeFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'has_options' => 'boolean',
            'allows_multiple' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'answer_type_id');
    }
}
