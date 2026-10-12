<?php

namespace App\Filament\Resources\ProjectCategories;

use App\Filament\Resources\ProjectCategories\Pages\CreateProjectCategory;
use App\Filament\Resources\ProjectCategories\Pages\EditProjectCategory;
use App\Filament\Resources\ProjectCategories\Pages\ListProjectCategories;
use App\Filament\Resources\ProjectCategories\Pages\ViewProjectCategory;
use App\Filament\Resources\ProjectCategories\Schemas\ProjectCategoryForm;
use App\Filament\Resources\ProjectCategories\Schemas\ProjectCategoryInfolist;
use App\Filament\Resources\ProjectCategories\Tables\ProjectCategoriesTable;
use App\Models\ProjectCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProjectCategoryResource extends Resource
{
    protected static ?string $model = ProjectCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?string $modelLabel = 'تصنيف مشروع';

    protected static ?string $pluralModelLabel = 'تصنيفات المشاريع';

    protected static string|\UnitEnum|null $navigationGroup = 'إعدادات المشاريع';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ProjectCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProjectCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProjectTypesRelationManager::class,
            RelationManagers\QuestionContextsRelationManager::class,
            RelationManagers\ProjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjectCategories::route('/'),
            'create' => CreateProjectCategory::route('/create'),
            'view' => ViewProjectCategory::route('/{record}'),
            'edit' => EditProjectCategory::route('/{record}/edit'),
        ];
    }
}
