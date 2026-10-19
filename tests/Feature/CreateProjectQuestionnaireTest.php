<?php

use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Models\AnswerType;
use App\Models\Client;
use App\Models\DesignStyle;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('opens the questionnaire with only active questions matching all context dimensions', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $project = Project::factory()->make();
    $general = Question::factory()->create(['title' => 'السؤال العام']);
    $general->contexts()->create([]);
    $matching = Question::factory()->create(['title' => 'السؤال المخصص']);
    $matching->contexts()->create(['department_id' => $project->department_id, 'project_category_id' => $project->project_category_id, 'project_type_id' => $project->project_type_id]);
    $matching->contexts()->create([]);
    $unrelated = Question::factory()->create(['title' => 'سؤال نوع آخر']);
    $unrelated->contexts()->create(['department_id' => $project->department_id, 'project_type_id' => ProjectType::factory()->create()->id]);
    $styled = Question::factory()->create(['title' => 'سؤال نمط آخر']);
    $styled->contexts()->create(['design_style_id' => DesignStyle::factory()->create()->id]);
    $inactive = Question::factory()->create(['is_active' => false]);
    $inactive->contexts()->create([]);

    Livewire::test(CreateProject::class)->fillForm(['department_id' => $project->department_id, 'project_category_id' => $project->project_category_id, 'project_type_id' => $project->project_type_id] + $project->getAttributes())
        ->fillForm(['additional_questions' => [['question' => 'سؤال إضافي', 'answer' => 'جواب إضافي']]])
        ->goToNextWizardStep()->assertHasNoFormErrors()->assertWizardCurrentStep(2)
        ->assertFormFieldExists('questionnaire.'.$general->id)
        ->assertFormFieldExists('questionnaire.'.$matching->id)
        ->assertFormFieldDoesNotExist('questionnaire.'.$unrelated->id)
        ->assertFormFieldDoesNotExist('questionnaire.'.$styled->id)
        ->assertFormFieldDoesNotExist('questionnaire.'.$inactive->id)
        ->fillForm(['questionnaire' => [$general->id => 'دمشق', $matching->id => 'متطلبات التصميم']])
        ->call('create')->assertHasNoFormErrors();

    expect(Project::sole()->additional_questions)->toBe([['question' => 'سؤال إضافي', 'answer' => 'جواب إضافي']]);
    $this->assertDatabaseCount('projects', 1);
    $this->assertDatabaseCount('answers', 2);
    $this->assertDatabaseHas('answers', ['question_id' => $general->id, 'value_text' => 'دمشق']);
});

it('requires answers to visible mandatory questions before saving the project', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $project = Project::factory()->make();
    $question = Question::factory()->create(['is_required' => true]);
    $question->contexts()->create([]);

    Livewire::test(CreateProject::class)->fillForm(['department_id' => $project->department_id, 'project_category_id' => $project->project_category_id, 'project_type_id' => $project->project_type_id] + $project->getAttributes())
        ->goToNextWizardStep()->call('create')->assertHasFormErrors(['questionnaire.'.$question->id => 'required']);

    $this->assertDatabaseCount('projects', 0);
    $this->assertDatabaseCount('answers', 0);
});

it('shows dependent questions when their condition matches and discards answers after they become hidden', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $project = Project::factory()->make();
    $source = Question::factory()->for(AnswerType::factory()->create(['code' => 'boolean']))->create();
    $source->contexts()->create([]);
    $dependent = Question::factory()->create(['is_required' => true]);
    $dependent->contexts()->create([]);
    $dependent->conditions()->create(['depends_on_question_id' => $source->id, 'operator' => 'equals', 'value' => '1', 'logic_type' => 'AND']);

    Livewire::test(CreateProject::class)->fillForm(['department_id' => $project->department_id, 'project_category_id' => $project->project_category_id, 'project_type_id' => $project->project_type_id] + $project->getAttributes())->goToNextWizardStep()
        ->assertFormFieldIsHidden('questionnaire.'.$dependent->id)
        ->fillForm(['questionnaire' => [$source->id => 1]])
        ->assertFormFieldIsVisible('questionnaire.'.$dependent->id)
        ->call('create')->assertHasFormErrors(['questionnaire.'.$dependent->id => 'required'])
        ->fillForm(['questionnaire' => [$source->id => 0, $dependent->id => 'إجابة قديمة']])
        ->assertFormFieldIsHidden('questionnaire.'.$dependent->id)
        ->call('create')->assertHasNoFormErrors();

    $this->assertDatabaseCount('answers', 1);
    $this->assertDatabaseHas('answers', ['question_id' => $source->id, 'value_boolean' => false]);
    $this->assertDatabaseMissing('answers', ['question_id' => $dependent->id]);
});

it('stores selected options as answer options instead of text', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $project = Project::factory()->make();
    $question = Question::factory()->for(AnswerType::factory()->multipleChoice())->create();
    $question->contexts()->create([]);
    $options = QuestionOption::factory()->for($question)->count(2)->create();

    Livewire::test(CreateProject::class)->fillForm(['department_id' => $project->department_id, 'project_category_id' => $project->project_category_id, 'project_type_id' => $project->project_type_id] + $project->getAttributes())->goToNextWizardStep()
        ->fillForm(['questionnaire' => [$question->id => $options->modelKeys()]])
        ->call('create')->assertHasNoFormErrors();

    $answer = Project::sole()->answers()->sole();
    expect($answer->options->modelKeys())->toEqual($options->modelKeys());
    expect($answer->value_text)->toBeNull();
});

it('rejects options belonging to another question', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $project = Project::factory()->make();
    $question = Question::factory()->for(AnswerType::factory()->singleChoice())->create();
    $question->contexts()->create([]);
    $other = QuestionOption::factory()->create();

    Livewire::test(CreateProject::class)->fillForm(['department_id' => $project->department_id, 'project_category_id' => $project->project_category_id, 'project_type_id' => $project->project_type_id] + $project->getAttributes())->goToNextWizardStep()
        ->fillForm(['questionnaire' => [$question->id => $other->id]])
        ->call('create')->assertHasFormErrors(['questionnaire.'.$question->id]);

    $this->assertDatabaseCount('projects', 0);
    $this->assertDatabaseCount('answers', 0);
});

it('creates and selects a client from the project form without losing project data', function (): void {
    $this->actingAs(User::factory()->administrator()->create());

    Livewire::test(CreateProject::class)->fillForm(['project_name' => 'شقة أحمد'])
        ->callAction(TestAction::make('createOption')->schemaComponent('client_id'), data: [
            'name' => 'أحمد', 'phone' => '0999999999', 'email' => 'ahmad@example.com', 'address' => 'دمشق',
        ])->assertHasNoActionErrors()
        ->assertSchemaStateSet(['client_id' => Client::sole()->id, 'project_name' => 'شقة أحمد']);

    $this->assertDatabaseHas('clients', ['name' => 'أحمد', 'phone' => '0999999999']);
});

it('attaches questionnaire files to their answer', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    Storage::fake('public');
    $project = Project::factory()->make();
    $question = Question::factory()->for(AnswerType::factory()->create(['code' => 'file']))->create();
    $question->contexts()->create([]);
    $image = UploadedFile::fake()->image('plan.jpg');

    Livewire::test(CreateProject::class)->fillForm(['department_id' => $project->department_id, 'project_category_id' => $project->project_category_id, 'project_type_id' => $project->project_type_id] + $project->getAttributes())->goToNextWizardStep()
        ->fillForm(['questionnaire' => [$question->id => [$image]]])
        ->call('create')->assertHasNoFormErrors();

    $media = Project::sole()->answers()->sole()->getFirstMedia('attachments');
    expect($media)->not->toBeNull();
    Storage::disk('public')->assertExists($media->getPathRelativeToRoot());
});

it('refreshes the questionnaire when project selections change and saves only the new questions', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $project = Project::factory()->make();
    $otherType = ProjectType::factory()->for($project->projectCategory)->create();
    $original = Question::factory()->create(['is_required' => true]);
    $original->contexts()->create(['project_type_id' => $project->project_type_id]);
    $replacement = Question::factory()->create(['is_required' => true]);
    $replacement->contexts()->create(['project_type_id' => $otherType->id]);

    Livewire::test(CreateProject::class)
        ->fillForm(['department_id' => $project->department_id, 'project_category_id' => $project->project_category_id, 'project_type_id' => $project->project_type_id] + $project->getAttributes())
        ->goToNextWizardStep()->fillForm(['questionnaire' => [$original->id => 'متطلبات قديمة']])
        ->goToPreviousWizardStep()->fillForm(['project_type_id' => $otherType->id])->goToNextWizardStep()
        ->assertFormFieldDoesNotExist('questionnaire.'.$original->id)
        ->assertFormFieldExists('questionnaire.'.$replacement->id)
        ->fillForm(['questionnaire' => [$replacement->id => 'متطلبات جديدة']])
        ->call('create')->assertHasNoFormErrors();

    $this->assertDatabaseHas('projects', ['project_type_id' => $otherType->id]);
    $this->assertDatabaseCount('answers', 1);
    $this->assertDatabaseHas('answers', ['question_id' => $replacement->id, 'value_text' => 'متطلبات جديدة']);
});

it('saves typed questionnaire values in the corresponding answer columns', function (string $code, mixed $value, string $column, mixed $stored): void {
    $this->actingAs(User::factory()->administrator()->create());
    $project = Project::factory()->make();
    $question = Question::factory()->for(AnswerType::factory()->create(['code' => $code]))->create(['is_required' => true]);
    $question->contexts()->create([]);

    Livewire::test(CreateProject::class)
        ->fillForm(['department_id' => $project->department_id, 'project_category_id' => $project->project_category_id, 'project_type_id' => $project->project_type_id] + $project->getAttributes())
        ->goToNextWizardStep()->fillForm(['questionnaire' => [$question->id => $value]])
        ->call('create')->assertHasNoFormErrors();

    $this->assertDatabaseHas('answers', ['question_id' => $question->id, $column => $stored]);
})->with([
    'number including zero' => ['number', 0, 'value_number', 0],
    'date' => ['date', '2026-10-15', 'value_date', '2026-10-15 00:00:00'],
    'time' => ['time', '14:30', 'value_time', '14:30'],
]);

it('saves grid answers as structured data', function (string $code, mixed $value): void {
    $this->actingAs(User::factory()->administrator()->create());
    $project = Project::factory()->make();
    $question = Question::factory()->for(AnswerType::factory()->create(['code' => $code]))->create([
        'is_required' => true, 'validation_rules' => ['rows' => ['الجودة', 'السعر'], 'columns' => ['مهم', 'أقل أهمية']],
    ]);
    $question->contexts()->create([]);

    Livewire::test(CreateProject::class)
        ->fillForm(['department_id' => $project->department_id, 'project_category_id' => $project->project_category_id, 'project_type_id' => $project->project_type_id] + $project->getAttributes())
        ->goToNextWizardStep()->fillForm(['questionnaire' => [$question->id => ['الجودة' => $value, 'السعر' => $value]]])
        ->call('create')->assertHasNoFormErrors();

    expect(Project::sole()->answers()->sole()->value_json)->toBe(['الجودة' => $value, 'السعر' => $value]);
})->with([
    'single choice grid' => ['multiple_choice_grid', 'مهم'],
    'checkbox grid' => ['checkbox_grid', ['مهم', 'أقل أهمية']],
]);

it('rejects numbers outside the configured range', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $project = Project::factory()->make();
    $question = Question::factory()->for(AnswerType::factory()->create(['code' => 'rating']))->create(['validation_rules' => ['min' => 1, 'max' => 5]]);
    $question->contexts()->create([]);

    Livewire::test(CreateProject::class)
        ->fillForm(['department_id' => $project->department_id, 'project_category_id' => $project->project_category_id, 'project_type_id' => $project->project_type_id] + $project->getAttributes())
        ->goToNextWizardStep()->fillForm(['questionnaire' => [$question->id => 6]])
        ->call('create')->assertHasFormErrors(['questionnaire.'.$question->id => 'max']);

    $this->assertDatabaseCount('projects', 0);
});

it('validates a new client before creating it in the project form', function (): void {
    $this->actingAs(User::factory()->administrator()->create());

    Livewire::test(CreateProject::class)
        ->callAction(TestAction::make('createOption')->schemaComponent('client_id'), data: ['name' => 'أحمد', 'email' => 'invalid'])
        ->assertHasActionErrors(['phone' => 'required', 'email' => 'email']);

    $this->assertDatabaseCount('clients', 0);
});

it('allows saving a project with no matching questions', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $project = Project::factory()->make();

    Livewire::test(CreateProject::class)
        ->fillForm(['department_id' => $project->department_id, 'project_category_id' => $project->project_category_id, 'project_type_id' => $project->project_type_id] + $project->getAttributes())
        ->goToNextWizardStep()->assertSee('لا توجد أسئلة فعالة مرتبطة باختيارات هذا المشروع')
        ->call('create')->assertHasNoFormErrors();

    $this->assertDatabaseCount('projects', 1);
    $this->assertDatabaseCount('answers', 0);
});

it('excludes questions whose section or answer type is inactive', function (string $relationship): void {
    $this->actingAs(User::factory()->administrator()->create());
    $project = Project::factory()->make();
    $question = Question::factory()->create(['is_required' => true]);
    $question->contexts()->create([]);
    $question->{$relationship}->update(['is_active' => false]);

    Livewire::test(CreateProject::class)
        ->fillForm(['department_id' => $project->department_id, 'project_category_id' => $project->project_category_id, 'project_type_id' => $project->project_type_id] + $project->getAttributes())
        ->goToNextWizardStep()->assertFormFieldDoesNotExist('questionnaire.'.$question->id)
        ->call('create')->assertHasNoFormErrors();

    $this->assertDatabaseCount('answers', 0);
})->with(['questionSection', 'answerType']);
