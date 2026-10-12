<?php

use App\Filament\Resources\Questions\Pages\CreateQuestion;
use App\Models\AnswerType;
use App\Models\QuestionSection;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('creates and selects a section from the question form then saves the question in it', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $answerType = AnswerType::factory()->create();

    $component = Livewire::test(CreateQuestion::class)
        ->fillForm(['title' => 'ما المتطلبات الإضافية؟'])
        ->callAction(TestAction::make('createOption')->schemaComponent('question_section_id'), data: [
            'name' => 'متطلبات إضافية', 'description' => 'أسئلة خاصة بالمشروع', 'is_active' => true, 'sort_order' => 5,
        ])->assertHasNoActionErrors();

    $section = QuestionSection::where('name', 'متطلبات إضافية')->sole();
    expect($section->description)->toBe('أسئلة خاصة بالمشروع');
    expect($section->sort_order)->toBe(5);
    $component->assertSchemaStateSet(['question_section_id' => $section->id, 'title' => 'ما المتطلبات الإضافية؟'])
        ->fillForm(['answer_type_id' => $answerType->id])
        ->call('create')->assertHasNoFormErrors();

    $this->assertDatabaseHas('questions', ['title' => 'ما المتطلبات الإضافية؟', 'question_section_id' => $section->id]);
});

it('requires a name before creating a section inline', function (): void {
    $this->actingAs(User::factory()->administrator()->create());

    Livewire::test(CreateQuestion::class)
        ->callAction(TestAction::make('createOption')->schemaComponent('question_section_id'), data: ['name' => ''])
        ->assertHasActionErrors(['name' => 'required']);

    $this->assertDatabaseCount('question_sections', 0);
});

it('hides section creation for question editors without section creation permission', function (): void {
    $this->seed(RolesAndPermissionsSeeder::class);
    $user = User::factory()->create()->assignRole('panel_user');
    $user->givePermissionTo(['ViewAny:Question', 'Create:Question']);
    $this->actingAs($user);

    Livewire::test(CreateQuestion::class)
        ->assertActionHidden(TestAction::make('createOption')->schemaComponent('question_section_id'));
    $this->get('/admin/question-sections/create')->assertForbidden();
    $this->assertDatabaseCount('question_sections', 0);
});
