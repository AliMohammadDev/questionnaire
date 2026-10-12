<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Projects\Schemas\ProjectForm;
use App\Filament\Support\ProjectQuestionnaire;
use App\Models\Project;
use App\Models\Question;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Computed;

class CreateProject extends CreateRecord
{
    use HasWizard {
        getWizardComponent as protected makeBaseWizardComponent;
    }

    protected static string $resource = ProjectResource::class;

    protected static bool $canCreateAnother = false;

    protected ?bool $hasDatabaseTransactions = true;

    /** @return array<Step> */
    public function getSteps(): array
    {
        return [
            Step::make('بيانات المشروع')->schema(ProjectForm::components()),
            Step::make('الاستبيان')->description('أجب عن الأسئلة المناسبة للقسم والتصنيف والنوع والنمط المختار.')
                ->schema(fn (): array => ProjectQuestionnaire::components($this->questionnaireQuestions)),
        ];
    }

    public function getWizardComponent(): Component
    {
        return $this->makeBaseWizardComponent()
            ->nextAction(fn (Action $action): Action => $action->label('الاستبيان'))
            ->previousAction(fn (Action $action): Action => $action->label('الرجوع لبيانات المشروع'));
    }

    /** @return Collection<int, Question> */
    #[Computed]
    public function questionnaireQuestions(): Collection
    {
        return ProjectQuestionnaire::questions($this->data ?? []);
    }

    /** @param array<string, mixed> $data */
    protected function handleRecordCreation(array $data): Model
    {
        $answers = $data['questionnaire'] ?? [];
        unset($data['questionnaire']);
        $project = Project::create($data);
        ProjectQuestionnaire::saveAnswers($project, ProjectQuestionnaire::questions($data), $answers);

        return $project;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'تم حفظ المشروع وإجابات الاستبيان';
    }
}
