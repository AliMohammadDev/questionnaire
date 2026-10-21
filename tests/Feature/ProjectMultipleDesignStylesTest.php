<?php

use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Models\DesignStyle;
use App\Models\Project;
use App\Models\Question;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('shows and saves questions from every selected style without duplicate questions', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $project = Project::factory()->make();
    $styles = DesignStyle::factory()->count(3)->create();
    $context = $project->only(['department_id', 'project_category_id', 'project_type_id']);
    $first = Question::factory()->create();
    $first->contexts()->create($context + ['design_style_id' => $styles[0]->id]);
    $second = Question::factory()->create();
    $second->contexts()->create($context + ['design_style_id' => $styles[1]->id]);
    $shared = Question::factory()->create();
    foreach ($styles->take(2) as $style) {
        $shared->contexts()->create($context + ['design_style_id' => $style->id]);
    }
    $general = Question::factory()->create();
    $general->contexts()->create($context);
    $unselected = Question::factory()->create();
    $unselected->contexts()->create($context + ['design_style_id' => $styles[2]->id]);

    Livewire::test(CreateProject::class)
        ->fillForm($context + ['design_style_ids' => [$styles[0]->id, $styles[1]->id]] + $project->getAttributes())
        ->goToNextWizardStep()->assertHasNoFormErrors()
        ->assertFormFieldExists('questionnaire.'.$first->id)
        ->assertFormFieldExists('questionnaire.'.$second->id)
        ->assertFormFieldExists('questionnaire.'.$shared->id)
        ->assertFormFieldExists('questionnaire.'.$general->id)
        ->assertFormFieldDoesNotExist('questionnaire.'.$unselected->id)
        ->fillForm(['questionnaire' => [$first->id => 'أول', $second->id => 'ثان', $shared->id => 'مشترك', $general->id => 'عام']])
        ->call('create')->assertHasNoFormErrors();

    $saved = Project::sole();
    expect($saved->selectedDesignStyleIds())->toEqualCanonicalizing([$styles[0]->id, $styles[1]->id]);
    $this->assertDatabaseCount('answers', 4);
    $this->assertDatabaseHas('answers', ['project_id' => $saved->id, 'question_id' => $second->id, 'value_text' => 'ثان']);
});

it('refreshes questions when the selected styles change', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $project = Project::factory()->make();
    $styles = DesignStyle::factory()->count(2)->create();
    $first = Question::factory()->create();
    $first->contexts()->create(['design_style_id' => $styles[0]->id]);
    $second = Question::factory()->create();
    $second->contexts()->create(['design_style_id' => $styles[1]->id]);

    Livewire::test(CreateProject::class)->fillForm($project->only(['department_id', 'project_category_id', 'project_type_id']) + ['design_style_ids' => [$styles[0]->id]] + $project->getAttributes())
        ->goToNextWizardStep()->assertFormFieldExists('questionnaire.'.$first->id)
        ->assertFormFieldDoesNotExist('questionnaire.'.$second->id)
        ->goToPreviousWizardStep()->fillForm(['design_style_ids' => [$styles[1]->id]])
        ->goToNextWizardStep()->assertHasNoFormErrors()
        ->assertFormFieldDoesNotExist('questionnaire.'.$first->id)
        ->assertFormFieldExists('questionnaire.'.$second->id);
});

it('edits and clears all project styles', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $styles = DesignStyle::factory()->count(2)->create();
    $project = Project::factory()->create();
    $project->designStyles()->sync($styles->modelKeys());

    Livewire::test(EditProject::class, ['record' => $project->id])
        ->assertSchemaStateSet(['design_style_ids' => array_map('strval', $styles->modelKeys())])
        ->fillForm(['design_style_ids' => [$styles[1]->id]])->call('save')->assertHasNoFormErrors();

    expect($project->fresh()->selectedDesignStyleIds())->toBe([$styles[1]->id]);
    Livewire::test(EditProject::class, ['record' => $project->id])
        ->fillForm(['design_style_ids' => []])->call('save')->assertHasNoFormErrors();

    expect($project->fresh()->selectedDesignStyleIds())->toBe([]);
    expect(Schema::hasColumn('projects', 'design_style_id'))->toBeFalse();
});

it('creates a project through the api with multiple styles and returns their questionnaire', function (): void {
    Sanctum::actingAs(User::factory()->create());
    $styles = DesignStyle::factory()->count(2)->create();
    $questions = Question::factory()->count(2)->create();
    foreach ($styles as $index => $style) {
        $questions[$index]->contexts()->create(['design_style_id' => $style->id]);
    }
    $data = Project::factory()->make()->only(['client_id', 'department_id', 'project_category_id', 'project_type_id']);

    $response = $this->postJson(route('api.projects.store'), $data + ['design_style_ids' => $styles->modelKeys()])
        ->assertCreated()->assertJsonPath('data.design_style_ids', $styles->modelKeys());
    $project = Project::findOrFail($response->json('data.id'));
    $this->getJson(route('api.projects.questionnaire.index', $project))->assertOk()->assertJsonCount(2, 'data');
    $this->postJson(route('api.projects.answers.store', $project), ['answers' => [
        $questions[0]->id => 'إجابة النمط الأول',
        $questions[1]->id => 'إجابة النمط الثاني',
    ]])->assertSuccessful();

    $this->assertDatabaseCount('design_style_project', 2);
    $this->assertDatabaseHas('answers', ['project_id' => $project->id, 'question_id' => $questions[1]->id, 'value_text' => 'إجابة النمط الثاني']);
});

it('rejects invalid multiple style values', function (mixed $ids, string $error): void {
    Sanctum::actingAs(User::factory()->create());
    $data = Project::factory()->make()->only(['client_id', 'department_id', 'project_category_id', 'project_type_id']);

    $this->postJson(route('api.projects.store'), $data + ['design_style_ids' => $ids])
        ->assertUnprocessable()->assertJsonValidationErrors([$error]);

    $this->assertDatabaseCount('projects', 0);
})->with([
    'not an array' => ['modern', 'design_style_ids'],
    'unknown style' => [[999999], 'design_style_ids.0'],
    'duplicates' => [[999999, 999999], 'design_style_ids.1'],
]);

it('protects a style used as a secondary project style from deletion', function (): void {
    $admin = User::factory()->administrator()->create();
    $styles = DesignStyle::factory()->count(2)->create();
    $project = Project::factory()->create();
    $project->designStyles()->sync($styles->modelKeys());

    expect(Gate::forUser($admin)->allows('delete', $styles[1]))->toBeFalse();
    expect($styles[1]->projects()->whereKey($project->id)->exists())->toBeTrue();
});

it('keeps saved styles when opening the edit form', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $style = DesignStyle::factory()->create();
    $project = Project::factory()->hasAttached($style, [], 'designStyles')->create();

    Livewire::test(EditProject::class, ['record' => $project->id])
        ->assertSchemaStateSet(['design_style_ids' => [$style->id]])
        ->call('save')->assertHasNoFormErrors();

    expect($project->fresh()->selectedDesignStyleIds())->toBe([$style->id]);
    $this->assertDatabaseHas('design_style_project', ['project_id' => $project->id, 'design_style_id' => $style->id]);
});

it('stores project styles only in the pivot table', function (): void {
    $styles = DesignStyle::factory()->count(2)->create();
    $project = Project::factory()->hasAttached($styles, [], 'designStyles')->create();
    expect(Schema::hasColumn('projects', 'design_style_id'))->toBeFalse();
    expect($project->fresh()->selectedDesignStyleIds())->toEqualCanonicalizing($styles->modelKeys());
    $this->assertDatabaseCount('design_style_project', 2);
});

it('rejects the removed singular style field when creating projects through the api', function (): void {
    Sanctum::actingAs(User::factory()->create());
    $style = DesignStyle::factory()->create();
    $data = Project::factory()->make()->only(['client_id', 'department_id', 'project_category_id', 'project_type_id']);

    $this->postJson(route('api.projects.store'), $data + ['design_style_id' => $style->id])
        ->assertUnprocessable()->assertJsonValidationErrors(['design_style_id']);

    $this->assertDatabaseCount('projects', 0);
});
