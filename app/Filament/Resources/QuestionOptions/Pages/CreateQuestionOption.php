<?php

namespace App\Filament\Resources\QuestionOptions\Pages;

use App\Filament\Resources\QuestionOptions\QuestionOptionResource;
use App\Filament\Resources\QuestionOptions\Schemas\QuestionOptionForm;
use App\Models\Question;
use Filament\Forms\Components\Repeater;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class CreateQuestionOption extends CreateRecord
{
    protected static string $resource = QuestionOptionResource::class;

    protected static ?string $title = 'إضافة خيارات للسؤال';

    protected ?bool $hasDatabaseTransactions = true;

    public function form(Schema $schema): Schema
    {
        return QuestionOptionForm::configureCreate($schema);
    }

    /**
     * @param  array{question_id: int, options: array<int, array{label: string, is_active: bool}>}  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $question = Question::query()->lockForUpdate()->findOrFail($data['question_id']);
        $sortOrder = $question->options()->max('sort_order');
        $sortOrder = $sortOrder === null ? 0 : $sortOrder + 1;
        /** @var Repeater $repeater */
        $repeater = $this->form->getComponent('options');
        $items = array_values($repeater->getItems());
        $firstOption = null;

        foreach ($data['options'] as $index => $optionData) {
            $option = $question->options()->create([
                'label' => $optionData['label'],
                'value' => $optionData['label'],
                'is_active' => $optionData['is_active'],
                'sort_order' => $sortOrder + $index,
            ]);
            $items[$index]->model($option)->saveRelationships();
            $firstOption ??= $option;
        }

        return $firstOption;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'تمت إضافة خيارات السؤال بنجاح';
    }
}
