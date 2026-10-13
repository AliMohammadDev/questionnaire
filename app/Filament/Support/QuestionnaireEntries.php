<?php

namespace App\Filament\Support;

use App\Models\Media;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

class QuestionnaireEntries
{
  public static function timestamps(): Section
  {
    return Section::make('معلومات السجل')
      ->icon(Heroicon::OutlinedClock)
      ->schema([
        TextEntry::make('id')->label('رقم السجل')->copyable(),
        TextEntry::make('created_at')->label('تاريخ الإضافة')->dateTime('Y/m/d H:i')->placeholder('—'),
        TextEntry::make('updated_at')->label('آخر تحديث')->dateTime('Y/m/d H:i')->placeholder('—'),
      ])
      ->columns(['default' => 1, 'md' => 3])
      ->collapsible()
      ->columnSpanFull();
  }

  public static function structuredData(string $name, string $label): RepeatableEntry
  {
    return RepeatableEntry::make($name)->label($label)
      ->state(function (Model $record) use ($name): array {
        $rows = [];

        foreach ($record->getAttribute($name) ?? [] as $key => $value) {
          $rows[] = [
            'label' => (string) $key,
            'value' => is_array($value) ? implode('، ', array_map(fn(mixed $item): string => is_array($item) ? json_encode($item, JSON_UNESCAPED_UNICODE) : (string) $item, $value)) : (is_bool($value) ? ($value ? 'نعم' : 'لا') : (string) $value),
          ];
        }

        return $rows;
      })
      ->schema([
        TextEntry::make('label')->label('البند'),
        TextEntry::make('value')->label('القيمة')->placeholder('—'),
      ])->columns(['default' => 1, 'md' => 2])->placeholder('لا توجد بيانات إضافية')->columnSpanFull();
  }

  public static function attachments(): RepeatableEntry
  {
    return RepeatableEntry::make('media')->label('المرفقات')
      ->schema([
        ImageEntry::make('preview')->label('معاينة')
          ->state(fn(Media $record): string => $record->getUrl())
          ->visible(fn(Media $record): bool => str_starts_with($record->mime_type ?? '', 'image/'))
          ->imageHeight(160),
        TextEntry::make('file_name')->label('الملف')
          ->url(fn(Media $record): string => $record->getUrl())
          ->openUrlInNewTab()->icon(Heroicon::OutlinedArrowTopRightOnSquare),
        TextEntry::make('mime_type')->label('نوع الملف')->badge(),
        TextEntry::make('size')->label('الحجم')->formatStateUsing(fn(int $state): string => Number::fileSize($state)),
      ])
      ->columns(['default' => 1, 'md' => 2])
      ->placeholder('لا توجد مرفقات')
      ->columnSpanFull();
  }
}
