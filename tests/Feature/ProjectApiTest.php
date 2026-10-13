<?php

use App\Models\DesignStyle;
use App\Models\Project;
use App\Models\ProjectType;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('creates a project for an existing client with a generated code', function (): void {
    $data = Project::factory()->make(['design_style_id' => DesignStyle::factory()->create()->id, 'notes' => 'مشروع جديد'])
        ->only(['client_id', 'department_id', 'project_category_id', 'project_type_id', 'design_style_id', 'project_name', 'notes']);

    $response = $this->postJson(route('api.projects.store'), $data + ['project_code' => 'CUSTOM', 'completed_at' => '2026-01-01']);
    $response->assertCreated()->assertJsonFragment($data);
    $project = Project::sole();
    expect($response->json('data.id'))->toBe($project->id);
    expect($project->project_code)->toStartWith('PRJ-')->not->toBe('CUSTOM');
    expect($project->completed_at)->toBeNull();
    $this->assertDatabaseHas('projects', $data);
    $this->assertDatabaseCount('clients', 1);
    $this->assertDatabaseCount('answers', 0);

    $this->postJson(route('api.projects.store'), $data)->assertCreated();
    expect(Project::query()->pluck('project_code')->unique())->toHaveCount(2);
});

it('allows creating a project without optional details', function (): void {
    $data = Project::factory()->make()->only(['client_id', 'department_id', 'project_category_id', 'project_type_id']);

    $this->postJson(route('api.projects.store'), $data)->assertCreated()
        ->assertJsonPath('data.design_style_id', null)->assertJsonPath('data.project_name', null);
});

it('rejects missing or nonexistent project references', function (bool $missing): void {
    $data = $missing ? [] : array_fill_keys(['client_id', 'department_id', 'project_category_id', 'project_type_id', 'design_style_id'], 99999);
    $errors = ['client_id', 'department_id', 'project_category_id', 'project_type_id'];
    if (! $missing) {
        $errors[] = 'design_style_id';
    }

    $this->postJson(route('api.projects.store'), $data)->assertUnprocessable()->assertJsonValidationErrors($errors);
    $this->assertDatabaseCount('projects', 0);
})->with([true, false]);

it('rejects catalog selections from a different hierarchy', function (string $dimension): void {
    $data = Project::factory()->make()->only(['client_id', 'department_id', 'project_category_id', 'project_type_id']);
    $other = ProjectType::factory()->create();
    $data[$dimension] = $dimension === 'department_id' ? $other->projectCategory->department_id : $other->id;

    $this->postJson(route('api.projects.store'), $data)->assertUnprocessable()
        ->assertJsonValidationErrors([$dimension === 'department_id' ? 'project_category_id' : 'project_type_id']);
    $this->assertDatabaseCount('projects', 0);
})->with(['department_id', 'project_type_id']);
