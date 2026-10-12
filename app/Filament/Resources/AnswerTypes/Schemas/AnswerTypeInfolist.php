<?php

namespace App\Filament\Resources\AnswerTypes\Schemas;

use App\Filament\Support\QuestionnaireEntries;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AnswerTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->icon(Heroicon::OutlinedInformationCircle)->schema([
                TextEntry::make('name')->label('اسم النوع')->placeholder('—'),
                TextEntry::make('code')->label('نوع الحقل')->placeholder('—')->badge()->formatStateUsing(fn (string $state): string => ['short_text' => 'نص قصير', 'paragraph' => 'نص طويل', 'number' => 'رقم', 'single_choice' => 'اختيار واحد', 'multiple_choice' => 'اختيارات متعددة', 'dropdown' => 'قائمة منسدلة', 'date' => 'تاريخ', 'time' => 'وقت', 'file' => 'ملف', 'rating' => 'تقييم', 'boolean' => 'نعم أو لا', 'linear_scale' => 'مقياس خطي', 'multiple_choice_grid' => 'شبكة اختيار واحد', 'checkbox_grid' => 'شبكة اختيارات متعددة', 'duration' => 'مدة زمنية'][$state] ?? $state),
                IconEntry::make('has_options')->label('يحتوي خيارات')->boolean()->placeholder('—'),
                IconEntry::make('allows_multiple')->label('يسمح بعدة اختيارات')->boolean()->placeholder('—'),
            ])->columns(['default' => 1, 'md' => 2])->columnSpanFull(),
            Section::make('الحالة والترتيب')->icon(Heroicon::OutlinedAdjustmentsHorizontal)->schema([
                TextEntry::make('is_active')->label('الحالة')->badge()->formatStateUsing(fn (bool $state): string => $state ? 'فعال' : 'غير فعال')->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                TextEntry::make('sort_order')->label('ترتيب الظهور')->placeholder('—'),
            ])->columns(['default' => 1, 'md' => 2])->columnSpanFull(),
            QuestionnaireEntries::timestamps(),
        ]);
    }
}
