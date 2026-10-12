<?php

namespace App\Filament\Resources\Media\Tables;

use App\Models\Media;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Number;

class MediaTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('الملف')->searchable(),
            TextColumn::make('file_name')->label('اسم الملف')->searchable(),
            TextColumn::make('collection_name')->label('المجموعة')->badge(),
            TextColumn::make('mime_type')->label('نوع الملف'),
            TextColumn::make('size')->label('الحجم')->formatStateUsing(fn (int $state): string => Number::fileSize($state)),
            TextColumn::make('created_at')->label('تاريخ الإضافة')->dateTime('Y/m/d H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
        ])->recordActions([ViewAction::make(), EditAction::make(), Action::make('open')->label('فتح الملف')->icon('heroicon-o-arrow-top-right-on-square')->url(fn (Media $record): string => $record->getUrl())->openUrlInNewTab()])
            ->defaultSort('id', 'desc')
            ->striped()->paginated([10, 25, 50])
            ->emptyStateHeading('لا توجد سجلات بعد')
            ->emptyStateDescription('ارفع الصور والمرفقات من صفحة النمط أو نوع المشروع أو الخيار أو الإجابة أو المشروع.');
    }
}
