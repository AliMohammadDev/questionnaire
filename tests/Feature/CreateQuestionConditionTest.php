<?php

use App\Filament\Resources\QuestionConditions\Pages\CreateQuestionCondition;
use App\Models\AnswerType;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
  Filament::setCurrentPanel(Filament::getPanel('admin'));
  $this->actingAs(User::factory()->administrator()->create());
});

it('saves a selected option condition without a comparison value', function (): void {
  $question = Question::factory()->create();
  $source = Question::factory()->for(AnswerType::factory()->singleChoice())->create();
  $option = QuestionOption::factory()->for($source)->create();

  Livewire::test(CreateQuestionCondition::class)->fillForm([
    'question_id' => $question->id,
    'depends_on_question_id' => $source->id,
    'operator' => 'selected',
    'question_option_id' => $option->id,
    'value' => null,
    'logic_type' => 'AND',
  ])->call('create')->assertHasNoFormErrors();

  $this->assertDatabaseHas('question_conditions', [
    'question_id' => $question->id,
    'depends_on_question_id' => $source->id,
    'question_option_id' => $option->id,
    'operator' => 'selected',
    'value' => null,
  ]);
});

it('rejects an option belonging to another question', function (): void {
  $question = Question::factory()->create();
  $source = Question::factory()->for(AnswerType::factory()->singleChoice())->create();
  $option = QuestionOption::factory()->create();

  Livewire::test(CreateQuestionCondition::class)->fillForm([
    'question_id' => $question->id,
    'depends_on_question_id' => $source->id,
    'operator' => 'selected',
    'question_option_id' => $option->id,
    'logic_type' => 'AND',
  ])->call('create')->assertHasFormErrors(['question_option_id']);

  $this->assertDatabaseCount('question_conditions', 0);
});
