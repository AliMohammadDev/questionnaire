<?php

namespace App\Filament\Resources\Media\Schemas;

use App\Filament\Support\QuestionnaireEntries;
use App\Models\Media;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Number;

class MediaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->icon(Heroicon::OutlinedInformationCircle)->schema([
                ImageEntry::make('preview')->label('معاينة الصورة')->state(fn (Media $record): string => $record->getUrl())->visible(fn (Media $record): bool => str_starts_with($record->mime_type ?? '', 'image/'))->imageHeight(240)->columnSpanFull(),
                TextEntry::make('name')->label('اسم الملف')->placeholder('—'),
                TextEntry::make('file_name')->label('اسم الملف المخزن')->placeholder('—'),
                TextEntry::make('collection_name')->label('مجموعة الملفات')->placeholder('—'),
                TextEntry::make('mime_type')->label('نوع الملف')->placeholder('—'),
                TextEntry::make('size')->label('الحجم')->formatStateUsing(fn (int $state): string => Number::fileSize($state)),
                TextEntry::make('model_type')->label('نوع السجل المرتبط')->formatStateUsing(fn (string $state): string => class_basename($state)),
                TextEntry::make('model_id')->label('رقم السجل المرتبط'),
                TextEntry::make('file_url')->label('فتح الملف')->state(fn (Media $record): string => $record->file_name)->url(fn (Media $record): string => $record->getUrl())->openUrlInNewTab(),
            ])->columns(['default' => 1, 'md' => 2])->columnSpanFull(),
            QuestionnaireEntries::timestamps(),
        ]);
    }
}
