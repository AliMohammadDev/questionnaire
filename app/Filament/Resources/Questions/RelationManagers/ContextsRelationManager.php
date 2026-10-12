<?php

namespace App\Filament\Resources\Questions\RelationManagers;

use App\Filament\Resources\QuestionContexts\QuestionContextResource;
use App\Filament\Support\RelatedRecordsRelationManager;

class ContextsRelationManager extends RelatedRecordsRelationManager
{
    protected static string $relationship = 'contexts';

    protected static ?string $relatedResource = QuestionContextResource::class;

    protected static ?string $title = 'سياقات ظهور السؤال';
}
