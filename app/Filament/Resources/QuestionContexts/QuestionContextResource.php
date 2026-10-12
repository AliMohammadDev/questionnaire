<?php

namespace App\Filament\Resources\QuestionContexts;

use App\Filament\Resources\QuestionContexts\Pages\CreateQuestionContext;
use App\Filament\Resources\QuestionContexts\Pages\EditQuestionContext;
use App\Filament\Resources\QuestionContexts\Pages\ListQuestionContexts;
use App\Filament\Resources\QuestionContexts\Pages\ViewQuestionContext;
use App\Filament\Resources\QuestionContexts\Schemas\QuestionContextForm;
use App\Filament\Resources\QuestionContexts\Schemas\QuestionContextInfolist;
use App\Filament\Resources\QuestionContexts\Tables\QuestionContextsTable;
use App\Models\QuestionContext;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QuestionContextResource extends Resource
{
    protected static ?string $model = QuestionContext::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFunnel;

    protected static ?string $modelLabel = 'سياق سؤال';

    protected static ?string $pluralModelLabel = 'سياقات الأسئلة';

    protected static string|\UnitEnum|null $navigationGroup = 'إعداد الاستبيان';

    protected static ?int $navigationSort = 9;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return QuestionContextForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return QuestionContextInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuestionContextsTable::configure($table);
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
            'index' => ListQuestionContexts::route('/'),
            'create' => CreateQuestionContext::route('/create'),
            'view' => ViewQuestionContext::route('/{record}'),
            'edit' => EditQuestionContext::route('/{record}/edit'),
        ];
    }
}
