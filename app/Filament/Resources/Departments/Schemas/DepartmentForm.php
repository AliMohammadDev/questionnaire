<?php

namespace App\Filament\Resources\Departments\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->schema([
                TextInput::make('name')->label('اسم القسم')->maxLength(255)->required(),
                Textarea::make('description')->label('الوصف')->rows(3)->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),
            Section::make('العرض والترتيب')->schema([
                Toggle::make('is_active')->label('فعال')->default(true),
                TextInput::make('sort_order')->label('ترتيب الظهور')->numeric()->integer()->minValue(0)->default(0)->required(),
            ])->columns(2)->columnSpanFull(),
            Section::make('صور القسم')->schema([
                SpatieMediaLibraryFileUpload::make('gallery')->label('الصور')->collection('gallery')->disk('public')
                    ->multiple()->reorderable()->image()->maxSize(10240)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->columnSpanFull(),
            ])->columnSpanFull(),
        ]);
    }
}
