<?php

namespace App\Filament\Resources\QuestionSections;

use App\Filament\Resources\QuestionSections\Pages\CreateQuestionSection;
use App\Filament\Resources\QuestionSections\Pages\EditQuestionSection;
use App\Filament\Resources\QuestionSections\Pages\ListQuestionSections;
use App\Filament\Resources\QuestionSections\Pages\ViewQuestionSection;
use App\Filament\Resources\QuestionSections\Schemas\QuestionSectionForm;
use App\Filament\Resources\QuestionSections\Schemas\QuestionSectionInfolist;
use App\Filament\Resources\QuestionSections\Tables\QuestionSectionsTable;
use App\Models\QuestionSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QuestionSectionResource extends Resource
{
    protected static ?string $model = QuestionSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'قسم أسئلة';

    protected static ?string $pluralModelLabel = 'أقسام الأسئلة';

    protected static string|\UnitEnum|null $navigationGroup = 'إعداد الاستبيان';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return QuestionSectionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return QuestionSectionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuestionSectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\QuestionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuestionSections::route('/'),
            'create' => CreateQuestionSection::route('/create'),
            'view' => ViewQuestionSection::route('/{record}'),
            'edit' => EditQuestionSection::route('/{record}/edit'),
        ];
    }
}
