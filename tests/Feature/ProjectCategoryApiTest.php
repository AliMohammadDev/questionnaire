<?php

use App\Models\ProjectCategory;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

it('returns all project-categories ordered by sort order and id', function (): void {
    $last = ProjectCategory::factory()->create(['sort_order' => 5]);
    $first = ProjectCategory::factory()->create(['sort_order' => 1, 'description' => 'قسم التصميم', 'is_active' => false]);
    $second = ProjectCategory::factory()->create(['sort_order' => 1]);

    $this->getJson(route('api.project-categories.index'))
        ->assertOk()
        ->assertExactJson([
            'data' => collect([$first, $second, $last])->map(fn (ProjectCategory $projectCategory): array => [
                'id' => $projectCategory->id,
                'department_id' => $projectCategory->department_id,
                'name' => $projectCategory->name,
                'description' => $projectCategory->description,
                'image' => null,
                'is_active' => $projectCategory->is_active,
                'sort_order' => $projectCategory->sort_order,
            ])->all(),
        ]);
});

it('returns the first gallery image for each projectCategory', function (): void {
    Storage::fake('public');
    $projectCategory = ProjectCategory::factory()->create();
    $image = $projectCategory->addMedia(UploadedFile::fake()->image('projectCategory.jpg'))->toMediaCollection('gallery');
    $projectCategory->addMedia(UploadedFile::fake()->image('second.jpg'))->toMediaCollection('gallery');
    $withoutImage = ProjectCategory::factory()->create();

    $this->getJson(route('api.project-categories.index'))
        ->assertOk()
        ->assertJsonPath('data.0.id', $projectCategory->id)
        ->assertJsonPath('data.0.image', $image->getUrl())
        ->assertJsonPath('data.1.id', $withoutImage->id)
        ->assertJsonPath('data.1.image', null);
});

it('returns an empty collection when there are no project-categories', function (): void {
    $this->getJson(route('api.project-categories.index'))
        ->assertOk()
        ->assertExactJson(['data' => []]);
});
