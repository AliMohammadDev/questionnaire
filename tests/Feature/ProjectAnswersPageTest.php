<?php

use App\Models\Answer;
use App\Models\Project;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('shows each question and its answer including unanswered applicable questions and saved historical answers', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $project = Project::factory()->create();
    $answered = Answer::factory()->for($project)->create(['value_text' => 'إجابة خاصة بالمشروع']);
    $unanswered = Question::factory()->create(['title' => 'سؤال لم تتم الإجابة عليه']);
    $unanswered->contexts()->create(['department_id' => $project->department_id]);
    $otherAnswer = Answer::factory()->create(['value_text' => 'إجابة مشروع آخر لا تظهر']);

    $this->get('/admin/projects/'.$project->id)->assertOk()
        ->assertSee('أسئلة المشروع وإجاباتها')->assertSee($answered->question->title)
        ->assertSee('إجابة خاصة بالمشروع')->assertSee($unanswered->title)
        ->assertSee('لم تتم الإجابة')->assertDontSee($otherAnswer->value_text)
        ->assertDontSee('fi-resource-relation-manager', false);
});

it('renders each answer value without losing zero or false', function (array $values, string $expected): void {
    $this->actingAs(User::factory()->administrator()->create());
    $answer = Answer::factory()->create(['value_text' => null] + $values);

    $this->get('/admin/projects/'.$answer->project_id)->assertOk()->assertSee($expected);
})->with([
    [['value_number' => 0], '0.0000'],
    [['value_boolean' => false], 'لا'],
    [['value_boolean' => true], 'نعم'],
    [['value_date' => '2026-10-12'], '2026/10/12'],
    [['value_time' => '10:30:00'], '10:30:00'],
    [['value_json' => ['المطبخ' => ['خشب', 'رخام']]], 'المطبخ: خشب، رخام'],
]);

it('shows selected options and downloadable answer attachments', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    Storage::fake('public');
    $answer = Answer::factory()->create(['value_text' => null]);
    $option = QuestionOption::factory()->for($answer->question)->create(['label' => 'خيار مختار']);
    $answer->options()->attach($option);
    $media = $answer->addMedia(UploadedFile::fake()->create('project-answer.pdf', 10, 'application/pdf'))->toMediaCollection('attachments');

    $this->get('/admin/projects/'.$answer->project_id)->assertOk()
        ->assertSee('خيار مختار')->assertSee('project-answer.pdf')->assertSee($media->getUrl());
});

it('does not expose answers to a project viewer without answer permissions', function (): void {
    $this->seed(RolesAndPermissionsSeeder::class);
    $user = User::factory()->create()->assignRole('panel_user');
    $user->givePermissionTo(['ViewAny:Project', 'View:Project']);
    $this->actingAs($user);
    $answer = Answer::factory()->create(['value_text' => 'إجابة محمية']);

    $this->get('/admin/projects/'.$answer->project_id)->assertOk()->assertDontSee('إجابة محمية')->assertDontSee('أسئلة المشروع وإجاباتها');
});
