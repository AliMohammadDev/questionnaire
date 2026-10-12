<?php

use App\Filament\Resources\Answers\Pages\EditAnswer;
use App\Filament\Resources\Questions\Pages\EditQuestion;
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
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\MediaSeeder;
use Database\Seeders\ProjectSeeder;
use Database\Seeders\QuestionSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

it('seeds a complete questionnaire with consistent project paths and selected options', function (): void {
    Storage::fake('public');

    $this->seed(DatabaseSeeder::class);

    $this->assertDatabaseCount('departments', 3);
    $this->assertDatabaseCount('project_categories', 9);
    $this->assertDatabaseCount('project_types', 27);
    $this->assertDatabaseCount('design_styles', 11);
    $this->assertDatabaseCount('answer_types', 15);
    $this->assertDatabaseCount('projects', 27);
    foreach (Project::with(['projectType.projectCategory', 'answers.answerOptions.questionOption'])->get() as $project) {
        expect($project->projectType->project_category_id)->toBe($project->project_category_id);
        expect($project->projectType->projectCategory->department_id)->toBe($project->department_id);
        foreach ($project->answers as $answer) {
            foreach ($answer->answerOptions as $option) {
                expect($option->questionOption->question_id)->toBe($answer->question_id);
            }
        }
    }
    foreach (Media::all() as $media) {
        Storage::disk('public')->assertExists($media->getPathRelativeToRoot());
        expect($media->getCustomProperty('demo'))->toBeTrue();
    }
    foreach ([Department::class, ProjectCategory::class, ProjectType::class, DesignStyle::class] as $model) {
        foreach ($model::all() as $record) {
            expect($record->getMedia($model === ProjectType::class ? 'image' : 'gallery'))->toHaveCount(2);
        }
    }
    foreach (QuestionOption::all() as $option) {
        expect($option->getMedia('image'))->toHaveCount(1);
    }
    expect(AnswerType::where('code', 'multiple_choice')->sole()->allows_multiple)->toBeTrue();
    expect(AnswerType::where('code', 'single_choice')->sole()->allows_multiple)->toBeFalse();
});

it('preserves user uploaded catalog pictures when seeding media again', function (): void {
    Storage::fake('public');
    $this->seed(DatabaseSeeder::class);
    $department = DepartmentSeeder::department('architecture');
    $uploaded = $department->addMedia(UploadedFile::fake()->image('my-picture.jpg'))->toMediaCollection('gallery');
    $pictureIds = $department->fresh()->getMedia('gallery')->modelKeys();

    $this->seed(MediaSeeder::class);

    expect($department->fresh()->getMedia('gallery')->modelKeys())->toBe($pictureIds);
    $this->assertModelExists($uploaded);
    Storage::disk('public')->assertExists($uploaded->getPathRelativeToRoot());
});

it('seeds different questions for residential commercial and industrial projects', function (): void {
    Storage::fake('public');

    $this->seed(DatabaseSeeder::class);

    $residential = Project::where('project_code', 'DEMO-ARCH-RES')->sole();
    $commercial = Project::where('project_code', 'DEMO-ARCH-COM')->sole();
    $industrial = Project::where('project_code', 'DEMO-ARCH-IND')->sole();
    expect($residential->answers()->where('question_id', QuestionSeeder::question('bedrooms')->id)->exists())->toBeTrue();
    expect($commercial->answers()->where('question_id', QuestionSeeder::question('restaurant-kitchen')->id)->exists())->toBeTrue();
    expect($industrial->answers()->where('question_id', QuestionSeeder::question('industrial-machines')->id)->exists())->toBeTrue();
    expect($industrial->answers()->where('question_id', QuestionSeeder::question('bedrooms')->id)->exists())->toBeFalse();
    $condition = QuestionSeeder::question('pool-area')->conditions()->sole();
    expect($condition->depends_on_question_id)->toBe(QuestionSeeder::question('pool')->id);
});

it('can seed twice without duplicates or overwriting edited catalog data', function (): void {
    Storage::fake('public');
    $this->seed(DatabaseSeeder::class);
    $models = [Department::class, ProjectCategory::class, ProjectType::class, DesignStyle::class, AnswerType::class, QuestionSection::class, Question::class, QuestionOption::class, QuestionContext::class, QuestionCondition::class, Client::class, Project::class, Answer::class, AnswerOption::class, Media::class, User::class];
    $counts = array_map(fn (string $model): int => $model::count(), $models);
    DepartmentSeeder::department('architecture')->update(['name' => 'اسم معدل من الإدارة']);
    $editedProject = Project::where('project_code', 'DEMO-ARCH-RES')->sole();
    $editedProject->projectCategory->update(['name' => 'تصنيف معدل', 'sort_order' => 99]);
    $editedProject->projectType->update(['name' => 'نوع معدل', 'sort_order' => 99]);
    $editedProject->designStyle->update(['name' => 'نمط معدل', 'sort_order' => 99]);
    $password = User::where('email', 'admin@gmail.com')->sole()->password;
    $villa = Project::where('project_code', 'DEMO-ARCH-RES')->sole();
    $villa->answers()->where('question_id', QuestionSeeder::question('budget')->id)->update(['value_number' => 25000]);
    $villa->answers()->create(['question_id' => QuestionSeeder::question('pool-area')->id, 'value_number' => 40]);
    QuestionSeeder::question('land-area')->contexts()->create(['department_id' => $villa->department_id]);

    $this->seed(DatabaseSeeder::class);

    expect(array_map(fn (string $model): int => $model::count(), $models))->toBe($counts);
    expect(DepartmentSeeder::department('architecture')->name)->toBe('اسم معدل من الإدارة');
    expect($editedProject->projectCategory->fresh()->name)->toBe('تصنيف معدل');
    expect($editedProject->projectType->fresh()->name)->toBe('نوع معدل');
    expect($editedProject->designStyle->fresh()->name)->toBe('نمط معدل');
    expect(User::where('email', 'admin@gmail.com')->sole()->password)->toBe($password);
    $this->assertDatabaseHas('answers', ['project_id' => $villa->id, 'question_id' => QuestionSeeder::question('budget')->id, 'value_number' => 6500]);
    $this->assertDatabaseMissing('answers', ['project_id' => $villa->id, 'question_id' => QuestionSeeder::question('pool-area')->id]);
    $this->assertDatabaseMissing('question_contexts', ['question_id' => QuestionSeeder::question('land-area')->id, 'department_id' => $villa->department_id, 'project_category_id' => null]);
});

it('renders and saves grid questions and answers from the seeded data', function (): void {
    Storage::fake('public');
    $this->seed(DatabaseSeeder::class);
    $this->actingAs(User::where('email', 'admin@gmail.com')->sole());
    Filament\Facades\Filament::setCurrentPanel(Filament\Facades\Filament::getPanel('admin'));
    $question = QuestionSeeder::question('priorities-grid');
    $answer = Project::where('project_code', 'DEMO-ARCH-RES')->sole()->answers()->where('question_id', $question->id)->sole();

    $this->get('/admin/questions/'.$question->id.'/edit')->assertOk();
    Livewire\Livewire::test(EditQuestion::class, ['record' => $question->id])->call('save')->assertHasNoFormErrors();
    Livewire\Livewire::test(EditAnswer::class, ['record' => $answer->id])
        ->fillForm(['value_json' => ['الجودة' => 'عالية', 'سرعة التسليم' => 'عالية', 'التكلفة' => 'منخفضة']])
        ->call('save')->assertHasNoFormErrors();

    expect($answer->fresh()->value_json['سرعة التسليم'])->toBe('عالية');
});

it('provides a dedicated question and a realistic demo project for every project type', function (): void {
    Storage::fake('public');

    $this->seed(DatabaseSeeder::class);

    $typeQuestionIds = collect(DepartmentSeeder::catalog())->flatMap(fn (array $department): array => $department['categories'])
        ->flatMap(fn (array $category): array => $category['types'])->keys()
        ->mapWithKeys(fn (string $key): array => [$key => QuestionSeeder::question('type-'.$key)->id]);
    foreach (Project::with(['projectType', 'designStyle', 'answers'])->get() as $project) {
        expect($project->answers->pluck('question_id')->intersect($typeQuestionIds->values())->values()->all())
            ->toBe([$typeQuestionIds[ProjectSeeder::scenarios()[$project->project_code]['type']]]);
        $styleQuestion = QuestionSeeder::question('style-'.ProjectSeeder::scenarios()[$project->project_code]['style']);
        expect($project->answers->contains('question_id', $styleQuestion->id))->toBeTrue();
    }
    expect(Project::distinct()->pluck('project_type_id'))->toHaveCount(27);
    expect(Project::distinct()->pluck('design_style_id'))->toHaveCount(11);
});

it('omits hidden and irrelevant answers and uses suitable commercial choices', function (): void {
    Storage::fake('public');

    $this->seed(DatabaseSeeder::class);

    $villa = Project::where('project_code', 'DEMO-ARCH-RES')->sole();
    $apartment = Project::where('project_code', 'DEMO-ARCH-APT')->sole();
    $digitalStore = Project::where('project_code', 'DEMO-WEB-DIGITAL')->sole();
    $warehouse = Project::where('project_code', 'DEMO-ARCH-WAREHOUSE')->sole();
    $retail = Project::where('project_code', 'DEMO-ARCH-RETAIL')->sole();
    $this->assertDatabaseHas('answers', ['project_id' => $villa->id, 'question_id' => QuestionSeeder::question('pool')->id, 'value_boolean' => false]);
    $this->assertDatabaseMissing('answers', ['project_id' => $villa->id, 'question_id' => QuestionSeeder::question('pool-area')->id]);
    $this->assertDatabaseMissing('answers', ['project_id' => $apartment->id, 'question_id' => QuestionSeeder::question('land-area')->id]);
    $this->assertDatabaseMissing('answers', ['project_id' => $digitalStore->id, 'question_id' => QuestionSeeder::question('domain-name')->id]);
    expect($digitalStore->answers()->where('question_id', QuestionSeeder::question('payment-methods')->id)->sole()->options->pluck('value')->all())->toBe(['bank']);
    expect($warehouse->answers()->where('question_id', QuestionSeeder::question('industrial-activity')->id)->sole()->options->pluck('value')->all())->toBe(['storage']);
    expect($retail->answers()->where('question_id', QuestionSeeder::question('commercial-activity')->id)->sole()->options->pluck('value')->all())->toBe(['retail']);
});

it('uses budgets and dates appropriate to the project scope and recorded dates', function (): void {
    Storage::fake('public');

    $this->seed(DatabaseSeeder::class);

    $budgetQuestion = QuestionSeeder::question('budget');
    $this->assertDatabaseHas('answers', ['project_id' => Project::where('project_code', 'DEMO-ARCH-RES')->sole()->id, 'question_id' => $budgetQuestion->id, 'value_number' => 6500]);
    $this->assertDatabaseHas('answers', ['project_id' => Project::where('project_code', 'DEMO-WEB-SHOP')->sole()->id, 'question_id' => $budgetQuestion->id, 'value_number' => 2800]);
    foreach (Project::whereNotNull('completed_at')->get() as $project) {
        $start = $project->answers()->where('question_id', QuestionSeeder::question('start-date')->id)->sole()->value_date;
        expect($project->submitted_at->lessThanOrEqualTo($start))->toBeTrue();
        expect($start->lessThanOrEqualTo($project->completed_at))->toBeTrue();
    }
    foreach (Project::whereNull('submitted_at')->get() as $project) {
        expect($project->submitted_at)->toBeNull();
        expect($project->completed_at)->toBeNull();
    }
});
