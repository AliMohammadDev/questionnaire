<?php

namespace App\Filament\Resources\QuestionOptions\Schemas;

use App\Filament\Support\QuestionnaireFields;
use App\Models\Question;
use Closure;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class QuestionOptionForm
{
    public static function configureCreate(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('السؤال')->description('اختر السؤال مرة واحدة، ثم أضف جميع خياراته أدناه.')
                ->schema([self::question()])->columnSpanFull(),
            Repeater::make('options')->key('options')->label('خيارات السؤال')
                ->schema([
                    TextInput::make('label')->label('نص الخيار')->maxLength(255)->required(),
                    Toggle::make('is_active')->label('فعال')->default(true),
                    SpatieMediaLibraryFileUpload::make('image')->label('صورة الخيار')->collection('image')->disk('public')->maxSize(10240)->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->columnSpanFull(),
                ])
                ->columns(2)->defaultItems(1)->minItems(1)->required()
                ->addActionLabel('إضافة خيار آخر')->reorderableWithButtons()
                ->itemLabel(fn (array $state): string => $state['label'] ?? 'خيار جديد')
                ->columnSpanFull(),
        ]);
    }

    public static function question(): Select
    {
        return QuestionnaireFields::relationship('question_id', 'question', 'title', 'السؤال')
            ->relationship('question', 'title', modifyQueryUsing: fn (Builder $query): Builder => $query
                ->with('answerType')
                ->whereHas('answerType', fn (Builder $query): Builder => $query->where('has_options', true)))
            ->getOptionLabelFromRecordUsing(fn (Question $record): string => $record->title.' — '.$record->answerType->name)
            ->rules([fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                if (! Question::find($value)?->answerType?->has_options) {
                    $fail('نوع إجابة السؤال لا يدعم الخيارات.');
                }
            }]);
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->schema([
                self::question(),
                TextInput::make('label')->label('نص الخيار')->maxLength(255)->required(),
            ])->columns(2)->columnSpanFull(),
            Section::make('العرض والترتيب')->schema([
                Toggle::make('is_active')->label('فعال')->default(true),
                TextInput::make('sort_order')->label('ترتيب الظهور')->numeric()->integer()->minValue(0)->default(0)->required(),
            ])->columns(2)->columnSpanFull(),
            Section::make('الصور والمرفقات')->schema([
                SpatieMediaLibraryFileUpload::make('image')->label('صورة الخيار')->collection('image')->disk('public')->maxSize(10240)->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),
        ]);
    }
}
