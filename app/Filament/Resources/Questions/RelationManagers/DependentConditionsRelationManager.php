<?php

namespace App\Filament\Resources\Questions\RelationManagers;

use App\Filament\Resources\QuestionConditions\QuestionConditionResource;
use App\Filament\Support\RelatedRecordsRelationManager;

class DependentConditionsRelationManager extends RelatedRecordsRelationManager
{
    protected static string $relationship = 'dependentConditions';

    protected static ?string $relatedResource = QuestionConditionResource::class;

    protected static ?string $title = 'الشروط المعتمدة على هذا السؤال';
}
