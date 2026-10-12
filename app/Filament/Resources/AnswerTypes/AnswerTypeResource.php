<?php

namespace App\Filament\Resources\AnswerTypes;

use App\Filament\Resources\AnswerTypes\Pages\CreateAnswerType;
use App\Filament\Resources\AnswerTypes\Pages\EditAnswerType;
use App\Filament\Resources\AnswerTypes\Pages\ListAnswerTypes;
use App\Filament\Resources\AnswerTypes\Pages\ViewAnswerType;
use App\Filament\Resources\AnswerTypes\Schemas\AnswerTypeForm;
use App\Filament\Resources\AnswerTypes\Schemas\AnswerTypeInfolist;
use App\Filament\Resources\AnswerTypes\Tables\AnswerTypesTable;
use App\Models\AnswerType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AnswerTypeResource extends Resource
{
    protected static ?string $model = AnswerType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static ?string $modelLabel = 'نوع إجابة';

    protected static ?string $pluralModelLabel = 'أنواع الإجابات';

    protected static string|\UnitEnum|null $navigationGroup = 'إعداد الاستبيان';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AnswerTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AnswerTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnswerTypesTable::configure($table);
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
            'index' => ListAnswerTypes::route('/'),
            'create' => CreateAnswerType::route('/create'),
            'view' => ViewAnswerType::route('/{record}'),
            'edit' => EditAnswerType::route('/{record}/edit'),
        ];
    }
}
