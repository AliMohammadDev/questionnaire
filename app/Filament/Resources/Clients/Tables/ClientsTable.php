<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('العميل')->searchable()->sortable(),
            TextColumn::make('phone')->label('الهاتف')->searchable()->copyable(),
            TextColumn::make('email')->label('البريد')->searchable(),
            TextColumn::make('created_at')->label('تاريخ الإضافة')->dateTime('Y/m/d H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
        ])->recordActions([ViewAction::make(), EditAction::make()])
            ->defaultSort('id', 'desc')
            ->striped()->paginated([10, 25, 50])
            ->emptyStateHeading('لا توجد سجلات بعد')
            ->emptyStateDescription('ابدأ بإضافة أول سجل من زر الإضافة أعلى الصفحة.');
    }
}
