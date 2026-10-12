<?php

namespace App\Filament\Resources\AnswerOptions;

use App\Filament\Resources\AnswerOptions\Pages\CreateAnswerOption;
use App\Filament\Resources\AnswerOptions\Pages\EditAnswerOption;
use App\Filament\Resources\AnswerOptions\Pages\ListAnswerOptions;
use App\Filament\Resources\AnswerOptions\Pages\ViewAnswerOption;
use App\Filament\Resources\AnswerOptions\Schemas\AnswerOptionForm;
use App\Filament\Resources\AnswerOptions\Schemas\AnswerOptionInfolist;
use App\Filament\Resources\AnswerOptions\Tables\AnswerOptionsTable;
use App\Models\AnswerOption;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AnswerOptionResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = AnswerOption::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckCircle;

    protected static ?string $modelLabel = 'خيار إجابة';

    protected static ?string $pluralModelLabel = 'خيارات الإجابات';

    protected static string|\UnitEnum|null $navigationGroup = 'الإجابات والمرفقات';

    protected static ?int $navigationSort = 14;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return AnswerOptionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AnswerOptionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnswerOptionsTable::configure($table);
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
            'index' => ListAnswerOptions::route('/'),
            'create' => CreateAnswerOption::route('/create'),
            'view' => ViewAnswerOption::route('/{record}'),
            'edit' => EditAnswerOption::route('/{record}/edit'),
        ];
    }
}
