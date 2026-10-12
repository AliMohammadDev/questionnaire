<?php

namespace App\Filament\Resources\Media;

use App\Filament\Resources\Media\Pages\EditMedia;
use App\Filament\Resources\Media\Pages\ListMedia;
use App\Filament\Resources\Media\Pages\ViewMedia;
use App\Filament\Resources\Media\Schemas\MediaForm;
use App\Filament\Resources\Media\Schemas\MediaInfolist;
use App\Filament\Resources\Media\Tables\MediaTable;
use App\Models\Media;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MediaResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = Media::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $modelLabel = 'ملف';

    protected static ?string $pluralModelLabel = 'مكتبة الوسائط';

    protected static string|\UnitEnum|null $navigationGroup = 'الإجابات والمرفقات';

    protected static ?int $navigationSort = 15;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return MediaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MediaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MediaTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
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
            'index' => ListMedia::route('/'),
            'view' => ViewMedia::route('/{record}'),
            'edit' => EditMedia::route('/{record}/edit'),
        ];
    }
}
