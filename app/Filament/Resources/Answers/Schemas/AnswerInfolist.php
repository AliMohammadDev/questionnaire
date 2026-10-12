<?php

namespace App\Filament\Resources\Answers\Schemas;

use App\Filament\Support\QuestionnaireEntries;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AnswerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->icon(Heroicon::OutlinedInformationCircle)->schema([
                TextEntry::make('project.project_code')->label('المشروع')->placeholder('—'),
                TextEntry::make('question.title')->label('السؤال')->placeholder('—'),
                TextEntry::make('value_text')->label('الإجابة النصية')->placeholder('—')->columnSpanFull(),
                TextEntry::make('value_number')->label('الإجابة الرقمية')->placeholder('—'),
                IconEntry::make('value_boolean')->label('الإجابة')->boolean()->placeholder('—'),
                TextEntry::make('value_date')->label('التاريخ')->placeholder('—')->date('Y/m/d'),
                TextEntry::make('value_time')->label('الوقت')->placeholder('—'),
                TextEntry::make('options.label')->label('الخيارات المختارة')->badge()->placeholder('لا توجد اختيارات')->columnSpanFull(),
                QuestionnaireEntries::structuredData('value_json', 'شبكة الإجابة والبيانات الإضافية'),
            ])->columns(['default' => 1, 'md' => 2])->columnSpanFull(),
            Section::make('الصور والمرفقات')->icon(Heroicon::OutlinedPhoto)->schema([QuestionnaireEntries::attachments()])->columnSpanFull(),
            QuestionnaireEntries::timestamps(),
        ]);
    }
}
