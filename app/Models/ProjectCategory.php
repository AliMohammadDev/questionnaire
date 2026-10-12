<?php

namespace App\Models;

use Database\Factories\ProjectCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['department_id', 'name', 'description', 'is_active', 'sort_order'])]
class ProjectCategory extends Model implements HasMedia
{
    /** @use HasFactory<ProjectCategoryFactory> */
    use HasFactory;

    use InteractsWithMedia;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'department_id' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function projectTypes(): HasMany
    {
        return $this->hasMany(ProjectType::class, 'project_category_id');
    }

    public function questionContexts(): HasMany
    {
        return $this->hasMany(QuestionContext::class, 'project_category_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'project_category_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery')->useDisk('public');
    }
}
