<?php

namespace App\Filament\Resources\ProjectTypes\Schemas;

use App\Filament\Support\QuestionnaireEntries;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ProjectTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->icon(Heroicon::OutlinedInformationCircle)->schema([
                TextEntry::make('projectCategory.name')->label('تصنيف المشروع')->placeholder('—'),
                TextEntry::make('name')->label('اسم النوع')->placeholder('—'),
                TextEntry::make('description')->label('الوصف')->placeholder('—')->columnSpanFull(),
            ])->columns(['default' => 1, 'md' => 2])->columnSpanFull(),
            Section::make('الحالة والترتيب')->icon(Heroicon::OutlinedAdjustmentsHorizontal)->schema([
                TextEntry::make('is_active')->label('الحالة')->badge()->formatStateUsing(fn (bool $state): string => $state ? 'فعال' : 'غير فعال')->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                TextEntry::make('sort_order')->label('ترتيب الظهور')->placeholder('—'),
            ])->columns(['default' => 1, 'md' => 2])->columnSpanFull(),
            Section::make('الصور والمرفقات')->icon(Heroicon::OutlinedPhoto)->schema([
                SpatieMediaLibraryImageEntry::make('image')->label('صور نوع المشروع')->collection('image')->disk('public')->visibility('public')->imageHeight(180)->placeholder('لا توجد صور')->columnSpanFull(),
            ])->columns(['default' => 1, 'md' => 2])->columnSpanFull(),
            QuestionnaireEntries::timestamps(),
        ]);
    }
}
