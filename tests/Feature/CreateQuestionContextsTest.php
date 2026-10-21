<?php

use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\QuestionContexts\Pages\CreateQuestionContext;
use App\Filament\Resources\QuestionContexts\Pages\EditQuestionContext;
use App\Models\DesignStyle;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectType;
use App\Models\Question;
use App\Models\QuestionContext;
use App\Models\User;
use App\Services\QuestionnaireService;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    $this->actingAs(User::factory()->administrator()->create());
});

it('creates all combinations of categories types and styles without duplicates', function (): void {
    $question = Question::factory()->create();
    $first = ProjectType::factory()->create();
    $second = ProjectType::factory()->for(ProjectCategory::factory()->for($first->projectCategory->department))->create();
    $styles = DesignStyle::factory()->count(2)->create();
    $data = [
        'question_id' => $question->id,
        'department_id' => $first->projectCategory->department_id,
        'project_category_ids' => [$first->project_category_id, $second->project_category_id],
        'project_type_ids' => [$first->id, $second->id],
        'design_style_ids' => $styles->modelKeys(),
    ];
    foreach ([1, 2] as $attempt) {
        Livewire::test(CreateQuestionContext::class)->fillForm($data)->call('create')->assertHasNoFormErrors();
    }
    $this->assertDatabaseCount('questions', 1);
    $this->assertDatabaseCount('question_contexts', 8);
    foreach ([$first->project_category_id, $second->project_category_id] as $categoryId) {
        foreach ([$first, $second] as $type) {
            foreach ($styles as $style) {
                $this->assertDatabaseHas('question_contexts', ['question_id' => $question->id, 'project_category_id' => $categoryId, 'project_type_id' => $type->id, 'design_style_id' => $style->id]);
            }
        }
    }
});

it('creates a general context when no levels are selected', function (): void {
    $question = Question::factory()->create();
    Livewire::test(CreateQuestionContext::class)->fillForm(['question_id' => $question->id])->call('create')->assertHasNoFormErrors();
    $this->assertDatabaseHas('question_contexts', ['question_id' => $question->id, 'department_id' => null, 'project_category_id' => null, 'project_type_id' => null, 'design_style_id' => null]);
});

it('rejects types and categories outside the selected department', function (): void {
    $first = ProjectType::factory()->create();
    $other = ProjectType::factory()->create();
    Livewire::test(CreateQuestionContext::class)->fillForm([
        'question_id' => Question::factory()->create()->id,
        'department_id' => $first->projectCategory->department_id,
        'project_category_ids' => [$other->project_category_id],
        'project_type_ids' => [$other->id],
    ])->call('create')->assertHasFormErrors();
    $this->assertDatabaseCount('question_contexts', 0);
});

it('creates category and style contexts covering all types when types are omitted', function (): void {
    $first = ProjectCategory::factory()->create();
    $second = ProjectCategory::factory()->for($first->department)->create();
    $question = Question::factory()->create();
    $styles = DesignStyle::factory()->count(2)->create();
    Livewire::test(CreateQuestionContext::class)->fillForm([
        'question_id' => $question->id,
        'department_id' => $first->department_id,
        'project_category_ids' => [$first->id, $second->id],
        'design_style_ids' => $styles->modelKeys(),
    ])->call('create')->assertHasNoFormErrors();
    $this->assertDatabaseCount('question_contexts', 4);
    expect($question->contexts()->whereNotNull('project_type_id')->exists())->toBeFalse();
});

it('saves a project using a type from another category and shows its generated question once', function (): void {
    $type = ProjectType::factory()->create();
    $otherCategory = ProjectCategory::factory()->for($type->projectCategory->department)->create();
    $styles = DesignStyle::factory()->count(2)->create();
    $question = Question::factory()->create(['is_required' => false]);

    Livewire::test(CreateQuestionContext::class)->fillForm([
        'question_id' => $question->id,
        'department_id' => $type->projectCategory->department_id,
        'project_category_ids' => [$type->project_category_id, $otherCategory->id],
        'project_type_ids' => [$type->id],
        'design_style_ids' => $styles->modelKeys(),
    ])->call('create')->assertHasNoFormErrors();

    $project = Project::factory()->make([
        'department_id' => $type->projectCategory->department_id,
        'project_category_id' => $otherCategory->id,
        'project_type_id' => $type->id,
    ]);
    $data = $project->only(['department_id', 'project_category_id', 'project_type_id']) + ['design_style_ids' => [$styles->first()->id]];
    $question->contexts()->create(['department_id' => $data['department_id']]);

    expect(app(QuestionnaireService::class)->getQuestions($data)->modelKeys())->toBe([$question->id]);
    Livewire::test(CreateProject::class)
        ->fillForm($data + $project->getAttributes())
        ->goToNextWizardStep()->assertHasNoFormErrors()
        ->assertFormFieldExists('questionnaire.'.$question->id)
        ->fillForm(['questionnaire' => [$question->id => 'إجابة للسياق المشترك']])
        ->call('create')->assertHasNoFormErrors();

    $this->assertDatabaseHas('projects', array_diff_key($data, ['design_style_ids' => true]));
    $this->assertDatabaseCount('questions', 1);
    $this->assertDatabaseCount('answers', 1);
});

it('allows editing a context with a type from another category in the same department', function (): void {
    $type = ProjectType::factory()->create();
    $category = ProjectCategory::factory()->for($type->projectCategory->department)->create();
    $context = QuestionContext::factory()->create([
        'department_id' => $type->projectCategory->department_id,
        'project_category_id' => $category->id,
        'project_type_id' => $type->id,
    ]);
    $style = DesignStyle::factory()->create();

    Livewire::test(EditQuestionContext::class, ['record' => $context->id])
        ->fillForm(['design_style_id' => $style->id])->call('save')->assertHasNoFormErrors();

    $this->assertDatabaseHas('question_contexts', [
        'id' => $context->id, 'project_category_id' => $category->id,
        'project_type_id' => $type->id, 'design_style_id' => $style->id,
    ]);
});

it('rejects a project type from another department in the admin form', function (): void {
    $project = Project::factory()->make();
    $otherType = ProjectType::factory()->create();

    Livewire::test(CreateProject::class)
        ->fillForm(['project_type_id' => $otherType->id] + $project->getAttributes())
        ->goToNextWizardStep()->assertHasFormErrors(['project_type_id']);

    $this->assertDatabaseCount('projects', 0);
});
