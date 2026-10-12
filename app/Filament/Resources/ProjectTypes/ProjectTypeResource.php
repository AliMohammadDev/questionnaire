<?php

namespace App\Filament\Resources\ProjectTypes;

use App\Filament\Resources\ProjectTypes\Pages\CreateProjectType;
use App\Filament\Resources\ProjectTypes\Pages\EditProjectType;
use App\Filament\Resources\ProjectTypes\Pages\ListProjectTypes;
use App\Filament\Resources\ProjectTypes\Pages\ViewProjectType;
use App\Filament\Resources\ProjectTypes\Schemas\ProjectTypeForm;
use App\Filament\Resources\ProjectTypes\Schemas\ProjectTypeInfolist;
use App\Filament\Resources\ProjectTypes\Tables\ProjectTypesTable;
use App\Models\ProjectType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProjectTypeResource extends Resource
{
    protected static ?string $model = ProjectType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHomeModern;

    protected static ?string $modelLabel = 'نوع مشروع';

    protected static ?string $pluralModelLabel = 'أنواع المشاريع';

    protected static string|\UnitEnum|null $navigationGroup = 'إعدادات المشاريع';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ProjectTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProjectTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\QuestionContextsRelationManager::class,
            RelationManagers\ProjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjectTypes::route('/'),
            'create' => CreateProjectType::route('/create'),
            'view' => ViewProjectType::route('/{record}'),
            'edit' => EditProjectType::route('/{record}/edit'),
        ];
    }
}
