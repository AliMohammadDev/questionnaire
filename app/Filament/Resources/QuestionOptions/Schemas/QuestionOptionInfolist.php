<?php

namespace App\Filament\Resources\QuestionOptions\Schemas;

use App\Filament\Support\QuestionnaireEntries;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class QuestionOptionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->icon(Heroicon::OutlinedInformationCircle)->schema([
                TextEntry::make('question.title')->label('السؤال')->placeholder('—'),
                TextEntry::make('label')->label('نص الخيار')->placeholder('—'),
                TextEntry::make('value')->label('قيمة الخيار')->placeholder('—'),
                IconEntry::make('is_active')->label('فعال')->boolean()->placeholder('—'),
                TextEntry::make('sort_order')->label('ترتيب الظهور')->placeholder('—'),
            ])->columns(['default' => 1, 'md' => 2])->columnSpanFull(),
            Section::make('الصور والمرفقات')->icon(Heroicon::OutlinedPhoto)->schema([SpatieMediaLibraryImageEntry::make('image')->label('صورة الخيار')->collection('image')->disk('public')->visibility('public')->imageHeight(180)->placeholder('لا توجد صور')->columnSpanFull()])->columnSpanFull(),
            QuestionnaireEntries::timestamps(),
        ]);
    }
}
