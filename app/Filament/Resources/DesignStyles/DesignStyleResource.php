<?php

namespace App\Filament\Resources\DesignStyles;

use App\Filament\Resources\DesignStyles\Pages\CreateDesignStyle;
use App\Filament\Resources\DesignStyles\Pages\EditDesignStyle;
use App\Filament\Resources\DesignStyles\Pages\ListDesignStyles;
use App\Filament\Resources\DesignStyles\Pages\ViewDesignStyle;
use App\Filament\Resources\DesignStyles\Schemas\DesignStyleForm;
use App\Filament\Resources\DesignStyles\Schemas\DesignStyleInfolist;
use App\Filament\Resources\DesignStyles\Tables\DesignStylesTable;
use App\Models\DesignStyle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DesignStyleResource extends Resource
{
    protected static ?string $model = DesignStyle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static ?string $modelLabel = 'نمط تصميمي';

    protected static ?string $pluralModelLabel = 'الأنماط التصميمية';

    protected static string|\UnitEnum|null $navigationGroup = 'إعدادات المشاريع';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DesignStyleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DesignStyleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DesignStylesTable::configure($table);
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
            'index' => ListDesignStyles::route('/'),
            'create' => CreateDesignStyle::route('/create'),
            'view' => ViewDesignStyle::route('/{record}'),
            'edit' => EditDesignStyle::route('/{record}/edit'),
        ];
    }
}
