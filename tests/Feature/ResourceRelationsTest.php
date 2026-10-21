<?php

use App\Filament\Resources\Answers\Pages\ViewAnswer;
use App\Filament\Resources\AnswerTypes\Pages\ViewAnswerType;
use App\Filament\Resources\Clients\Pages\ViewClient;
use App\Filament\Resources\Departments\Pages\ViewDepartment;
use App\Filament\Resources\Departments\RelationManagers\ProjectCategoriesRelationManager;
use App\Filament\Resources\Departments\RelationManagers\ProjectsRelationManager;
use App\Filament\Resources\Departments\RelationManagers\QuestionContextsRelationManager;
use App\Filament\Resources\DesignStyles\Pages\ViewDesignStyle;
use App\Filament\Resources\ProjectCategories\Pages\ViewProjectCategory;
use App\Filament\Resources\ProjectCategories\RelationManagers\ProjectTypesRelationManager;
use App\Filament\Resources\Projects\Pages\ViewProject;
use App\Filament\Resources\ProjectTypes\Pages\ViewProjectType;
use App\Filament\Resources\QuestionOptions\Pages\ViewQuestionOption;
use App\Filament\Resources\QuestionOptions\RelationManagers\AnswerOptionsRelationManager;
use App\Filament\Resources\QuestionOptions\RelationManagers\QuestionConditionsRelationManager;
use App\Filament\Resources\Questions\Pages\ViewQuestion;
use App\Filament\Resources\Questions\RelationManagers\AnswersRelationManager;
use App\Filament\Resources\Questions\RelationManagers\ConditionsRelationManager;
use App\Filament\Resources\Questions\RelationManagers\ContextsRelationManager;
use App\Filament\Resources\Questions\RelationManagers\DependentConditionsRelationManager;
use App\Filament\Resources\Questions\RelationManagers\OptionsRelationManager;
use App\Filament\Resources\QuestionSections\Pages\ViewQuestionSection;
use App\Filament\Resources\QuestionSections\RelationManagers\QuestionsRelationManager;
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
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('lists only records belonging to the displayed owner', function (string $ownerModel, string $relatedModel, string $relationship, string $manager, string $page): void {
    $this->actingAs(User::factory()->administrator()->create());
    $owner = $ownerModel::factory()->create();
    $related = $relatedModel::factory()->create();
    $owner->{$relationship}()->save($related);
    $unrelated = $relatedModel::factory()->create();
    $otherOwner = $ownerModel::factory()->create($ownerModel === AnswerType::class ? ['code' => 'other-answer-type'] : []);
    $otherOwner->{$relationship}()->save($unrelated);

    Livewire::test($manager, ['ownerRecord' => $owner, 'pageClass' => $page])
        ->loadTable()->assertSuccessful()
        ->assertCanSeeTableRecords([$related])
        ->assertCanNotSeeTableRecords([$unrelated])
        ->assertTableActionExists('view')
        ->assertTableActionHasUrl('view', $manager::getRelatedResource()::getUrl('view', ['record' => $related]), $related)
        ->assertTableActionHidden('edit', $related)
        ->assertTableActionHidden('delete', $related);
})->with([
    'Question options' => [Question::class, QuestionOption::class, 'options', OptionsRelationManager::class, ViewQuestion::class],
    'Question contexts' => [Question::class, QuestionContext::class, 'contexts', ContextsRelationManager::class, ViewQuestion::class],
    'Question conditions' => [Question::class, QuestionCondition::class, 'conditions', ConditionsRelationManager::class, ViewQuestion::class],
    'Question dependentConditions' => [Question::class, QuestionCondition::class, 'dependentConditions', DependentConditionsRelationManager::class, ViewQuestion::class],
    'Question answers' => [Question::class, Answer::class, 'answers', AnswersRelationManager::class, ViewQuestion::class],
    'Department projectCategories' => [Department::class, ProjectCategory::class, 'projectCategories', ProjectCategoriesRelationManager::class, ViewDepartment::class],
    'Department questionContexts' => [Department::class, QuestionContext::class, 'questionContexts', QuestionContextsRelationManager::class, ViewDepartment::class],
    'Department projects' => [Department::class, Project::class, 'projects', ProjectsRelationManager::class, ViewDepartment::class],
    'QuestionSection questions' => [QuestionSection::class, Question::class, 'questions', QuestionsRelationManager::class, ViewQuestionSection::class],
    'Project answers' => [Project::class, Answer::class, 'answers', App\Filament\Resources\Projects\RelationManagers\AnswersRelationManager::class, ViewProject::class],
    'AnswerType questions' => [AnswerType::class, Question::class, 'questions', App\Filament\Resources\AnswerTypes\RelationManagers\QuestionsRelationManager::class, ViewAnswerType::class],
    'ProjectType questionContexts' => [ProjectType::class, QuestionContext::class, 'questionContexts', App\Filament\Resources\ProjectTypes\RelationManagers\QuestionContextsRelationManager::class, ViewProjectType::class],
    'ProjectType projects' => [ProjectType::class, Project::class, 'projects', App\Filament\Resources\ProjectTypes\RelationManagers\ProjectsRelationManager::class, ViewProjectType::class],
    'ProjectCategory projectTypes' => [ProjectCategory::class, ProjectType::class, 'projectTypes', ProjectTypesRelationManager::class, ViewProjectCategory::class],
    'ProjectCategory questionContexts' => [ProjectCategory::class, QuestionContext::class, 'questionContexts', App\Filament\Resources\ProjectCategories\RelationManagers\QuestionContextsRelationManager::class, ViewProjectCategory::class],
    'ProjectCategory projects' => [ProjectCategory::class, Project::class, 'projects', App\Filament\Resources\ProjectCategories\RelationManagers\ProjectsRelationManager::class, ViewProjectCategory::class],
    'QuestionOption questionConditions' => [QuestionOption::class, QuestionCondition::class, 'questionConditions', QuestionConditionsRelationManager::class, ViewQuestionOption::class],
    'QuestionOption answerOptions' => [QuestionOption::class, AnswerOption::class, 'answerOptions', AnswerOptionsRelationManager::class, ViewQuestionOption::class],
    'Client projects' => [Client::class, Project::class, 'projects', App\Filament\Resources\Clients\RelationManagers\ProjectsRelationManager::class, ViewClient::class],
    'Answer answerOptions' => [Answer::class, AnswerOption::class, 'answerOptions', App\Filament\Resources\Answers\RelationManagers\AnswerOptionsRelationManager::class, ViewAnswer::class],
    'DesignStyle questionContexts' => [DesignStyle::class, QuestionContext::class, 'questionContexts', App\Filament\Resources\DesignStyles\RelationManagers\QuestionContextsRelationManager::class, ViewDesignStyle::class],
    'DesignStyle projects' => [DesignStyle::class, Project::class, 'projects', App\Filament\Resources\DesignStyles\RelationManagers\ProjectsRelationManager::class, ViewDesignStyle::class],
]);

it('lists only attached records for many to many relationships', function (string $ownerModel, string $relatedModel, string $relationship, string $manager, string $page): void {
    $this->actingAs(User::factory()->administrator()->create());
    $owner = $ownerModel::factory()->create();
    $related = $relatedModel::factory()->create();
    $owner->{$relationship}()->attach($related);
    $unrelated = $relatedModel::factory()->create();

    Livewire::test($manager, ['ownerRecord' => $owner, 'pageClass' => $page])
        ->loadTable()->assertSuccessful()
        ->assertCanSeeTableRecords([$related])
        ->assertCanNotSeeTableRecords([$unrelated]);
})->with([
    'QuestionOption answers' => [QuestionOption::class, Answer::class, 'answers', App\Filament\Resources\QuestionOptions\RelationManagers\AnswersRelationManager::class, ViewQuestionOption::class],
    'Answer options' => [Answer::class, QuestionOption::class, 'options', App\Filament\Resources\Answers\RelationManagers\OptionsRelationManager::class, ViewAnswer::class],
]);

it('shows relationship tabs on a department detail page', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $department = Department::factory()->create();

    $this->get('/admin/departments/'.$department->id)->assertOk()
        ->assertSee('التصنيفات التابعة')->assertSee('المشاريع')->assertSee('سياقات الأسئلة');
});
