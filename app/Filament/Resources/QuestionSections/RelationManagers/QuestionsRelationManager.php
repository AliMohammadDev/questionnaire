<?php

namespace App\Filament\Resources\QuestionSections\RelationManagers;

use App\Filament\Resources\Questions\QuestionResource;
use App\Filament\Support\RelatedRecordsRelationManager;

class QuestionsRelationManager extends RelatedRecordsRelationManager
{
    protected static string $relationship = 'questions';

    protected static ?string $relatedResource = QuestionResource::class;

    protected static ?string $title = 'الأسئلة';
}
