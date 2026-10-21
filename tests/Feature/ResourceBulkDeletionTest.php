<?php

use App\Filament\Resources\Questions\Pages\ListQuestions;
use App\Filament\Resources\QuestionSections\Pages\ListQuestionSections;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Question;
use App\Models\QuestionSection;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('deletes selected questions and preserves unselected questions', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $questions = Question::factory()->count(3)->create();
    $unselected = Question::factory()->create();

    Livewire::test(ListQuestions::class)->callTableBulkAction('delete', $questions)
        ->assertHasNoActionErrors()->assertNotified('تم حذف السجلات المحددة بنجاح');

    foreach ($questions as $question) {
        $this->assertModelMissing($question);
    }
    $this->assertModelExists($unselected);
});

it('deletes unused sections but preserves sections with questions and reports the partial result', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $unused = QuestionSection::factory()->create();
    $question = Question::factory()->create();

    Livewire::test(ListQuestionSections::class)
        ->callTableBulkAction('delete', [$unused->id, $question->question_section_id])
        ->assertNotified('تم حذف 1 من أصل 2 سجلاً محدداً.');

    $this->assertModelMissing($unused);
    $this->assertModelExists($question->questionSection);
    $this->assertModelExists($question);
});

it('keeps questions with contexts when all selected questions are blocked', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $question = Question::factory()->create();
    $context = $question->contexts()->create([]);

    Livewire::test(ListQuestions::class)->callTableBulkAction('delete', [$question->id])
        ->assertNotified('تم حذف 0 من أصل 1 سجلاً محدداً.');

    $this->assertModelExists($question);
    $this->assertModelExists($context);
});

it('preserves the signed in user during bulk user deletion', function (): void {
    $admin = User::factory()->administrator()->create();
    $this->actingAs($admin);
    $other = User::factory()->create();

    Livewire::test(ListUsers::class)->callTableBulkAction('delete', [$admin->id, $other->id])
        ->assertNotified('تم حذف 1 من أصل 2 سجلاً محدداً.');

    $this->assertModelExists($admin);
    $this->assertModelMissing($other);
});

it('hides bulk deletion without bulk delete permission', function (): void {
    $this->seed(RolesAndPermissionsSeeder::class);
    $user = User::factory()->create()->assignRole('panel_user');
    $user->givePermissionTo(['ViewAny:Question', 'Delete:Question']);
    $this->actingAs($user);

    Livewire::test(ListQuestions::class)->assertTableBulkActionHidden('delete');
});

it('checks individual delete permission even when bulk deletion is permitted', function (): void {
    $this->seed(RolesAndPermissionsSeeder::class);
    $user = User::factory()->create()->assignRole('panel_user');
    $user->givePermissionTo(['ViewAny:Question', 'DeleteAny:Question']);
    $this->actingAs($user);
    $question = Question::factory()->create();

    Livewire::test(ListQuestions::class)->callTableBulkAction('delete', [$question->id])
        ->assertNotified('تم حذف 0 من أصل 1 سجلاً محدداً.');

    $this->assertModelExists($question);
});
