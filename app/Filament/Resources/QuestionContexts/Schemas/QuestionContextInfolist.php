<?php

namespace App\Filament\Resources\QuestionContexts\Schemas;

use App\Filament\Support\QuestionnaireEntries;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class QuestionContextInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->icon(Heroicon::OutlinedInformationCircle)->schema([
                TextEntry::make('question.title')->label('السؤال')->placeholder('—'),
                TextEntry::make('department.name')->label('القسم الرئيسي')->placeholder('—'),
                TextEntry::make('projectCategory.name')->label('تصنيف المشروع')->placeholder('—'),
                TextEntry::make('projectType.name')->label('نوع المشروع')->placeholder('—'),
                TextEntry::make('designStyle.name')->label('النمط التصميمي')->placeholder('—'),
            ])->columns(['default' => 1, 'md' => 2])->columnSpanFull(),
            QuestionnaireEntries::timestamps(),
        ]);
    }
}
