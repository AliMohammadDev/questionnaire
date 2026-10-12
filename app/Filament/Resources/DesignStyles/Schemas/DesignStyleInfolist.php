<?php

namespace App\Filament\Resources\DesignStyles\Schemas;

use App\Filament\Support\QuestionnaireEntries;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class DesignStyleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->icon(Heroicon::OutlinedInformationCircle)->schema([
                TextEntry::make('name')->label('اسم النمط')->placeholder('—'),
                TextEntry::make('description')->label('الوصف')->placeholder('—')->columnSpanFull(),
                IconEntry::make('is_active')->label('فعال')->boolean()->placeholder('—'),
                TextEntry::make('sort_order')->label('ترتيب الظهور')->placeholder('—'),
            ])->columns(['default' => 1, 'md' => 2])->columnSpanFull(),
            Section::make('الصور والمرفقات')->icon(Heroicon::OutlinedPhoto)->schema([SpatieMediaLibraryImageEntry::make('cover')->label('صورة الغلاف')->collection('cover')->disk('public')->visibility('public')->imageHeight(180)->placeholder('لا توجد صور')->columnSpanFull(), SpatieMediaLibraryImageEntry::make('gallery')->label('معرض الصور')->collection('gallery')->disk('public')->visibility('public')->imageHeight(180)->placeholder('لا توجد صور')->columnSpanFull()])->columnSpanFull(),
            QuestionnaireEntries::timestamps(),
        ]);
    }
}
