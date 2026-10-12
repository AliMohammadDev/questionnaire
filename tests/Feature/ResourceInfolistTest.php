<?php

use App\Models\Answer;
use App\Models\AnswerOption;
use App\Models\AnswerType;
use App\Models\Client;
use App\Models\Department;
use App\Models\DesignStyle;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectType;
use App\Models\Question;
use App\Models\QuestionCondition;
use App\Models\QuestionContext;
use App\Models\QuestionOption;
use App\Models\QuestionSection;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('renders record details for every resource', function (string $model, string $path): void {
    $this->actingAs(User::factory()->administrator()->create());
    $record = $model::factory()->create();

    $this->get('/admin/'.$path.'/'.$record->id)->assertOk()->assertSee('معلومات السجل');
})->with([
    [Department::class, 'departments'],
    [ProjectCategory::class, 'project-categories'],
    [ProjectType::class, 'project-types'],
    [DesignStyle::class, 'design-styles'],
    [QuestionSection::class, 'question-sections'],
    [AnswerType::class, 'answer-types'],
    [Question::class, 'questions'],
    [QuestionOption::class, 'question-options'],
    [QuestionContext::class, 'question-contexts'],
    [QuestionCondition::class, 'question-conditions'],
    [Client::class, 'clients'],
    [Project::class, 'projects'],
    [Answer::class, 'answers'],
    [AnswerOption::class, 'answer-options'],
    [User::class, 'users'],
]);

it('shows project details and related client names', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $project = Project::factory()->create();

    $this->get('/admin/projects/'.$project->id)->assertOk()
        ->assertSee($project->project_code)->assertSee($project->client->name);
});

it('shows false and zero answers without treating them as empty', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $answer = Answer::factory()->create(['value_number' => 0, 'value_boolean' => false]);

    $this->get('/admin/answers/'.$answer->id)->assertOk()->assertSee('0.0000');
});

it('shows media and attachment links', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    Storage::fake('public');
    $project = Project::factory()->create();
    $media = $project->addMedia(UploadedFile::fake()->create('brief.pdf', 10, 'application/pdf'))->toMediaCollection('attachments');

    $this->get('/admin/projects/'.$project->id)->assertOk()->assertSee('brief.pdf')->assertSee($media->getUrl());
    $this->get('/admin/media/'.$media->id)->assertOk()->assertSee('brief.pdf')->assertSee($media->getUrl());
});

it('keeps password hashes off user detail pages', function (): void {
    $user = User::factory()->administrator()->create();
    $this->actingAs($user);

    $this->get('/admin/users/'.$user->id)->assertOk()->assertSee($user->email)->assertDontSee($user->password);
});

it('redirects guests away from record details', function (): void {
    $department = Department::factory()->create();

    $this->get('/admin/departments/'.$department->id)->assertRedirect('/admin/login');
});

it('returns 404 for a missing record', function (): void {
    $this->actingAs(User::factory()->administrator()->create());

    $this->get('/admin/departments/999999')->assertNotFound();
});

it('shows grid answer rows and their selected values', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $answer = Answer::factory()->create(['value_json' => ['المطبخ' => ['خشب', 'رخام'], 'المدخل' => 'حجر']]);

    $this->get('/admin/answers/'.$answer->id)->assertOk()
        ->assertSee('المطبخ')->assertSee('خشب، رخام')->assertSee('المدخل')->assertSee('حجر');
});

it('shows an uploaded cover on design style detail pages', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    Storage::fake('public');
    $style = DesignStyle::factory()->create();
    $media = $style->addMedia(UploadedFile::fake()->image('cover.jpg'))->toMediaCollection('cover');

    $this->get('/admin/design-styles/'.$style->id)->assertOk()->assertSee($media->getUrl());
    $this->get('/admin/media/'.$media->id)->assertOk()->assertSee($media->getUrl());
});
