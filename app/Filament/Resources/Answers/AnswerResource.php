<?php

namespace App\Filament\Resources\Answers;

use App\Filament\Resources\Answers\Pages\CreateAnswer;
use App\Filament\Resources\Answers\Pages\EditAnswer;
use App\Filament\Resources\Answers\Pages\ListAnswers;
use App\Filament\Resources\Answers\Pages\ViewAnswer;
use App\Filament\Resources\Answers\Schemas\AnswerForm;
use App\Filament\Resources\Answers\Schemas\AnswerInfolist;
use App\Filament\Resources\Answers\Tables\AnswersTable;
use App\Models\Answer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AnswerResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = Answer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $modelLabel = 'إجابة';

    protected static ?string $pluralModelLabel = 'الإجابات';

    protected static string|\UnitEnum|null $navigationGroup = 'الإجابات والمرفقات';

    protected static ?int $navigationSort = 13;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return AnswerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AnswerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnswersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AnswerOptionsRelationManager::class,
            RelationManagers\OptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnswers::route('/'),
            'create' => CreateAnswer::route('/create'),
            'view' => ViewAnswer::route('/{record}'),
            'edit' => EditAnswer::route('/{record}/edit'),
        ];
    }
}
