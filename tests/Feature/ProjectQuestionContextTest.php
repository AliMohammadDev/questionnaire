<?php

use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\QuestionContexts\Pages\CreateQuestionContext;
use App\Models\DesignStyle;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectType;
use App\Models\Question;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('updates displayed and saved questions when a project context selection changes', function (string $dimension): void {
    $this->actingAs(User::factory()->administrator()->create());
    $style = DesignStyle::factory()->create();
    $project = Project::factory()->make();
    $initial = ['department_id' => $project->department_id, 'project_category_id' => $project->project_category_id, 'project_type_id' => $project->project_type_id, 'design_style_id' => $style->id];
    $otherType = match ($dimension) {
        'department_id' => ProjectType::factory()->create(),
        'project_category_id' => ProjectType::factory()->for(ProjectCategory::factory()->for($project->department))->create(),
        'project_type_id' => ProjectType::factory()->for($project->projectCategory)->create(),
        default => null,
    };
    $replacement = match ($dimension) {
        'department_id', 'project_category_id', 'project_type_id' => array_replace($initial, [
            'department_id' => $otherType->projectCategory->department_id,
            'project_category_id' => $otherType->project_category_id,
            'project_type_id' => $otherType->id,
        ]),
        'design_style_id' => array_replace($initial, ['design_style_id' => DesignStyle::factory()->create()->id]),
    };
    $general = Question::factory()->create(['title' => 'سؤال لجميع المشاريع']);
    $general->contexts()->create([]);
    $originalQuestion = Question::factory()->create(['is_required' => true, 'title' => 'سؤال الاختيارات الأصلية']);
    $originalQuestion->contexts()->create($initial);
    $newQuestion = Question::factory()->create(['is_required' => true, 'title' => 'سؤال الاختيارات الجديدة']);
    $newQuestion->contexts()->create($replacement);

    $formData = static function (array $context): array {
        $context['design_style_ids'] = [$context['design_style_id']];
        unset($context['design_style_id']);

        return $context;
    };

    Livewire::test(CreateProject::class)->fillForm($formData($initial) + $project->getAttributes())
        ->goToNextWizardStep()->assertHasNoFormErrors()->assertWizardCurrentStep(2)
        ->assertFormFieldExists('questionnaire.'.$general->id)
        ->assertFormFieldExists('questionnaire.'.$originalQuestion->id)
        ->assertFormFieldDoesNotExist('questionnaire.'.$newQuestion->id)
        ->fillForm(['questionnaire' => [$originalQuestion->id => 'جواب قبل تغيير الاختيارات']])
        ->goToPreviousWizardStep()->fillForm($formData($replacement))->goToNextWizardStep()->assertHasNoFormErrors()
        ->assertFormFieldExists('questionnaire.'.$general->id)
        ->assertFormFieldDoesNotExist('questionnaire.'.$originalQuestion->id)
        ->assertFormFieldExists('questionnaire.'.$newQuestion->id)
        ->fillForm(['questionnaire' => [$newQuestion->id => 'جواب بعد تغيير الاختيارات']])
        ->call('create')->assertHasNoFormErrors();

    $this->assertDatabaseCount('projects', 1);
    $this->assertDatabaseHas('projects', array_diff_key($replacement, ['design_style_id' => true]));
    $this->assertDatabaseHas('design_style_project', ['project_id' => Project::sole()->id, 'design_style_id' => $replacement['design_style_id']]);
    $this->assertDatabaseCount('answers', 1);
    $this->assertDatabaseHas('answers', ['question_id' => $newQuestion->id, 'value_text' => 'جواب بعد تغيير الاختيارات']);
    $this->assertDatabaseMissing('answers', ['question_id' => $originalQuestion->id]);
})->with(['department_id', 'project_category_id', 'project_type_id', 'design_style_id']);

it('requires all specified dimensions to match within the same context row', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $project = Project::factory()->make();
    $otherType = ProjectType::factory()->create();
    $question = Question::factory()->create(['is_required' => true]);
    $question->contexts()->create(['department_id' => $project->department_id, 'project_type_id' => $otherType->id]);
    $question->contexts()->create(['department_id' => $otherType->projectCategory->department_id, 'project_type_id' => $project->project_type_id]);

    Livewire::test(CreateProject::class)
        ->fillForm(['department_id' => $project->department_id, 'project_category_id' => $project->project_category_id, 'project_type_id' => $project->project_type_id] + $project->getAttributes())
        ->goToNextWizardStep()->assertFormFieldDoesNotExist('questionnaire.'.$question->id)
        ->call('create')->assertHasNoFormErrors();

    $this->assertDatabaseCount('answers', 0);
});

it('creates a question context without a project status', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $question = Question::factory()->create();

    Livewire::test(CreateQuestionContext::class)->fillForm(['question_id' => $question->id])
        ->call('create')->assertHasNoFormErrors();

    $this->assertDatabaseHas('question_contexts', ['question_id' => $question->id]);
});

it('keeps the same questionnaire when only project dates change', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $question = Question::factory()->create();
    $question->contexts()->create([]);
    $project = Project::factory()->make();
    $data = ['department_id' => $project->department_id, 'project_category_id' => $project->project_category_id, 'project_type_id' => $project->project_type_id] + $project->getAttributes();
    $component = Livewire::test(CreateProject::class)->fillForm($data);
    $component->goToNextWizardStep()->assertHasNoFormErrors()->assertWizardCurrentStep(2)->assertFormFieldExists('questionnaire.'.$question->id)
        ->goToPreviousWizardStep()->fillForm(['submitted_at' => '2026-10-01 09:00:00', 'completed_at' => '2026-10-12 12:00:00'])
        ->goToNextWizardStep()->assertFormFieldExists('questionnaire.'.$question->id);
});
