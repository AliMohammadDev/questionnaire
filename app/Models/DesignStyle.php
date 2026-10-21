<?php

namespace App\Models;

use Database\Factories\DesignStyleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['name', 'description', 'is_active', 'sort_order'])]
class DesignStyle extends Model implements HasMedia
{
    /** @use HasFactory<DesignStyleFactory> */
    use HasFactory;

    use InteractsWithMedia;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function questionContexts(): HasMany
    {
        return $this->hasMany(QuestionContext::class, 'design_style_id');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->useDisk('public')->singleFile();
        $this->addMediaCollection('gallery')->useDisk('public');
    }
}
