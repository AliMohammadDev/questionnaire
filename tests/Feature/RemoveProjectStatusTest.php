<?php

use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Support\ProjectQuestionnaire;
use App\Filament\Widgets\QuestionnaireStats;
use App\Models\Answer;
use App\Models\Project;
use App\Models\Question;
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

it('creates projects and answers without a project status', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $project = Project::factory()->make();
    $question = Question::factory()->create();
    $question->contexts()->create(['department_id' => $project->department_id]);

    $data = ['department_id' => $project->department_id, 'project_category_id' => $project->project_category_id, 'project_type_id' => $project->project_type_id] + $project->getAttributes();
    Livewire::test(CreateProject::class)->fillForm($data)
        ->goToNextWizardStep()
        ->fillForm(['questionnaire' => [$question->id => 'إجابة دون حالة مشروع']])
        ->call('create')->assertHasNoFormErrors();

    $this->assertDatabaseHas('projects', ['project_code' => $project->project_code]);
    $this->assertDatabaseHas('answers', ['question_id' => $question->id, 'value_text' => 'إجابة دون حالة مشروع']);
    expect(Schema::hasColumn('projects', 'status'))->toBeFalse();
    expect(Schema::hasColumn('question_contexts', 'status'))->toBeFalse();
});

it('removes existing status columns while preserving projects contexts and answers', function (): void {
    $answer = Answer::factory()->create();
    $context = $answer->question->contexts()->create(['department_id' => $answer->project->department_id]);
    $migration = require database_path('migrations/2026_10_12_122019_remove_status_from_projects_and_question_contexts.php');
    $migration->down();
    DB::table('projects')->where('id', $answer->project_id)->update(['status' => 'reviewing']);
    DB::table('question_contexts')->where('id', $context->id)->update(['status' => 'approved']);

    $migration->up();

    $this->assertModelExists($answer);
    $this->assertModelExists($answer->project);
    $this->assertModelExists($context);
    expect(ProjectQuestionnaire::questions($answer->project->questionnaireData())->modelKeys())->toContain($answer->question_id);
    expect(Schema::hasColumn('projects', 'status'))->toBeFalse();
    expect(Schema::hasColumn('question_contexts', 'status'))->toBeFalse();
});

it('renders the dashboard and management pages without project status controls', function (): void {
    $this->actingAs(User::factory()->administrator()->create());

    $this->get('/admin')->assertOk()->assertDontSee('بانتظار المراجعة');
    Livewire::withoutLazyLoading()->test(QuestionnaireStats::class)->assertSee('إجابات الاستبيانات المسجلة')->assertDontSee('بانتظار المراجعة');
    $this->get('/admin/projects/create')->assertOk()->assertDontSee('حالة المشروع');
    $this->get('/admin/projects')->assertOk()->assertDontSee('قيد المراجعة');
    $this->get('/admin/question-contexts/create')->assertOk()->assertDontSee('حالة المشروع');
});
