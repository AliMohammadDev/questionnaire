<?php

namespace App\Filament\Resources\Departments\RelationManagers;

use App\Filament\Resources\ProjectCategories\ProjectCategoryResource;
use App\Filament\Support\RelatedRecordsRelationManager;

class ProjectCategoriesRelationManager extends RelatedRecordsRelationManager
{
    protected static string $relationship = 'projectCategories';

    protected static ?string $relatedResource = ProjectCategoryResource::class;

    protected static ?string $title = 'التصنيفات التابعة';
}
