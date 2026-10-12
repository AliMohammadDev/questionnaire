<?php

namespace App\Filament\Resources\Questions\Schemas;

use App\Filament\Support\QuestionnaireEntries;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class QuestionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->icon(Heroicon::OutlinedInformationCircle)->schema([
                TextEntry::make('questionSection.name')->label('قسم الأسئلة')->placeholder('—'),
                TextEntry::make('answerType.name')->label('نوع الإجابة')->placeholder('—'),
                TextEntry::make('title')->label('نص السؤال')->placeholder('—')->columnSpanFull(),
                TextEntry::make('description')->label('شرح السؤال')->placeholder('—')->columnSpanFull(),
                TextEntry::make('placeholder')->label('النص الإرشادي')->placeholder('—'),
                IconEntry::make('is_required')->label('الإجابة إلزامية')->boolean()->placeholder('—'),
                TextEntry::make('validation_rules.rows')->label('صفوف شبكة الإجابة')->placeholder('—')->badge()->columnSpanFull(),
                TextEntry::make('validation_rules.columns')->label('خيارات أعمدة الشبكة')->placeholder('—')->badge()->columnSpanFull(),
                QuestionnaireEntries::structuredData('validation_rules', 'قواعد التحقق'),
            ])->columns(['default' => 1, 'md' => 2])->columnSpanFull(),
            Section::make('الحالة والترتيب')->icon(Heroicon::OutlinedAdjustmentsHorizontal)->schema([
                TextEntry::make('is_active')->label('الحالة')->badge()->formatStateUsing(fn (bool $state): string => $state ? 'فعال' : 'غير فعال')->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                TextEntry::make('sort_order')->label('ترتيب الظهور')->placeholder('—'),
            ])->columns(['default' => 1, 'md' => 2])->columnSpanFull(),
            QuestionnaireEntries::timestamps(),
        ]);
    }
}
