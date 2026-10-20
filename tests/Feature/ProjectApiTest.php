<?php

use App\Models\DesignStyle;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(LazilyRefreshDatabase::class);

it('creates a project for an existing client with a generated code', function (): void {
    Sanctum::actingAs(User::factory()->create());
    $data = Project::factory()->make(['design_style_id' => DesignStyle::factory()->create()->id, 'notes' => 'مشروع جديد'])
        ->only(['client_id', 'department_id', 'project_category_id', 'project_type_id', 'design_style_id', 'project_name', 'notes']);

    $response = $this->postJson(route('api.projects.store'), $data + ['project_code' => 'CUSTOM', 'completed_at' => '2026-01-01']);
    $response->assertCreated()->assertJsonFragment($data);
    $project = Project::sole();
    expect($response->json('data.id'))->toBe($project->id);
    expect($project->project_code)->toStartWith('PRJ-')->not->toBe('CUSTOM');
    expect($project->completed_at)->toBeNull();
    $this->assertDatabaseHas('projects', $data);
    $this->assertDatabaseCount('clients', 1);
    $this->assertDatabaseCount('answers', 0);

    $this->postJson(route('api.projects.store'), $data)->assertCreated();
    expect(Project::query()->pluck('project_code')->unique())->toHaveCount(2);
});

it('allows creating a project without optional details', function (): void {
    Sanctum::actingAs(User::factory()->create());
    $data = Project::factory()->make()->only(['client_id', 'department_id', 'project_category_id', 'project_type_id']);

    $this->postJson(route('api.projects.store'), $data)->assertCreated()
        ->assertJsonPath('data.design_style_id', null)->assertJsonPath('data.project_name', null);
});

it('rejects missing or nonexistent project references', function (bool $missing): void {
    Sanctum::actingAs(User::factory()->create());
    $data = $missing ? [] : array_fill_keys(['client_id', 'department_id', 'project_category_id', 'project_type_id', 'design_style_id'], 99999);
    $errors = ['client_id', 'department_id', 'project_category_id', 'project_type_id'];
    if (! $missing) {
        $errors[] = 'design_style_id';
    }

    $this->postJson(route('api.projects.store'), $data)->assertUnprocessable()->assertJsonValidationErrors($errors);
    $this->assertDatabaseCount('projects', 0);
})->with([true, false]);

it('rejects catalog selections from a different hierarchy', function (string $dimension): void {
    Sanctum::actingAs(User::factory()->create());
    $data = Project::factory()->make()->only(['client_id', 'department_id', 'project_category_id', 'project_type_id']);
    $other = ProjectType::factory()->create();
    $data[$dimension] = $dimension === 'department_id' ? $other->projectCategory->department_id : $other->id;

    $this->postJson(route('api.projects.store'), $data)->assertUnprocessable()
        ->assertJsonValidationErrors([$dimension === 'department_id' ? 'project_category_id' : 'project_type_id']);
    $this->assertDatabaseCount('projects', 0);
})->with(['department_id', 'project_type_id']);

it('stores additional questions when creating a project', function (): void {
    Sanctum::actingAs(User::factory()->create());
    $data = Project::factory()->make()->only(['client_id', 'department_id', 'project_category_id', 'project_type_id']);
    $questions = [['question' => 'مكان القهوة؟', 'answer' => 'المطبخ', 'notes' => 'قرب النافذة'], ['question' => 'أثاث قديم؟', 'answer' => null, 'notes' => null]];

    $response = $this->postJson(route('api.projects.store'), $data + ['additional_questions' => $questions]);
    $response->assertCreated()->assertJsonPath('data.additional_questions', $questions);
    expect(Project::sole()->additional_questions)->toBe($questions);
});

it('replaces and clears additional questions without changing project details', function (): void {
    $project = Project::factory()->create(['additional_questions' => [['question' => 'قديم', 'answer' => 'جواب']]]);
    $questions = [['question' => 'جديد', 'answer' => 'نعم', 'notes' => 'ملاحظة معدلة'], ['question' => 'بانتظار الإجابة']];
    $route = route('api.projects.additional-questions.update', $project);

    $this->patchJson($route, ['additional_questions' => $questions, 'project_name' => 'تغيير غير مسموح'])
        ->assertOk()->assertJsonPath('data.additional_questions', $questions);
    expect($project->fresh()->additional_questions)->toBe($questions);
    expect($project->fresh()->project_name)->toBe($project->project_name);
    $this->getJson(route('api.projects.questionnaire.index', $project))->assertOk()
        ->assertJsonPath('additional_questions', $questions);

    $this->patchJson($route, ['additional_questions' => []])->assertOk()->assertJsonPath('data.additional_questions', []);
    expect($project->fresh()->additional_questions)->toBe([]);
});

it('rejects malformed additional questions without changing saved values', function (array $payload, string $error): void {
    $project = Project::factory()->create(['additional_questions' => [['question' => 'محفوظ', 'answer' => null]]]);

    $this->patchJson(route('api.projects.additional-questions.update', $project), $payload)
        ->assertUnprocessable()->assertJsonValidationErrors([$error]);
    expect($project->fresh()->additional_questions)->toBe($project->additional_questions);
})->with([
    'missing list' => [[], 'additional_questions'],
    'invalid list' => [['additional_questions' => 'text'], 'additional_questions'],
    'empty question' => [['additional_questions' => [['question' => '']]], 'additional_questions.0.question'],
    'invalid answer' => [['additional_questions' => [['question' => 'سؤال', 'answer' => ['invalid']]]], 'additional_questions.0.answer'],
    'invalid notes' => [['additional_questions' => [['question' => 'سؤال', 'notes' => ['invalid']]]], 'additional_questions.0.notes'],
    'unknown keys' => [['additional_questions' => [['question' => 'سؤال', 'unexpected' => true]]], 'additional_questions.0'],
]);

it('assigns the authenticated creator even when another user id is supplied', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $data = Project::factory()->make()->only(['client_id', 'department_id', 'project_category_id', 'project_type_id']);
    $token = $user->createToken('project-creation');

    $this->withToken($token->plainTextToken)->postJson(route('api.projects.store'), $data + ['user_id' => $otherUser->id])
        ->assertCreated()->assertJsonPath('data.user_id', $user->id);

    $project = Project::sole();
    expect($project->user->is($user))->toBeTrue();
    expect($user->projects()->sole()->is($project))->toBeTrue();
    $this->assertDatabaseHas('projects', ['id' => $project->id, 'user_id' => $user->id]);
});

it('returns 401 when creating a project without a valid token', function (?string $token): void {
    $headers = $token === null ? [] : ['Authorization' => 'Bearer '.$token];

    $this->postJson(route('api.projects.store'), [], $headers)->assertUnauthorized();

    $this->assertDatabaseCount('projects', 0);
})->with([null, 'invalid-token']);
