<?php

use App\Models\AnswerType;
use App\Models\DesignStyle;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionSection;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('returns general questions and only contexts matching all project selections', function (string $dimension): void {
    $project = Project::factory()->create(['design_style_id' => DesignStyle::factory()->create()->id]);
    $context = $project->only(['department_id', 'project_category_id', 'project_type_id', 'design_style_id']);
    $otherType = ProjectType::factory()->create();
    $other = [
        'department_id' => $otherType->projectCategory->department_id,
        'project_category_id' => $otherType->project_category_id,
        'project_type_id' => $otherType->id,
        'design_style_id' => DesignStyle::factory()->create()->id,
    ];
    $general = Question::factory()->create(['sort_order' => 0]);
    $general->contexts()->create([]);
    $matching = Question::factory()->create(['sort_order' => 1]);
    $matching->contexts()->create($context);
    $unrelated = Question::factory()->create();
    $unrelated->contexts()->create(array_replace($context, [$dimension => $other[$dimension]]));
    $withoutContext = Question::factory()->create();
    $inactive = Question::factory()->create(['is_active' => false]);
    $inactive->contexts()->create([]);

    $this->getJson(route('api.projects.questionnaire.index', $project))->assertOk()
        ->assertJsonPath('project_id', $project->id)->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.id', $general->id)->assertJsonPath('data.1.id', $matching->id);
})->with(['department_id', 'project_category_id', 'project_type_id', 'design_style_id']);

it('does not combine dimensions from separate context rows', function (): void {
    $project = Project::factory()->create();
    $otherType = ProjectType::factory()->create();
    $question = Question::factory()->create();
    $question->contexts()->create(['department_id' => $project->department_id, 'project_type_id' => $otherType->id]);
    $question->contexts()->create(['department_id' => $otherType->projectCategory->department_id, 'project_type_id' => $project->project_type_id]);

    $this->getJson(route('api.projects.questionnaire.index', $project))->assertOk()->assertJsonCount(0, 'data');
});

it('returns field details, active ordered options and visibility conditions', function (): void {
    $project = Project::factory()->create();
    $source = Question::factory()->for(AnswerType::factory()->singleChoice())->create(['sort_order' => 0]);
    $source->contexts()->create([]);
    $last = QuestionOption::factory()->for($source)->create(['sort_order' => 5]);
    $first = QuestionOption::factory()->for($source)->create(['sort_order' => 1]);
    QuestionOption::factory()->for($source)->create(['is_active' => false]);
    $dependent = Question::factory()->create(['sort_order' => 1, 'is_required' => true, 'validation_rules' => ['min' => 1]]);
    $dependent->contexts()->create([]);
    $dependent->conditions()->create([
        'depends_on_question_id' => $source->id,
        'operator' => 'selected',
        'question_option_id' => $first->id,
        'logic_type' => 'AND',
    ]);

    $this->getJson(route('api.projects.questionnaire.index', $project))->assertOk()
        ->assertJsonPath('data.0.answer_type.code', 'single_choice')
        ->assertJsonPath('data.0.section.id', $source->question_section_id)
        ->assertJsonCount(2, 'data.0.options')
        ->assertJsonPath('data.0.options.0.id', $first->id)
        ->assertJsonPath('data.0.options.1.id', $last->id)
        ->assertJsonPath('data.1.is_required', true)
        ->assertJsonPath('data.1.validation_rules.min', 1)
        ->assertJsonPath('data.1.conditions.0.depends_on_question_id', $source->id)
        ->assertJsonPath('data.1.conditions.0.depends_on_question.id', $source->id)
        ->assertJsonPath('data.1.conditions.0.depends_on_question.title', $source->title)
        ->assertJsonPath('data.1.conditions.0.question_option_id', $first->id);
});

it('excludes inactive question sections and answer types', function (): void {
    $project = Project::factory()->create();
    $inactiveSection = Question::factory()->for(QuestionSection::factory()->state(['is_active' => false]))->create();
    $inactiveSection->contexts()->create([]);
    $inactiveType = Question::factory()->for(AnswerType::factory()->state(['code' => 'inactive', 'is_active' => false]))->create();
    $inactiveType->contexts()->create([]);

    $this->getJson(route('api.projects.questionnaire.index', $project))->assertOk()->assertJsonCount(0, 'data');
});

it('returns an empty questionnaire when no questions apply', function (): void {
    $project = Project::factory()->create();

    $this->getJson(route('api.projects.questionnaire.index', $project))
        ->assertOk()->assertExactJson(['data' => [], 'project_id' => $project->id]);
});

it('returns json not found for a nonexistent project', function (): void {
    $this->getJson(route('api.projects.questionnaire.index', ['project' => 99999]))->assertNotFound()->assertHeader('content-type', 'application/json');
});
