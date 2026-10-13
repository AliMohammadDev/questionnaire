<?php

use App\Models\DesignStyle;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

it('returns all design styles ordered by sort order and id', function (): void {
    $last = DesignStyle::factory()->create(['sort_order' => 5]);
    $first = DesignStyle::factory()->create(['sort_order' => 1, 'description' => 'قسم التصميم', 'is_active' => false]);
    $second = DesignStyle::factory()->create(['sort_order' => 1]);

    $this->getJson(route('api.design-styles.index'))
        ->assertOk()
        ->assertExactJson([
            'data' => collect([$first, $second, $last])->map(fn (DesignStyle $designStyle): array => [
                'id' => $designStyle->id,
                'name' => $designStyle->name,
                'description' => $designStyle->description,
                'image' => null,
                'is_active' => $designStyle->is_active,
                'sort_order' => $designStyle->sort_order,
            ])->all(),
        ]);
});

it('returns the cover image for each design style', function (): void {
    Storage::fake('public');
    $designStyle = DesignStyle::factory()->create();
    $image = $designStyle->addMedia(UploadedFile::fake()->image('designStyle.jpg'))->toMediaCollection('cover');
    $withoutImage = DesignStyle::factory()->create();

    $this->getJson(route('api.design-styles.index'))
        ->assertOk()
        ->assertJsonPath('data.0.id', $designStyle->id)
        ->assertJsonPath('data.0.image', $image->getUrl())
        ->assertJsonPath('data.1.id', $withoutImage->id)
        ->assertJsonPath('data.1.image', null);
});

it('returns an empty collection when there are no design styles', function (): void {
    $this->getJson(route('api.design-styles.index'))
        ->assertOk()
        ->assertExactJson(['data' => []]);
});
