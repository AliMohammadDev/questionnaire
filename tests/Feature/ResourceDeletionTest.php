<?php

use App\Filament\Resources\AnswerOptions\Pages\ListAnswerOptions;
use App\Filament\Resources\Answers\Pages\ListAnswers;
use App\Filament\Resources\AnswerTypes\Pages\ListAnswerTypes;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Filament\Resources\Departments\Pages\EditDepartment;
use App\Filament\Resources\Departments\Pages\ListDepartments;
use App\Filament\Resources\DesignStyles\Pages\ListDesignStyles;
use App\Filament\Resources\Media\Pages\ListMedia;
use App\Filament\Resources\ProjectCategories\Pages\ListProjectCategories;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\ProjectTypes\Pages\ListProjectTypes;
use App\Filament\Resources\QuestionConditions\Pages\ListQuestionConditions;
use App\Filament\Resources\QuestionContexts\Pages\ListQuestionContexts;
use App\Filament\Resources\QuestionOptions\Pages\ListQuestionOptions;
use App\Filament\Resources\Questions\Pages\EditQuestion;
use App\Filament\Resources\Questions\Pages\ListQuestions;
use App\Filament\Resources\Questions\Pages\ViewQuestion;
use App\Filament\Resources\QuestionSections\Pages\ListQuestionSections;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Filament\Resources\Users\Pages\ListUsers;
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
use Database\Seeders\RolesAndPermissionsSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('deletes an unused record from each resource list', function (string $model, string $page): void {
    $this->actingAs(User::factory()->administrator()->create());
    $record = $model::factory()->create();

    Livewire::test($page)->callTableAction('delete', $record)
        ->assertHasNoActionErrors()->assertNotified('تم الحذف بنجاح');

    $this->assertModelMissing($record);
})->with([
    'departments' => [Department::class, ListDepartments::class],
    'categories' => [ProjectCategory::class, ListProjectCategories::class],
    'types' => [ProjectType::class, ListProjectTypes::class],
    'styles' => [DesignStyle::class, ListDesignStyles::class],
    'sections' => [QuestionSection::class, ListQuestionSections::class],
    'answer types' => [AnswerType::class, ListAnswerTypes::class],
    'questions' => [Question::class, ListQuestions::class],
    'options' => [QuestionOption::class, ListQuestionOptions::class],
    'contexts' => [QuestionContext::class, ListQuestionContexts::class],
    'conditions' => [QuestionCondition::class, ListQuestionConditions::class],
    'clients' => [Client::class, ListClients::class],
    'projects' => [Project::class, ListProjects::class],
    'answers' => [Answer::class, ListAnswers::class],
    'answer options' => [AnswerOption::class, ListAnswerOptions::class],
    'users' => [User::class, ListUsers::class],
]);

it('explains blocked section deletion and keeps its questions', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $question = Question::factory()->create();

    Livewire::test(ListQuestionSections::class)
        ->assertTableActionVisible('delete', $question->questionSection)
        ->callTableAction('delete', $question->questionSection)
        ->assertNotified('لا يمكن الحذف لوجود أسئلة مرتبطة بهذا السجل. احذفها أو عدّل ارتباطها أولاً. لن تُحذف تلقائياً.');

    $this->assertModelExists($question->questionSection);
    $this->assertModelExists($question);
});

it('explains blocked question deletion on list edit and detail pages', function (string $page, bool $isList): void {
    $this->actingAs(User::factory()->administrator()->create());
    $question = Question::factory()->create();
    $context = $question->contexts()->create([]);
    $message = 'لا يمكن الحذف لوجود سياقات السؤال مرتبطة بهذا السجل. احذفها أو عدّل ارتباطها أولاً. لن تُحذف تلقائياً.';

    $component = Livewire::test($page, $isList ? [] : ['record' => $question->id]);
    if ($isList) {
        $component->callTableAction('delete', $question);
    } else {
        $component->callAction('delete');
    }
    $component->assertNotified($message);

    $this->assertModelExists($question);
    $this->assertModelExists($context);
})->with([
    'list' => [ListQuestions::class, true],
    'edit' => [EditQuestion::class, false],
    'detail' => [ViewQuestion::class, false],
]);

it('keeps deletion hidden without delete permission', function (): void {
    $this->seed(RolesAndPermissionsSeeder::class);
    $user = User::factory()->create()->assignRole('panel_user');
    $user->givePermissionTo('ViewAny:Question');
    $this->actingAs($user);
    $question = Question::factory()->create();

    Livewire::test(ListQuestions::class)->assertTableActionHidden('delete', $question);

    expect(Gate::forUser($user)->allows('delete', $question))->toBeFalse();
    $this->assertModelExists($question);
});

it('blocks deletion of a user linked to a project', function (): void {
    $admin = User::factory()->administrator()->create();
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    expect(Gate::forUser($admin)->inspect('delete', $user)->message())
        ->toBe('لا يمكن الحذف لوجود مشاريع مرتبطة بهذا السجل. احذفها أو عدّل ارتباطها أولاً. لن تُحذف تلقائياً.');
    $this->assertModelExists($project);
});

it('blocks deletion of roles still assigned to users', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $role = Role::create(['name' => 'دور مستخدم', 'guard_name' => 'web']);
    $user = User::factory()->create()->assignRole($role);

    Livewire::test(ListRoles::class)->callTableAction('delete', $role)
        ->assertNotified('لا يمكن الحذف لوجود مستخدمون مسند إليهم الدور مرتبطة بهذا السجل. احذفها أو عدّل ارتباطها أولاً. لن تُحذف تلقائياً.');

    $this->assertModelExists($role);
    expect($user->fresh()->hasRole($role))->toBeTrue();
});

it('warns that attached files will be deleted before confirming deletion', function (): void {
    Storage::fake('public');
    $this->actingAs(User::factory()->administrator()->create());
    $department = Department::factory()->create();
    $media = $department->addMedia(UploadedFile::fake()->image('department.jpg'))->toMediaCollection('gallery');
    $path = $media->getPathRelativeToRoot();

    $component = Livewire::test(EditDepartment::class, ['record' => $department->id])
        ->mountAction('delete')
        ->assertActionMounted('delete');
    expect($component->instance()->getMountedAction()->getModalDescription())
        ->toBe('سيتم حذف السجل وجميع ملفاته وصوره المرفقة نهائياً. هل تريد المتابعة؟');
    $this->assertModelExists($department);
    Storage::disk('public')->assertExists($path);

    $component->callMountedAction()->assertHasNoActionErrors();

    $this->assertModelMissing($department);
    $this->assertModelMissing($media);
    Storage::disk('public')->assertMissing($path);
});

it('deletes an unused role after confirmation', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $role = Role::create(['name' => 'دور غير مستخدم', 'guard_name' => 'web']);

    Livewire::test(ListRoles::class)->callTableAction('delete', $role)
        ->assertHasNoActionErrors()->assertNotified('تم الحذف بنجاح');

    $this->assertModelMissing($role);
});

it('deletes a media record and its file from the media list', function (): void {
    Storage::fake('public');
    $this->actingAs(User::factory()->administrator()->create());
    $department = Department::factory()->create();
    $media = $department->addMedia(UploadedFile::fake()->image('attachment.jpg'))->toMediaCollection('gallery');
    $path = $media->getPathRelativeToRoot();

    Livewire::test(ListMedia::class)
        ->callTableAction('delete', $media)->assertHasNoActionErrors();

    $this->assertModelMissing($media);
    $this->assertModelExists($department);
    Storage::disk('public')->assertMissing($path);
});
