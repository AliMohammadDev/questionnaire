<?php

namespace App\Filament\Resources\QuestionContexts\Pages;

use App\Filament\Resources\QuestionContexts\QuestionContextResource;
use App\Filament\Resources\QuestionContexts\Schemas\QuestionContextForm;
use App\Models\ProjectType;
use App\Models\Question;
use App\Models\QuestionContext;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class CreateQuestionContext extends CreateRecord
{
  protected static string $resource = QuestionContextResource::class;

  protected ?bool $hasDatabaseTransactions = true;

  public function form(Schema $schema): Schema
  {
    return QuestionContextForm::configureCreate($schema);
  }

  /** @param array{question_id: int, department_id?: int|null, project_category_ids?: array<int>, project_type_ids?: array<int>, design_style_ids?: array<int>} $data */
  protected function handleRecordCreation(array $data): Model
  {
    Question::query()->whereKey($data['question_id'])->lockForUpdate()->firstOrFail();
    $categories = $data['project_category_ids'] ?: [null];
    $styles = $data['design_style_ids'] ?: [null];
    $types = ProjectType::query()->whereIn('id', $data['project_type_ids'])->get();
    $firstContext = null;

    foreach ($categories as $categoryId) {
      $categoryTypes = $types->filter(fn(ProjectType $type): bool => $categoryId === null || $type->project_category_id == $categoryId)->modelKeys();
      if ($types->isEmpty()) {
        $categoryTypes = [null];
      }
      foreach ($categoryTypes as $typeId) {
        foreach ($styles as $styleId) {
          $context = QuestionContext::firstOrCreate([
            'question_id' => $data['question_id'],
            'department_id' => $data['department_id'] ?? null,
            'project_category_id' => $categoryId,
            'project_type_id' => $typeId,
            'design_style_id' => $styleId,
          ]);
          $firstContext ??= $context;
        }
      }
    }

    return $firstContext;
  }

  protected function getRedirectUrl(): string
  {
    return static::getResource()::getUrl('index');
  }

  protected function getCreatedNotificationTitle(): ?string
  {
    return 'تمت إضافة سياقات السؤال بنجاح';
  }
}
