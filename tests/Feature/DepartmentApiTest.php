<?php

use App\Models\Department;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

it('returns all departments ordered by sort order and id', function (): void {
    $last = Department::factory()->create(['sort_order' => 5]);
    $first = Department::factory()->create(['sort_order' => 1, 'description' => 'قسم التصميم', 'is_active' => false]);
    $second = Department::factory()->create(['sort_order' => 1]);

    $this->getJson(route('api.departments.index'))
        ->assertOk()
        ->assertExactJson([
            'data' => collect([$first, $second, $last])->map(fn (Department $department): array => [
                'id' => $department->id,
                'name' => $department->name,
                'description' => $department->description,
                'image' => null,
                'is_active' => $department->is_active,
                'sort_order' => $department->sort_order,
            ])->all(),
        ]);
});

it('returns the first gallery image for each department', function (): void {
    Storage::fake('public');
    $department = Department::factory()->create();
    $image = $department->addMedia(UploadedFile::fake()->image('department.jpg'))->toMediaCollection('gallery');
    $department->addMedia(UploadedFile::fake()->image('second.jpg'))->toMediaCollection('gallery');
    $withoutImage = Department::factory()->create();

    $this->getJson(route('api.departments.index'))
        ->assertOk()
        ->assertJsonPath('data.0.id', $department->id)
        ->assertJsonPath('data.0.image', $image->getUrl())
        ->assertJsonPath('data.1.id', $withoutImage->id)
        ->assertJsonPath('data.1.image', null);
});

it('returns an empty collection when there are no departments', function (): void {
    $this->getJson(route('api.departments.index'))
        ->assertOk()
        ->assertExactJson(['data' => []]);
});
