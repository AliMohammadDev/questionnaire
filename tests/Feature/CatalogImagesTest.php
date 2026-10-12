<?php

use App\Filament\Resources\Departments\Pages\CreateDepartment;
use App\Filament\Resources\DesignStyles\Pages\CreateDesignStyle;
use App\Filament\Resources\ProjectCategories\Pages\CreateProjectCategory;
use App\Filament\Resources\ProjectTypes\Pages\CreateProjectType;
use App\Models\Department;
use App\Models\DesignStyle;
use App\Models\ProjectCategory;
use App\Models\ProjectType;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('uploads multiple catalog images through the form and displays them on record details', function (string $model, string $page, string $field, string $path): void {
    $this->actingAs(User::factory()->administrator()->create());
    Storage::fake('public');
    $data = ['name' => 'سجل مصور', $field => [UploadedFile::fake()->image('first.jpg'), UploadedFile::fake()->image('second.jpg')]];
    if ($model === ProjectCategory::class) {
        $data['department_id'] = Department::factory()->create()->id;
    }
    if ($model === ProjectType::class) {
        $data['project_category_id'] = ProjectCategory::factory()->create()->id;
    }

    Livewire::test($page)->fillForm($data)->call('create')->assertHasNoFormErrors();

    $record = $model::where('name', 'سجل مصور')->sole();
    $images = $record->getMedia($field);
    expect($images)->toHaveCount(2);
    $response = $this->get('/admin/'.$path.'/'.$record->id)->assertOk();
    foreach ($images as $image) {
        Storage::disk('public')->assertExists($image->getPathRelativeToRoot());
        $response->assertSee($image->getUrl());
    }
})->with([
    [Department::class, CreateDepartment::class, 'gallery', 'departments'],
    [ProjectCategory::class, CreateProjectCategory::class, 'gallery', 'project-categories'],
    [ProjectType::class, CreateProjectType::class, 'image', 'project-types'],
    [DesignStyle::class, CreateDesignStyle::class, 'gallery', 'design-styles'],
]);
