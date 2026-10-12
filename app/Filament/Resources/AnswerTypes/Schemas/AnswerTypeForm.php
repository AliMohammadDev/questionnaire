<?php

namespace App\Filament\Resources\AnswerTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class AnswerTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->schema([
                TextInput::make('name')->label('اسم النوع')->maxLength(255)->required(),
                Select::make('code')->label('نوع الحقل')->options(['short_text' => 'نص قصير', 'paragraph' => 'نص طويل', 'number' => 'رقم', 'single_choice' => 'اختيار واحد', 'multiple_choice' => 'اختيارات متعددة', 'dropdown' => 'قائمة منسدلة', 'date' => 'تاريخ', 'time' => 'وقت', 'file' => 'ملف', 'rating' => 'تقييم', 'boolean' => 'نعم أو لا', 'linear_scale' => 'مقياس خطي', 'multiple_choice_grid' => 'شبكة اختيار واحد', 'checkbox_grid' => 'شبكة اختيارات متعددة', 'duration' => 'مدة زمنية'])->required()->unique(ignoreRecord: true)->live()->afterStateUpdated(function (Set $set, ?string $state): void {
                    $set('has_options', in_array($state, ['single_choice', 'multiple_choice', 'dropdown']));
                    $set('allows_multiple', in_array($state, ['multiple_choice', 'checkbox_grid'], true));
                }),
                Toggle::make('has_options')->label('يحتوي خيارات')->default(false)->disabled()->dehydrated(),
                Toggle::make('allows_multiple')->label('يسمح بعدة اختيارات')->default(false)->disabled()->dehydrated(),
            ])->columns(2)->columnSpanFull(),
            Section::make('العرض والترتيب')->schema([
                Toggle::make('is_active')->label('فعال')->default(true),
                TextInput::make('sort_order')->label('ترتيب الظهور')->numeric()->integer()->minValue(0)->default(0)->required(),
            ])->columns(2)->columnSpanFull(),
        ]);
    }
}
