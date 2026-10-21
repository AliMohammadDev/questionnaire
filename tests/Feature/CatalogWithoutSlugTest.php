<?php

use App\Filament\Resources\Departments\Pages\CreateDepartment;
use App\Filament\Resources\DesignStyles\Pages\CreateDesignStyle;
use App\Filament\Resources\ProjectCategories\Pages\CreateProjectCategory;
use App\Filament\Resources\ProjectTypes\Pages\CreateProjectType;
use App\Models\Department;
use App\Models\DesignStyle;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectType;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('creates catalog records through the admin form without a slug column', function (string $model, string $page): void {
    $this->actingAs(User::factory()->administrator()->create());
    $data = ['name' => 'سجل جديد', 'is_active' => true, 'sort_order' => 0];
    if ($model === ProjectCategory::class) {
        $data['department_id'] = Department::factory()->create()->id;
    }
    if ($model === ProjectType::class) {
        $data['project_category_id'] = ProjectCategory::factory()->create()->id;
    }

    Livewire::test($page)->fillForm($data)->call('create')->assertHasNoFormErrors();

    $record = $model::where('name', 'سجل جديد')->sole();
    expect(Schema::hasColumn($record->getTable(), 'slug'))->toBeFalse();
    $this->assertModelExists($record);
})->with([
    [Department::class, CreateDepartment::class],
    [ProjectCategory::class, CreateProjectCategory::class],
    [ProjectType::class, CreateProjectType::class],
    [DesignStyle::class, CreateDesignStyle::class],
]);

it('removes existing slug values while preserving catalog records and project relationships', function (): void {
    $project = Project::factory()->hasAttached(DesignStyle::factory(), [], 'designStyles')->create();
    $records = [$project->department, $project->projectCategory, $project->projectType, $project->designStyles->first()];
    $migration = require database_path('migrations/2026_10_12_115516_remove_slug_columns_from_project_catalog_tables.php');
    $migration->down();
    foreach ($records as $record) {
        DB::table($record->getTable())->where('id', $record->id)->update(['slug' => 'existing-short-name']);
    }

    $migration->up();

    foreach ($records as $record) {
        $this->assertDatabaseHas($record->getTable(), ['id' => $record->id, 'name' => $record->name]);
        expect(Schema::hasColumn($record->getTable(), 'slug'))->toBeFalse();
    }
    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'department_id' => $project->department_id,
        'project_category_id' => $project->project_category_id,
        'project_type_id' => $project->project_type_id,
    ]);
    $this->assertDatabaseHas('design_style_project', ['project_id' => $project->id, 'design_style_id' => $project->designStyles->first()->id]);
});
