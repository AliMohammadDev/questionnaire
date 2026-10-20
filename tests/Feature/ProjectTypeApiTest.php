<?php

use App\Models\ProjectType;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

uses(LazilyRefreshDatabase::class);

it('returns all project types ordered by sort order and id', function (): void {
    Sanctum::actingAs(User::factory()->create());
    $last = ProjectType::factory()->create(['sort_order' => 5]);
    $first = ProjectType::factory()->create(['sort_order' => 1, 'description' => 'قسم التصميم', 'is_active' => false]);
    $second = ProjectType::factory()->create(['sort_order' => 1]);

    $this->getJson(route('api.project-types.index'))
        ->assertOk()
        ->assertExactJson([
            'data' => collect([$first, $second, $last])->map(fn (ProjectType $projectType): array => [
                'id' => $projectType->id,
                'project_category_id' => $projectType->project_category_id,
                'name' => $projectType->name,
                'description' => $projectType->description,
                'image' => null,
                'is_active' => $projectType->is_active,
                'sort_order' => $projectType->sort_order,
            ])->all(),
        ]);
});

it('returns the first image for each project type', function (): void {
    Sanctum::actingAs(User::factory()->create());
    Storage::fake('public');
    $projectType = ProjectType::factory()->create();
    $image = $projectType->addMedia(UploadedFile::fake()->image('projectType.jpg'))->toMediaCollection('image');
    $projectType->addMedia(UploadedFile::fake()->image('second.jpg'))->toMediaCollection('image');
    $withoutImage = ProjectType::factory()->create();

    $this->getJson(route('api.project-types.index'))
        ->assertOk()
        ->assertJsonPath('data.0.id', $projectType->id)
        ->assertJsonPath('data.0.image', $image->getUrl())
        ->assertJsonPath('data.1.id', $withoutImage->id)
        ->assertJsonPath('data.1.image', null);
});

it('returns an empty collection when there are no project types', function (): void {
    Sanctum::actingAs(User::factory()->create());
    $this->getJson(route('api.project-types.index'))
        ->assertOk()
        ->assertExactJson(['data' => []]);
});
