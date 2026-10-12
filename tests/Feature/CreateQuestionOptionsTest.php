<?php

use App\Filament\Resources\QuestionOptions\Pages\CreateQuestionOption;
use App\Models\AnswerType;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('creates several options for one question in their displayed order', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $question = Question::factory()->for(AnswerType::factory()->singleChoice())->create();
    QuestionOption::factory()->for($question)->create(['sort_order' => 4]);

    Livewire::test(CreateQuestionOption::class)->fillForm([
        'question_id' => $question->id,
        'options' => [
            ['label' => 'خشب', 'is_active' => true],
            ['label' => 'رخام', 'is_active' => false],
        ],
    ])->call('create')->assertHasNoFormErrors()->assertRedirect('/admin/question-options');

    $this->assertDatabaseHas('question_options', ['question_id' => $question->id, 'label' => 'خشب', 'value' => 'خشب', 'sort_order' => 5, 'is_active' => true]);
    $this->assertDatabaseHas('question_options', ['question_id' => $question->id, 'label' => 'رخام', 'sort_order' => 6, 'is_active' => false]);
    $this->assertDatabaseCount('question_options', 3);
});

it('rejects the entire batch when an option has no label', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $question = Question::factory()->for(AnswerType::factory()->singleChoice())->create();

    Livewire::test(CreateQuestionOption::class)->fillForm([
        'question_id' => $question->id,
        'options' => [['label' => 'خشب', 'is_active' => true], ['label' => '', 'is_active' => true]],
    ])->call('create')->assertHasFormErrors();

    $this->assertDatabaseCount('question_options', 0);
});

it('rejects questions whose answer type does not support options', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $question = Question::factory()->for(AnswerType::factory())->create();

    Livewire::test(CreateQuestionOption::class)->fillForm([
        'question_id' => $question->id,
        'options' => [['label' => 'خشب', 'is_active' => true]],
    ])->call('create')->assertHasFormErrors(['question_id']);

    $this->assertDatabaseCount('question_options', 0);
});

it('requires at least one option', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $question = Question::factory()->for(AnswerType::factory()->singleChoice())->create();

    Livewire::test(CreateQuestionOption::class)->fillForm(['question_id' => $question->id, 'options' => []])
        ->call('create')->assertHasFormErrors(['options']);

    $this->assertDatabaseCount('question_options', 0);
});

it('saves each image on its own option', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    Storage::fake('public');
    $question = Question::factory()->for(AnswerType::factory()->singleChoice())->create();

    $woodImage = UploadedFile::fake()->image('wood.jpg', 20, 30);
    $marbleImage = UploadedFile::fake()->image('marble.jpg', 40, 50);
    $woodContents = file_get_contents($woodImage->getRealPath());
    $marbleContents = file_get_contents($marbleImage->getRealPath());

    Livewire::test(CreateQuestionOption::class)->fillForm([
        'question_id' => $question->id,
        'options' => [
            ['label' => 'خشب', 'is_active' => true, 'image' => [$woodImage]],
            ['label' => 'رخام', 'is_active' => true, 'image' => [$marbleImage]],
        ],
    ])->call('create')->assertHasNoFormErrors();

    $wood = $question->options()->where('label', 'خشب')->sole()->getFirstMedia('image');
    $marble = $question->options()->where('label', 'رخام')->sole()->getFirstMedia('image');
    expect($wood)->not->toBeNull();
    expect($marble)->not->toBeNull();
    expect(Storage::disk('public')->get($wood->getPathRelativeToRoot()))->toBe($woodContents);
    expect(Storage::disk('public')->get($marble->getPathRelativeToRoot()))->toBe($marbleContents);
    $this->assertDatabaseCount('media', 2);
    Storage::disk('public')->assertExists($wood->getPathRelativeToRoot());
    Storage::disk('public')->assertExists($marble->getPathRelativeToRoot());
});
