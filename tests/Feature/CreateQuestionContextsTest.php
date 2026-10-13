<?php

use App\Filament\Resources\QuestionContexts\Pages\CreateQuestionContext;
use App\Models\DesignStyle;
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
    $this->actingAs(User::factory()->administrator()->create());
});

it('creates compatible contexts for multiple categories types and styles without duplicates', function (): void {
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
    $this->assertDatabaseCount('question_contexts', 4);
    foreach ([$first, $second] as $type) {
        foreach ($styles as $style) {
            $this->assertDatabaseHas('question_contexts', ['question_id' => $question->id, 'project_category_id' => $type->project_category_id, 'project_type_id' => $type->id, 'design_style_id' => $style->id]);
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
