<?php

namespace App\Filament\Resources\Departments\RelationManagers;

use App\Filament\Resources\QuestionContexts\QuestionContextResource;
use App\Filament\Support\RelatedRecordsRelationManager;

class QuestionContextsRelationManager extends RelatedRecordsRelationManager
{
    protected static string $relationship = 'questionContexts';

    protected static ?string $relatedResource = QuestionContextResource::class;

    protected static ?string $title = 'سياقات الأسئلة';
}
