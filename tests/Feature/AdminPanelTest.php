<?php

use App\Filament\Resources\Answers\Pages\CreateAnswer;
use App\Filament\Resources\Departments\Pages\CreateDepartment;
use App\Filament\Resources\Departments\Pages\EditDepartment;
use App\Filament\Resources\Departments\Pages\ListDepartments;
use App\Filament\Resources\Departments\Pages\ViewDepartment;
use App\Filament\Resources\DesignStyles\Pages\CreateDesignStyle;
use App\Filament\Resources\ProjectCategories\Pages\CreateProjectCategory;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\Answer;
use App\Models\AnswerOption;
use App\Models\AnswerType;
use App\Models\Client;
use App\Models\Department;
use App\Models\DesignStyle;
use App\Models\Media;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectType;
use App\Models\Question;
use App\Models\QuestionCondition;
use App\Models\QuestionContext;
use App\Models\QuestionOption;
use App\Models\QuestionSection;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('redirects guests to the administrator login', function (): void {
    $this->get('/admin')->assertRedirect('/admin/login');
});

it('allows administrators to access the admin panel', function (): void {
    $this->actingAs(User::factory()->administrator()->create());

    $this->get('/admin')->assertOk();
});

it('renders the Arabic login page', function (): void {
    $this->get('/admin/login')->assertOk()->assertSee('dir="rtl"', false);
});

it('renders resource lists and create forms for administrators', function (string $path): void {
    $this->actingAs(User::factory()->administrator()->create());

    $this->get('/admin/'.$path)->assertOk();
})->with([
    'dashboard' => '',
    'departments' => 'departments', 'new department' => 'departments/create',
    'categories' => 'project-categories', 'new category' => 'project-categories/create',
    'types' => 'project-types', 'new type' => 'project-types/create',
    'styles' => 'design-styles', 'new style' => 'design-styles/create',
    'sections' => 'question-sections', 'new section' => 'question-sections/create',
    'answer types' => 'answer-types', 'new answer type' => 'answer-types/create',
    'questions' => 'questions', 'new question' => 'questions/create',
    'options' => 'question-options', 'new option' => 'question-options/create',
    'contexts' => 'question-contexts', 'new context' => 'question-contexts/create',
    'conditions' => 'question-conditions', 'new condition' => 'question-conditions/create',
    'clients' => 'clients', 'new client' => 'clients/create',
    'projects' => 'projects', 'new project' => 'projects/create',
    'answers' => 'answers', 'new answer' => 'answers/create',
    'answer options' => 'answer-options', 'new answer option' => 'answer-options/create',
    'media' => 'media',
    'users' => 'users', 'new user' => 'users/create',
]);

it('creates a department through the admin form', function (): void {
    $this->actingAs(User::factory()->administrator()->create());

    Livewire::test(CreateDepartment::class)
        ->fillForm(['name' => 'الهندسة المعمارية', 'is_active' => true, 'sort_order' => 1])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('departments', ['name' => 'الهندسة المعمارية', 'sort_order' => 1]);
});

it('rejects incomplete department data', function (): void {
    $this->actingAs(User::factory()->administrator()->create());

    Livewire::test(CreateDepartment::class)->fillForm(['name' => ''])->call('create')
        ->assertHasFormErrors(['name' => 'required']);

    $this->assertDatabaseCount('departments', 0);
});

it('creates categories without a short name', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $department = Department::factory()->create();

    Livewire::test(CreateProjectCategory::class)
        ->fillForm(['department_id' => $department->id, 'name' => 'سكني'])
        ->call('create')->assertHasNoFormErrors();

    $this->assertDatabaseHas('project_categories', ['department_id' => $department->id, 'name' => 'سكني']);
});

it('rejects a project category from another department', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $category = ProjectCategory::factory()->create();
    $department = Department::factory()->create();
    $client = Client::factory()->create();
    $type = ProjectType::factory()->for($category)->create();

    Livewire::test(CreateProject::class)
        ->fillForm(['client_id' => $client->id, 'project_code' => 'PRJ-TEST', 'department_id' => $department->id, 'project_category_id' => $category->id, 'project_type_id' => $type->id])
        ->call('create')->assertHasFormErrors(['project_category_id']);

    $this->assertDatabaseCount('projects', 0);
});

it('creates an answer and saves its selected option', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $option = QuestionOption::factory()->create();
    $project = Project::factory()->create();

    Livewire::test(CreateAnswer::class)
        ->fillForm(['project_id' => $project->id, 'question_id' => $option->question_id, 'options' => [$option->id]])
        ->call('create')->assertHasNoFormErrors();

    $answer = Answer::where('project_id', $project->id)->sole();
    $this->assertDatabaseHas('answer_options', ['answer_id' => $answer->id, 'question_option_id' => $option->id]);
});

it('rejects an answer option belonging to a different question', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $type = AnswerType::factory()->singleChoice()->create();
    $question = Question::factory()->for($type)->create();
    $otherQuestion = Question::factory()->for($type)->create();
    $option = QuestionOption::factory()->for($otherQuestion)->create();
    $project = Project::factory()->create();

    Livewire::test(CreateAnswer::class)
        ->fillForm(['project_id' => $project->id, 'question_id' => $question->id, 'options' => [$option->id]])
        ->call('create')->assertHasFormErrors(['options']);

    $this->assertDatabaseCount('answers', 0);
});

it('uploads a design style cover through Spatie', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    Storage::fake('public');
    $image = UploadedFile::fake()->image('cover.jpg');

    Livewire::test(CreateDesignStyle::class)
        ->fillForm(['name' => 'مودرن', 'cover' => [$image]])
        ->call('create')->assertHasNoFormErrors();

    $style = DesignStyle::where('name', 'مودرن')->sole();
    $media = $style->getFirstMedia('cover');
    expect($media)->toBeInstanceOf(Media::class);
    Storage::disk('public')->assertExists($media->getPathRelativeToRoot());
});

it('creates a user with a hashed password', function (): void {
    $this->actingAs(User::factory()->administrator()->create());

    Livewire::test(CreateUser::class)
        ->fillForm(['name' => 'New User', 'email' => 'new@example.test', 'password' => 'long-secure-password', 'roles' => [Role::findByName('panel_user')->id]])
        ->call('create')->assertHasNoFormErrors();

    $user = User::where('email', 'new@example.test')->sole();
    expect($user->canAccessPanel(Filament::getPanel('admin')))->toBeTrue();
    expect(Hash::check('long-secure-password', $user->password))->toBeTrue();
});

it('keeps the user password when the edit field is empty', function (): void {
    $admin = User::factory()->administrator()->create();
    $this->actingAs($admin);
    $password = $admin->password;

    Livewire::test(EditUser::class, ['record' => $admin->id])
        ->fillForm(['name' => 'Updated', 'password' => ''])->call('save')->assertHasNoFormErrors();

    expect($admin->fresh()->password)->toBe($password);
});

it('renders edit forms with existing records and relationships', function (string $model, string $path): void {
    $this->actingAs(User::factory()->administrator()->create());
    $record = $model::factory()->create();

    $this->get('/admin/'.$path.'/'.$record->id.'/edit')->assertOk();
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

it('allows registered users to access admin forms', function (): void {
    $this->actingAs(User::factory()->administrator()->create());

    Livewire::test(CreateDepartment::class)->assertOk();
});

it('prevents deleting a department with project categories', function (): void {
    $admin = User::factory()->administrator()->create();
    $category = ProjectCategory::factory()->create();

    expect(Gate::forUser($admin)->allows('delete', $category->department))->toBeFalse();
});

it('deletes an unused department from its edit page', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $department = Department::factory()->create();

    Livewire::test(EditDepartment::class, ['record' => $department->id])
        ->callAction('delete')->assertHasNoActionErrors();

    $this->assertModelMissing($department);
});

it('forbids creating raw media records', function (): void {
    $this->actingAs(User::factory()->administrator()->create());

    $this->get('/admin/media/create')->assertNotFound();
});

it('creates an administrator through the setup command', function (): void {
    $this->artisan('app:create-admin')
        ->expectsQuestion('Administrator name', 'Administrator')
        ->expectsQuestion('Administrator email', 'admin@example.test')
        ->expectsQuestion('Password (at least 12 characters)', 'a-secure-admin-password')
        ->assertSuccessful();

    $user = User::where('email', 'admin@example.test')->sole();
    expect($user->canAccessPanel(Filament::getPanel('admin')))->toBeTrue();
    expect(Hash::check('a-secure-admin-password', $user->password))->toBeTrue();
});

it('rejects multiple selections for a single-choice answer', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $type = AnswerType::factory()->singleChoice()->create();
    $question = Question::factory()->for($type)->create();
    $options = QuestionOption::factory()->for($question)->count(2)->create();
    $project = Project::factory()->create();

    Livewire::test(CreateAnswer::class)
        ->fillForm(['project_id' => $project->id, 'question_id' => $question->id, 'options' => $options->modelKeys()])
        ->call('create')->assertHasFormErrors(['options']);

    $this->assertDatabaseCount('answers', 0);
});

it('renders media edit pages for Spatie records', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    Storage::fake('public');
    $style = DesignStyle::factory()->create();
    $media = $style->addMedia(UploadedFile::fake()->image('cover.jpg'))->toMediaCollection('cover');

    $this->get('/admin/media/'.$media->id.'/edit')->assertOk();
});

it('seeds the administrator without resetting its password on subsequent runs', function (): void {
    $this->seed(AdminUserSeeder::class);
    $user = User::where('email', 'admin@gmail.com')->sole();
    $password = $user->password;

    $this->seed(AdminUserSeeder::class);

    expect($user->fresh()->password)->toBe($password);
    expect($user->canAccessPanel(Filament::getPanel('admin')))->toBeTrue();
    $this->assertDatabaseCount('users', 1);
});

it('deletes an unused department from the department list', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $department = Department::factory()->create();

    Livewire::test(ListDepartments::class)
        ->callTableAction('delete', $department)->assertHasNoActionErrors();

    $this->assertModelMissing($department);
});

it('hides department deletion in the list when categories depend on it', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $category = ProjectCategory::factory()->create();

    Livewire::test(ListDepartments::class)
        ->assertTableActionHidden('delete', $category->department);

    $this->assertModelExists($category->department);
});

it('deletes an unused department from its detail page', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $department = Department::factory()->create();

    Livewire::test(ViewDepartment::class, ['record' => $department->id])
        ->callAction('delete')->assertHasNoActionErrors();

    $this->assertModelMissing($department);
});
