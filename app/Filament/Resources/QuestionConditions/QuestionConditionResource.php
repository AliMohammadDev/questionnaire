<?php

namespace App\Filament\Resources\QuestionConditions;

use App\Filament\Resources\QuestionConditions\Pages\CreateQuestionCondition;
use App\Filament\Resources\QuestionConditions\Pages\EditQuestionCondition;
use App\Filament\Resources\QuestionConditions\Pages\ListQuestionConditions;
use App\Filament\Resources\QuestionConditions\Pages\ViewQuestionCondition;
use App\Filament\Resources\QuestionConditions\Schemas\QuestionConditionForm;
use App\Filament\Resources\QuestionConditions\Schemas\QuestionConditionInfolist;
use App\Filament\Resources\QuestionConditions\Tables\QuestionConditionsTable;
use App\Models\QuestionCondition;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QuestionConditionResource extends Resource
{
    protected static ?string $model = QuestionCondition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static ?string $modelLabel = 'شرط ظهور';

    protected static ?string $pluralModelLabel = 'شروط ظهور الأسئلة';

    protected static string|\UnitEnum|null $navigationGroup = 'إعداد الاستبيان';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return QuestionConditionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return QuestionConditionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuestionConditionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuestionConditions::route('/'),
            'create' => CreateQuestionCondition::route('/create'),
            'view' => ViewQuestionCondition::route('/{record}'),
            'edit' => EditQuestionCondition::route('/{record}/edit'),
        ];
    }
}
