<?php

use App\Models\AnswerType;
use App\Models\Project;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    $this->project = Project::factory()->create();
});

it('stores text answers and updates them without duplicates', function (): void {
    $question = Question::factory()->create(['is_required' => true]);
    $question->contexts()->create([]);
    $url = route('api.projects.answers.store', $this->project);
    $this->postJson($url, ['answers' => [$question->id => 'دمشق']])->assertOk();
    $this->postJson($url, ['answers' => [$question->id => 'حلب']])->assertOk();
    $this->assertDatabaseCount('answers', 1);
    $this->assertDatabaseHas('answers', ['project_id' => $this->project->id, 'question_id' => $question->id, 'value_text' => 'حلب']);
});

it('validates required answers and numeric limits before saving any answer', function (): void {
    $text = Question::factory()->create(['is_required' => true]);
    $text->contexts()->create([]);
    $number = Question::factory()->for(AnswerType::factory()->state(['code' => 'number']))->create(['validation_rules' => ['min' => 5, 'max' => 500]]);
    $number->contexts()->create([]);
    $url = route('api.projects.answers.store', $this->project);
    $this->postJson($url, ['answers' => [$number->id => 501]])->assertUnprocessable()->assertJsonValidationErrors(['answers.'.$text->id, 'answers.'.$number->id]);
    $this->assertDatabaseCount('answers', 0);
    $this->postJson($url, ['answers' => [$text->id => 'دمشق', $number->id => 40]])->assertOk();
    $this->assertDatabaseHas('answers', ['question_id' => $number->id, 'value_number' => 40]);
});

it('stores only active options belonging to the answered question', function (): void {
    $question = Question::factory()->for(AnswerType::factory()->multipleChoice())->create();
    $question->contexts()->create([]);
    $option = QuestionOption::factory()->for($question)->create();
    $inactive = QuestionOption::factory()->for($question)->create(['is_active' => false]);
    $other = QuestionOption::factory()->create();
    $url = route('api.projects.answers.store', $this->project);
    foreach ([$inactive->id, $other->id] as $id) {
        $this->postJson($url, ['answers' => [$question->id => [$id]]])->assertUnprocessable();
    }
    $this->postJson($url, ['answers' => [$question->id => [$option->id]]])->assertOk();
    $this->assertDatabaseHas('answer_options', ['question_option_id' => $option->id]);
});

it('enforces conditional required questions and removes hidden previous answers', function (): void {
    $source = Question::factory()->for(AnswerType::factory()->state(['code' => 'boolean']))->create(['is_required' => true]);
    $source->contexts()->create([]);
    $dependent = Question::factory()->create(['is_required' => true]);
    $dependent->contexts()->create([]);
    $dependent->conditions()->create(['depends_on_question_id' => $source->id, 'operator' => 'equals', 'value' => '1', 'logic_type' => 'AND']);
    $url = route('api.projects.answers.store', $this->project);
    $this->postJson($url, ['answers' => [$source->id => 1]])->assertUnprocessable()->assertJsonValidationErrors(['answers.'.$dependent->id]);
    $this->postJson($url, ['answers' => [$source->id => 1, $dependent->id => '40']])->assertOk();
    $this->postJson($url, ['answers' => [$source->id => 0, $dependent->id => 'stale']])->assertOk();
    $this->assertDatabaseMissing('answers', ['question_id' => $dependent->id]);
    $this->assertDatabaseHas('answers', ['question_id' => $source->id, 'value_boolean' => false]);
});

it('rejects questions outside the project questionnaire', function (): void {
    $question = Question::factory()->create();
    $this->postJson(route('api.projects.answers.store', $this->project), ['answers' => [$question->id => 'wrong']])->assertUnprocessable();
    $this->assertDatabaseCount('answers', 0);
});

it('stores valid grid answers and rejects invalid columns', function (): void {
    $question = Question::factory()->for(AnswerType::factory()->state(['code' => 'checkbox_grid']))->create(['is_required' => true, 'validation_rules' => ['rows' => ['الغرفة'], 'columns' => ['صغير', 'كبير']]]);
    $question->contexts()->create([]);
    $url = route('api.projects.answers.store', $this->project);
    $this->postJson($url, ['answers' => [$question->id => ['الغرفة' => ['invalid']]]])->assertUnprocessable();
    $this->postJson($url, ['answers' => [$question->id => ['الغرفة' => ['كبير']]]])->assertOk();
    expect($this->project->answers()->sole()->value_json)->toBe(['الغرفة' => ['كبير']]);
});

it('stores uploaded answer attachments and rejects file paths', function (): void {
    Storage::fake('public');
    $question = Question::factory()->for(AnswerType::factory()->state(['code' => 'file']))->create();
    $question->contexts()->create([]);
    $url = route('api.projects.answers.store', $this->project);
    $this->postJson($url, ['answers' => [$question->id => ['/etc/passwd']]])->assertUnprocessable();
    $this->post($url, ['answers' => [$question->id => [UploadedFile::fake()->image('answer.jpg')]]], ['Accept' => 'application/json'])->assertOk();
    expect($this->project->answers()->sole()->getMedia('attachments'))->toHaveCount(1);
});

it('rejects missing answers and nonexistent projects', function (): void {
    $this->postJson(route('api.projects.answers.store', $this->project), [])->assertUnprocessable();
    $this->postJson(route('api.projects.answers.store', ['project' => 99999]), ['answers' => []])->assertNotFound();
});

it('stores scalar answers in their corresponding columns', function (string $type, mixed $value, string $column): void {
    $question = Question::factory()->for(AnswerType::factory()->state(['code' => $type]))->create();
    $question->contexts()->create([]);
    $this->postJson(route('api.projects.answers.store', $this->project), ['answers' => [$question->id => $value]])->assertOk();
    $answer = $this->project->answers()->sole();
    expect($answer->question_id)->toBe($question->id);
    expect($type === 'date' ? $answer->value_date->format('Y-m-d') : $answer->{$column})->toBe($value);
})->with([
    'date' => ['date', '2026-10-13', 'value_date'],
    'time' => ['time', '14:30', 'value_time'],
    'boolean false' => ['boolean', false, 'value_boolean'],
]);

it('clears omitted optional choice answers and their option links', function (): void {
    $question = Question::factory()->for(AnswerType::factory()->singleChoice())->create();
    $question->contexts()->create([]);
    $option = QuestionOption::factory()->for($question)->create();
    $url = route('api.projects.answers.store', $this->project);
    $this->postJson($url, ['answers' => [$question->id => $option->id]])->assertOk();
    $this->postJson($url, ['answers' => []])->assertOk();
    $this->assertDatabaseCount('answers', 0);
    $this->assertDatabaseCount('answer_options', 0);
});
