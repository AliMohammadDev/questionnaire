<?php

namespace App\Filament\Resources\QuestionConditions\Pages;

use App\Filament\Resources\QuestionConditions\QuestionConditionResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQuestionCondition extends EditRecord
{
    protected static string $resource = QuestionConditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')->label('رجوع')->icon('heroicon-o-arrow-right')->color('gray')
                ->url(static::getResource()::getUrl('index')),
            DeleteAction::make(),
        ];
    }
}
