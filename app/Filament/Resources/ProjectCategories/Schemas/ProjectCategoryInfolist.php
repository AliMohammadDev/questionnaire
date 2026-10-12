<?php

namespace App\Filament\Resources\ProjectCategories\Schemas;

use App\Filament\Support\QuestionnaireEntries;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ProjectCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->icon(Heroicon::OutlinedInformationCircle)->schema([
                TextEntry::make('department.name')->label('القسم الرئيسي')->placeholder('—'),
                TextEntry::make('name')->label('اسم التصنيف')->placeholder('—'),
                TextEntry::make('description')->label('الوصف')->placeholder('—')->columnSpanFull(),
                IconEntry::make('is_active')->label('فعال')->boolean()->placeholder('—'),
                TextEntry::make('sort_order')->label('ترتيب الظهور')->placeholder('—'),
            ])->columns(['default' => 1, 'md' => 2])->columnSpanFull(),
            Section::make('الصور')->schema([QuestionnaireEntries::attachments()])->columnSpanFull(),
            QuestionnaireEntries::timestamps(),
        ]);
    }
}
