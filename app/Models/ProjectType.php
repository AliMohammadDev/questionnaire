<?php

namespace App\Models;

use Database\Factories\ProjectTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['project_category_id', 'name', 'description', 'is_active', 'sort_order'])]
class ProjectType extends Model implements HasMedia
{
    /** @use HasFactory<ProjectTypeFactory> */
    use HasFactory;

    use InteractsWithMedia;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'project_category_id' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function projectCategory(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    public function questionContexts(): HasMany
    {
        return $this->hasMany(QuestionContext::class, 'project_type_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'project_type_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->useDisk('public');
    }
}
