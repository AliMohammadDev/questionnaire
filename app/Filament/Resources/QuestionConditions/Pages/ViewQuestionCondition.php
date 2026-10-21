<?php

namespace App\Filament\Resources\QuestionConditions\Pages;

use App\Filament\Resources\QuestionConditions\QuestionConditionResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewQuestionCondition extends ViewRecord
{
    protected static string $resource = QuestionConditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')->label('رجوع')->icon('heroicon-o-arrow-right')->color('gray')
                ->url(static::getResource()::getUrl('index')),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
